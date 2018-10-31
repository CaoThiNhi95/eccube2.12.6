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

require_once CLASS_EX_REALDIR . 'page_extends/shopping/LC_Page_Shopping_Payment_Ex.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Shopping_Payment extends LC_Page_Shopping_Payment_Ex {

    /**
     * SC_FormParam_Exのインスタンスにパラメータをセットする。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param array $arrParams パラメータの配列
     */
    static function sfInitFormParam(SC_FormParam_Ex &$objFormParam, $arrParams = array()){
        
        $objFormParam->addParam('配送業者', 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('2回目以降のお支払い方法', 'period_payment_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'EXIST_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お支払い方法', 'payment_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'EXIST_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('定期販売種別', 'period_type', INT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('お届け日'  , 'period_date', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('お届け曜日', 'period_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK', 'EXIST_CHECK'));
        $objFormParam->addParam('お届け時間', 'period_delivery_time', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け曜日', 'period_week', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        
        $regex = '/^deliv_date([0-9])+$/u';
        foreach($arrParams as $key => $arrParam){
            if(preg_match($regex, $key, $arrMatches)){
                $shipping_id = $arrMatches[1];
                $objFormParam->addParam(sprintf('お届け日',$shipping_id), sprintf('deliv_date%s', $shipping_id), STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                $objFormParam->addParam(sprintf('お届け時間',$shipping_id), sprintf('deliv_time_id%s', $shipping_id), INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
            }
        }
    }
    

    /**
     * SC_FormParam_Exのインスタンスにパラメータをセットする。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param array $arrParams パラメータの配列
     */
    static function sfSetFormParam(SC_FormParam_Ex &$objFormParam, $arrParams){

        $objFormParam->setParam($arrParams);
        
        //2回目以降の支払い方法が選択されていなかったら
        if(SC_Utils_Ex::isBlank($objFormParam->getValue('period_payment_id'))){
            //1回目の支払い方法を2回目の支払い方法にセット
            $objFormParam->setValue('period_payment_id', $objFormParam->getValue('payment_id'));
        }
        
        $objFormParam->convParam();
    }
}