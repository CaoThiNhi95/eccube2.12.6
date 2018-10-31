<?php
/*
 *
 * AddProductColumns
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

// TODO 検索画面でも表示できるようになるかしら
// TODO ファイル項目も増やせるようになるかしら (難易度高そう)
// TODO 項目を上下に移動 (メーカー参照)
// TODO transactionを使ってみるなど
// TODO CSV対応
// TODO nl2brの有無を変更できるように

/**
 * プラグインのメインクラス
 *
 * @package AddProductColumns
 * @author DAISY CO.,LTD.
 * @version $
 */
class AddProductColumns extends SC_Plugin_Base {
    
    /**
     * インストール時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    public function install($arrPlugin) {
        
        require_once PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/include.php';
        self::copyFiles($arrPlugin);
        self::createTables();
    }
    
    /**
     * 削除時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    public function uninstall($arrPlugin) {
        
        require_once PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/include.php';
        self::dropTables();
        self::deleteFiles($arrPlugin);
    }
    
    /**
     * 有効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    public function enable($arrPlugin) {
    }
    
    /**
     * 無効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    public function disable($arrPlugin) {
    }
    
    /**
     * プラグイン用テーブルの作成
     * @return void
     */
    static function createTables(){
        
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $objSql = self::getSqlClass();

        //テーブル作成
        foreach($objSql->getSchema() as $table => $arrSchemaFields){
            
            $arrFields = array();

            foreach($arrSchemaFields as $field => $attribute){
                
                $arrFields[] = sprintf('%s %s', $field, $attribute);
            }
            
            $fields = implode(',', $arrFields);
            $sql = sprintf('CREATE TABLE %s (%s)', $table, $fields);
            $objQuery->query($sql);
        }
    }
    
    /**
     * RDBMS別のスキーマを取得する
     * 
     * @return array スキーマ配列 
     */
    static function getSqlClass(){
        
        switch(DB_TYPE){
            
            case 'mysql':
                $objSql = new plg_AddProductColumns_Sql_MySQL_Ex();
                break;
            
            case 'pgsql':
                $objSql = new plg_AddProductColumns_Sql_PostgreSQL_Ex();
                break;
            
            default:
                $objSql = null;
                break;
        }
        return $objSql;
    }

    /**
     * プラグイン用テーブルの削除
     */
    static function dropTables() {
        
        $objQuery = &SC_Query_Ex::getSingletonInstance();
        $arrExistsTables = $objQuery->listTables();
        $objSql = self::getSqlClass();
        $arrTables = array_keys($objSql->getSchema());
        
        //テーブル削除
        foreach($arrTables as $table){
            
            if(in_array($table, $arrExistsTables)){
                
                $sql = sprintf('DROP TABLE %s', $table);
                $objQuery->query($sql);
            }
        }
        
        switch(DB_TYPE){
            
            case 'pgsql':
                $arrDropSequences = array(
                    'plg_apc_dtb_columns_column_id',
                    'plg_apc_dtb_values_value_id'
                );
                $arrSequences = $objQuery->listSequences();
                foreach($arrDropSequences as $sequence){
                    
                    if(in_array($sequence, $arrSequences)){
                        
                        $objQuery->query(sprintf('DROP SEQUENCE %s_seq', $sequence));
                    }
                }
                break;
            
            case 'mysql':
                break;
        }
    }
    
    /**
     * プラグイン用ファイルをコピー 
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    static function copyFiles($arrPlugin){
        
        //html
        plg_AddProductColumns_SC_Utils_Ex::copy_recursive(PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/html/', PLUGIN_HTML_REALDIR . 'AddProductColumns/');
        //アイコン
        plg_AddProductColumns_SC_Utils_Ex::copy_recursive(PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/logo.png', PLUGIN_HTML_REALDIR . 'AddProductColumns/logo.png');
    }
    
    /**
     * プラグイン用コアファイルを削除
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void 
     */
    static function deleteFiles($arrPlugin){
        //html
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . 'AddProductColumns/');
    }
    
    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    static function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        
        $objTransform = new SC_Helper_Transform($source);
        $templates_dir = PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/templates/';
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE:
            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_PC:
                break;
            case DEVICE_TYPE_ADMIN:
            default:
                // 商品ナビゲーション
                if (strpos($filename, 'products/subnavi.tpl') !== false) {
                    $objTransform->select('ul.level1')->appendChild(file_get_contents($templates_dir . 'admin/products/plg_AddProductColumns_snippet_subnavi.tpl'));
                }
                // 商品登録・編集画面
                elseif(strpos($filename, 'products/product.tpl') !== false){
                    $objTransform->select('#products .form', 0)->insertAfter(file_get_contents($templates_dir.'admin/products/plg_AddProductColumns_snippet_product.tpl'));
                }
                // 商品登録・編集の確認画面
                elseif(strpos($filename, 'products/confirm.tpl') !== false){
                    $objTransform->select('#products table', 0)->insertAfter(file_get_contents($templates_dir.'admin/products/plg_AddProductColumns_snippet_confirm.tpl'));
                }
                break;
        }
        $source = $objTransform->getHTML();
    }
    
    /**
     * processのプレコールバック関数
     * 
     * @param LC_Page_EX $objPage 
     */
    function preProcess (LC_Page_EX $objPage) {
        
        require_once PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/include.php';
    }
    
    /**
     * 商品一覧ページ after
     * @param LC_Page_EX $objPage 
     */
    function LC_Page_Products_List_action_after(LC_Page_Products_List_Ex $objPage){
        
        $objPluginPage = new plg_AddProductColumns_LC_Page_Products_List_Ex();
        $objPluginPage->action_after($objPage);
    }
    
    /**
     * 商品詳細ページ after
     * @param LC_Page_EX $objPage 
     */
    function LC_Page_Products_Detail_action_after(LC_Page_EX $objPage){
        
        $objPluginPage = new plg_AddProductColumns_LC_Page_Products_Detail_Ex();
        $objPluginPage->action_after($objPage);
    }
    
    /**
     * 商品登録・編集ページ after
     * @param LC_Page_Admin_EX $objPage 
     */
    function LC_Page_Admin_Products_Product_action_after(LC_Page_Admin_EX $objPage){
        
        $objPluginPage = new plg_AddProductColumns_LC_Page_Admin_Products_Product_Ex();
        $objPluginPage->action_after($objPage);
    }
}