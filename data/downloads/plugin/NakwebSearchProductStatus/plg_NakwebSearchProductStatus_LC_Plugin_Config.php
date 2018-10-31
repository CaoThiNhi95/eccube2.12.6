<?php
/*
 * NakwebSearchProductStatus
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
 
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * プラグインの設定クラス
 *
 * @package NakwebSearchProductStatus
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class plg_NakwebSearchProductStatus_LC_Plugin_Config extends LC_Page_Admin_Ex {

    var $arrForm = array();

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        // プラグインコードの取得（フォルダ名からプラグインコードを取得する）
        $this->plugin_code  = basename(dirname(__FILE__));
        // メインページファイル
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR . $this->plugin_code . "/templates/config.tpl";
        // サブタイトル
        $this->tpl_subtitle = "商品検索条件 追加プラグイン";
        // 管理画面で表示する説明文
        $this->tpl_note     = "商品検索条件 追加プラグインを表示する際の設定を行います。";

        // 条件の有効無効選択ボックス
        $this->arrEnableCheck = array('0' => '無効', '1' => '有効');

    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();


        $arrForm = array();

        switch ($this->getMode()) {
            case 'edit':

                $arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                // エラーなしの場合にはデータを更新
                if (count($this->arrErr) == 0) {

                    // データ更新
                    $arrConf = $arrForm;
                    $this->arrErr = $this->lfSetPluginConfig($arrConf);
                    if (count($this->arrErr) == 0) {
                        $this->tpl_onload = "alert('登録が完了しました。');";
                        $this->tpl_onload .= 'window.close();';
                    }

                }
                break;

            default:

                // プラグイン情報を取得.
                $data = $this->lfGetPluginConfig();
                foreach ($data as $data_key => $data_value) {
                    $arrForm[$data_key] = $data_value;
                }

                break;

        }
        $this->arrForm = $arrForm;
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('商品ステータス', 'product_status_id', '', '', array());
        $objFormParam->addParam('商品コード', 'product_code', '', '', array());
    }

    /**
     * プラグイン設定の取得
     *
     * @param type $arrData
     * @return type
     */
    function lfGetPluginConfig() {
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode($this->plugin_code);
        return unserialize($plugin['free_field1']);
    }

    /**
     * プラグイン設定の更新
     *
     * @param type $arrData
     * @return type
     */
    function lfSetPluginConfig($arrData) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql_conf = array();
        $sql_conf['free_field1'] = serialize($arrData);
        $sql_conf['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        $objQuery->update('dtb_plugin', $sql_conf, $where, array($this->plugin_code));
    }


}
?>
