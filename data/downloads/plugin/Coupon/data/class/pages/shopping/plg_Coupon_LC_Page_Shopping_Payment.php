<?php

require_once CLASS_EX_REALDIR . 'page_extends/shopping/LC_Page_Shopping_Payment_Ex.php';
require_once CLASS_EX_REALDIR . 'SC_FormParam_Ex.php';

/**
 * クーポン入力画面 のページクラス.
 *
 * @package Coupon
 */
class plg_Coupon_LC_Page_Shopping_Payment extends LC_Page_Shopping_Payment_Ex {

	function plg_Coupon_LC_Page_Shopping_Payment(){

	}

	function exec(&$objPage){
		$this->action($objPage);
	}

	function action(&$objPage){

        $objSiteSess = new SC_SiteSession_Ex();
		$objFormParam = new SC_FormParam_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();


		// パラメーター情報の初期化
		$this->setFormParams($objFormParam, $_POST, false, $objPage->arrShipping);


		switch ($this->getMode()) {
			case "confirm":
				// パラメータのチェック
				$objPage->arrErr["coupon_id_name"] = $this->lfCheckError($objFormParam,$_POST["coupon_check"],$_POST["coupon_id_name"], $objPage->arrPrices['payment_total']);


				if (SC_Utils_Ex::isBlank($objPage->arrErr)) {

					//受注一時テーブルの更新を行う
					$this->lfRegistData($objPage->tpl_uniqid, $objFormParam->getDbArray(), $objPurchase);

					// 正常に登録されたことを記録しておく
					$objSiteSess->setRegistFlag();

					// 確認ページへ移動
					SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);
					exit;
				} else {

					//確認画面へのリダイレクトを防ぐ
					$flg = false;
					foreach($objPage->arrErr as $key=>$data){
						if($key!="coupon_id_name"){
							$flg = true;
						}
					}
					//クーポンに関するエラーのみの場合リダイレクト処理
					if($flg==false){
						SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
						SC_Response_Ex::actionExit();
					}
				}
				break;

			default:
				//入力チェック
				if($objPage->arrForm["coupon_check"]["value"]==1){
					$objPage->arrErr["coupon_id_name"] = $this->lfCheckError($objFormParam,$objPage->arrForm["coupon_check"]["value"],$objPage->arrForm["coupon_id_name"]["value"], $objPage->arrPrices['payment_total']);
				}
				break;
		}

	}


	/**
	 * パラメーターの初期化を行い, 初期値を設定する.
	 *
	 * @param SC_FormParam $objFormParam SC_FormParam インスタンス
	 * @param array $arrParam 設定する値の配列
	 * @param boolean $deliv_only deliv_id チェックのみの場合 true
	 * @param array $arrShipping 配送先情報の配列
	 */
	function setFormParams(&$objFormParam, $arrParam, $deliv_only, &$arrShipping) {
		$this->lfInitParam($objFormParam, $deliv_only, $arrShipping,$num);
		$objFormParam->setParam($arrParam);
		$objFormParam->convParam();
	}

	/**
	 * パラメーター情報の初期化を行う.
	 *
	 * @param SC_FormParam $objFormParam SC_FormParam インスタンス
	 * @param boolean $deliv_only 必須チェックは deliv_id のみの場合 true
	 * @param array $arrShipping 配送先情報の配列
	 * @return void
	 */
	function lfInitParam(&$objFormParam, $deliv_only, &$arrShipping) {
        // 2013.02.28 SEED クーポン機能用に追加
        $objFormParam->addParam("クーポン使用", "coupon_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');
        $objFormParam->addParam("クーポンコード", "coupon_id_name", STEXT_LEN, "n", array("MAX_LENGTH_CHECK"));

        $objFormParam->setParam($arrParam);
        $objFormParam->convParam();
	}

	/**
	 * 入力内容のチェックを行なう.
	 *
	 * @param SC_FormParam $objFormParam SC_FormParam インスタンス
	 * @param integer $payment_total
	 * @return array 入力チェック結果の配列
	 */
	function lfCheckError(&$objFormParam,$coupon_check,$coupn_id_name, $payment_total=0) {

		// 入力データを渡す。
		$arrForm =  $objFormParam->getHashArray();
		$objErr = new SC_CheckError_Ex($arrForm);
		$objErr->arrErr = $objFormParam->checkError();


		// 2013.02.28 SEED クーポン用変数の初期化
		if (!isset($coupon_check)) $coupon_check = "";
		if (!isset($coupn_id_name)) $coupn_id_name = "";


		// 2013.02.28 SEED クーポンチェック(coupon_check=1:使用する)
		if($coupon_check == '1' || (SC_MobileUserAgent::isMobile()&&strlen($coupn_id_name)>0) ) {
			$objErr->doFunc(array("クーポンコード", "coupon_id_name"), array("EXIST_CHECK"));
			// クーポンコードの存在・有効チェック
			if(strlen($coupn_id_name)>0) {
				if( !$this->lfIsExistCoupon($coupn_id_name) ){
					$objErr->arrErr['coupon_id_name'] = "※ 入力されたクーポンコードは存在しません<br>";
				}else if( !$this->lfIsEnableCoupon($coupn_id_name) ){
					$objErr->arrErr['coupon_id_name'] = "※ 入力されたクーポンコードは有効期限が切れています<br>";
				}else if( !$this->lfIsTargetRangeCoupon($coupn_id_name) ){
					$objErr->arrErr['coupon_id_name'] = "※ 入力されたクーポンコードは対象の商品が限定されています<br>";
				}else if( !$this->lfIsUsedCoupon($coupn_id_name) ){
					$objErr->arrErr['coupon_id_name'] = "※ 入力されたクーポンコードは一度利用されています。<br>";
				}else if( !$this->lfPriceCheckCoupon($coupn_id_name,$payment_total) ){
					$objQuery = new SC_Query_Ex();
					$arrRet = $objQuery->select("*", "dtb_coupon", "del_flg=0 AND coupon_id_name = ? ", array($coupn_id_name));
					$objErr->arrErr['coupon_id_name'] = "※ 入力されたクーポンコードのご使用条件はご購入合計金額 ".$arrRet[0]['discount_price']."円以上となっております。<br>";

				}else if( !$this->lfUseLimitCheckCoupon($coupn_id_name,$payment_total) ){ // 2013.03.04  利用可能購入金額下限チェックを追加
					$objQuery = new SC_Query_Ex();
					$arrRet = $objQuery->select("*", "dtb_coupon", "del_flg=0 AND coupon_id_name = ? ", array($coupn_id_name));
					$objErr->arrErr['coupon_id_name'] = "※ 入力されたクーポンコードのご使用条件はご購入合計金額 ".$arrRet[0]['use_limit']."円以上となっております。<br>";
				}
			}
		}
		return $objErr->arrErr["coupon_id_name"];
	}


	// 2013.02.28 SEED クーポン名からクーポンID取得
	function lfGetCouponId($coupon_id_name) {
		$objQuery = new SC_Query_Ex();
		$where = "coupon_id_name = ?";
		$arrRet = $objQuery->select("coupon_id", "dtb_coupon", $where, array($coupon_id_name));
		if ( isset($arrRet[0]['coupon_id']) ){
			return $arrRet[0]['coupon_id'];
		} else {
			return false;
		}
	}


	// 2013.02.28 SEED クーポンの存在チェック
	function lfIsExistCoupon($coupon_id_name) {
		$objQuery = new SC_Query_Ex();
		$where = "del_flg=0 AND coupon_id_name = ? ";
		$arrRet = $objQuery->select("coupon_id", "dtb_coupon", $where, array($coupon_id_name));
		return (isset($arrRet[0]['coupon_id']))? true : false;
	}

	// 2013.02.28 SEED クーポンの有効期限チェック
	function lfIsEnableCoupon($coupon_id_name) {
		$objQuery = new SC_Query_Ex();
		$where = "del_flg=0 AND enable_flg=0 AND coupon_id_name = ? AND now()>=start_date AND now() <=end_date ";
		$arrRet = $objQuery->select("coupon_id", "dtb_coupon", $where, array($coupon_id_name));
		return (isset($arrRet[0]['coupon_id']))? true : false;
	}

	// 2013.02.28 SEED クーポンの対象商品限定チェック
	function lfIsTargetRangeCoupon($coupon_id_name) {
		// クーポン対象の商品ID取得
		$objQuery = new SC_Query_Ex();
		$coupon_id = $this->lfGetCouponId($coupon_id_name);
		if($coupon_id == false) return false;

		// 全商品か限定か
		$arrRet = $objQuery->select("coupon_target", "dtb_coupon", "coupon_id = ?", array($coupon_id));
		if( $arrRet[0]['coupon_target']!=0 ) {
			$arrRet = $objQuery->select("product_id", "dtb_coupon_products", "coupon_id = ?", array($coupon_id));

			// カート内にある商品ID取得
			$objCartSess = new SC_CartSession_Ex();
			$cartkey = $objCartSess->getKey();
			$cartItems = $objCartSess->getCartList($cartkey) ;
			if (count($cartItems) > 0) {
				$cnt=1 ;
				foreach ($cartItems as $item) {
					$arrID['product_id'][$cnt] = $item['productsClass']['product_id'] ;
					$cnt++ ;
				}
			}

			$target_flg = false;
			foreach($arrRet as $target){
				if(in_array($target['product_id'],$arrID['product_id'])) {
					$target_flg = true;
					break;
				}
			}
			return $target_flg;
		} else {
			// 全商品対象の場合
			return true;
		}
	}

	// 2013.02.28 SEED クーポンの複数回利用チェック
	function lfIsUsedCoupon($coupon_id_name) {
		$objQuery = new SC_Query_Ex();
		$coupon_id = $this->lfGetCouponId($coupon_id_name);

		$arrRet = $objQuery->select("count_limit", "dtb_coupon", "coupon_id=?", array($coupon_id));
		// 使用回数無制限のときは以降のチェックを飛ばす
		if (isset($arrRet[0]['count_limit']) && (int)$arrRet[0]['count_limit'] === 0) {
			return true;
		}

		$customer_id = $_SESSION["customer"]["customer_id"];
		$arrRet = $objQuery->select("order_id", "dtb_coupon_used", "coupon_id=? AND customer_id=?", array($coupon_id,$customer_id));
		return (!isset($arrRet[0]['order_id']))? true : false;
	}

	// 2013.02.28 SEED クーポンの金額利用制限チェック
	function lfPriceCheckCoupon($coupon_id_name, $payment_total) {
		$objQuery = new SC_Query_Ex();
		$where = "del_flg=0 AND coupon_id_name = ? ";
		$arrRet = $objQuery->select("*", "dtb_coupon", $where, array($coupon_id_name));
		if( $arrRet[0]['discount_type'] == 0 && $arrRet[0]['discount_price'] > $payment_total ){
			return false;
		}
		return true;
	}

	// 2013.02.28 SEED 利用可能購入金額下限チェックを追加
	function lfUseLimitCheckCoupon($coupon_id_name, $payment_total) {
		$objQuery = new SC_Query_Ex();
		$where = "del_flg=0 AND coupon_id_name = ? ";
		$arrRet = $objQuery->select("*", "dtb_coupon", $where, array($coupon_id_name));
		if( $arrRet[0]['use_limit'] > $payment_total ){
			return false;
		}
		return true;
	}


	/**
	 * 受注一時テーブルの更新を行う（クーポンに関するカラムのみ)
	 *
	 * @param integer $uniqid 受注一時テーブルのユニークID
	 * @param array $arrForm フォームの入力値
	 * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
	 * @return void
	 */
	function lfRegistData($uniqid, $arrForm, &$objPurchase) {

		$arrForm['order_temp_id'] = $uniqid;
		$arrForm['update_date'] = 'CURRENT_TIMESTAMP';

		// 2013.02.28 SEED クーポンIDの設定
		$arrForm['coupon_id'] = $this->lfGetCouponId($arrForm['coupon_id_name']);
		if(!$arrForm['coupon_id']){
		  $arrForm["coupon_id"] = 0;
		}
		$objPurchase->saveOrderTemp($uniqid, $arrForm);
	}

}