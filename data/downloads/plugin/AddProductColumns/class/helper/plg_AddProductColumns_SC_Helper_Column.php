<?php
/*
 * AddProductColumns
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
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

/**
 * AddProductColumns のヘルパー
 *
 * @package Column
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_AddProductColumns_SC_Helper_Column {
    
    /**
     * 単一の追加項目を取得する。
     * 
     * @param integer $column_id
     * @return array 
     */
    function getColumn($column_id){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $cols = '*';
        $table = 'plg_apc_dtb_columns';
        $where = 'column_id = ?';
        $arrWhereValues = array($column_id);
        $arrColumn = $objQuery->getRow($cols, $table, $where, $arrWhereValues);
        return $arrColumn;
    }
    
    /**
     * 複数の追加項目を取得する。
     * 
     * @return integer 
     */
    function getColumns(SC_Query_Ex &$objQuery = null){
        
        if(is_null($objQuery)){
            
            $objQuery = SC_Query_Ex::getSingletonInstance();
        }
        $cols = '*';
        $table = 'plg_apc_dtb_columns';
        $arrColumns = $objQuery->select($cols, $table);
        return $arrColumns;
    }
    
    /**
     * 追加項目を保存する。
     * 
     * @param array $arrColumn 
     * @return integer 追加項目ID
     */
    function saveColumn($arrColumn){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $column_id = isset($arrColumn['column_id']) ? $arrColumn['column_id'] : null;
        $table = 'plg_apc_dtb_columns';
        $where = 'column_id = ?';
        $arrWhereValues = array($column_id);
        $exists = $objQuery->exists($table, $where, $arrWhereValues);
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrColumn);
        
        if($exists){
            
            $objQuery->update($table, $arrValues, $where, $arrWhereValues);
        }
        else{
            
            $arrValues['column_id'] = $objQuery->nextVal('plg_apc_dtb_columns_column_id');
            $objQuery->insert($table, $arrValues);
        }
        
        return $arrValues['column_id'];
    }
    
    /**
     * 追加項目を削除する。
     * 
     * @param integer $column_id 
     */
    function deleteColumn($column_id){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $table = 'plg_apc_dtb_columns';
        $where = 'column_id = ?';
        $arrWhereValues = array($column_id);
        $objQuery->delete($table, $where, $arrWhereValues);
        
        $table = 'plg_apc_dtb_values';
        $where = 'column_id = ?';
        $objQuery->delete($table, $where, $arrWhereValues);
    }
    
    /**
     * 複数商品に追加項目値をセットする。
     * 
     * @param array $arrProducts 複数商品配列のポインタ
     */
    function applyValuesToProducts(&$arrProducts){
        
        $arrProductIds = array();
        $arrPoints = array();

        if(is_array($arrProducts)){
            
            foreach($arrProducts as &$arrPoint){

                $product_id = $arrPoint['product_id'];
                $arrPoints[$product_id] = &$arrPoint;
                $arrProductIds[] = $product_id;
            }
        }
        
        if(!empty($arrProductIds)){

            $objQuery = SC_Query_Ex::getSingletonInstance();
            
            //まず項目情報を全商品にセットする
            $table = 'plg_apc_dtb_columns';
            $arrColumns = $objQuery->select('*', $table);
            foreach($arrColumns as $arrColumn){
                
                foreach($arrPoints as $product_id => &$arrPoint){
                    
                    $arrPoints[$product_id][PAPC_PREFIX . $arrColumn['column_id']] = $arrColumn;
                }
            }
            
            //次に商品情報をセットする
            $table = <<<EOSQL
                plg_apc_dtb_values AS val
                    INNER JOIN plg_apc_dtb_columns columns
                    ON val.column_id = columns.column_id
EOSQL;
            $where = sprintf('product_id IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrProductIds)));
            $arrWhereValues = $arrProductIds;
            $arrValues = $objQuery->select('*', $table, $where, $arrWhereValues);
            foreach($arrValues as $arrValue){

                $product_id = $arrValue['product_id'];
                if(!empty($arrPoints[$product_id])){

                    $arrPoints[$product_id][PAPC_PREFIX . $arrValue['column_id']] = $arrValue;
                }
            }
        }
    }
    
    /**
     * 単一商品に追加項目値をセットする。
     * 
     * @param array $arrProduct 単一商品の配列ポインタ
     */
    function applyValuesToProduct(&$arrProduct){
        
        $arrProducts = array(&$arrProduct);
        $this->applyValuesToProducts($arrProducts);
    }
    
    /**
     * 商品に紐づく追加項目値を取得する。
     * 
     * @param integer $product_id
     * @return array 
     */
    function getProductValues($product_id){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $table = 'plg_apc_dtb_values';
        $where = 'product_id = ?';
        $arrWhereValues = array($product_id);
        $cols = '*';
        $arrResult = $objQuery->select($cols, $table, $where, $arrWhereValues);
        
        return $arrResult;
    }
    
    /**
     * 商品に紐づく追加項目値を、PAPC_PREFIX . column_idのキーとセットで取得する。
     * 
     * @param type $product_id
     * @return type 
     */
    function getProductValuesAsParam($product_id){
        
        $arrResult = array();
        $arrValues = $this->getProductValues($product_id);
        foreach($arrValues as $arrValue){
            
            $arrResult[PAPC_PREFIX . $arrValue['column_id']] = $arrValue['value'];
        }
        
        return $arrResult;
    }
    
    /**
     * 追加項目値を保存する。
     * 
     * @param integer $product_id
     * @param array $arrParam 
     */
    function saveValues($product_id, $arrParam){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $table = 'plg_apc_dtb_values';
        $where = 'product_id = ? AND column_id = ?';
        
        foreach($arrParam as $key => $value){
            
            if(preg_match(sprintf('/%s([0-9]+)/ui', PAPC_PREFIX), $key, $arrMatches)){
                
                $column_id = $arrMatches[1];
                $arrWhereValues = array($product_id, $column_id);
                $arrValues = compact('value', 'column_id', 'product_id');
                $exists = $objQuery->exists($table, $where, $arrWhereValues);
                
                if($exists){
                    
                    $objQuery->update($table, $arrValues, $where, $arrWhereValues);
                }
                else{
                    
                    $arrValues['value_id'] = $objQuery->nextVal('plg_apc_dtb_values_value_id');
                    $objQuery->insert($table, $arrValues);
                }
            }
        }
    }
}
