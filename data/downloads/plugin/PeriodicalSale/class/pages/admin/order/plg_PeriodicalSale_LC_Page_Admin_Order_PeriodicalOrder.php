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

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalOrder extends LC_Page_Admin_Ex {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR ."PeriodicalSale/templates/admin/order/plg_PeriodicalSale_periodical_order.tpl";
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'periodical';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '定期販売管理';
        
        $objMasterData = new SC_DB_MasterData_Ex();
        $this->arrDAYS = $objMasterData->getMasterData('mtb_wday');
        $this->arrORDERSTATUSES = $objMasterData->getMasterData('mtb_order_status');
        $this->arrSEX = $objMasterData->getMasterData('mtb_sex');
        $this->arrPAYMENTS = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $this->arrPERIODICALORDERSTATUSES = $objMasterData->getMasterData('plg_ps_mtb_p_order_statuses');
        $this->arrPAGEMAX = $objMasterData->getMasterData('mtb_page_max');
        
        $objDate = new SC_Date_Ex();
        //初回購入検索用
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrORDERYEAR = $objDate->getYear();
        //生年月日検索用
        $objDate->setStartYear(BIRTH_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrBIRTHYEAR = $objDate->getYear();
        //月日の設定
        $this->arrMONTHS = $objDate->getMonth();
        $this->arrDATES = $objDate->getDay();
        
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
        
        $objFormParam = new SC_FormParam_Ex();
        $mode = $this->getMode();
        $post = $_POST;
        
        $this->lfInitFormParam($objFormParam);
        $this->lfSetFormParam($objFormParam, $post);
        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        switch($mode){
            
            //複数の定期から受注を発行
            case 'commit':
                $arrPeriodicalOrderIds = $objFormParam->getValue('arrPeriodicalOrderIds');
                plg_PeriodicalSale_SC_Helper_Purchase::commitOrders($arrPeriodicalOrderIds, false);
                //リダイレクト
                SC_Response_Ex::sendRedirect('plg_PeriodicalSale_periodical_order.php?commit_complete');
                SC_Response_Ex::actionExit();
                break;
                
            //削除
            case 'delete':
                $periodical_order_id = $objFormParam->getValue('periodical_order_id');
                plg_PeriodicalSale_SC_Helper_Purchase::deletePeriodicalOrders($periodical_order_id);
                $this->tpl_onload .= 'alert("受注を削除しました。");';
                break;
        }
        
        $this->arrErr = $this->lfCheckError($objFormParam);

        if(empty($this->arrErr)){

            $this->tpl_linemax = $this->lfGetNumberOfLines($objFormParam);

            //ページ送りの処理
            $page_max = SC_Utils_Ex::sfGetSearchPageMax($objFormParam->getValue('search_page_max'));
            //ページ送りの取得
            $objNavi = new SC_PageNavi_Ex($this->arrHidden['search_pageno'], $this->tpl_linemax, $page_max, 'fnNaviSearchPage', NAVI_PMAX);
            $this->arrPagenavi = $objNavi->arrPagenavi;
            $this->arrResults = $this->lfGetPeriodicalOrders($objFormParam, $page_max, $objNavi->start_row);
        }
        
        if(isset($_GET['commit_complete'])){
            $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR ."PeriodicalSale/templates/admin/order/plg_PeriodicalSale_periodical_order_commit_complete.tpl";
        }
    }
    
    /**
     * 検索条件から全行数を取得する
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @return integer 件数
     */
    function lfGetNumberOfLines(SC_FormParam_Ex &$objFormParam){
        
        $objQuery =& $this->lfBuildPeriodicalOrderQuery($objFormParam);
        return plg_PeriodicalSale_SC_PeriodicalOrder::count(false, $objQuery);
    }
    
    /**
     * 検索条件とLIMITとOFFSETから、定期受注の検索結果を取得する
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param integer $limit
     * @param integer $offset
     * @return array 定期受注の検索結果
     */
    function lfGetPeriodicalOrders(&$objFormParam, $limit = 10, $offset = 0){
        
        $objQuery = $this->lfBuildPeriodicalOrderQuery($objFormParam);
        $objQuery->setLimitOffset($limit, $offset);
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $objPeriodicalOrder
            ->fetch(false, $objQuery)
            ->attach(plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER);
        return $objPeriodicalOrder->get();
    }
    
    /**
     * SC_FormParam_Exから検索条件をセットしたSC_Query_Exのインスタンスを生成する
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     * @return SC_Query_Ex インスタンス
     */
    function lfBuildPeriodicalOrderQuery(SC_FormParam_Ex &$objFormParam){
        
        $arrWhere = array('1 = 1');
        $arrWhereValues = array();
        $arrValues = $objFormParam->getHashArray();
        $objDbFactory = SC_DB_DBFactory_Ex::getInstance();
        $objQuery =& plg_PeriodicalSale_SC_PeriodicalOrder::getDefaultQuery(plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDERS);
        
        foreach($arrValues as $key => $value){
            
            if(SC_Utils_Ex::isBlank($value)){
                continue;
            }
            
            switch($key){
                case 'search_periodical_order_id1':
                    $arrWhere[] = 'periodical_orders.periodical_order_id >= ?';
                    $arrWhereValues[] = $value;
                    break;
                case 'search_periodical_order_id2':
                    $arrWhere[] = 'periodical_orders.periodical_order_id <= ?';
                    $arrWhereValues[] = $value;
                    break;
                case 'search_order_name':
                    $arrWhere[] = sprintf('%s LIKE ?', $objDbFactory->concatColumn(array('periodical_orders.order_name01', 'periodical_orders.order_name02')));
                    $arrWhereValues[] = sprintf('%%%s%%', $value);
                    break;
                case 'search_order_kana':
                    $arrWhere[] = sprintf('%s LIKE ?', $objDbFactory->concatColumn(array('periodical_orders.order_kana01', 'periodical_orders.order_kana02')));
                    $arrWhereValues[] = sprintf('%%%s%%', $value);
                    break;
                case 'search_order_email':
                    $arrWhere[] = 'periodical_orders.order_email LIKE ?';
                    $arrWhereValues[] = sprintf('%%%s%%', $value);
                    break;
                case 'search_last_order_status':
                    $arrWhere[] = 'last_order.status = ?';
                    $arrWhereValues[] = $value;
                    break;
                case 'search_periodical_status':
                    $arrWhere[] = 'periodical_orders.periodical_status = ?';
                    $arrWhereValues[] = $value;
                    break;
                case 'search_order_tel':
                    $arrWhere[] = sprintf('%s LIKE ?', $objDbFactory->concatColumn(array('periodical_orders.order_tel01', 'periodical_orders.order_tel02', 'periodical_orders.order_tel03')));
                    $arrWhereValues[] = sprintf('%%%d%%', preg_replace('/[()-]+/', '', $value));
                    break;
                case 'search_order_sex':
                    $arrTempWhere = array();
                    foreach($value as $element){
                        if(!empty($element)){
                            $arrTempWhere[] = 'periodical_orders.order_sex = ?';
                            $arrWhereValues[] = $element;
                        }
                    }
                    if(!empty($arrTempWhere)){
                        $temp_where = sprintf('(%s)', implode(' OR ', $arrTempWhere));
                        $arrWhere[] .= $temp_where;
                    }
                    break;
                case 'search_birth_year_start':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_birth_year_start'), $objFormParam->getValue('search_birth_month_start'), $objFormParam->getValue('search_birth_date_start'));
                    $arrWhere[] = 'periodical_orders.order_birth >= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_birth_year_end':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_birth_year_end'), $objFormParam->getValue('search_birth_month_end'), $objFormParam->getValue('search_birth_date_end'), true);
                    $arrWhere[] = 'periodical_orders.order_birth <= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_payment_id':
                    $arrTempWhere = array();
                    foreach($value as $element){
                        if(!empty($element)){
                            $arrTempWhere[] = 'periodical_orders.payment_id = ?';
                            $arrWhereValues[] = $element;
                        }
                    }
                    if(!empty($arrTempWhere)){
                        $temp_where = sprintf('(%s)', implode(' OR ', $arrTempWhere));
                        $arrWhere[] .= $temp_where;
                    }
                    break;
                case 'search_total_periodical_times1':
                    $arrWhere[] = 'periodical_orders.total_periodical_times >= ?';
                    $arrWhereValues[] = $value;
                    break;
                case 'search_total_periodical_times2':
                    $arrWhere[] = 'periodical_orders.total_periodical_times <= ?';
                    $arrWhereValues[] = $value;
                    break;
                case 'search_first_order_year_start':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_first_order_year_start'), $objFormParam->getValue('search_first_order_month_start'), $objFormParam->getValue('search_first_order_date_start'));
                    $arrWhere[] = 'first_order.create_date >= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_first_order_year_end':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_first_order_year_end'), $objFormParam->getValue('search_first_order_month_end'), $objFormParam->getValue('search_first_order_date_end'), true);
                    $arrWhere[] = 'first_order.create_date <= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_next_period_year_start':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_next_period_year_start'), $objFormParam->getValue('search_next_period_month_start'), $objFormParam->getValue('search_next_period_date_start'));
                    $arrWhere[] = 'next_period >= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_next_period_year_end':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_next_period_year_end'), $objFormParam->getValue('search_next_period_month_end'), $objFormParam->getValue('search_next_period_date_end'), true);
                    $arrWhere[] = 'next_period <= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_last_order_commit_year_start':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_last_order_commit_year_start'), $objFormParam->getValue('search_last_order_commit_month_start'), $objFormParam->getValue('search_last_order_commit_date_start'));
                    $arrWhere[] = 'last_order.commit_date >= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_last_order_commit_year_end':
                    $date = SC_Utils_Ex::sfGetTimestamp($objFormParam->getValue('search_last_order_commit_year_end'), $objFormParam->getValue('search_last_order_commit_month_end'), $objFormParam->getValue('search_last_order_commit_date_end'), true);
                    $arrWhere[] = 'last_order.commit_date <= ?';
                    $arrWhereValues[] = $date;
                    break;
                case 'search_order_by':
                    switch($objFormParam->getValue('search_order_by')){
                    case 'next_period_asc':
                        $objQuery->setOrder('next_period ASC');
                        break;
                    case 'next_period_desc':
                        $objQuery->setOrder('next_period DESC');
                        break;
                    case 'periodical_order_id_asc':
                        $objQuery->setOrder('periodical_orders.periodical_order_id ASC');
                        break;
                    case 'periodical_order_id_desc':
                        $objQuery->setOrder('periodical_orders.periodical_order_id DESC');
                        break;
                    case 'total_periodical_times_asc':
                        $objQuery->setOrder('periodical_orders.total_periodical_times ASC');
                        break;
                    case 'total_periodical_times_desc':
                        $objQuery->setOrder('periodical_orders.total_periodical_times DESC');
                        break;
                    case 'update_date_asc':
                        $objQuery->setOrder('periodical_orders.update_date ASC');
                        break;
                    case 'update_date_desc':
                        $objQuery->setOrder('periodical_orders.update_date DESC');
                        break;
                    }
                    break;
            }
        }
        $objQuery->andWhere(implode(' AND ', $arrWhere));
        $objQuery->arrWhereVal = array_merge($objQuery->arrWhereVal, $arrWhereValues);
        return $objQuery;
    }
    
    /**
     * エラーチェック
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     * @return array エラー情報配列
     */
    function lfCheckError(SC_FormParam_Ex &$objFormParam){
        
        $objError = new SC_CheckError_Ex();
        $objError->arrErr = $objFormParam->checkError();
        
        $objError->doFunc(array('定期ID1', '定期ID2', 'search_periodical_order_id1', 'search_periodical_order_id2'), array('GREATER_CHECK'));
        $objError->doFunc(array('定期回数1', '定期回数2', 'search_total_periodical_times1', 'search_total_periodical_times2'), array('GREATER_CHECK'));
        
        // 生年月日
        $objError->doFunc(array('開始', 'search_birth_year_start', 'search_birth_month_start', 'search_birth_date_start'), array('CHECK_DATE'));
        $objError->doFunc(array('終了', 'search_birth_year_end', 'search_birth_month_end', 'search_birth_date_end'), array('CHECK_DATE'));
        $objError->doFunc(array('開始', '終了', 'search_birth_year_start', 'search_birth_month_start', 'search_birth_date_start', 'search_birth_year_end', 'search_birth_month_end', 'search_birth_date_end'), array('CHECK_SET_TERM'));
        // 生年月日
        $objError->doFunc(array('開始', 'search_first_order_year_start', 'search_first_order_month_start', 'search_first_order_date_start'), array('CHECK_DATE'));
        $objError->doFunc(array('終了', 'search_first_order_year_end', 'search_first_order_month_end', 'search_first_order_date_end'), array('CHECK_DATE'));
        $objError->doFunc(array('開始', '終了', 'search_first_order_year_start', 'search_first_order_month_start', 'search_first_order_date_start', 'search_first_order_year_end', 'search_first_order_month_end', 'search_first_order_date_end'), array('CHECK_SET_TERM'));
        // 最終発送日
        $objError->doFunc(array('開始', 'search_last_order_commit_year_start', 'search_last_order_commit_month_start', 'search_last_order_commit_date_start'), array('CHECK_DATE'));
        $objError->doFunc(array('終了', 'search_last_order_commit_year_end', 'search_last_order_commit_month_end', 'search_last_order_commit_date_end'), array('CHECK_DATE'));
        $objError->doFunc(array('開始', '終了', 'search_last_order_commit_year_start', 'search_last_order_commit_month_start', 'search_last_order_commit_date_start', 'search_last_order_commit_year_end', 'search_last_order_commit_month_end', 'search_last_order_commit_date_end'), array('CHECK_SET_TERM'));
        // 次回お届け日
        $objError->doFunc(array('開始', 'search_next_period_year_start', 'search_next_period_month_start', 'search_next_period_date_start'), array('CHECK_DATE'));
        $objError->doFunc(array('終了', 'search_next_period_year_end', 'search_next_period_month_end', 'search_next_period_date_end'), array('CHECK_DATE'));
        $objError->doFunc(array('開始', '終了', 'search_next_period_year_start', 'search_next_period_month_start', 'search_next_period_date_start', 'search_next_period_year_end', 'search_next_period_month_end', 'search_next_period_date_end'), array('CHECK_SET_TERM'));
        
        return $objError->arrErr;
    }
    
    /**
     * SC_FormParam_Exのインスタンスを初期化する
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     */
    static function lfInitFormParam(SC_FormParam_Ex &$objFormParam){
        
        $objFormParam->addParam('定期ID1', 'search_periodical_order_id1', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('定期ID2', 'search_periodical_order_id2', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('継続状況', 'search_periodical_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('対応状況', 'search_last_order_status', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('注文者 お名前', 'search_order_name', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('注文者 お名前(フリガナ)', 'search_order_kana', STEXT_LEN, 'KVCa', array('KANA_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('性別', 'search_order_sex', INT_LEN, 'n', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メールアドレス', 'search_order_email', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号', 'search_order_tel', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('支払い方法', 'search_payment_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('表示件数', 'search_page_max', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('定期回数1', 'search_total_periodical_times1', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('定期回数2', 'search_total_periodical_times2', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        // 初回受注日
        $objFormParam->addParam('開始年', 'search_first_order_year_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始月', 'search_first_order_month_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始日', 'search_first_order_date_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了年', 'search_first_order_year_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了月', 'search_first_order_month_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了日', 'search_first_order_date_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        // 最終発送日
        $objFormParam->addParam('開始年', 'search_last_order_commit_year_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始月', 'search_last_order_commit_month_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始日', 'search_last_order_commit_date_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了年', 'search_last_order_commit_year_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了月', 'search_last_order_commit_month_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了日', 'search_last_order_commit_date_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        // 生年月日
        $objFormParam->addParam('開始年', 'search_birth_year_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始月', 'search_birth_month_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始日', 'search_birth_date_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了年', 'search_birth_year_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了月', 'search_birth_month_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了日', 'search_birth_date_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        // 次回お届け日
        $objFormParam->addParam('開始年', 'search_next_period_year_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始月', 'search_next_period_month_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('開始日', 'search_next_period_date_start', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了年', 'search_next_period_year_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了月', 'search_next_period_month_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('終了日', 'search_next_period_date_end', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('ページ送り番号','search_pageno', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        //ソート
        $objFormParam->addParam('ソート', 'search_order_by', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'), 'next_period_asc');
        
        $objFormParam->addParam('定期受注ID','arrPeriodicalOrderIds',INT_LEN,'n',array('MAX_LENGTH_CHECK','NUM_CHECK'));
        $objFormParam->addParam('定期受注ID', 'periodical_order_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }
    
    /**
     * SC_FormParam_Exインスタンスにパラメータをセットする
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     * @param array $arrParams
     */
    static function lfSetFormParam(SC_FormParam_Ex &$objFormParam, $arrParams){
        
        $objFormParam->setParam($arrParams);
        $objFormParam->convParam();
        $objFormParam->trimParam();
    }
}