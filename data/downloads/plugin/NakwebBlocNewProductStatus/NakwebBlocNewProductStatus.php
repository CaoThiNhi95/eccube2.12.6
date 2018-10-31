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

require_once "plg_NakwebBlocNewProductStatus_SC_Plugin_Bloc_DB_Set.php";

/**
 * プラグインのメインクラス
 *
 * @package NakwebBlocNewProductStatus
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class NakwebBlocNewProductStatus extends SC_Plugin_Base {

    // 静的定数(CONSTはPHP5.3以降)
    private static $nakweb_plugin_individual = 'plg_nakweb_00004';    // nakweb プラグイン番号

    /**
     * コンストラクタ
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

        // 必要なファイルをコピーします.
        // ロゴ画像
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/logo.png', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/logo.png');

        // 管理画面用ファイル
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/config.php', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/config.php');

        // プラグイン用データベース設定（plugin config）
        $arrData  = array();
        $arrData['title']   = '新着商品';
        $arrData['limit']   = 4;
        $arrData['period']  = 7;
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $sql_conf = array();
        $sql_conf['free_field1'] = serialize($arrData);
        $sql_conf['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sql_conf, $where, array($arrPlugin['plugin_code']));

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

        // メディアディレクトリ削除.
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/css');
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/img');
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code']);

        // プラグイン用データベース設定 (dtb_bloc，dtb_blocposition)
        $objPlgDb = new plg_NakwebBlocNewProductStatus_SC_Plugin_Bloc_DB_Set();
        $arrDbSetFlg = array();
        $arrDbSetFlg['Insert'] = false;
        $arrDbSetFlg['Update'] = false;
        $arrDbSetFlg['Delete'] = true;
        $ret = $objPlgDb->sfProductStatusBlocDatabase($arrPlugin, $arrDbSetFlg);

        return $ret;

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

        // プラグイン用データベース設定
        $objPlgDb = new plg_NakwebBlocNewProductStatus_SC_Plugin_Bloc_DB_Set();
        $arrDbSetFlg = array();
        $arrDbSetFlg['Insert'] = true;
        $arrDbSetFlg['Update'] = false;
        $arrDbSetFlg['Delete'] = false;
        $ret = $objPlgDb->sfProductStatusBlocDatabase($arrPlugin, $arrDbSetFlg);

        return $ret;

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

        // プラグイン用データベース設定
        $objPlgDb = new plg_NakwebBlocNewProductStatus_SC_Plugin_Bloc_DB_Set();
        $arrDbSetFlg = array();
        $arrDbSetFlg['Insert'] = false;
        $arrDbSetFlg['Update'] = false;
        $arrDbSetFlg['Delete'] = true;
        $ret = $objPlgDb->sfProductStatusBlocDatabase($arrPlugin, $arrDbSetFlg);

        return $ret;

    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     * 
     * @param SC_Helper_Plugin $objHelperPlugin 
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
         // nop
    }

    // // スーパーフックポイント（preProcess）
    // function preProcess() {
    //     // nop
    // }

    // // スーパーフックポイント（prosess）
    // function prosess() {
    //     // nop
    // }



    //==========================================================================
    // Original Function
    //==========================================================================


}
?>
