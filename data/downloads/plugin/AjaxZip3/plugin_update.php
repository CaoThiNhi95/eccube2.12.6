<?php
/**
 * プラグイン のアップデート用クラス.
 *
 * @package AjaxZip3
 * @author SystemFriend Inc
 * @version $Id: plugin_update.php 680 2015-07-21 06:14:06Z habu $
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
        // 旧バージョンのプラグインのインストール時に展開したファイルを削除
        // インストール時に作成したディレクトリを削除
        if (SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "AjaxZip3/media") === false) {
            // ディレクトリ削除に失敗してもスルーする
            // trigger_error("AjaxZip3/mediaディレクトリの削除に失敗しました", E_USER_ERROR);
        }
        if (SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "AjaxZip3") === false) {
            // ディレクトリ削除に失敗してもスルーする
            // trigger_error("AjaxZip3ディレクトリの削除に失敗しました", E_USER_ERROR);
        }

        // プラグイン保存ディレクトリを作成し、一時展開用ディレクトリから移動
        $plugin_dir_path = PLUGIN_UPLOAD_REALDIR . 'AjaxZip3/';
        if (!file_exists($plugin_dir_path)) {
            if (!mkdir($plugin_dir_path)) {
                trigger_error($plugin_dir_path . "ディレクトリの作成に失敗しました", E_USER_ERROR);
            }
        }
        SC_Utils_Ex::copyDirectory(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR, $plugin_dir_path);

        // プラグインhtmlディレクトリ作成
        $plugin_html_dir = PLUGIN_HTML_REALDIR . 'AjaxZip3';
        if (!file_exists($plugin_html_dir)) {
            if (!mkdir($plugin_html_dir)) {
                trigger_error($plugin_html_dir . "ディレクトリの作成に失敗しました", E_USER_ERROR);
            }
        }

        // 必要なファイルをコピー
        if (copy(PLUGIN_UPLOAD_REALDIR . "AjaxZip3/logo.png", PLUGIN_HTML_REALDIR . "AjaxZip3/logo.png") === false) {
            trigger_error("ロゴ画像のコピーに失敗しました", E_USER_ERROR);
        }
        if (!mkdir(PLUGIN_HTML_REALDIR . "AjaxZip3/media")) {
            trigger_error("AjaxZip3/mediaディレクトリの作成に失敗しました", E_USER_ERROR);
        }
        if (SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . "AjaxZip3/media/", PLUGIN_HTML_REALDIR . "AjaxZip3/media/") === false) {
            trigger_error("AjaxZip3/mediaディレクトリへのファイルコピーに失敗しました", E_USER_ERROR);
        }

        // FIXME: 本当はplugin_info.phpから情報を取得したいが、new ReflectionClass('plugin_info')すると、"Cannot redeclare class"エラーが発生するため、とりあえず決めウチでデータを更新している
        // バージョン情報等の変更
        $arrPluginInfo['PLUGIN_CODE'] = 'AjaxZip3';
        $arrPluginInfo['PLUGIN_VERSION'] = '1.2';
        $arrPluginInfo['COMPLIANT_VERSION'] = '2.12.0 - 2.12.6';
        // UPDATEの実行
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $objQuery->update('dtb_plugin', $arrPluginInfo, 'plugin_code = ?', array($arrPluginInfo['PLUGIN_CODE']));
        $objQuery->commit();
    }
}
?>