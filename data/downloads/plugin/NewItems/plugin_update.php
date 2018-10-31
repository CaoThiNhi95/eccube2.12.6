<?php
/**
 * プラグイン のアップデート用クラス.
 *
 * @package NewItems
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
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . 'logo.png', PLUGIN_HTML_REALDIR . 'NewItems/logo.png');
        self::updatePluginRow($arrPlugin);
    }

    /**
     * プラグインの情報をアップデートする
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     */
    static function updatePluginRow($arrPlugin){

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objPage = new LC_Page_Admin_OwnersStore_Ex();
        $objReflection = new ReflectionClass('plugin_info');
        $arrPluginInfo = $objPage->getPluginInfo($objReflection);

        $arrValues = array(
            'plugin_name' =>        $arrPluginInfo['PLUGIN_NAME'],
            'class_name' =>         $arrPluginInfo['CLASS_NAME'],
            'author' =>             $arrPluginInfo['AUTHOR'],
            'plugin_version' =>     $arrPluginInfo['PLUGIN_VERSION'],
            'compliant_version' =>  $arrPluginInfo['COMPLIANT_VERSION'],
            'plugin_description' => $arrPluginInfo['DESCRIPTION'],
            'update_date' =>        'CURRENT_TIMESTAMP',
        );

        // AUTHOR_SITE_URLが定義されているか判定.
        $author_site_url = $arrPluginInfo['AUTHOR_SITE_URL'];
        if ($author_site_url !== null) {
            $arrValues['author_site_url'] = $arrPluginInfo['AUTHOR_SITE_URL'];
        }
        // PLUGIN_SITE_URLが定義されているか判定.
        $plugin_site_url = $arrPluginInfo['PLUGIN_SITE_URL'];
        if ($plugin_site_url !== null) {
            $arrValues['plugin_site_url'] = $plugin_site_url;
        }


        $table = 'dtb_plugin';
        $where = 'plugin_id = ?';
        $objQuery->update($table, $arrValues, $where, array($arrPlugin['plugin_id']));
    }
}
?>