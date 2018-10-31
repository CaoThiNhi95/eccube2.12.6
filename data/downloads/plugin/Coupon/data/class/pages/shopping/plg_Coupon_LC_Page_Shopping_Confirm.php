<?php

require_once CLASS_EX_REALDIR . 'page_extends/shopping/LC_Page_Shopping_Confirm_Ex.php';
require_once CLASS_EX_REALDIR . 'SC_FormParam_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/SC_Coupon.php';

/**
 * クーポン入力画面 のページクラス.
 *
 * @package Coupon
 */
class plg_Coupon_LC_Page_Shopping_Confirm extends LC_Page_Shopping_Confirm_Ex {

	function plg_Coupon_LC_Page_Shopping_Confirm(){

	}

	function exec(&$objPage){

		//クーポンを使用するときのみ
		if($objPage->arrForm["coupon_check"]==1){
			$this->action($objPage);
		}else{
			if($this->getMode()=="confirm"){
				// 決済モジュールを使用するかどうか
				//$this->use_module = SC_Helper_Payment_Ex::useModule($objPage->arrForm['payment_id']);
				$this->use_module = $this->useModule($objPage->arrForm['payment_id']);
				//決済モジュールを使用しない場合は送信
				if(!$this->use_module){
					//メールの送信
					$objHelperMail = new SC_Helper_Mail_Ex();
					$template_id = SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE ? 2 : 1;
					$objHelperMail->sfSendOrderMail($objPage->arrForm['order_id'],$template_id,'','','',true,true);
				}
			}
		}
	}

    function action(&$objPage) {

        $objCartSess = new SC_CartSession_Ex();
        $objSiteSess = new SC_SiteSession_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objQuery = new SC_Query_Ex();
        $objDb = new SC_Helper_DB_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objHelperMail = new SC_Helper_Mail_Ex();



        // 一時受注テーブルの読込
        $arrOrderTemp = $objPurchase->getOrderTemp($objPage->tpl_uniqid);

        // 2012.02.06 SEED_KN 合計金額の一時保存
        $total_temp = $objPage->tpl_total_inctax[$objPage->cartKey] ;

        // カート集計を元に最終計算
        $arrCalcResults = $objCartSess->calculate($objPage->cartKey, $objCustomer,
                                                  $arrOrderTemp['use_point'],
                                                  $objPurchase->getShippingPref($objPage->is_multiple),
                                                  $arrOrderTemp['charge'],
                                                  $arrOrderTemp['discount'],
                                                  $arrOrderTemp['deliv_id']);


        //割引額の算出
        $resultCoupon = $this->calcCoupon($objPage->arrCartItems,$arrCalcResults,$arrOrderTemp['use_point'],$arrOrderTemp["coupon_id_name"]);
        $objPage->arrForm = array_merge($arrOrderTemp, $resultCoupon);
        // 2011.11.27 SEED_KN 引数にクーポンコードを追加
        // 2011.11.27 SEED_KN 最終的なクーポンの金額、パーセンテージをセット
        if( $objPage->arrForm['coupon_discount_price_tmp'] != "" ) {
            $objPage->arrForm['coupon_discount_price'] = $objPage->arrForm['coupon_discount_price_tmp'] ; }
        if( $objPage->arrForm['coupon_discount_percent_tmp'] != "" ) {
            $objPage->arrForm['coupon_discount_percent'] = $objPage->arrForm['coupon_discount_percent_tmp'] ; }

        // 2011.12.20 SEED_KN coupon_idを取得する
        if( $arrOrderTemp['coupon_id_name'] != "" ) {
            $objPage->arrForm['coupon_id'] = $this->lfGetCouponId($arrOrderTemp['coupon_id_name']); }



        switch($this->getMode()) {

            case 'confirm':

            	if($objPage->arrForm['coupon_id']){
            		try{
            			$objQuery->begin();

            			//クーポン情報の取得
            			$coupon_data = $objQuery->getRow("*","dtb_coupon","coupon_id = ?",array($objPage->arrForm['coupon_id']));

            			//注文情報の取得
            			$order_data = $objQuery->getRow("*","dtb_order","order_id = ?",array($objPage->arrForm['order_id']));

            			//dtb_orderの更新
            			$this->updateOrderTable($objQuery,$order_data,$coupon_data);

            			$objQuery->commit();
            			
            			// 決済モジュールを使用するかどうか
						//$this->use_module = SC_Helper_Payment_Ex::useModule($objPage->arrForm['payment_id']);
            			$this->use_module = $this->useModule($objPage->arrForm['payment_id']);
            			//決済モジュールを使用しない場合は送信
            			if(!$this->use_module){

            				//クーポン利用の履歴
            				$this->insertCouponHistory($objQuery,$order_data,$coupon_data);

            				//メールの送信
            				$template_id = SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE ? 2 : 1;
            				$objHelperMail->sfSendOrderMail($objPage->arrForm['order_id'],$template_id,'','','',true,true);

            			}
            		}catch(Exception $e){
            			$objQuery->rollback();
            		}
            	}

                break;
             default:
                break;
        }
    }

    // 2013.02.28 SEED クーポン名からクーポンID取得
    function lfGetCouponId($coupon_id_name) {
        $objQuery = new SC_Query_Ex();
        $where = "coupon_id_name LIKE ?";
        $arrRet = $objQuery->select("coupon_id", "dtb_coupon", $where, array($coupon_id_name));
        if ( isset($arrRet[0]['coupon_id']) ){
            return $arrRet[0]['coupon_id'];
        } else {
            return false;
        }
    }

    // 2013.02.28 SEED
    // クーポンコードが取得出来ていれば、クーポン情報を取得する。
    function getCouponInfo($coupon_id_name) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
    	$arrRet = array() ;
    	$objQuery->setLimit('1');
    	$arrRet = $objQuery->select(" * ", "dtb_coupon", "coupon_id_name = ?",array($coupon_id_name));
    	return $arrRet[0];
    }



    /**
     * 割引額の算出
     */
    function calcCoupon($arrCartList,$arrCalcResults,$use_point,$coupon_id_name){

    	$arrCouponInfo = array() ;
    	if( $coupon_id_name != "" ) {
    		$arrCouponInfo = $this->getCouponInfo($coupon_id_name);

    		$total_price = $arrCalcResults['subtotal'] - ( $use_point * POINT_VALUE ) ;
    		$target_price = 0 ;
    		if( $arrCouponInfo['coupon_target'] == 0 ) {
    			foreach($arrCartList as $product) { $target_price += ( $product["price"] * $product["quantity"] ) ; }
    		}else{
    			$objQuery =& SC_Query_Ex::getSingletonInstance();
    			$targets = $objQuery->getCol("product_id","dtb_coupon_products",
    					"coupon_id = ?",array($arrCouponInfo["coupon_id"]));
    			foreach($arrCartList as $product) {
    				if( in_array($product["productsClass"]["product_id"],$targets) ) $target_price += ( $product["price"] * $product["quantity"] ) ; }
    		}
    		$CONF = SC_Helper_DB_Ex::sfGetBasisData();
    		list($arrCalcResults['coupon_discount_price_tmp'],
    				$arrCalcResults['coupon_discount_percent_tmp'])
    				= SC_Coupon::sfCouponDiscount($target_price, $total_price, $arrCouponInfo, $CONF['tax_rule']);
    		// 合計金額からクーポンの割引額を引く
    		$arrCalcResults['payment_total'] -= $arrCalcResults['coupon_discount_price_tmp'] ;
    	}

    	return $arrCalcResults;
    }


    /**
     * 一時テーブルの更新
     */
    function updateOrderTmp(){

    }

    /**
     * dtb_orderの更新
     */
    function updateOrderTable(&$objQuery,$order_data,$coupon_data){

    	//商品リストの取得
    	$products_list = $objQuery->select("*","dtb_order_detail","order_id = ? ",array($order_data["order_id"]));

    	//割引額の計算
		$total_price = $order_data['subtotal'] - ( $order_data['use_point'] * POINT_VALUE ) ;
    	$target_price = 0 ;
    	if( $coupon_data['coupon_target'] == 0 ) {
    		//全商品対象クーポン
    		foreach($products_list as $product) { $target_price += ( $product["price"] * $product["quantity"] ) ; }
    	}else{
    		//商品限定クーポン
    		$targets = $objQuery->getCol("product_id","dtb_coupon_products",
    				"coupon_id = ?",array($coupon_data["coupon_id"]));
    		foreach($products_list as $product) {
    			if(in_array($product["product_id"],$targets)){
    				$target_price += ( $product["price"] * $product["quantity"] ) ;
    			}
    		}
    	}
    	$CONF = SC_Helper_DB_Ex::sfGetBasisData();
    	list($arrCalcResults['coupon_discount_price_tmp'],
    			$arrCalcResults['coupon_discount_percent_tmp'])
    			= SC_Coupon::sfCouponDiscount($target_price, $total_price, $coupon_data, $CONF['tax_rule']);

    	// 合計金額からクーポンの割引額を引く
    	$order_data["payment_total"] -= $arrCalcResults['coupon_discount_price_tmp'] ;

    	$sqlVal = array();
    	$sqlVal["payment_total"] = $order_data["payment_total"];
    	$sqlVal["coupon_discount_price"]   = $arrCalcResults["coupon_discount_price_tmp"];
    	$sqlVal["coupon_discount_percent"] = $arrCalcResults["coupon_discount_percent_tmp"];

    	//dtb_orderの更新
    	$objQuery->update("dtb_order",$sqlVal,"order_id = ? ",array($order_data["order_id"]));

    }


    /**
     * クーポン利用の履歴
     */
    function insertCouponHistory(&$objQuery,$order_data,$coupon_data){

    	$sqlVal = array();
    	$sqlVal["coupon_used_id"] = $objQuery->nextVal("dtb_coupon_used_coupon_used_id");
    	$sqlVal["coupon_id"]         = $coupon_data["coupon_id"];
    	$sqlVal["customer_id"]       = $order_data["customer_id"];
    	$sqlVal["order_id"]          = $order_data["order_id"];
    	$sqlVal["create_date"]       = "CURRENT_TIMESTAMP";

    	$objQuery->insert("dtb_coupon_used",$sqlVal);

    }
    
    /**
     * 決済モジュールを使用するかどうか.
     *
     * dtb_payment.memo03 に値が入っている場合は決済モジュールと見なす.
     *
     * @param integer $payment_id 支払い方法ID
     * @return boolean 決済モジュールを使用する支払い方法の場合 true
     */
    function useModule($payment_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $memo03 = $objQuery->get('memo03', 'dtb_payment', 'payment_id = ?', array($payment_id));
        return !SC_Utils_Ex::isBlank($memo03);
    }

}