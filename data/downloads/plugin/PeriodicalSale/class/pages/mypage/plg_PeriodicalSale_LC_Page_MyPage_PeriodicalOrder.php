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

require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/plg_PeriodicalSale_SC_PeriodicalOrder.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_MyPage_PeriodicalOrder extends LC_Page_AbstractMypage_Ex {

    /** ページナンバー */
    var $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'periodical_order';
        $this->tpl_title = '定期購入履歴一覧';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_subtitle = 'MYページ';
        } 
        else {
            $this->tpl_subtitle = '定期購入履歴一覧';
        }
        
        $objMasterData = new SC_DB_MasterData_Ex();
        $this->arrPERIODICALORDERSTATUSES = $objMasterData->getMasterData('plg_ps_mtb_p_order_statuses');
        $this->arrPAYMENTS = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $this->arrDAYS = $objMasterData->getMasterData('mtb_wday');
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        
        switch(SC_Display_Ex::detectDevice()){
            //スマートフォンのもっと見る機能にバグがあるため、応急処置
            case DEVICE_TYPE_SMARTPHONE:
                $search_pmax = 9999;
                break;
            default:
                $search_pmax = SEARCH_PMAX;
                break;
        }
        
        $this->objNavi = new SC_PageNavi_Ex($_REQUEST['pageno'], $this->lfGetPeriodicalOrders(), $search_pmax, 'fnNaviPage', NAVI_PMAX, 'pageno=#page#', SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);
        $this->arrPeriodicalOrders = $this->lfGetPeriodicalOrders($search_pmax, $this->objNavi->start_row);

        $mode = $this->getMode();
        switch($mode){
            case 'getList':
                echo SC_Utils_Ex::jsonEncode($this->arrPeriodicalOrders);
                SC_Response_Ex::actionExit();
                break;
        }
        
        $this->disp_number = $search_pmax;
    }
    
    /**
     * 定期受注を取得する
     * 
     * @param integer $limit LIMIT
     * @param integer $offset OFFSET
     * @return array 定期受注の配列
     */
    function lfGetPeriodicalOrders($limit = 10, $offset = null){
        
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        if(!is_numeric($offset)){
            return $objPeriodicalOrder->count(true);
        }
        
        $objQuery = plg_PeriodicalSale_SC_PeriodicalOrder::getDefaultQuery(plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDERS);
        $objQuery->setLimitOffset($limit, $offset);
        return $objPeriodicalOrder
            ->fetch(true, $objQuery)
            ->get();
    }
}