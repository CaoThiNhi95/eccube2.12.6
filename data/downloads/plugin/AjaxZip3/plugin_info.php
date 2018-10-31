<?php
/**
 * プラグイン の情報クラス.
 *
 * @package AjaxZip3
 * @author SystemFriend Inc
 * @version $Id: plugin_info.php 680 2015-07-21 06:14:06Z habu $
 *
 * 更新履歴:
 * 2012/05/31 v1.0 新規作成
 * 2012/10/23 v1.1 お届け先追加･変更画面、お問い合わせ画面、非会員購入での不具合を修正
 * 2015/07/21 v1.2 Google Codeの閉鎖対応（デフォルトでGitHubのJSを見るように修正）
 */
class plugin_info{
    static $PLUGIN_CODE       = "AjaxZip3";
    static $PLUGIN_NAME       = "ajaxzip3連携";
    static $CLASS_NAME        = "AjaxZip3";
    static $PLUGIN_VERSION    = "1.2";
    static $COMPLIANT_VERSION = "2.12.0 - 2.12.6";
    static $AUTHOR            = "株式会社システムフレンド";
    static $DESCRIPTION       = "メンテナンスフリーの郵便番号検索API「ajaxzip3」を利用するようにします。郵便番号辞書のアップデートを行なわなくても良くなります。";
    static $PLUGIN_SITE_URL   = "http://ec-cube.systemfriend.co.jp/";
    static $AUTHOR_SITE_URL   = "http://www.systemfriend.co.jp/";
    static $HOOK_POINTS       = "prefilterTransform";
    static $LICENSE           = "MIT";
}
