<?php
/*
 * WpPost
 * Copyright (C) 2012 GIZMO CO.,LTD. All Rights Reserved.
 * http://www.gizmo.co.jp/
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
 * @package WpPost
 * @author Gizmo CO.,LTD.
 * @version $Id: $
 */
class WpPost extends SC_Plugin_Base {

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
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        // dtb_blocに記事一覧を追加する.
        $exist = $objQuery->exists('dtb_bloc', 'device_type_id = ? AND filename =?', array(DEVICE_TYPE_PC, "plg_WpPost_list"));
        if ($exist){
            $arrBlocIdList = $objQuery->getCol('bloc_id', "dtb_bloc", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "plg_WpPost_list"));
            $bloc_id_list = (int) $arrBlocIdList[0];
            $objQuery->delete("dtb_bloc", "bloc_id = ?", array($bloc_id_list));
        }
        $sqlval_bloc_comment = array();
        $sqlval_bloc_comment['device_type_id'] = DEVICE_TYPE_PC;
        $sqlval_bloc_comment['bloc_id'] = $objQuery->max('bloc_id', "dtb_bloc", "device_type_id = " . DEVICE_TYPE_PC) + 1;
        $sqlval_bloc_comment['bloc_name'] = "WpPost記事一覧";
        $sqlval_bloc_comment['tpl_path'] = "plg_WpPost_list.tpl";
        $sqlval_bloc_comment['filename'] = "plg_WpPost_list";
        $sqlval_bloc_comment['create_date'] = "CURRENT_TIMESTAMP";
        $sqlval_bloc_comment['update_date'] = "CURRENT_TIMESTAMP";
        $sqlval_bloc_comment['php_path'] = "frontparts/bloc/plg_WpPost_postlist.php";
        $sqlval_bloc_comment['deletable_flg'] = 0;
        $sqlval_bloc_comment['plugin_id'] = $arrPlugin['plugin_id'];
        // INSERTの実行
        $objQuery->insert("dtb_bloc", $sqlval_bloc_comment);

        // dtb_pagelayoutにポスト用ページを追加する.
        $exist = $objQuery->exists('dtb_pagelayout', 'device_type_id = ? AND filename =?', array(DEVICE_TYPE_PC, "wppost/plg_WpPost_post"));
        if ($exist){
            $arrBlocIdList = $objQuery->getCol('page_id', "dtb_pagelayout", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "wppost/plg_WpPost_post"));
            $bloc_id_list = (int) $arrBlocIdList[0];
            $objQuery->delete("dtb_pagelayout", "page_id = ?", array($bloc_id_list));
        }
        $sqlval_post = array();
        $sqlval_post['device_type_id'] = DEVICE_TYPE_PC;
        $sqlval_post['page_id'] = $objQuery->max('page_id', "dtb_pagelayout", "device_type_id = " . DEVICE_TYPE_PC) + 1;
        $sqlval_post['page_name'] = "WpPostポスト表示";
        $sqlval_post['url'] = "wppost/plg_WpPost_post.php";
        $sqlval_post['filename'] = "wppost/plg_WpPost_post";
        $sqlval_post['header_chk'] = "1";
        $sqlval_post['footer_chk'] = "1";
        $sqlval_post['edit_flg'] = "2";
        $sqlval_post['create_date'] = "CURRENT_TIMESTAMP";
        $sqlval_post['update_date'] = "CURRENT_TIMESTAMP";
        // INSERTの実行
        $objQuery->insert("dtb_pagelayout", $sqlval_post);

        // dtb_pagelayoutにカテゴリ用ページを追加する.
        $exist = $objQuery->exists('dtb_pagelayout', 'device_type_id = ? AND filename =?', array(DEVICE_TYPE_PC, "wppost/plg_WpPost_category"));
        if ($exist){
            $arrBlocIdList = $objQuery->getCol('page_id', "dtb_pagelayout", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "wppost/plg_WpPost_category"));
            $bloc_id_list = (int) $arrBlocIdList[0];
            $objQuery->delete("dtb_pagelayout", "page_id = ?", array($bloc_id_list));
        }
        $sqlval_category = array();
        $sqlval_category['device_type_id'] = DEVICE_TYPE_PC;
        $sqlval_category['page_id'] = $objQuery->max('page_id', "dtb_pagelayout", "device_type_id = " . DEVICE_TYPE_PC) + 1;
        $sqlval_category['page_name'] = "WpPostカテゴリ表示";
        $sqlval_category['url'] = "wppost/plg_WpPost_category.php";
        $sqlval_category['filename'] = "wppost/plg_WpPost_category";
        $sqlval_category['header_chk'] = "1";
        $sqlval_category['footer_chk'] = "1";
        $sqlval_category['edit_flg'] = "2";
        $sqlval_category['create_date'] = "CURRENT_TIMESTAMP";
        $sqlval_category['update_date'] = "CURRENT_TIMESTAMP";
        // INSERTの実行
        $objQuery->insert("dtb_pagelayout", $sqlval_category);

        // プラグイン独自の設定データを追加
        $sqlval = array();
        $sqlval['free_field1'] = "";
        $sqlval['free_field2'] = '記事カテゴリー';
        $sqlval['free_field3'] = "";
        $sqlval['free_field4'] = "";
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = 'WpPost'";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where);

        /**
         * 最新記事一覧
         *
         * postlist_title ブロックのタイトル
         * postlist_format 表示形式 記事のみ:1 固定ページのみ:2 両方:3
         * postlist_num 表示する数 デフォルト:5
         * postlist_postcount 記事数を表示 表示する:1 表示しない:0 デフォルト:1
         */
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $dbname = $dbFactory->sfGetDBVersion();
        if (strpos($dbname, 'MySQL') !== false){
            //MySQL
            $sql = "SHOW TABLES LIKE 'plg_%';";
        } else {
            //PostgreSQL
            $sql = "select tablename from pg_tables where tablename like 'plg_%';";
        }
        $tablelists = $objQuery->getAll($sql);
        if ($tablelists){
            $plg_tables = array();
            foreach ($tablelists as $tablelist) {
                foreach ($tablelist as $val) {
                    $plg_tables[] = $val;
                }
            }
            if (in_array("plg_WpPost_postlist", $plg_tables)){
                $sql_drop = "DROP TABLE plg_WpPost_postlist;";
                $objQuery->query($sql_drop);
            } else if (in_array("plg_wppost_postlist", $plg_tables)){
                $sql_drop = "DROP TABLE plg_wppost_postlist;";
                $objQuery->query($sql_drop);
            }
        }
        $sql = "CREATE TABLE plg_wppost_postlist (
                     ID INTEGER NOT NULL,
                     postlist_title text,
                     postlist_num smallint,
                     postlist_format smallint,
                     postlist_include text,
                     postlist_exclude text
                );";
        $objQuery->query($sql);

        //記事設定用のテーブルにデフォルトデータを入れる
        $sqlval_wppost_postlist = array();
        $sqlval_wppost_postlist['id'] = 1;
        $sqlval_wppost_postlist['postlist_title'] = '最近の記事';
        $sqlval_wppost_postlist['postlist_num'] = 5;
        $sqlval_wppost_postlist['postlist_format'] = 3;
        $sqlval_wppost_postlist['postlist_include'] = '';
        $sqlval_wppost_postlist['postlist_exclude'] = '';
        // INSERTの実行
        $objQuery->insert("plg_wppost_postlist", $sqlval_wppost_postlist);

        /**
         * コメント設定用のテーブル追加
         *
         * show_comment コメントの受付と表示 しない:0 する:1
         * comment_turn 表示順 0:新着順 1:古いものから
         * comment_login 投稿にはログイン必要 不要:0 必要:1
         * 廃止comment_format 表示を入れ子にするか必要 しない:0 する:1
         * comment_num 1ページで表示するコメント数 全て:0/デフォルト:5
         * comment_avatar_size アバターのサイズ デフォルト:32 非表示0
         */
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $dbname = $dbFactory->sfGetDBVersion();
        if (strpos($dbname, 'MySQL') !== false){
            //MySQL
            $sql = "SHOW TABLES LIKE 'plg_%';";
        } else {
            //PostgreSQL
            $sql = "select tablename from pg_tables where tablename like 'plg_%';";
        }
        $tablelists = $objQuery->getAll($sql);
        if ($tablelists){
            $plg_tables = array();
            foreach ($tablelists as $tablelist) {
                foreach ($tablelist as $val) {
                    $plg_tables[] = $val;
                }
            }
            if (in_array("plg_WpPost_comment", $plg_tables)){
                $sql_drop = "DROP TABLE plg_WpPost_comment;";
                $objQuery->query($sql_drop);
            } else if (in_array("plg_wppost_comment", $plg_tables)){
                $sql_drop = "DROP TABLE plg_wppost_comment;";
                $objQuery->query($sql_drop);
            }
        }
        $sql = "CREATE TABLE plg_wppost_comment (
                     ID INTEGER NOT NULL,
                     show_comment smallint,
                     comment_turn smallint,
                     comment_login smallint,
                     comment_login_ec smallint,
                     comment_login_fb smallint,
                     comment_login_tw smallint,
                     fb_appid text,
                     fb_secret text,
                     tw_consumer_key text,
                     tw_consumer_secret text,
                     comment_format smallint,
                     comment_num smallint,
                     comment_avatar_size smallint,
                     comment_restext text
                );";
        $objQuery->query($sql);

        //コメント設定用のテーブルにデフォルトデータを入れる
        $sqlval_wppost_comment = array();
        $sqlval_wppost_comment['id'] = 1;
        $sqlval_wppost_comment['show_comment'] = 0;
        $sqlval_wppost_comment['comment_turn'] = 0;
        $sqlval_wppost_comment['comment_login'] = 0;
        $sqlval_wppost_comment['comment_login_ec'] = "";
        $sqlval_wppost_comment['comment_login_fb'] = "";
        $sqlval_wppost_comment['comment_login_tw'] = "";
        $sqlval_wppost_comment['fb_appid'] = "";
        $sqlval_wppost_comment['fb_secret'] = "";
        $sqlval_wppost_comment['tw_consumer_key'] = "";
        $sqlval_wppost_comment['tw_consumer_secret'] = "";
        $sqlval_wppost_comment['comment_format'] = 1;
        $sqlval_wppost_comment['comment_num'] = 5;
        $sqlval_wppost_comment['comment_avatar_size'] = 32;
        $sqlval_wppost_comment['comment_restext'] = "このコメントに返信";
        // INSERTの実行
        $objQuery->insert("plg_wppost_comment", $sqlval_wppost_comment);

        $objQuery->commit();


        // テンプレートとPHP
        // ディレクトリの用意
        mkdir(TEMPLATE_REALDIR . "wppost");
        mkdir(HTML_REALDIR . "wppost");

        //記事用ファイル
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/wppost/plg_WpPost_post.php", HTML_REALDIR . "wppost/plg_WpPost_post.php") === false) print_r("失敗");
        //カテゴリー用ファイル
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/templates/plg_WpPost_category.tpl", TEMPLATE_REALDIR . "wppost/plg_WpPost_category.tpl") === false) print_r("失敗");
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/wppost/plg_WpPost_category.php", HTML_REALDIR . "wppost/plg_WpPost_category.php") === false) print_r("失敗");

        // バージョン別インストール
        // 2.12.4以下
        if (strcmp(ECCUBE_VERSION, "2.12.5") < 0){
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page_2123.php", PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page.php") === false) print_r("失敗");
            //ポスト用テンプレート
            // PC
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/templates/plg_WpPost_post_2126.tpl", TEMPLATE_REALDIR . "wppost/plg_WpPost_post.tpl") === false) print_r("失敗");
        // 2.12.5以上2.13.0未満
        } elseif (strcmp(ECCUBE_VERSION, "2.13.0") < 0) {
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page_2126.php", PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page.php") === false) print_r("失敗");
            //ポスト用テンプレート
            // PC
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/templates/plg_WpPost_post_2126.tpl", TEMPLATE_REALDIR . "wppost/plg_WpPost_post.tpl") === false) print_r("失敗");
        //2.13.0
        } elseif (strcmp(ECCUBE_VERSION, "2.13.0") == 0) {
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page_2130.php", PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page.php") === false) print_r("失敗");
            //ポスト用テンプレート
            // PC
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/templates/plg_WpPost_post_2131.tpl", TEMPLATE_REALDIR . "wppost/plg_WpPost_post.tpl") === false) print_r("失敗");
        // 2.13.1以上
        } elseif (strcmp(ECCUBE_VERSION, "2.13.0") > 0) {
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page_2131.php", PLUGIN_UPLOAD_REALDIR . "WpPost/plg_WpPost_LC_Page.php") === false) print_r("失敗");
            //ポスト用テンプレート
            // PC
            if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/templates/plg_WpPost_post_2131.tpl", TEMPLATE_REALDIR . "wppost/plg_WpPost_post.tpl") === false) print_r("失敗");
        }
        // PC
        // 記事一覧
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/templates/plg_WpPost_list.tpl", TEMPLATE_REALDIR . "frontparts/bloc/plg_WpPost_list.tpl") === false) print_r("失敗");
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/bloc/plg_WpPost_postlist.php", HTML_REALDIR . "frontparts/bloc/plg_WpPost_postlist.php") === false) print_r("失敗");

        //管理用
        //設定ファイル
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/config.php", PLUGIN_HTML_REALDIR . "WpPost/config.php") === false) print_r("失敗");
        // CSS、画像
        if(copy(PLUGIN_UPLOAD_REALDIR . "WpPost/logo.png", PLUGIN_HTML_REALDIR . "WpPost/logo.png") === false) print_r("失敗");
        mkdir(PLUGIN_HTML_REALDIR . "WpPost/media");
        if(SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . "WpPost/media/", PLUGIN_HTML_REALDIR . "WpPost/media/") === false) print_r("失敗");

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
        $objQuery = SC_Query_Ex::getSingletonInstance();

        //ブロックの削除
        //記事一覧
        $exist = $objQuery->exists('dtb_bloc', 'device_type_id = ? AND filename =?', array(DEVICE_TYPE_PC, "plg_WpPost_list"));
        if ($exist){
            $arrBlocIdList = $objQuery->getCol('bloc_id', "dtb_bloc", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "plg_WpPost_list"));
            $bloc_id_list = (int) $arrBlocIdList[0];
            $where = "bloc_id = ?";
            $objQuery->delete("dtb_bloc", $where, array($bloc_id_list));
            $objQuery->delete("dtb_blocposition", $where, array($bloc_id_list));
        }

        //記事一覧設定用のテーブル削除
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $dbname = $dbFactory->sfGetDBVersion();
        if (strpos($dbname, 'MySQL') !== false){
            //MySQL
            $sql = "SHOW TABLES LIKE 'plg_%';";
        } else {
            //PostgreSQL
            $sql = "select tablename from pg_tables where tablename like 'plg_%';";
        }
        $tablelists = $objQuery->getAll($sql);
        if ($tablelists){
            $plg_tables = array();
            foreach ($tablelists as $tablelist) {
                foreach ($tablelist as $val) {
                    $plg_tables[] = $val;
                }
            }
            if (in_array("plg_WpPost_postlist", $plg_tables)){
                $sql_drop = "DROP TABLE plg_WpPost_postlist;";
                $objQuery->query($sql_drop);
            } else if (in_array("plg_wppost_postlist", $plg_tables)){
                $sql_drop = "DROP TABLE plg_wppost_postlist;";
                $objQuery->query($sql_drop);
            }
        }

        //コメント設定用のテーブル削除
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $dbname = $dbFactory->sfGetDBVersion();
        if (strpos($dbname, 'MySQL') !== false){
            //MySQL
            $sql = "SHOW TABLES LIKE 'plg_%';";
        } else {
            //PostgreSQL
            $sql = "select tablename from pg_tables where tablename like 'plg_%';";
        }
        $tablelists = $objQuery->getAll($sql);
        if ($tablelists){
            $plg_tables = array();
            foreach ($tablelists as $tablelist) {
                foreach ($tablelist as $val) {
                    $plg_tables[] = $val;
                }
            }
            if (in_array("plg_WpPost_comment", $plg_tables)){
                $sql_drop = "DROP TABLE plg_WpPost_comment;";
                $objQuery->query($sql_drop);
            } else if (in_array("plg_wppost_comment", $plg_tables)){
                $sql_drop = "DROP TABLE plg_wppost_comment;";
                $objQuery->query($sql_drop);
            }
        }

        //dtb_pagelayoutポスト用ページの削除
        $exist = $objQuery->exists('dtb_pagelayout', 'device_type_id = ? AND filename =?', array(DEVICE_TYPE_PC, "wppost/plg_WpPost_post"));
        if ($exist){
            $arrPageIdPost = $objQuery->getCol('page_id', "dtb_pagelayout", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "wppost/plg_WpPost_post"));
            $page_id_post = (int) $arrPageIdPost[0];
            $where = "page_id = ?";
            $objQuery->delete("dtb_pagelayout", $where, array($page_id_post));
        }

        //dtb_pagelayoutカテゴリ用ページの削除
        $exist = $objQuery->exists('dtb_pagelayout', 'device_type_id = ? AND filename =?', array(DEVICE_TYPE_PC, "wppost/plg_WpPost_category"));
        if ($exist){
            $arrPageIdCat = $objQuery->getCol('page_id', "dtb_pagelayout", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "wppost/plg_WpPost_category"));
            $page_id_cat = (int) $arrPageIdCat[0];
            $where = "page_id = ?";
            $objQuery->delete("dtb_pagelayout", $where, array($page_id_cat));
        }

        // メディアディレクトリ削除.
        if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "WpPost/media") === false);

        // ブロックテンプレート削除
        //PC
        //記事一覧
        if(SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . "frontparts/bloc/plg_WpPost_list.tpl") === false);
        if(SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR  . "frontparts/bloc/plg_WpPost_postlist.php") === false);
        // 記事・カテゴリ表示ディレクトリごと削除
        if(SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . "wppost") === false);

        // PLUGIN_HTML_REALDIRディレクトリ削除.
        if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "WpPost") === false);
        //HTML_REALDIRディレクトリ削除
        if(SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . "wppost") === false);
        //PLUGIN_UPLOAD_REALDIRディレクトリ削除
        if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_UPLOAD_REALDIR . "WpPost") === false);

    }
    
    /**
     * アップデート
     * updateはアップデート時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($arrPlugin) {
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
     * @param SC_Helper_Plugin $objHelperPlugin 
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        // ヘッダへの追加
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'WpPost/templates/';
        $objHelperPlugin->setHeadNavi($template_dir . 'plg_WpPost_header.tpl');
        $objHelperPlugin->addAction('prefilterTransform', array(&$this, 'prefilterTransform'));

    }

    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        // SC_Helper_Transformのインスタンスを生成.
        $objTransform = new SC_Helper_Transform($source);
        // 呼び出し元テンプレートを判定します.
        $template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/templates/';

        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE:
                break;
            case DEVICE_TYPE_SMARTPHONE:
                break;
            case DEVICE_TYPE_PC:
                // SEO対応追加
                if (strpos($filename, 'site_frame.tpl') !== false) {
                    $objTransform->select('head')->appendChild(file_get_contents($template_dir . 'plg_WpPost_header_add.tpl'));
                }
                break;
            default:
                break;
        }

        $source = $objTransform->getHTML();
    }
}
?>
