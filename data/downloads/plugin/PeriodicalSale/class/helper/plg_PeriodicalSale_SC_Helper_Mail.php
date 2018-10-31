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

require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/PeriodicalSale.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/plg_PeriodicalSale_SC_PeriodicalOrder.php';

/**
 * メール操作のヘルパークラス
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_SC_Helper_Mail extends SC_Helper_Mail_Ex{
    
    /**
     * 定期受注のメールを送信する。
     * $headerと$footerと$subjectが''の場合、DBから取得する。
     * 
     * @param integer $periodical_order_id 定期受注ID
     * @param integer $template_id メールテンプレートID
     * @param string $subject 件名
     * @param string $header ヘッダー文
     * @param string $footer フッター文
     * @param boolean $send falseの場合、送信しない
     * @return SC_SendMail_Ex インスタンス
     */
    function sfSendPeriodicalOrderMail($periodical_order_id, $template_id, $subject = '', $header = '', $footer = '', $send = true){
        
        $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        if($subject == '' && $header == '' && $footer == ''){
            
            $where = 'template_id = ?';
            $arrWhereValues = array($template_id);
            $arrMail = $objQuery->getRow('subject, header, footer', 'dtb_mailtemplate', $where, $arrWhereValues);
            if(!empty($arrMail)){
                
                $header = $arrMail['header'];
                $footer = $arrMail['footer'];
                $subject = $arrMail['subject'];
            }
        }
        
        $tpl_header = $header;
        $tpl_footer = $footer;
        $tmp_subject = $subject;
        
        //メール掲載データ取得
        $arrModes = array(
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDER_DETAILS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPMENT_ITEMS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPPINGS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER
        );
        $objMasterData = new SC_DB_MasterData_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $arrPeriodicalOrder = $objPeriodicalOrder
            ->fetchByPeriodicalOrderIds($periodical_order_id)
            ->attach($arrModes)
            ->getOne();
        $delivery_id = $arrPeriodicalOrder['deliv_id'];
        $arrPERIODTYPES = PeriodicalSale::getPeriodTypes();
        $arrDELIVERYTIMES = $objPurchase->getDelivTime($delivery_id);
        $arrDAYS = $objMasterData->getMasterData('mtb_wday');
        $arrDATES = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
        $arrWEEKS = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
        $arrPREFS = $objMasterData->getMasterData('mtb_pref');
        
        if(empty($arrPeriodicalOrder)){
            trigger_error(sprintf('該当する定期受注が存在しません。 (定期注文ID: %d)', $periodical_order_id), E_USER_ERROR);
        }
        
        //メール送信処理
        if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE){
            $objMailView = new SC_MobileView_Ex();
        }
        else{
            $objMailView = new SC_SiteView_Ex();
        }
        
        $objMailView->assignarray(compact(
                'arrPeriodicalOrder', 
                'arrPERIODTYPES', 
                'arrDELIVERYTIMES', 
                'arrDAYS', 
                'arrDATES',
                'arrWEEKS',
                'arrPREFS',
                'tpl_header',
                'tpl_footer'
        ));
        $body = $objMailView->fetch($this->arrMAILTPLPATH[$template_id]);
        
        //メール送信処理
        $objSendMail = new SC_SendMail_Ex();
        $bcc = $arrInfo['email01'];
        $from = $arrInfo['email03'];
        $error = $arrInfo['email04'];
        $from_name = $arrInfo['shop_name'];
        $tosubject = $this->sfMakeSubject($tmp_subject, $objMailView);
        
        $objSendMail->setItem('', $tosubject, $body, $from, $from_name, $from, $error, $error, $bcc);
        $objSendMail->setTo($arrPeriodicalOrder['order_email'], sprintf('%s %s 様', $arrPeriodicalOrder['order_name01'], $arrPeriodicalOrder['order_name02']));
        if($send){
            if($objSendMail->sendMail()){
                $last_order_id = $arrPeriodicalOrder[plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER]['order_id'];
                $this->sfSaveMailHistory($last_order_id, $template_id, $tosubject, $body);
            }
        }
        return $objSendMail;
    }
}
