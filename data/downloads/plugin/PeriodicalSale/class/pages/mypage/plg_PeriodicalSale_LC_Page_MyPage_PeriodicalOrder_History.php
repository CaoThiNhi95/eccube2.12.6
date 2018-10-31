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
class plg_PeriodicalSale_LC_Page_MyPage_PeriodicalOrder_History extends LC_Page_AbstractMypage_Ex {



    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno     = 'periodical_order';
        $this->tpl_subtitle     = '定期購入詳細';
        $this->tpl_title     = '定期購入詳細';
        $this->httpCacheControl('nocache');
        $objMasterData = new SC_DB_MasterData_Ex();
        $objDb = new SC_Helper_DB_Ex();
        $this->arrPAYMENTS = $objDb->sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $this->arrPERIODTYPES = PeriodicalSale::getPeriodTypes();
        $this->arrDAYS = $objMasterData->getMasterData('mtb_wday');
        $this->arrDATES = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
        $this->arrWEEKS = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
        $this->arrORDERSTATUSES = $objMasterData->getMasterData('mtb_order_status');
        $this->arrPREFS = $objMasterData->getMasterData('mtb_pref');
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {

        if (!SC_Utils_Ex::sfIsInt($_GET['periodical_order_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }
        
        $periodical_order_id = $_GET['periodical_order_id'];
        $this->arrPeriodicalOrder = $this->lfGetPeriodicalOrder($periodical_order_id);
        if(empty($this->arrPeriodicalOrder)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }
        
        $objPurchase = new SC_Helper_Purchase_Ex();
        $delivery_id = $this->arrPeriodicalOrder['deliv_id'];
        $this->arrDELIVERYTIMES = $objPurchase->getDelivTime($delivery_id);
    }
    
    /**
     * 定期受注を取得する。
     * 
     * @param integer $periodical_order_id 定期受注ID
     * @return array 定期受注の配列
     */
    function lfGetPeriodicalOrder($periodical_order_id){
        
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $objPeriodicalOrder->fetchByPeriodicalOrderIds($periodical_order_id, true);
        
        $arrModes = array(
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_ORDERS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDER_DETAILS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPPINGS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPMENT_ITEMS
        );
        
        return $objPeriodicalOrder
            ->attach($arrModes)
            ->getOne();
    }
}