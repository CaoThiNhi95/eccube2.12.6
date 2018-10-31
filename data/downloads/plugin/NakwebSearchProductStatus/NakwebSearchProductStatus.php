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

require_once "plg_NakwebSearchProductStatus_LC_Page_Products_List.php";

/**
 * プラグインのメインクラス
 *
 * @package NakwebSearchProductStatus
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class NakwebSearchProductStatus extends SC_Plugin_Base {

    // 静的定数(CONSTはPHP5.3以降)
    protected static $nakweb_plgin_individual = 'plg_nakweb_00003';    // nakweb プラグイン番号

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
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/logo.png", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");

        // 管理画面用ファイル
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/config.php", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/config.php");

        // プラグイン用データベース設定（plugin config）
        $arrData  = array();
        $arrData['product_status_id'] = 1;
        $arrData['product_code']      = 1;
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
        // nop
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
        // nop
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
        // nop
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     * 
     * @param $objHelperPlugin
     * @param $priority
     */
    function register($objHelperPlugin, $priority) {
        parent::register($objHelperPlugin, $priority);
        //// 検索ブロック用 商品ステータス検索条件の追加
        $objHelperPlugin->addAction('prefilterTransform', array(&$this, 'prefilterTransform'), $this->arrSelfInfo['priority']);

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

    // テンプレートのフック（PC用商品検索ブロックの絞り込み条件追加）
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/templates/';
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE:
            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_ADMIN:
                break;
            case DEVICE_TYPE_PC:
            default:
                if (strpos($filename, 'frontparts/bloc/search_products.tpl') !== false) {
                    $objTransform->select('div.block_outer div#search_area div.block_body dl')->insertBefore(file_get_contents($template_dir . 'plg_NakwebSearchProductStatus_search_products.tpl'));
                } elseif (strpos($filename, 'products/list.tpl') !== false) {
                    $objTransform->select('div#undercolumn form#form1')->appendChild(file_get_contents($template_dir . 'plg_NakwebSearchProductStatus_products_list_form1_input.tpl'));
                    $objTransform->select('div#undercolumn ul.pagecond_area')->appendFirst(file_get_contents($template_dir . 'plg_NakwebSearchProductStatus_products_list_pagecond_area.tpl'));
                    $objTransform->select('div#undercolumn div.attention')->insertBefore(file_get_contents($template_dir . 'plg_NakwebSearchProductStatus_products_list_form1_input.tpl'));
                }
                break;
        }
        $source = $objTransform->getHTML();
    }


    //--------------------------------------------------------------------------
    // 検索結果一覧表示用条件追加用

    // 検索結果の取得
    function plg_ploducts_List_action($objPage) {
        // プラグイン用識別子
        $plg_head = NakwebSearchProductStatus::$nakweb_plgin_individual;

        switch ($objPage->mode) {
            case 'json':
                // 処理を行わない
                break;
            default:
                // 商品ステータスを条件に含んだ検索結果の取得
                $objProductList = new plg_NakwebSearchProductStatus_LC_Page_Products_List();
                register_shutdown_function(array($objProductList, 'destroy'));
                $objProductList->init($plg_head);
                $objProductList->process();
                break;
        }

    }

    //--------------------------------------------------------------------------
    // SearchBox 条件追加用

    /**
     * 検索条件用商品ステータスリストを追加する
     *
     * @param  $objPage
     * @return void
     */
    function lfSetPlgProductStatusList($objPage) {
        // プラグイン用識別子
        $plg_head = NakwebSearchProductStatus::$nakweb_plgin_individual;

        // 商品ステータスID取得
        $product_status_id = $this->lfGetProductStatusId();
        // 選択中の商品ステータスIDを判定する
        $select_product_statusc_id = $this->lfGetSelectedProductStatusId($product_status_id);
        $this->setDispParam($objPage, $plg_head . '_ploduct_status_id', $select_product_status_id);
        // 商品ステータス検索用選択リスト
        $arrProductStatusList = $this->lfGetProductStatusList();
        $this->setDispParam($objPage, $plg_head . '_arrProductStatusList', $arrProductStatusList);

    }

    /**
     * 商品ステータスIDを取得する.
     *
     * @return string $ 商品ステータスID
     */
    function lfGetProductStatusId() {
        // プラグイン用識別子
        $plg_head = NakwebSearchProductStatus::$nakweb_plgin_individual;
        $ps_name = $plg_head . 'product_status_id';
        // 商品ステータスの変数取得
        $product_status_id = '';
        if (isset($_GET[$ps_name]) && $_GET[$ps_name] != '' && is_numeric($_GET[$ps_name])) {
            $product_status_id = $_GET[$ps_name];
        }
        return $product_status_id;
    }

    /**
     * 選択中の商品ステータスIDを取得する
     *
     * @return array $ 選択中の商品ステータスID
     */
    function lfGetSelectedProductStatusId($product_status_id) {
        // 選択中の商品ステータスIDを判定する
        $masterData = new SC_DB_MasterData_Ex();
        $arrStatusId = $masterData->getMasterData('mtb_status');
        // 指定した商品ステータスの取得
        $status_id = $arrStatusId[$product_status_id];
        return $status_id;
    }

    /**
     * 商品ステータス検索用選択リストを取得する
     *
     * @return array $ 商品ステータス検索用選択リスト
     */
    function lfGetProductStatusList() {

        // 条件の使用状態取得
        $plugin_code   = basename(dirname(__FILE__));
        $plugin_data   = SC_Plugin_Util_Ex::getPluginByPluginCode($plugin_code);
        $arrPluginData = unserialize($plugin_data['free_field1']);

        // 商品ステータスのWHERE文字列取得
        $arrList = array();
        if ($arrPluginData['product_status_id'] == 1) {

            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $objQuery->setOption('ORDER BY rank ASC');

            $col   = 'T1.id, name';
            $from  = 'mtb_status AS T1 LEFT JOIN (';
            $from .= ' SELECT product_status_id, count(*) AS product_count FROM dtb_product_status AS S1 LEFT JOIN';
            $from .= ' dtb_products AS S2 ON S1.product_id = S2.product_id';
            $from .= ' WHERE S2.del_flg = 0 AND S1.del_flg = 0 GROUP BY product_status_id) AS T2 ON T1.id = T2.product_status_id';    //ここの部分にて、削除された商品IDからは取得しないように設定するようにすること。
            $where = ' product_count > 0';

            $arrRet = $objQuery->select($col, $from, $where);

            $max = count($arrRet);

            for ($cnt = 0; $cnt < $max; $cnt++) {
                $id = $arrRet[$cnt]['id'];
                $name = $arrRet[$cnt]['name'];
                $arrProductStatusList[$id] = $name;
            }

            if (is_array($arrProductStatusList)) {
                // 文字サイズを制限する
                foreach ($arrProductStatusList as $key => $val) {
                    $arrProductStatusList[$key] = SC_Utils_Ex::sfCutString($val, SEARCH_CATEGORY_LEN, false);
                }
            }
        }

        return $arrProductStatusList;

    }


    /**
     * テンプレートに値を渡す
     *
     * @param string $obj オブジェクト
     * @param string $key キー名
     * @param string $val 値
     * @param string $add true の場合は追記する 
     * @return void
     */
    function setDispParam(&$obj, $key, $val, $add = false) {
        if ($add == true) {
            // 追記処理
            $obj->$key .= $val;
        } else {
            // 上書処理
            $obj->$key  = $val;
        }
    }



}
?>
