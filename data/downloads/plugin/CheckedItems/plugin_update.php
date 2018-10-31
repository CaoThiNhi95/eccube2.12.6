<?php
/*
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
 * プラグイン のアップデート用クラス.
 *
 * @package CheckedItems
 * @author DAISY Inc.
 * @version $Id: $
 */
class plugin_update{

    static public $VERSION_HISTORY = array(
        '0.1'=>1
       ,'0.2'=>2
       ,'1.0'=>10
       ,'1.1'=>11
       ,'1.2'=>12
    );

   /**
     * アップデート
     * updateはアップデート時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($arrPlugin) {

        $old_version    = $arrPlugin['plugin_version'];
        $new_version    = plugin_info::$PLUGIN_VERSION;
        $version_weight = self::$VERSION_HISTORY[$old_version];

        if( $version_weight < self::$VERSION_HISTORY[$new_version] ){

            switch($version_weight){
                case   1 :
                case   2 :
                    // スマホ用テンプレコピー
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "templates/plg_checkeditems_sphone.tpl", PLUGIN_UPLOAD_REALDIR . "CheckedItems/templates/plg_checkeditems_sphone.tpl");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "templates/plg_checkeditems_sphone.tpl", SMARTPHONE_TEMPLATE_REALDIR . "frontparts/bloc/plg_checkeditems.tpl");
                    // ブロック修正コピー
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "LC_Page_FrontParts_Bloc_CheckedItems.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/LC_Page_FrontParts_Bloc_CheckedItems.php");

                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "plugin_update.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/plugin_update.php");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "plugin_info.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/plugin_info.php");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "templates/config.tpl", PLUGIN_UPLOAD_REALDIR . "CheckedItems/templates/config.tpl");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "LC_Page_Plugin_CheckedItems_Config.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/LC_Page_Plugin_CheckedItems_Config.php");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "logo.png", PLUGIN_UPLOAD_REALDIR . "CheckedItems/logo.png");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "logo.png", PLUGIN_HTML_REALDIR . "CheckedItems/logo.png");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "CheckedItems.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/CheckedItems.php");
                    self::deleteBloc($arrPlugin);
                    
                    self::savePluginConfig(); // プラグイン情報更新
                case   10 :
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "CheckedItems.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/CheckedItems.php");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "LC_Page_FrontParts_Bloc_CheckedItems.php", PLUGIN_UPLOAD_REALDIR . "CheckedItems/LC_Page_FrontParts_Bloc_CheckedItems.php");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "logo.png", PLUGIN_UPLOAD_REALDIR . "CheckedItems/logo.png");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "logo.png", PLUGIN_HTML_REALDIR . "CheckedItems/logo.png");
                case   11 :
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "logo.png", PLUGIN_UPLOAD_REALDIR . "CheckedItems/logo.png");
                    copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "logo.png", PLUGIN_HTML_REALDIR . "CheckedItems/logo.png");
                break;
                default:
                break;
            }
        }else{
            return "このバージョンはアップデート対応しておりません。";
        }
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
                'device_type_id'    => $type
               ,'bloc_id'           => $objQuery->max('bloc_id', "dtb_bloc", "device_type_id = " . $type) + 1
               ,'bloc_name'         => $arrPlugin['plugin_name']
               ,'tpl_path'          => 'plg_checkeditems.tpl'
               ,'filename'          => 'plg_checkeditems'
               ,'create_date'       => 'CURRENT_TIMESTAMP'
               ,'update_date'       => 'CURRENT_TIMESTAMP'
               ,'php_path'          => 'frontparts/bloc/plg_checkeditems.php'
               ,'deletable_flg'     => 0
               ,'plugin_id'         => $arrPlugin['plugin_id']
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
    
    /**
     * プラグイン情報の更新
     * @param type $arrData
     * @return void
     */
    function savePluginConfig() {
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        // アップデートデータ設定(無効にする)
        $sqlval = array(
            'plugin_name'           => plugin_info::$PLUGIN_NAME
           ,'author_site_url'       => plugin_info::$AUTHOR_SITE_URL
           ,'plugin_site_url'       => plugin_info::$PLUGIN_SITE_URL
           ,'plugin_version'        => plugin_info::$PLUGIN_VERSION
           ,'compliant_version'     => plugin_info::$COMPLIANT_VERSION
           ,'plugin_description'    => plugin_info::$DESCRIPTION
           ,'free_field3'           => '3'
           ,'enable'    => '2'
        );

        $objQuery->begin();
        // UPDATEする値を作成する。
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where, array(plugin_info::$PLUGIN_CODE));

        $objQuery->commit();
    }

}
