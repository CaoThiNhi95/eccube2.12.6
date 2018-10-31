<?php

class plugin_info{
    static $PLUGIN_CODE       = "RefineKeyword";
    static $PLUGIN_NAME       = "商品一覧に絞り込み用キーワード表示";
    static $CLASS_NAME        = "RefineKeyword";
    static $PLUGIN_VERSION    = "0.1";
    static $COMPLIANT_VERSION = "2.12";
    static $AUTHOR            = "Nobuhiko Kimoto";
    static $DESCRIPTION       = "商品一覧ページに絞込み検索ができるキーワードを生成・表示します";
    static $PLUGIN_SITE_URL   = "http://nob-log.info/";
    static $AUTHOR_SITE_URL   = "http://nob-log.info/";
    static $HOOK_POINTS       =  array(
        array('prefilterTransform', 'prefilterTransform'),
        array('LC_Page_Products_List_action_after', 'LC_Page_Products_List_action_after')
    );
    static $LICENSE           = "LGPL";
}

