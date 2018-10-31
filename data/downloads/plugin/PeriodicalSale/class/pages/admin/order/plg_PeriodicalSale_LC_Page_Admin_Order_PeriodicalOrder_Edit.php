<?php
/*
 * PeriodicalSale
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/plg_PeriodicalSale_SC_PeriodicalOrder.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/pages/admin/order/plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalOrder.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalOrder_Edit extends LC_Page_Admin_Ex {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR ."PeriodicalSale/templates/admin/order/plg_PeriodicalSale_periodical_order_edit.tpl";
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'periodical';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '定期販売管理';
        
        $objMasterData = new SC_DB_MasterData_Ex();
        $this->arrDAYS = $objMasterData->getMasterData('mtb_wday');
        $this->arrPREFS = $objMasterData->getMasterData('mtb_pref');
        $this->arrORDERSTATUSES = $objMasterData->getMasterData('mtb_order_status');
        $this->arrSEX = $objMasterData->getMasterData('mtb_sex');
        $this->arrPERIODICALORDERSTATUSES = $objMasterData->getMasterData('plg_ps_mtb_p_order_statuses');
        $this->arrWEEKS = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
        $this->arrPAYMENTS = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $this->arrDELIVERIES = SC_Helper_DB_Ex::sfGetIDValueList('dtb_deliv', 'deliv_id', 'name');
        $this->arrORDERSTATUSCOLORS = $objMasterData->getMasterData('mtb_order_status_color');
        $this->arrPERIODDATES = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
        
        $objDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrDATES = $objDate->getDay();
        $this->arrSHIPPINGYEAR = $objDate->getYear();
        $this->arrSHIPPINGMONTH = $objDate->getMonth();
        $this->arrSHIPPINGDATE = $objDate->getDay();

        $this->httpCacheControl('nocache');
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

        $post = $_POST;
        $objFormParam = new SC_FormParam_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $this->lfInitFormParam($objFormParam);
        $this->lfSetFormParam($objFormParam, $post);
        
        $periodical_order_id = $objFormParam->getValue('periodical_order_id');
        $mode = $this->getMode();
        
        $arrModes = array(
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDER_DETAILS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPPINGS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPMENT_ITEMS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_ORDERS
        );
        $arrPeriodicalOrder = $objPeriodicalOrder
            ->fetchByPeriodicalOrderIds($periodical_order_id)
            ->attach($arrModes)
            ->getOne();
        
        $this->arrOrders = $arrPeriodicalOrder['orders'];
        $this->arrLastOrder = $arrPeriodicalOrder['last_order'];
        $this->arrSearchHidden = $objFormParam->getSearchArray();
        
        //trigger_errorでもいいかも
        switch($mode){
            
            //編集
            case 'pre_edit':
            //発行
            case 'commit_order':
                //定期受注が見つからなかった場合
                if(empty($arrPeriodicalOrder)){
                    //リダイレクト
                    SC_Response_Ex::sendRedirect(ADMIN_HOME_URLPATH);
                    SC_Response_Ex::actionExit();
                }
                break;
        }
        
        switch($mode){
            
            //編集画面
            case 'pre_edit':
                $this->setPeriodicalOrderToFormParam($objFormParam, $arrPeriodicalOrder);
                break;
            
            //編集
            case 'edit':
                
                $this->lfRecalculate($objFormParam, true);
                $this->arrErr = $this->lfCheckError($objFormParam);
                //エラーがなければ
                if(empty($this->arrErr)){
                    $periodical_order_id = $this->lfRegisterPeriodicalOrder($objFormParam, $periodical_order_id, $arrPeriodicalOrder);
                    $this->setPeriodicalOrderToFormParamByPeriodicalOrderId($objFormParam, $periodical_order_id);
                    $this->tpl_onload = 'window.alert("定期情報を編集しました。");';
                }
                break;
                
            //発行
            case 'commit_order':
                    
                    $this->lfCommitOrder($objFormParam);

                    //更新したデータを取得、セットする
                    $arrPeriodicalOrder = $objPeriodicalOrder
                        ->fetchByPeriodicalOrderIds($periodical_order_id)
                        ->getOne();
                    //リダイレクト
                    SC_Response_Ex::sendRedirect(sprintf('plg_PeriodicalSale_periodical_order_edit.php?commit_complete&periodical_order_id=%d', $arrPeriodicalOrder['periodical_order_id']));
                    SC_Response_Ex::actionExit();
                    
                break;
            
            //再計算
            case 'recalculate':
                $this->lfRecalculate($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam);
                break;
            
            //配送方法変更(Ajax)
            case 'select_delivery':
                $delivery_id = $objFormParam->getValue('deliv_id');
                $total = $objFormParam->getValue('total');
                $arrSelectedDeliv = $this->lfGetSelectedDeliv($delivery_id, $total, $objPurchase);
                echo SC_Utils_Ex::jsonEncode($arrSelectedDeliv);
                SC_Response_Ex::actionExit();
                break;
            
            //商品選択
            case 'select_product_detail':
                $this->lfDoRegisterProduct($objFormParam);
                $this->lfRecalculate($objFormParam);
                break;
            
            //商品削除
            case 'delete_product':
                $delete_no = $objFormParam->getValue('delete_no');
                $this->lfDoDeleteProduct($delete_no, $objFormParam);
                $this->lfRecalculate($objFormParam);
                break;
            
            //顧客設定
            case 'search_customer':
                $this->lfSetCustomer($objFormParam->getValue('edit_customer_id'), $objFormParam);
                break;
            
            default:
                if(isset($_GET['commit_complete']) && !empty($_GET['periodical_order_id'])){
                    $arrPeriodicalOrder = $objPeriodicalOrder
                        ->fetchByPeriodicalOrderIds($_GET['periodical_order_id'])
                        ->attach(plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER)
                        ->getOne();
                    $this->setPeriodicalOrderToFormParam($objFormParam, $arrPeriodicalOrder);
                    $this->arrLastOrder = $arrPeriodicalOrder[plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER];
                    $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR  .'PeriodicalSale/templates/admin/order/plg_PeriodicalSale_periodical_order_edit_commit_complete.tpl';
                }
                break;
        }
        $this->arrForm = $objFormParam->getFormParamList();
        $delivery_id = $this->arrForm['deliv_id']['value'];
        $this->arrDELIVERYTIMES = $objPurchase->getDelivTime($delivery_id);
    }
    
    /**
     * 受注を発行する
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     */
    function lfCommitOrder(SC_FormParam_Ex &$objFormParam){
        
        //最初からID渡せばいいだけな気も
        $periodical_order_id = $objFormParam->getValue('periodical_order_id');
        plg_PeriodicalSale_SC_Helper_Purchase::commitOrders($periodical_order_id, false);
    }

    /**
     * 受注商品の追加/更新を行う。
     * 小画面で選択した受注商品をフォームに反映させる。
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     */
    function lfDoRegisterProduct(&$objFormParam) {

        $product_class_id = $objFormParam->getValue('add_product_class_id');
        if (SC_Utils_Ex::isBlank($product_class_id)) {
            $product_class_id = $objFormParam->getValue('edit_product_class_id');
            $changed_no = $objFormParam->getValue('no');
        }
        // FXIME バリデーションを通さず $objFormParam の値で DB 問い合わせしている。(管理機能のため、さほど問題は無いと思うものの…)

        // 商品規格IDが指定されていない場合、例外エラーを発生
        if (strlen($product_class_id) === 0) {
            trigger_error('商品規格指定なし', E_USER_ERROR);
        }

        // 選択済みの商品であれば数量を1増やす
        $exists = false;
        $arrExistsProductClassIds = $objFormParam->getValue('product_class_id');
        foreach ($arrExistsProductClassIds as $key => $value) {
            $exists_product_class_id = $arrExistsProductClassIds[$key];
            if ($exists_product_class_id == $product_class_id) {
                $exists = true;
                $exists_no = $key;
                $arrExistsQuantity = $objFormParam->getValue('quantity');
                $arrExistsQuantity[$key]++;
                $objFormParam->setValue('quantity', $arrExistsQuantity);
            }
        }

        // 新しく商品を追加した場合はフォームに登録
        // 商品を変更した場合は、該当行を変更
        if (!$exists) {
            $objProduct = new SC_Product_Ex();
            $arrProduct = $objProduct->getDetailAndProductsClass($product_class_id);
            
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $table = 'plg_ps_dtb_p_products';
            $where = 'product_id = ?';
            $arrWhereValues = array($arrProduct['product_id']);
            $arrPeriodicalProduct = $objQuery->getRow('*', $table, $where, $arrWhereValues);

            // 一致する商品規格がない場合、例外エラーを発生
            if (empty($arrProduct)) {
                trigger_error('商品規格一致なし', E_USER_ERROR);   
            }

            $arrProduct['quantity'] = 1;
            $arrProduct['price'] = $arrProduct['price02'] + $arrPeriodicalProduct['period_price_difference'];
            $arrProduct['product_name'] = $arrProduct['name'];

            $arrUpdateKeys = array(
                'product_id', 'product_class_id', 'product_type_id', 'point_rate',
                'product_code', 'product_name', 'classcategory_name1', 'classcategory_name2',
                'quantity', 'price',
            );
            foreach ($arrUpdateKeys as $key) {
                $arrValues = $objFormParam->getValue($key);

                if (!is_array($arrValues)) {
                    $arrValues = array();
                }

                if (isset($changed_no)) {
                    $arrValues[$changed_no] = $arrProduct[$key];
                } 
                else {
                    $added_no = 0;
                    if (is_array($arrExistsProductClassIds)) {
                        $added_no = count($arrExistsProductClassIds);
                    }
                    $arrValues[$added_no] = $arrProduct[$key];
                }
                $objFormParam->setValue($key, $arrValues);
            }
        } 
        elseif (isset($changed_no) && $exists_no != $changed_no) {
            // 変更したが、選択済みの商品だった場合は、変更対象行を削除。
            $this->lfDoDeleteProduct($changed_no, $objFormParam);
        }
    }

    /**
     * 会員情報をフォームに設定する.
     *
     * @param integer $customer_id 会員ID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfSetCustomer($customer_id, &$objFormParam) {
        
        $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        foreach ($arrCustomer as $key => $val) {
            $objFormParam->setValue('order_' . $key, $val);
        }
        $objFormParam->setValue('customer_id', $customer_id);
        $objFormParam->setValue('customer_point', $arrCustomer['point']);
    }

    /**
     * 受注商品を削除する.
     *
     * @param integer $delete_no 削除する受注商品の項番
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     */
    function lfDoDeleteProduct($delete_no, &$objFormParam) {
        $arrDeleteKeys = array(
            'product_id', 'product_class_id', 'product_type_id', 'point_rate',
            'product_code', 'product_name', 'classcategory_name1', 'classcategory_name2',
            'quantity', 'price',
        );
        foreach ($arrDeleteKeys as $key) {
            $arrNewValues = array();
            $arrValues = $objFormParam->getValue($key);
            foreach ($arrValues as $index => $val) {
                if ($index != $delete_no) {
                    $arrNewValues[] = $val;
                }
            }
            $objFormParam->setValue($key, $arrNewValues);
        }
    }

    /**
     * 配送業者IDから, 支払い方法, お届け時間の配列を取得する.
     *
     * 結果の連想配列の添字の値は以下の通り
     * - 'arrDelivTime' - お届け時間の配列
     * - 'arrPayment' - 支払い方法の配列
     *
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param integer $delivery_id 配送業者ID
     * @return array 支払い方法, お届け時間を格納した配列
     */
    function lfGetSelectedDeliv($delivery_id, $total, &$objPurchase = null) {
        
        if(empty($objPurchase)){
            $objPurchase = new SC_Helper_Purchase_Ex();
        }
        
        $arrResults = array(
            'arrDelivTime' => $objPurchase->getDelivTime($delivery_id),
            'arrPayment' => $objPurchase->getPaymentsByPrice($total, $delivery_id)
        );
        return $arrResults;
    }
    
    /**
     * 定期受注情報を登録する。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param integer $periodical_order_id
     * @param array $arrBeforePeriodicalOrder以前の定期受注情報
     * @return integer 定期受注ID 
     */
    function lfRegisterPeriodicalOrder(SC_FormParam_Ex &$objFormParam, $periodical_order_id, $arrBeforePeriodicalOrder){
        
        $arrValues = $objFormParam->getDbArray();
        $arrValues['next_period'] = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('next_shipping_year'), $objFormParam->getValue('next_shipping_month'), $objFormParam->getValue('next_shipping_date'));
        
        if(isset($arrValues['payment_id']) && $arrValues['payment_id'] != $arrBeforePeriodicalOrder['payment_id']){
            $arrValues['payment_method'] = $this->arrPAYMENTS[$arrValues['payment_id']];
        }
        //定期基本情報を保存
        $new_periodical_order_id = plg_PeriodicalSale_SC_Helper_Purchase::savePeriodicalOrder($periodical_order_id, $arrValues);
        //定期商品情報を保存
        plg_PeriodicalSale_SC_Helper_Purchase::savePeriodicalOrderDetails($new_periodical_order_id, $arrValues);
        //定期配送情報を保存
        plg_PeriodicalSale_SC_Helper_Purchase::savePeriodicalShippings($new_periodical_order_id, $arrValues);
        //定期配送商品情報を保存
        plg_PeriodicalSale_SC_Helper_Purchase::savePeriodicalShipmentItems($new_periodical_order_id, $arrValues);
        
        return $new_periodical_order_id;
    }
    
    /**
     * エラーの有無をチェックする。
     * 
     * @param SC_FormParam $objFormParam
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam){
        
        $objError = new SC_CheckError_Ex();
        $objError->arrErr = $objFormParam->checkError();
        // 生年月日
        $objError->doFunc(array('開始', 'search_birth_year_start', 'search_birth_month_start', 'search_birth_date_start'), array('CHECK_DATE'));
        
        return $objError->arrErr;
    }
    
    /**
     * 価格関係を再計算する。
     * 
     * @param SC_FormParam_Ex $objFormParam 
     */
    function lfRecalculate(&$objFormParam, $exclude_point = false){
        
        $arrValues = $objFormParam->getHashArray();
        $subtotal = 0;
        $add_point = 0;
        $tax = 0;
        
        foreach($arrValues['quantity'] as $index => $temp){
            $price = $arrValues['price'][$index];
            $quantity = $arrValues['quantity'][$index];
            $point_rate = $arrValues['point_rate'][$index];
            $subtotal += SC_Helper_DB_Ex::sfCalcIncTax($price) * $quantity;
            $tax += SC_Helper_DB_Ex::sfTax($price) * $quantity;
            $add_point += SC_Utils_Ex::sfPrePoint($price, $point_rate) * $quantity;
        }
        
        $total = $subtotal - $arrValues['discount'] + $arrValues['deliv_fee'] + $arrValues['charge'];
        $payment_total = $total;
        
        $arrValues = array_merge($arrValues, compact('tax','subtotal','total','payment_total','add_point'));
        
        //ポイント計算なしなら
        if($exclude_point){
            unset($arrValues['add_point']);
        }
        
        if($total < 0){
            $arrErr['total'] = '合計額がマイナスにならないように調整してください。<br />';
        }
        if($payment_total < 0){
            $arrErr['payment_total'] = 'お支払い合計額がマイナスにならないように調整してください。<br />';
        }
        
        $objFormParam->setParam($arrValues);
    }
    
    /**
     * パラメータを初期化する。
     * 
     * @param SC_FormParam_Ex $objFormParam
     */
    function lfInitFormParam(SC_FormParam_Ex $objFormParam){
        
        plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalOrder::lfInitFormParam($objFormParam);
        
        //plg_ps_dtb_p_orders関係
        $objFormParam->addParam('定期受注ID', 'periodical_order_id', INT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('会員ID', 'customer_id', INT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'), 0);
        $objFormParam->addParam('注文者 お名前(姓)', 'order_name01', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('注文者 お名前(名)', 'order_name02', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('注文者 お名前(フリガナ・姓)', 'order_kana01', STEXT_LEN, 'KVCa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('注文者 お名前(フリガナ・名)', 'order_kana02', STEXT_LEN, 'KVCa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メールアドレス', 'order_email', null, 'KVCa', array('NO_SPTAB', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('郵便番号1', 'order_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('郵便番号2', 'order_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('都道府県', 'order_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('住所1', 'order_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('住所2', 'order_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('電話番号1', 'order_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('電話番号2', 'order_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('電話番号3', 'order_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('FAX番号1', 'order_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号2', 'order_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号3', 'order_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('定期受注日', 'create_date');
        $objFormParam->addParam('定期継続状況', 'periodical_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('値引き', 'discount', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('送料', 'deliv_fee', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('手数料', 'charge', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('小計', 'subtotal');
        $objFormParam->addParam('合計', 'total');
        $objFormParam->addParam('支払い合計', 'payment_total');
        if(USE_POINT){
            $objFormParam->addParam('加算ポイント', 'add_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        }
        $objFormParam->addParam('配送業者', 'deliv_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('支払方法', 'payment_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('定期回数', 'total_periodical_times', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'), '0');
        $objFormParam->addParam('備考', 'message');
        $objFormParam->addParam('メモ', 'note', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('次回定期予定日', 'next_period');
        $objFormParam->addParam('定期タイプ', 'period_type', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('定期お届け時間', 'period_delivery_time', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('定期お届け週', 'period_week', INT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('定期お届け日付', 'period_date', INT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('定期お届け曜日', 'period_day', INT_LEN, 'n', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    
        //plg_ps_dtb_p_order_details関係
        $objFormParam->addParam('定期商品ID', 'periodical_order_detail_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品種別ID', 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('商品規格ID', 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('単価', 'price', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('数量', 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('ポイント付与率', 'point_rate');
        $objFormParam->addParam('商品コード', 'product_code');
        $objFormParam->addParam('商品名', 'product_name');
        $objFormParam->addParam('規格名1', 'classcategory_name1');
        $objFormParam->addParam('規格名2', 'classcategory_name2');
        
        //plg_ps_dtb_p_shippings関係
        //XXX periodical_shipping_idとshipping_idのdefault値が適当
        $objFormParam->addParam('定期配送ID', 'periodical_shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('配送先ID', 'shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('お名前(姓)', 'shipping_name01', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('お名前(名)', 'shipping_name02', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('お名前(フリガナ・姓)', 'shipping_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('お名前(フリガナ・名)', 'shipping_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('郵便番号1', 'shipping_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('郵便番号2', 'shipping_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('都道府県', 'shipping_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('住所1', 'shipping_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('住所2', 'shipping_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('電話番号1', 'shipping_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('電話番号2', 'shipping_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('電話番号3', 'shipping_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('FAX番号1', 'shipping_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号2', 'shipping_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号3', 'shipping_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('お届け時間ID', 'time_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        
        $objFormParam->addParam('定期配送商品ID', 'periodical_shipment_item_id', INT_LEN, 'n', array( 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        
        $objFormParam->addParam('商品項番', 'no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('削除用項番', 'delete_no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('追加商品規格ID', 'add_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('修正商品規格ID', 'edit_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('変更先顧客ID', 'edit_customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('アンカーキー', 'anchor_key', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        
        $objFormParam->addParam('次回受注お届け年', 'next_shipping_year', INT_LEN, 'n');
        $objFormParam->addParam('次回受注お届け月', 'next_shipping_month', INT_LEN, 'n');
        $objFormParam->addParam('次回受注お届け日', 'next_shipping_date', INT_LEN, 'n');
        
    }
    
    /**
     * パラメータをセットする
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     * @param array $arrParam 
     */
    function lfSetFormParam(SC_FormParam_Ex &$objFormParam, $arrParam){
        
        $objFormParam->setParam($arrParam);
        
        if(!SC_Utils_Ex::isBlank($objFormParam->getValue('next_period'))){
            $time = strtotime($objFormParam->getValue('next_period'));
            if(SC_Utils_Ex::isBlank($objFormParam->getValue('next_shipping_year'))){
                $objFormParam->setValue('next_shipping_year', date('Y', $time));
            }
            if(SC_Utils_Ex::isBlank($objFormParam->getValue('next_shipping_month'))){
                $objFormParam->setValue('next_shipping_month', date('n', $time));
            }
            if(SC_Utils_Ex::isBlank($objFormParam->getValue('next_shipping_date'))){
                $objFormParam->setValue('next_shipping_date', date('j', $time));
            }
        }
        
        $objFormParam->convParam();
    }
    
    /**
     * SC_FormParam_Exのインスタンスに定期受注情報をセットする。
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     * @param integer $periodical_order_id 定期受注ID
     */
    function setPeriodicalOrderToFormParamByPeriodicalOrderId(SC_FormParam_Ex &$objFormParam, $periodical_order_id){
        
        $arrModes = array(
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDERS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPPINGS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPMENT_ITEMS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_ORDERS
        );
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $arrPeriodicalOrder = $objPeriodicalOrder
            ->fetchByPeriodicalOrderIds($periodical_order_id)
            ->attach($arrModes)
            ->getOne();
        $this->setPeriodicalOrderToFormParam($objFormParam, $arrPeriodicalOrder);
    }
    
    /**
     * SC_FormParam_Exのインスタンスに定期受注情報をセットする。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param array $arrPeriodicalOrder 定期受注情報の配列
     */
    function setPeriodicalOrderToFormParam(SC_FormParam_Ex &$objFormParam, $arrPeriodicalOrder){
        
        //データの配列を生成
        $arrPeriodicalOrderDetails = SC_Utils_Ex::sfSwapArray($arrPeriodicalOrder['periodical_order_details']);
        $arrPeriodicalShippings = SC_Utils_Ex::sfSwapArray($arrPeriodicalOrder['periodical_shippings']);
        $arrPeriodicalShipmentItems = SC_Utils_Ex::sfSwapArray($arrPeriodicalOrder['periodical_shipment_items']);
        
        //定期情報(アソシエーションなし)
        $table = 'plg_ps_dtb_p_orders';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        //テーブルに存在するフィールドのみ抽出
        $arrExtractedPeriodicalOrder = $objQuery->extractOnlyColsOf($table, $arrPeriodicalOrder);

        //最後にsetParam($arrIndependentPeriodic)しないと、periodical_order_idが配列になってしまうので注意
        $this->lfSetFormParam($objFormParam, $arrPeriodicalShippings);
        $this->lfSetFormParam($objFormParam, $arrPeriodicalOrderDetails);
        $this->lfSetFormParam($objFormParam, $arrPeriodicalShipmentItems);
        $this->lfSetFormParam($objFormParam, $arrExtractedPeriodicalOrder);
    }
}
