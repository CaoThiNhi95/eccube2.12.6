<?php

class plugin_update
{
    /**
      * アップデート
      * updateはアップデート時に実行されます.
      * 引数にはdtb_pluginのプラグイン情報が渡されます.
      *
      * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
      * @return void
      */
    function update($arrPlugin)
    {
        $plugin_id = $arrPlugin['plugin_id'];
        $plugin_version = '0.1.3';
        $compliant_version = '2.12.1, 2.12.2, 2.12.3';

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $objQuery->begin();

        $table = "dtb_plugin";
        $where = "plugin_id = ?";

        $arrVal = array(
            'plugin_version'    => $plugin_version,
            'compliant_version'    => $compliant_version,
            'update_date'       => 'CURRENT_TIMESTAMP',
        );

        $objQuery->update($table, $arrVal, $where, array($plugin_id));


        $arrTargetFiles = array(
            'ImageImagick.php',
            'SC_UploadFileImagick.php',
            'plugin_info.php',
        );

        foreach ($arrTargetFiles as $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "/$file",
            PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/$file");
        }

        $objQuery->commit();
    }
}
