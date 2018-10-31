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
class plg_AddProductColumns_LC_Page_Admin_Products_Product extends LC_Page_Admin_Ex{
    
    function action_after(LC_Page_Admin_Products_Product_Ex $objPage){
        
        //表示テンプレートをリセット
        $objPage->tpl_mainpage = 'products/product.tpl';
        $objPage->arrColumns = $this->getColumns();
        
        $post = $_POST;
        $objFormParam = new SC_FormParam_Ex();
        $this->initFormParam($objFormParam, $objPage->arrColumns);
        $this->setFormParam($objFormParam, $post);
        
        $objOriginalFormParam = new SC_FormParam_Ex();
        // アップロードファイル情報の初期化
        $objUpFile = new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $objPage->lfInitFile($objUpFile);
        $objUpFile->setHiddenFileList($_POST);
        // ダウンロード販売ファイル情報の初期化
        $objDownFile = new SC_UploadFile_Ex(DOWN_TEMP_REALDIR, DOWN_SAVE_REALDIR);
        $objPage->lfInitDownFile($objDownFile);
        $objDownFile->setHiddenFileList($_POST);
        
        $mode = $objPage->getMode();
        switch($mode){
            
            case 'pre_edit':
            case 'copy':
                $product_id = !empty($objPage->arrForm['copy_product_id']) ? $objPage->arrForm['copy_product_id'] : $objPage->arrForm['product_id'];
                $this->setValuesToFormParam($objFormParam, $product_id);
                break;
            
            case 'edit':
                $objPage->lfInitFormParam($objOriginalFormParam, $_POST);
                $arrOriginalForm = $objOriginalFormParam->getHashArray();
                $arrErr = array_merge($objPage->arrErr, $this->checkError($objFormParam));
                if(empty($arrErr)){
                    
                    $objPage->tpl_mainpage = 'products/confirm.tpl';
                }
                elseif(empty($objPage->arrErr)){
                    
                    // この時点ではarrFormが確認画面用になっているので、入力画面用にする
                    $objPage->arrForm = $objPage->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrOriginalForm);
                    $objPage->tpl_onload = $objPage->lfSetOnloadJavaScript_InputPage();
                }
                $objPage->arrErr = $arrErr;
                break;
            
            case 'complete':
                $objPage->lfInitFormParam($objOriginalFormParam, $_POST);
                $arrOriginalForm = $objOriginalFormParam->getHashArray();
                $arrErr = array_merge($objPage->arrErr, $this->checkError($objFormParam));
                if(empty($arrErr)){
                    
                    $product_id = $objPage->arrForm['product_id'];
                    $objFormParam->setValue('product_id', $product_id);
                    $this->saveValues($objFormParam);
                    //完了画面を表示
                    $objPage->tpl_mainpage = 'products/complete.tpl';
                }
                elseif(empty($objPage->arrErr)){
                    
                    // この時点ではarrFormが完了画面用になっているので、入力画面用にする
                    $objPage->arrForm = $objPage->lfSetViewParam_InputPage($objUpFile, $objDownFile, $arrOriginalForm);
                    $objPage->tpl_onload = $objPage->lfSetOnloadJavaScript_InputPage();
                }
                $objPage->arrErr = $arrErr;
                break;
        }
        
        $objPage->arrForm = array_merge($objFormParam->getHashArray(), $objPage->arrForm);
    }
    
    /**
     * 追加項目を取得する。
     * 
     * @return array 
     */
    function getColumns(){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('column_id ASC');
        $arrColumns = $objColumn->getColumns();
        return $arrColumns;
    }
    
    /**
     * パラメータを初期化する。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param array $arrColumns 追加項目の配列
     */
    function initFormParam(SC_FormParam_Ex &$objFormParam, $arrColumns){
        
        foreach($arrColumns as $arrColumn){
            
            
            switch($arrColumn['type']){
                
                case COLUMN_TYPE_TEXT:
                case COLUMN_TYPE_TEXTAREA:
                    $arrCheck = array('SPTAB_CHECK', 'MAX_LENGTH_CHECK');
                    break;
                
                //text, textarea以外の場合は初期化しない
                default:
                    continue 2;
                    break;
            }
            
            if(!empty($arrColumn['required'])){
                
                $arrCheck[] = 'EXIST_CHECK';
            }
            
            $objFormParam->addParam($arrColumn['name'], PAPC_PREFIX . $arrColumn['column_id'], $arrColumn['max_length'], 'KVa', $arrCheck);
        }
        
        $objFormParam->addParam('商品ID', 'product_id');
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
     * 追加項目値をDBからセットする。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @param integer $product_id 
     */
    function setValuesToFormParam(SC_FormParam_Ex &$objFormParam, $product_id){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $arrParam = $objColumn->getProductValuesAsParam($product_id);
        $objFormParam->setParam($arrParam);
    }
    
    /**
     * エラーチェック。
     * 
     * @param SC_FormParam_Ex $objFormParam
     * @return array 
     */
    function checkError(SC_FormParam_Ex &$objFormParam){
        
//        $objError = new SC_CheckError_Ex($objFormParam->getHashArray());
        $arrErr = $objFormParam->checkError();
        return $arrErr;
    }
    
    /**
     * 追加商品データをデータベースに登録する
     * @param array $arrList 追加商品データの配列
     */
    function saveValues(SC_FormParam_Ex &$objFormParam){
        
        $objColumn = new plg_AddProductColumns_SC_Helper_Column_Ex();
        $arrParam = $objFormParam->getHashArray();
        $product_id = $arrParam['product_id'];
        $objColumn->saveValues($product_id, $arrParam);
    }
}