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

require_once CLASS_EX_REALDIR . 'page_extends/shopping/LC_Page_Shopping_Confirm_Ex.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Shopping_Confirm extends LC_Page_Shopping_Confirm_Ex {
    
    /**
     * 表示に必要な配送情報を取得。
     * 
     * @param array $arrTempPeriodicalOrder 一時定期受注データ
     * @param array $arrShippings 配送データ ($_SESSION['shipping'])
     * @return array 配送情報の配列
     */
    static function sfGetShippingParams($arrTempPeriodicalOrder, $arrShippings){
        
        $arrValues = $arrShippings;
        $regex = '/([0-9]{2,4})[^0-9]([0-9]{1,2})[^0-9]([0-9]{1,2})/u';
        foreach($arrValues as $key => $arrValue){
            
            //配送希望日が設定されていたら
            if(preg_match($regex, $arrValue['shipping_date'], $arrMatches)){
                $from_time = mktime(0, 0, 0, $arrMatches[2], $arrMatches[3], $arrMatches[1]);
            }
            //配送希望日が設定されていなかったら
            else{
                
                //セッションを元にカート内容を取得
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objCartSession = new SC_CartSession_Ex();
                $cart_key = $objCartSession->getKey();
                $arrDeliveryDates = array_values($objPurchase->getDelivDate($objCartSession, $cart_key));
                
                //可能な最短お届け日が取得できたら
                if(is_array($arrDeliveryDates) && preg_match($regex, $arrDeliveryDates[0], $arrMatches)){
                    $from_time = mktime(0, 0, 0, $arrMatches[2], $arrMatches[3], $arrMatches[1]);
                }
                else{
                    $from_time = time();
                }
            }
            
            $arrValue = array_merge($arrValue, array());
            $arrTempPeriodicalOrder = array_merge($arrTempPeriodicalOrder, array());
            $arrPeriodInfo = array_merge($arrValue, $arrTempPeriodicalOrder, compact('from_time'));
            $arrValues[$key]['next_period'] = date('Y-m-d', plg_PeriodicalSale_SC_Helper_Datetime::getNextPeriodTime($arrPeriodInfo));
        }
        return $arrValues;
    }
}