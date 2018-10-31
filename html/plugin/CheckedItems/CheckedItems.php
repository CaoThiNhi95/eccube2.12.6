<?php
/*
 *
 * CheckedItems
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

/**
 * プラグインのメインクラス
 *
 * @package CheckedItems
 * @author DAISY Inc.
 * @version $Id: $
 */
class CheckedItems extends SC_Plugin_Base {

    /**
     * コンストラクタ
     *
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        
        // プラグイン
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/logo.png', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/logo.png');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/CheckedItems.php', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/CheckedItems.php');

        // ブロック
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/plg_checkeditems.tpl', TEMPLATE_REALDIR . "frontparts/bloc/plg_checkeditems.tpl");
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/plg_checkeditems_sphone.tpl', SMARTPHONE_TEMPLATE_REALDIR . "frontparts/bloc/plg_checkeditems.tpl");
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/bloc/plg_checkeditems.php', HTML_REALDIR . "frontparts/bloc/plg_checkeditems.php");

        mkdir(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/media');
        SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/media/', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/media/');

        // 初期設定値を挿入
        self::insertFreeField();

    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {

        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "CheckedItems/media");
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR  . "frontparts/bloc/plg_checkeditems.php");
        SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . "frontparts/bloc/plg_checkeditems.tpl");
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "CheckedItems");

    }
    
    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {

        // ブロック登録
        self::insertBloc($arrPlugin);

    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {

        // ブロック削除
        self::deleteBloc($arrPlugin);

    }

    // プラグイン独自の設定データを追加
    function insertFreeField() {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $sqlval['free_field1'] = "30";	// クッキー保存時間
        $sqlval['free_field2'] = "5";	// データ取得個数
        $sqlval['free_field3'] = "3";	// データ取得個数
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where, array('CheckedItems'));
    }

    function insertBloc($arrPlugin) {

        $objQuery = SC_Query_Ex::getSingletonInstance();

        // PCとスマホのみ
        $arrDeviceTypeName = array(
            DEVICE_TYPE_PC,
            DEVICE_TYPE_SMARTPHONE
        );

        foreach($arrDeviceTypeName as $type){
            $sqlval_bloc = array(
                'device_type_id'    => $type,
                'bloc_id'           => $objQuery->max('bloc_id', "dtb_bloc", "device_type_id = " . $type) + 1,
                'bloc_name'         => $arrPlugin['plugin_name'],
                'tpl_path'          => 'plg_checkeditems.tpl',
                'filename'          => 'plg_checkeditems',
                'create_date'       => 'CURRENT_TIMESTAMP',
                'update_date'       => 'CURRENT_TIMESTAMP',
                'php_path'          => 'frontparts/bloc/plg_checkeditems.php',
                'deletable_flg'     => 0,
                'plugin_id'         => $arrPlugin['plugin_id']
            );
            $objQuery->insert('dtb_bloc',$sqlval_bloc);
        }

    }

    function deleteBloc($arrPlugin) {

        $objQuery = SC_Query_Ex::getSingletonInstance();
        
        // 削除対象ブロック情報取得
        $col   = "bloc_id, device_type_id";
        $where = "filename = ?";
        $arrValues = array(
            'plg_checkeditems',
        );
        $arrBlocItems = $objQuery->select($col, 'dtb_bloc', $where, $arrValues);

        foreach( $arrBlocItems as $bloc_item ){
            $bloc_id     = $bloc_item['bloc_id'];
            $device_type = $bloc_item['device_type_id'];

            // 対象デバイスのみ処理
            switch( $device_type ){
                case DEVICE_TYPE_PC :
                case DEVICE_TYPE_SMARTPHONE :
                    $where      = 'bloc_id = ? AND device_type_id = ?';
                    $arrValues  = array($bloc_id, $device_type);

                    $objQuery->delete('dtb_bloc', $where, $arrValues);
                    $objQuery->delete('dtb_blocposition', $where, $arrValues);
                break;
                default :
                break;
            }
        }

    }

    function LC_Page_Products_Detail_action_after($objPage) {

        // プロダクトIDの正当性チェック
        $product_id = self::CheckProductId($objPage->objFormParam->getValue('product_id'));
        
        if( $product_id ) {
            // 商品閲覧履歴取得（最近見た商品）
            self::setItemHistory($product_id);
        }
    }
    function checkProductId($product_id) {

        $where = 'del_flg = 0 AND status = 1';

        if (!SC_Utils_Ex::sfIsInt($product_id)
            || SC_Utils_Ex::sfIsZeroFilling($product_id)
            || !SC_Helper_DB_Ex::sfIsRecord('dtb_products', 'product_id', (array)$product_id, $where)
        ) {
            return false;
        }

        return $product_id;
        
    }
    function setItemHistory($product_id) {
        self::clearOldCookie();
        self::setCookie($product_id);
    }
    
    // 古いバージョンのクッキー削除用
    function clearOldCookie() {

        $key = 'plg_CheckedItems_product';
        if( is_array($_COOKIE[$key]) ){
            // クッキーを削除する
            foreach($_COOKIE[$key] as $idx=>$value){
                setcookie($key . '['. $idx .']', '', time()-1800, "/" );
            }
        }

    }
    
    function getCookieArray() {
        $key = 'plg_CheckedItems_product';
        $strItems = isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        
        if( $strItems ){
            $arrItems = explode(',', $strItems);
        }else{
            $arrItems = array();
        }
        return $arrItems;
    }
    
    function setCookie($product_id) {
        
        $plgInfo = self::getPlgInfo();
        
        $arrItems = self::getCookieArray();
        $strParam = '';
        $key = 'plg_CheckedItems_product';
        $expire = time()+60*60*24*$plgInfo['save_limit'];
        $item_count = $plgInfo['item_count'];
        
        if( !empty($arrItems) ){
            foreach( $arrItems as $idx=>$val) {
                if( $val != $product_id ){
                    $arrParam[] = $val;
                }
            }
            $arrParam[] = $product_id;
            if( count($arrParam) > $item_count ) {
                for( $cnt=0; $cnt <= count($arrParam) - $item_count; $cnt++ ){
                    unset($arrParam[$cnt]);
                }
            }
            
            $strParam = implode(',', $arrParam);
        }else{
            $strParam = $product_id;
        }
        
        setcookie($key, $strParam, $expire, ROOT_URLPATH, DOMAIN_NAME);
        
    }
    
    function getPlgInfo() {
        
        $arrParam = array();
        
        // プラグイン情報を取得.
        $plugin     = SC_Plugin_Util_Ex::getPluginByPluginCode("CheckedItems");
        //保存期間
        $arrParam['save_limit'] = is_numeric($plugin['free_field1']) ? $plugin['free_field1'] : 0;
        //保存件数
        $device_type = SC_Display_Ex::detectDevice();
        switch( $device_type ){
            case DEVICE_TYPE_SMARTPHONE :
                //保存件数
                $arrParam['item_count'] = is_numeric($plugin['free_field3']) ? $plugin['free_field3'] : 0;
            break;
            case DEVICE_TYPE_PC:
            default:
                //保存件数
                $arrParam['item_count'] = is_numeric($plugin['free_field2']) ? $plugin['free_field2'] : 0;
            break;
        } 
        
        return $arrParam;
    }
}
