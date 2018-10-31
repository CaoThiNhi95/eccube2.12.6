<?php

class plugin_info
{
    static $PLUGIN_CODE         = 'ImageImagick';
    static $PLUGIN_NAME         = '画質向上プラグイン';
    static $PLUGIN_VERSION      = '0.1.3';
    static $COMPLIANT_VERSION   = '2.12.1, 2.12.2, 2.12.3';
    static $AUTHOR              = '株式会社サイバーウィル';
    static $DESCRIPTION = '商品画像等の画質を向上するプラグイン';
    static $PLUGIN_SITE_URL = 'http://www.cyber-will.co.jp/';
    static $AUTHOR_SITE_URL = 'http://www.cyber-will.co.jp/';
    static $CLASS_NAME = 'ImageImagick';
    static $HOOK_POINTS = array(
        array('loadClassFileChange', 'loadClassFileChange'),
    );
    static $LICENSE = 'LGPL';
}

