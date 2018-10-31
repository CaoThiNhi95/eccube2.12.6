<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/SC_Coupon.php';

/**
 * クーポン管理(編集) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: plg_Coupon_LC_Page_Admin_Contents_Coupon_Input.php 21232 2011-09-04 15:44:01Z Seasoft $
 */
class plg_Coupon_LC_Page_Admin_Contents_Coupon_Input extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/plg_Coupon_coupon_input.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subno = 'coupon';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'クーポン管理【新規作成】';
        if( $_REQUEST['mode'] == 'edit'||$_REQUEST['mode'] == 'complete_edit' ) { $this->tpl_subtitle = 'クーポン管理【編集】'; }

        $this->start_selected_year = $this->end_selected_year = date("Y");
        $this->start_selected_month= $this->end_selected_month= date("n");
        $this->start_selected_day  = $this->end_selected_day  = date("j");

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrEnable = $masterData->getMasterData("mtb_coupon_enable");
        $this->arrDiscountType = $masterData->getMasterData("mtb_coupon_discount_type");
        $this->arrCouponTarget = $masterData->getMasterData("mtb_coupon_target");
        $this->arrCountLimit = $masterData->getMasterData("mtb_coupon_count_limit");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        // 初期化（オブジェクト作成）
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDate = new SC_Date(ADMIN_NEWS_STARTYEAR);
        $objQuery = new SC_Query_Ex();

        //---- 日付プルダウン設定
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // onLoadの時のJavaScriptの設定
        $this->tpl_onload = "fnFormInit();" ;

        if (!isset($_POST['prodcuts_list']) and !is_array($_POST['prodcuts_list']) ){
            $this->prodcuts_list = array();
        } else {
            $this->prodcuts_list = $_POST['prodcuts_list'];
        }

        if (!isset($_REQUEST['coupon_id'])) $_REQUEST['coupon_id'] = "";
        if ( $_REQUEST['coupon_id'] && SC_Coupon::sfCheckNumLength($_REQUEST['coupon_id'])===true ){
            // 編集

            // データの取得
            $sql = "SELECT *, cast(start_date as date) as startdate, cast(end_date as date) as enddate FROM dtb_coupon WHERE coupon_id = ? AND del_flg = 0";
            $result = $objQuery->getAll($sql, array($_REQUEST['coupon_id']));
            $this->arrForm = $result[0];

            //日付の指定
            $arrData = split("-",$this->arrForm["startdate"]);
            $this->start_selected_year =$arrData[0];
            $this->start_selected_month=$arrData[1];
            $this->start_selected_day  =$arrData[2];
            $arrData = split("-",$this->arrForm["enddate"]);
            $this->end_selected_year =$arrData[0];
            $this->end_selected_month=$arrData[1];
            $this->end_selected_day  =$arrData[2];
        }
        foreach( $_POST as $key=>$val ) $this->arrForm[$key] = $val;

        // modeの初期化
        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // モードによる処理分岐
        switch($this->getMode()) {
            case 'product_select':
            	if(!in_array($_POST['product_id'],$this->prodcuts_list)){
            		array_push($this->prodcuts_list, $_POST['product_id']);
            	}
                break;
            case 'delete_product':
                foreach($this->prodcuts_list as $key => $val){
                    if( $val == $_POST['product_id'] ){ unset($this->prodcuts_list[$key]); }
                }
                break;
            case 'edit':
                $this->prodcuts_list = $objQuery->getCol("product_id", "dtb_coupon_products", "coupon_id = ?",array($_REQUEST['coupon_id']));
                break;
            case 'regist':
                // 新規登録
                $this->arrForm = $this->lfConvData( $_POST );
                $this->arrErr = $this->lfErrorCheck($objQuery, $this->arrForm);
                if ( ! $this->arrErr ){
                    // エラーが無いときは登録・編集
                    $this->lfRegistData($objQuery, $this->arrForm, $_POST['coupon_id']);
                    // 自分を再読込して、完了画面へ遷移(関数がバージョンアップで変わっているので、注意)
                    if($_POST['coupon_id']){
                    	SC_Response_Ex::reload(array("mode" => "complete_edit"));
                    }else{
                    	SC_Response_Ex::reload(array("mode" => "complete"));
                    }
                }
                break;
            case 'complete':
            case 'complete_edit':
            	// 完了画面表示
                $this->tpl_mainpage = 'contents/plg_Coupon_coupon_complete.tpl';
                // onLoadの時のJavaScriptの設定 => 完了画面では不要なのでリセット
                $this->tpl_onload = "" ;
                break;
            default:break;
        }

        //対象商品名取得
        if( count($this->prodcuts_list) > 0 ) {
            $ids = implode(",", $this->prodcuts_list);
            $this->arrProducts = $objQuery->select(" * ", "dtb_products", "product_id IN ( ".$ids." )");
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // クーポン情報の登録／編集
    function lfRegistData( $objQuery, $arrVal, $id = null ){

        // 登録／編集データの作成
        $sqlval['coupon_id_name'] = $arrVal['coupon_id_name'];
        $sqlval['discount_price'] = $arrVal['discount_price'];
        $sqlval['discount_percent'] = $arrVal['discount_percent'];
        $sqlval['discount_type'] = $arrVal['discount_type'];
        $sqlval['memo'] = $arrVal['memo'];
        $sqlval['coupon_target'] = $arrVal['coupon_target'];
        $sqlval['enable_flg'] = $arrVal['enable_flg'];
        $sqlval['start_date'] = $arrVal['start_year'] ."-". $arrVal['start_month'] ."-". $arrVal['start_day']." 00:00:00";
        $sqlval['end_date']   = $arrVal['end_year'] ."-". $arrVal['end_month'] ."-". $arrVal['end_day']." 23:59:59";
        $sqlval['update_date'] = "Now()";
        // 2012.01.30 seed.abe 利用可能購入金額下限を追加
        $sqlval['use_limit'] = $arrVal['use_limit'];
        $sqlval['count_limit'] = $arrVal['count_limit'];

        if ( $id ){
            $objQuery->update("dtb_coupon", $sqlval, "coupon_id=".$id );
        } else {
            $sqlval['create_date'] = "Now()";
            $id = $objQuery->nextVal("dtb_coupon_coupon_id");
            $sqlval['coupon_id'] = $id;
            $ret = $objQuery->insert("dtb_coupon", $sqlval);
        }

        // 一旦対象商品をすべて削除
        $objQuery->delete("dtb_coupon_products", "coupon_id = ?" ,array($id) );

        // 全商品対象でなければ対象商品を登録
        if( $arrVal['coupon_target'] != 0 ) {
            if (isset($arrVal['prodcuts_list'])){
                foreach($arrVal['prodcuts_list'] as $product_id){
                    $data = array( 'coupon_id'=> $id, 'product_id'=> $product_id);
                    $objQuery->insert("dtb_coupon_products", $data);
                }
            }
        }
    }

    // 文字列の変換（mb_convert_kanaの変換オプション）
    function lfConvData( $data ){
        $arrFlag = array( "coupon_id_name" => "a",
                          "memo" => "KV",
                          "discount_percent" => "n",
                          "discount_price" => "n",
                          // 2012.01.30 seed.abe 利用可能購入金額下限を追加
                          "use_limit" => "n",
                          "count_limit" => "n"
                        );

        if ( is_array($data) ){
            foreach ($arrFlag as $key=>$line) {
                $data[$key] = mb_convert_kana($data[$key], $line);
            }
        }

        return $data;
    }

    // 入力エラーチェック
    function lfErrorCheck($objQuery, $arrForm) {

        // 初期化
        $objErr = new SC_CheckError_Ex($arrForm);

        $objErr->doFunc(array("クーポンID", "coupon_id_name", 20),
                        array("EXIST_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK"));
        $objErr->doFunc(array(" <,>,\\,\",& ", "coupon_id_name", array("<",">","\\","\"","&") ),
                        array("PROHIBITED_STR_CHECK"));
        if (strlen($arrForm["coupon_id_name"]) > 0) {
            $arrForm['coupon_id_name'] = strtolower($arrForm['coupon_id_name']);
            $checkID = ereg_replace( "_", "#_", $arrForm["coupon_id_name"]);
            if (strlen($arrForm["coupon_id"])>0) {
                $sql = "SELECT * FROM dtb_coupon WHERE coupon_id_name ILIKE ? escape '#' AND del_flg = 0 AND coupon_id <> ?";
                $result = $objQuery->getAll($sql, array($checkID, $arrForm["coupon_id"]));
            } else {
                $sql = "SELECT * FROM dtb_coupon WHERE coupon_id_name ILIKE ? escape '#' AND del_flg = 0";
                $result = $objQuery->getAll($sql, array($checkID));
            }
            if (count($result) > 0) {
                $objErr->arrErr["coupon_id_name"] .= "※ すでに発行されたクーポンIDです。<br/>" ; }
        }

        if ( $arrForm["discount_type"] == 0 ) {
            $objErr->doFunc(array("値引き額", "discount_price", STEXT_LEN), array("EXIST_CHECK","NUM_CHECK","ZERO_CHECK"));
        } else {
            $objErr->doFunc(array("値引き率", "discount_percent", STEXT_LEN), array("EXIST_CHECK","NUM_CHECK","ZERO_CHECK"));
        }

        $objErr->doFunc(array("定率・定額", "discount_type"), array("EXIST_CHECK", "ALNUM_CHECK"));
        $objErr->doFunc(array("対象商品", "coupon_target"), array("EXIST_CHECK", "ALNUM_CHECK"));

        if ( $arrForm["coupon_target"] == 1 && count($arrForm["prodcuts_list"])==0 ) {
             $objErr->arrErr["coupon_target"] .= "<br>※対象商品が選択されていません。"; }

        $objErr->doFunc(array("メモ", 'memo', LTEXT_LEN), array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objErr->doFunc(array("有効・無効", "enable_flg"), array("EXIST_CHECK", "ALNUM_CHECK"));
        $objErr->doFunc(array("開始日付(年)", "start_year"), array("EXIST_CHECK"));
        $objErr->doFunc(array("開始日付(月)", "start_month"), array("EXIST_CHECK"));
        $objErr->doFunc(array("開始日付(日)", "start_day"), array("EXIST_CHECK"));
        $objErr->doFunc(array("開始日", "start_year", "start_month", "start_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日付(年)", "end_year"), array("EXIST_CHECK"));
        $objErr->doFunc(array("終了日付(月)", "end_month"), array("EXIST_CHECK"));
        $objErr->doFunc(array("終了日付(日)", "end_day"), array("EXIST_CHECK"));
        $objErr->doFunc(array("終了日", "end_year", "end_month", "end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
        // 2012.01.30 seed.abe 利用可能購入金額下限を追加
        $objErr->doFunc(array("利用可能購入金額下限", "use_limit", STEXT_LEN), array("EXIST_CHECK","NUM_CHECK","ZERO_CHECK"));
        $objErr->doFunc(array("回数制限", "count_limit", STEXT_LEN), array("EXIST_CHECK","NUM_CHECK"));
        return $objErr->arrErr;
    }

}
?>
