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
        // バージョン1.0からのアップデート
        case("1.0"):
        case("1.1"):
        case("1.2"):
            plugin_update::updatever($arrPlugin);
            plugin_update::updateDtbPluginData($arrPlugin);
           break;
        default:
           break;
        }
    }

    /**
     * 0.1と1.1のアップデートを実行します.
     * @param type $param 
     */
    function updatever($arrPlugin) {

        // 変更のあったファイルを上書きします.
        // 管理画面用ファイル
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/config.php", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/config.php");

        //// プログラムファイル
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/NakwebBlocNewProductStatus.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/NakwebBlocNewProductStatus.php");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/plugin_info.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plugin_info.php");
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/plg_NakwebBlocNewProductStatus_LC_Page_FrontParts_Bloc_Plugin.php", PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plg_NakwebBlocNewProductStatus_LC_Page_FrontParts_Bloc_Plugin.php");

        // プラグイン用の変数設定
        $plugin_data  = '';
        $arrData   = array();
        $arrData['product_status_id'] = 1;
        $arrData['product_code']      = 1;
        $plugin_data  = serialize($arrData);

        // dtb_pluhinを更新します.
        plugin_update::updateDtbPluginData($arrPlugin, $plugin_data);

    }

    /**
     * dtb_pluginを更新します.
     * Ver,1.2以下に対するアップデートです
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
        $sql_conf['plugin_name']        = '商品ステータス別新着表示ブロック';
        $sql_conf['plugin_description'] = 'PCサイト用の商品ステータス別の新着表示ブロックを追加します。（一度無効にするとブロックの変更内容やレイアウト設定などは初期化されます。）';
        $sql_conf['plugin_version']     = '1.2.1';
        $sql_conf['compliant_version']  = '2.12.2 ～ 2.13.5';
        $sql_conf['update_date']        = 'CURRENT_TIMESTAMP';
        $where = "plugin_id = ?";
        $objQuery->update($table, $sql_conf, $where, array($arrPlugin['plugin_id']));
    }

}
?>
