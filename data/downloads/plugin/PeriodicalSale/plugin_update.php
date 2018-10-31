<?php

require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/PeriodicalSale.php';
/**
 * プラグイン のアップデート用クラス.
 *
 * @package PeriodicalSale
 * @author DAISY Inc.
 * @version $Id: $
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
        switch($arrPlugin['plugin_version']){
            case '1.0':
                self::updateTables($arrPlugin);
            case '1.1':
                self::copyFiles($arrPlugin);
            case '1.1.fix1':
                self::updatePluginRow($arrPlugin, '1.1.fix2');
                copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'logo.png', PLUGIN_HTML_REALDIR . 'PeriodicalSale/logo.png');
                break;
        }
    }
    
    /**
     * アップデートに必要なファイルをコピーする
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    static function copyFiles($arrPlugin){
        
        //class
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'PeriodicalSale.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/PeriodicalSale.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'class/helper/plg_PeriodicalSale_SC_Helper_Datetime.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/class/helper/plg_PeriodicalSale_SC_Helper_Datetime.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'class/sql/plg_PeriodicalSale_PostgreSQL.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/class/sql/plg_PeriodicalSale_PostgreSQL.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'class/pages/admin/products/plg_PeriodicalSale_LC_Page_Admin_Products_Product.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/class/pages/admin/products/plg_PeriodicalSale_LC_Page_Admin_Products_Product.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'class/sql/plg_PeriodicalSale_MySQL.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/class/sql/plg_PeriodicalSale_MySQL.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'class/pages/admin/order/plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalOrder_Edit.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/class/pages/admin/order/plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalOrder_Edit.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'class/helper/plg_PeriodicalSale_SC_Helper_Purchase.php', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/class/helper/plg_PeriodicalSale_SC_Helper_Purchase.php');
        //tpl
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'templates/admin/products/plg_PeriodicalSale_confirm.tpl', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/admin/products/plg_PeriodicalSale_confirm.tpl');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'templates/admin/products/plg_PeriodicalSale_product.tpl', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/admin/products/plg_PeriodicalSale_product.tpl');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'templates/sphone/shopping/plg_PeriodicalSale_snippet_payment.tpl', PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/sphone/shopping/plg_PeriodicalSale_snippet_payment.tpl');
    }
    
    /**
     * テーブルを更新する
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     */
    static function updateTables($arrPlugin){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $sql = <<<EOSQL
            ALTER TABLE plg_ps_dtb_p_products ADD
                period_price_difference INTEGER NOT NULL DEFAULT 0
EOSQL;
        $objQuery->query($sql);
    }
    
    /**
     * プラグインの情報をアップデートする
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @param integer $plugin_version プラグインのバージョン
     * @return void
     */
    static function updatePluginRow($arrPlugin,$plugin_version){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_plugin';
        $arrSqlValues = array(
            'plugin_version' => $plugin_version,
            'update_date' => 'CURRENT_TIMESTAMP'
        );
        $where = 'plugin_id = ?';
        $objQuery->update($table,$arrSqlValues,$where,array($arrPlugin['plugin_id']));
    }
}
?>