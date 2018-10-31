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

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Admin_Products_Product extends LC_Page_Admin_Ex {

    /**
     * パラメータを初期化する
     * 
     * @param SC_FormParam_Ex $objFormParam
     */
    static function sfInitFormParam(SC_FormParam_Ex &$objFormParam){
        
        $objFormParam->addParam('定期販売', 'is_periodical', INT_LEN, 'n', array('MAX_LENGTH_CHECK','NUM_CHECK'));
        $objFormParam->addParam('商品種別', 'product_type_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK','NUM_CHECK'));
        $objFormParam->addParam('定期価格差', 'period_price_difference', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('価格', 'price02', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }
    
    /**
     * パラメータをセットする
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param array $arrParams パラメータ配列
     */
    static function sfSetFormParam(SC_FormParam_Ex &$objFormParam,$arrParams){
        
        $objFormParam->setParam($arrParams);
        
        //ダウンロード商品なら
        if($objFormParam->getValue('product_type_id') == 2){
            //定期販売は0
            $objFormParam->setValue('is_periodical', 0);
        }
        
        if(!is_numeric($objFormParam->getValue('period_price_difference'))){
            
            $objFormParam->setValue('period_price_difference', 0);
        }
        
        $objFormParam->convParam();
    }
    
    static function sfCheckError(SC_FormParam_Ex &$objFormParam){
        
        $objError = new SC_CheckError_Ex();
        $objError->arrErr = $objFormParam->checkError();
        $arrParams = $objFormParam->getHashArray();
        
        if(!empty($objError->arrErr['period_price_difference'])){
            
            if(preg_match('/^-[0-9]+$/', $arrParams['period_price_difference'])){
                
                unset($objError->arrErr['period_price_difference']);
            }
            
            if($arrParams['price02'] + $arrParams['period_price_difference'] < 0){
                
                $objError->arrErr['period_price_difference'] = '定期価格差は商品価格より高く設定できません。<br />';
            }
        }
        
        return $objError->arrErr;
    }
    
    /**
     * 定期商品を登録する。
     * 
     * @param array $arrProduct 商品のデータ配列
     */
    static function sfRegistPeriodicalProduct($arrProduct){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_products';
        $where = 'product_id = ?';
        $arrWhereValues = array($arrProduct['product_id']);
        $objQuery->delete($table,$where,$arrWhereValues);
        
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrProduct);
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->insert($table,$arrValues);
    }
    
    /**
     * 定期商品情報をセットする。
     * 
     * @param array $arrProduct 商品のデータ配列のポインタ
     */
    static function sfSetPeriodicalProduct(&$arrProduct){
        
        if(empty($arrProduct['product_id'])) return;
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_products';
        $where = 'product_id = ?';
        $arrWhereValues = array($arrProduct['product_id']);
        $arrPeriodicalSaleProduct = $objQuery->select('*',$table,$where,$arrWhereValues);
        
        if(!empty($arrPeriodicalSaleProduct[0])){
            $arrProduct += $arrPeriodicalSaleProduct[0];
        }
    }
}
