<?php
/*
 * NakwebBlocNewProductStatus
 * Copyright (C) 2012 NAKWEB CO.,LTD. All Rights Reserved.
 * http://www.nakweb.com/
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
 * @package NakwebBlocNewProductStatus
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class plugin_update{
   /**
     * アップデート
     * updateはアップデート時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($arrPlugin) {
        switch($arrPlugin['plugin_version']){
        // バージョン0.1からのアップデート
        case("0.1"):
        //バージョン1.1からのアップデート
        case("1.1"):
        //バージョン1.2からのアップデート
        case("1.2"):
        //バージョン1.3からのアップデート
        case("1.3"):
        //バージョン1.4からのアップデート
        case("1.4"):
        //バージョン1.5からのリビジョンアップデート
        case("1.5"):
        case("1.5.1"):
           plugin_update::allupdatever15($arrPlugin);
           plugin_update::insertFookPoint($arrPlugin);
           break;
        default:
           break;
        }
    }

    /**
     * 全てのバージョンにて統一アップデートを行います。
     * @param type $param 
     */
    function allupdatever15($arrPlugin) {

        // 変更のあったファイルを上書きします.
        // 管理画面用ファイル
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/config.php", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/config.php");

        //// プログラムファイル
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/plugin_info.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plugin_info.php");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/NakwebSearchProductStatus.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/NakwebSearchProductStatus.php");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/plg_NakwebSearchProductStatus_LC_Page_Products_List.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plg_NakwebSearchProductStatus_LC_Page_Products_List.php");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/plg_NakwebSearchProductStatus_LC_Plugin_Config.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plg_NakwebSearchProductStatus_LC_Plugin_Config.php");

        //// テンプレートファイル
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/templates/plg_NakwebSearchProductStatus_search_products.tpl", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/templates/plg_NakwebSearchProductStatus_search_products.tpl");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/templates/plg_NakwebSearchProductStatus_products_list_form1_input.tpl", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/templates/plg_NakwebSearchProductStatus_products_list_form1_input.tpl");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/templates/plg_NakwebSearchProductStatus_products_list_pagecond_area.tpl", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/templates/plg_NakwebSearchProductStatus_products_list_pagecond_area.tpl");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/templates/config.tpl", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/templates/config.tpl");

        // プラグイン用の変数設定
        $plugin_data  = '';
        $arrData   = array();
        $arrData['product_status_id'] = 1;
        $arrData['product_code']      = 1;
        $plugin_data  = serialize($arrData);

        // dtb_pluginを更新します.
        plugin_update::updateDtbPluginData($arrPlugin, $plugin_data);

    }

    /**
     * dtb_pluginを更新します.
     * 各バージョンに対するアップデートです
     *
     * @param int $arrPlugin プラグイン情報
     * @return void
     */
    function updateDtbPluginData($arrPlugin, $plugin_data = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $table = "dtb_plugin";
        if (strlen($plugin_data) > 0) {
            // データが存在している場合は更新する（シリアライズ化を事前に行なっておくこと）
            $sql_conf['free_field1']    = $plugin_data;
        }
        $sql_conf['plugin_name']        = '商品検索条件 追加プラグイン';
        $sql_conf['plugin_description'] = 'PC版の商品検索ブロックに対して絞り込み条件を追加します。';
        $sql_conf['plugin_version']     = '1.5.2';
        $sql_conf['compliant_version']  = '2.12.2 ～ 2.13.5';
        $sql_conf['update_date']        = 'CURRENT_TIMESTAMP';
        $where = "plugin_id = ?";
        $objQuery->update($table, $sql_conf, $where, array($arrPlugin['plugin_id']));
    }

    /**
     * バージョン1.5では登録されていなかったフックポイントを登録する。
     * 
     * @param int $arrPlugin プラグイン情報
     * @return void
     */
    function insertFookPoint($arrPlugin) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // フックポイントをDB登録.
        $hook_point = array(
            array("LC_Page_Products_List_action_before", 'plg_ploducts_List_action'),
            array("LC_Page_FrontParts_Bloc_SearchProducts_action_after", 'lfSetPlgProductStatusList')
        );
        /**
         * FIXME コードが重複しているため、要修正
         */
        // フックポイントが配列で定義されている場合
        if (is_array($hook_point)) {
            foreach ($hook_point as $h) {
                $arr_sqlval_plugin_hookpoint = array();
                $id = $objQuery->nextVal('dtb_plugin_hookpoint_plugin_hookpoint_id');
                $arr_sqlval_plugin_hookpoint['plugin_hookpoint_id'] = $id;
                $arr_sqlval_plugin_hookpoint['plugin_id'] = $arrPlugin['plugin_id'];
                $arr_sqlval_plugin_hookpoint['hook_point'] = $h[0];
                $arr_sqlval_plugin_hookpoint['callback'] = $h[1];
                $arr_sqlval_plugin_hookpoint['update_date'] = 'CURRENT_TIMESTAMP';
                $objQuery->insert('dtb_plugin_hookpoint', $arr_sqlval_plugin_hookpoint);
            }
        // 文字列定義の場合
        } else {
            $array_hook_point = explode(',', $hook_point);
            foreach ($array_hook_point as $h) {
                $arr_sqlval_plugin_hookpoint = array();
                $id = $objQuery->nextVal('dtb_plugin_hookpoint_plugin_hookpoint_id');
                $arr_sqlval_plugin_hookpoint['plugin_hookpoint_id'] = $id;
                $arr_sqlval_plugin_hookpoint['plugin_id'] = $arrPlugin['plugin_id'];
                $arr_sqlval_plugin_hookpoint['hook_point'] = $h;
                $arr_sqlval_plugin_hookpoint['update_date'] = 'CURRENT_TIMESTAMP';
                $objQuery->insert('dtb_plugin_hookpoint', $arr_sqlval_plugin_hookpoint);
            }
        }
        
        $objQuery->commit();
    }

}
?>
