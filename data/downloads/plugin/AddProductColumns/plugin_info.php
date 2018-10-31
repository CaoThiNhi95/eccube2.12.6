<?php
/**
 * プラグイン の情報クラス.
 *
 * @package AddProductColumns
 * @author DAISY Inc.
 * @version $Id: $
 */
class plugin_info{
    
    static $PLUGIN_CODE       = 'AddProductColumns';
    static $PLUGIN_NAME       = '商品情報追加プラグイン';
    static $CLASS_NAME        = 'AddProductColumns';
    static $PLUGIN_VERSION    = '2.0.fix5';
    static $COMPLIANT_VERSION = '2.12.0-2.12.6|2.13.0-2.13.3';
    static $AUTHOR            = 'DAISY inc.';
    static $DESCRIPTION       = '商品項目を追加し、商品一覧・詳細ページの好きなところに表示できます。';
    static $PLUGIN_SITE_URL    = 'http://www.ec-cube.net/owners/index.php';
    static $AUTHOR_SITE_URL    = 'http://www.daisy.link/';
    static $HOOK_POINTS       = array(
        array('prefilterTransform', 'prefilterTransform'), 
        array('LC_Page_Admin_Products_Product_action_after', 'LC_Page_Admin_Products_Product_action_after'), 
        array('LC_Page_Products_Detail_action_after', 'LC_Page_Products_Detail_action_after'), 
        array('LC_Page_Products_List_action_after', 'LC_Page_Products_List_action_after')
    );
}
