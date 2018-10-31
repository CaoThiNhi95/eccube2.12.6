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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * AddProductColumns の管理ページクラス.
 *
 * @package Page
 * @author DAISY CO.,LTD.
 * @version $
 */
 
class plg_AddProductColumns_LC_Page_Admin_Products_Columns extends LC_Page_Admin_Ex{
    
    /**
     * Page を初期化する
     * @return void
     */
    function init(){

        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/templates/admin/products/plg_AddProductColumns_columns.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'add_product_columns';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品情報管理';
    }
    
    /**
     * Page のプロセス
     * 
     * @return void
     */
    function process(){
        $this->action();
        $this->sendResponse();
    }
    
    function action(){
        
        $post = $_POST;
        $objFormParam = new SC_FormParam_Ex();
        $this->initFormParam($objFormParam);
        $this->setFormParam($objFormParam, $post);

        $mode = $this->getmode();
        switch($mode){
            
            //登録が押された場合
            case 'add':
                $arrErr = $this->checkError($objFormParam);
                $this->arrErr = array_merge((array)$this->arrErr, $arrErr);
                if(empty($this->arrErr)){
                    
                    $column_id = $this->saveColumn($objFormParam);
                    //拡張版のために再度セット
                    $this->setColumnToFormParam($objFormParam, $column_id);
                    $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/templates/admin/products/plg_AddProductColumns_columns_complete.tpl';
                }
                break;
            
            //削除が押された場合
            case 'delete':
                $this->deleteColumn($objFormParam->getValue('column_id'));
                $this->tpl_onload = 'alert("項目を削除しました。");';
                break;
            
            //編集が押された場合
            case 'edit':
                $this->setColumnToFormParam($objFormParam, $objFormParam->getValue('column_id'));
                break;
        }

        $this->arrForm = array_merge((array)$this->arrForm, $objFormParam->getFormParamList());
        $this->arrColumns = $this->getColumns();
    }
    
    /**
     * column_idが$column_idの追加商品項目と、それに紐付けられた追加商品データを削除する
     * @param integer $column_id 
     */
    function deleteColumn($column_id){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $objColumn->deleteColumn($column_id);
    }
    
    /**
     * 追加商品項目を登録する。
     * 
     * @param SC_FormParam_Ex $objFormParam 
     * @return integer 追加項目ID
     */
    function saveColumn(SC_FormParam_Ex &$objFormParam){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $arrColumn = $objFormParam->getHashArray();
        return $objColumn->saveColumn($arrColumn);
    }
    
    /**
     * パラメーター情報の初期化
     * 
     * @param SC_FormParam_Ex $objFormParam SC_FormParam_Exインスタンス
     * @return void
     **/
    function initFormParam(&$objFormParam){
        
        $objFormParam->addParam('ID', 'column_id', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('項目名', 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('入力パターン', 'type', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'ALPHA_CHECK'));
        $objFormParam->addParam('必須チェック', 'required', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
        $objFormParam->addParam('文字数制限', 'max_length', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
    }
    
    /**
     * パラメータをセットする。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param array $arrParam 
     */
    function setFormParam(SC_FormParam_Ex &$objFormParam, $arrParam){
        
        $objFormParam->setParam($arrParam);
        $objFormParam->convParam();
    }
    
    /**
     * エラーチェックを行う。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @return array
     */
    function checkError(SC_FormParam_Ex &$objFormParam){
        
        $arrErr = $objFormParam->checkError();
        $arrParam = $objFormParam->getFormParamList();
        $objError = new SC_CheckError_Ex($objFormParam->getHashArray());
        
        switch($objFormParam->getValue('type')){
            
            case COLUMN_TYPE_TEXT:
            case COLUMN_TYPE_TEXTAREA:
                
                $objError->doFunc(array($arrParam['max_length']['disp_name'], 'max_length'), array('EXIST_CHECK'));
                $objError->doFunc(array($arrParam['required']['disp_name'], 'required'), array('EXIST_CHECK'));
                break;
        }
        
        $arrErr = array_merge($objError->arrErr, $arrErr);
        return $arrErr;
    }
   
    /**
     * 追加商品項目をIDを指定して取得する
     * @param integer $column_id
     * @return type 
     */
    function setColumnToFormParam(SC_FormParam_Ex &$objFormParam, $column_id){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $arrColumn = $objColumn->getColumn($column_id);
        $objFormParam->setParam($arrColumn);
    }
    
    /**
     * 追加商品項目のデータを取得する
     * @return array 追加商品項目配列
     **/
    function getColumns(){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('column_id ASC');
        $arrColumns = $objColumn->getColumns($objQuery);
        
        foreach($arrColumns as $key => $arrColumn){
            
            if($arrColumn['type'] != COLUMN_TYPE_TEXT && $arrColumn['type'] != COLUMN_TYPE_TEXTAREA){
                
                unset($arrColumns[$key]);
            }
        }
        return $arrColumns;
    }
}