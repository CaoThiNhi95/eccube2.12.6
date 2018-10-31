<?php
/*
 * 商品一覧に絞り込み用キーワード表示
 * 商品一覧ページに絞込み検索ができるキーワードを生成・表示します
 * Copyright (C) 2013 Nobuhiko Kimoto
 * http://nob-log.info/contact/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * プラグインのメインクラス
 *
 * @package RefineKeyword
 * @author aratana CO.,LTD.
 * @version $Id: $
 */
class RefineKeyword extends SC_Plugin_Base
{
    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo)
    {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    public function install($arrPlugin)
    {
        // プラグインのロゴ画像をアップ
        if (file_exists(PLUGIN_UPLOAD_REALDIR ."RefineKeyword/logo.png")) {
            if(copy(PLUGIN_UPLOAD_REALDIR . "RefineKeyword/logo.png", PLUGIN_HTML_REALDIR . "RefineKeyword/logo.png") === false);
        }
    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function uninstall($arrPlugin)
    {
        // ロゴ画像削除
        if (file_exists(PLUGIN_HTML_REALDIR ."RefineKeyword/logo.png")) {
            if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "RefineKeyword/logo.png") === false);
        }
    }

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function enable($arrPlugin)
    {
        // nop
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function disable($arrPlugin)
    {
        // nop
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     */
    public function register(SC_Helper_Plugin $objHelperPlugin)
    {
        //$objHelperPlugin->addAction('outputfilterTransform', array($this, 'outputfilterTransform'));
        return parent::register($objHelperPlugin, $priority);
    }

    // プラグイン独自の設定データを追加
    public function insertFreeField()
    {
/*
//設定値を書き込む必要がある場合はコメントを外してください
$objQuery = SC_Query_Ex::getSingletonInstance();
$sqlval = array();
$sqlval['free_field1'] = "1";
$sqlval['free_field2'] = "1";
$sqlval['update_date'] = 'CURRENT_TIMESTAMP';
$where = "plugin_code = ?";
// UPDATEの実行
$objQuery->update('dtb_plugin', $sqlval, $where, array('商品一覧に絞り込み用キーワード表示'));
 */
    }

    public function insertBloc($arrPlugin)
    {
/*
//ブロックを挿入する必要がある場合はコメントを外してください
$objQuery = SC_Query_Ex::getSingletonInstance();
// dtb_blocにブロックを追加する.
$sqlval_bloc = array();
$sqlval_bloc['device_type_id'] = DEVICE_TYPE_PC;
$sqlval_bloc['bloc_id'] = $objQuery->max('bloc_id', "dtb_bloc", "device_type_id = " . DEVICE_TYPE_PC) + 1;
$sqlval_bloc['bloc_name'] = $arrPlugin['plugin_name'];
$sqlval_bloc['tpl_path'] = "plg_topicPath_topicpath.tpl";
$sqlval_bloc['filename'] = "plg_topicPath_topicpath";
$sqlval_bloc['create_date'] = "CURRENT_TIMESTAMP";
$sqlval_bloc['update_date'] = "CURRENT_TIMESTAMP";
$sqlval_bloc['php_path'] = "frontparts/bloc/plg_topicPath_topicpath.php";
$sqlval_bloc['deletable_flg'] = 0;
$sqlval_bloc['plugin_id'] = $arrPlugin['plugin_id'];
$objQuery->insert("dtb_bloc", $sqlval_bloc);
 */
    }

    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param  LC_Page_Ex $objPage  ページオブジェクト
     * @param  string     $filename テンプレートのファイル名
     * @return void
     */
    public function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RefineKeyword/';
        switch ($objPage->arrPageLayout['device_type_id']) {
        case DEVICE_TYPE_MOBILE:
        case DEVICE_TYPE_SMARTPHONE:
            break;
        case DEVICE_TYPE_PC:
            if (strpos($filename, 'products/list.tpl') !== false) {
                $objTransform->select('div.pagenumber_area')->insertBefore(file_get_contents($template_dir . 'RefineKeyword.tpl'));
            }
            break;
        case DEVICE_TYPE_ADMIN:
        default:
            //管理画面商品編集画面のテンプレートをフックするサンプル
/*
if (strpos($filename, 'products/product.tpl') !== false) {
$objTransform->select('table.form tr',1)->insertBefore(file_get_contents($template_dir . 'RefineKeyword.tpl'));
}
 */
            break;
        }
        //トランスフォームされた値で書き換え
        $source = $objTransform->getHTML();
    }

    public function LC_Page_Products_List_action_after($objPage)
    {
        $arrKeyWords = array();
        $arrSearchCondition = $objPage->lfGetSearchCondition($objPage->arrSearchData);
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($arrSearchCondition['where'], $arrSearchCondition['arrval']);
        $rs = $objQuery->select('comment3', 'dtb_products AS alldtl');

        foreach($rs as $arrProducts) {
            $arrKeyWords = array_merge($arrKeyWords, explode(',', $arrProducts['comment3']));
        }

        $objPage->arrKeywords = array_unique($arrKeyWords);
    }
}
