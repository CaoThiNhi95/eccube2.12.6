<?php
/*
 * WPPost
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
 * プラグイン のアップデート用クラス.
 *
 * @package KuronekoB2
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
 
class plugin_update{
	/**
	 *アップデート
	 *updateはアップデート時に実行されます。
	 *引数にはdtb_pluginのプラグイン情報が渡されます。
	 *
	 *@param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
	 *@return void
	 */
	function update($arrPlugin){
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

		// info
		if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plugin_info.php", PLUGIN_UPLOAD_REALDIR."WpPost/plugin_info.php") === false) print_r("infoファイル失敗");

		// カテゴリ
		if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_Category_LC_Page.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_Category_LC_Page.php") === false) print_r("カテゴリPHP失敗");

		// バージョン別インストール
        if (strcmp(ECCUBE_VERSION, "2.12.5") < 0){
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_LC_Page_2123.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_LC_Page.php") === false) print_r("記事PHP失敗");
            // 記事用テンプレート
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."templates/plg_WpPost_post_2126.tpl", TEMPLATE_REALDIR."wppost/plg_WpPost_post.tpl") === false) print_r("記事テンプレートファイル失敗");
        // 2.12.5以上の2.12系
        } elseif (strcmp(ECCUBE_VERSION, "2.13.0") < 0) {
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_LC_Page_2126.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_LC_Page.php") === false) print_r("記事PHP失敗");
            // 記事用テンプレート
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."templates/plg_WpPost_post_2126.tpl", TEMPLATE_REALDIR."wppost/plg_WpPost_post.tpl") === false) print_r("記事テンプレートファイル失敗");
        //2.13.0
        } elseif (strcmp(ECCUBE_VERSION, "2.13.0") == 0) {
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_LC_Page_2130.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_LC_Page.php") === false) print_r("記事PHP失敗");
            // 記事用テンプレート
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."templates/plg_WpPost_post_2131.tpl", TEMPLATE_REALDIR."wppost/plg_WpPost_post.tpl") === false) print_r("記事テンプレートファイル失敗");
        // 2.13.1以上
        } else {
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_LC_Page_2131.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_LC_Page.php") === false) print_r("記事PHP失敗");
            // 記事用テンプレート
            if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."templates/plg_WpPost_post_2131.tpl", TEMPLATE_REALDIR."wppost/plg_WpPost_post.tpl") === false) print_r("記事テンプレートファイル失敗");
        }
		// ブロック
        if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_LC_Page_FrontParts_Bloc_postlist.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_LC_Page_FrontParts_Bloc_postlist.php") === false) print_r("失敗");
        
		// 管理
        if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plg_WpPost_LC_Page_Config.php", PLUGIN_UPLOAD_REALDIR."WpPost/plg_WpPost_LC_Page_Config.php") === false) print_r("管理画面PHP失敗");
        if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."templates/plg_WpPost_config.tpl", PLUGIN_UPLOAD_REALDIR."WpPost/templates/plg_WpPost_config.tpl") === false) print_r("失敗");
        if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."WpPost.php", PLUGIN_UPLOAD_REALDIR."WpPost/WpPost.php") === false) print_r("失敗");
        if(copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR."plugin_update.php", PLUGIN_UPLOAD_REALDIR."WpPost/plugin_update.php") === false) print_r("失敗");

        //テーブル名を小文字に変更
        $tablelist = $objQuery->listTables();
        if (in_array("plg_WpPost_comment", $tablelist)){
            $sql = "ALTER TABLE plg_WpPost_comment RENAME TO plg_wppost_comment;";
            $objQuery->query($sql);
            $sql = "ALTER TABLE plg_WpPost_postlist RENAME TO plg_wppost_postlist;";
            $objQuery->query($sql);
        }

    } //function update

}
?>