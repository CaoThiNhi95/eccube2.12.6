<?php
/**
 * プラグイン のアップデート用クラス.
 *
 * @package Coupon
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
        $newVer = 1.6;

        switch ($arrPlugin['plugin_version']) {
           // バージョン1.0からのアップデート
           case "1.0":
               plugin_update::update_1_1($arrPlugin);
               plugin_update::update_1_2($arrPlugin);
               plugin_update::update_1_3($arrPlugin);
               plugin_update::update_1_4($arrPlugin);
               plugin_update::update_1_5($arrPlugin);
               plugin_update::update_1_6($arrPlugin);
               $update_ver = $newVer;
               break;
           
           // バージョン1.1からのアップデート    
           case "1.1":
               plugin_update::update_1_2($arrPlugin);
               plugin_update::update_1_3($arrPlugin);
               plugin_update::update_1_4($arrPlugin);
               plugin_update::update_1_5($arrPlugin);
               plugin_update::update_1_6($arrPlugin);
               $update_ver = $newVer;
               break;
               
           // バージョン1.2からのアップデート    
           case "1.2":
               plugin_update::update_1_3($arrPlugin);
               plugin_update::update_1_4($arrPlugin);
               plugin_update::update_1_5($arrPlugin);
               plugin_update::update_1_6($arrPlugin);
               $update_ver = $newVer;
               break;

           // バージョン1.3からのアップデート    
           case "1.3":
               plugin_update::update_1_4($arrPlugin);
               plugin_update::update_1_5($arrPlugin);
               plugin_update::update_1_6($arrPlugin);
               $update_ver = $newVer;
               break;

           // バージョン1.4からのアップデート    
           case "1.4":
               plugin_update::update_1_5($arrPlugin);
               plugin_update::update_1_6($arrPlugin);
               $update_ver = $newVer;
               break;


           // バージョン1.4からのアップデート    
           case "1.5":
               plugin_update::update_1_6($arrPlugin);
               $update_ver = $newVer;
               break;

           default:
               $update_ver = 1.0;
               break;
        }
        
        // dtb_pluginを更新します.
        plugin_update::updateDtbPluginVersion($arrPlugin['plugin_id'], $update_ver);
    }

    /**
     * 1.1へのアップデートを実行します.
     * @param type $param
     */
    function update_1_1($arrPlugin) {
        $plugin_id = $arrPlugin['plugin_id'];

        // 変更のあったファイルを上書きします.
        $arrChangeFiles[] = 'Coupon.php';
        foreach ($arrChangeFiles AS $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/' . $file);
        }
    }

    /**
     * 1.2へのアップデートを実行します.
     * @param type $param
     */
    function update_1_2($arrPlugin) {
        $plugin_id = $arrPlugin['plugin_id'];

        // 変更のあったファイルを上書きします.
        $arrChangeFiles[] = 'data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Confirm.php';
        $arrChangeFiles[] = 'data/class/pages/admin/order/plg_Coupon_LC_Page_Admin_Order_Edit.php';
        foreach ($arrChangeFiles AS $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/' . $file);
        }
    }
    
    
    /**
     * 1.3へのアップデートを実行します.
     * @param type $param
     */
    function update_1_3($arrPlugin) {
        $plugin_id = $arrPlugin['plugin_id'];

        // 変更のあったファイルを上書きします.
        $arrChangeFiles[] = 'Coupon.php';
        $arrChangeFiles[] = 'data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Confirm.php';
        $arrChangeFiles[] = 'data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Complete.php';
        $arrChangeFiles[] = 'data/class/plg_Coupon_Postgres.php';//新規
        foreach ($arrChangeFiles AS $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/' . $file);
        }
        
    }

    /**
     * 1.4へのアップデートを実行します.
     * @param type $param
     */
    function update_1_4($arrPlugin) {
        $plugin_id = $arrPlugin['plugin_id'];

        // 変更のあったファイルを上書きします.
        $arrChangeFiles[] = 'Coupon.php';
        $arrChangeFiles[] = 'data/class/plg_Coupon_Postgres.php';
        $arrChangeFiles[] = 'data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Payment.php';
        foreach ($arrChangeFiles AS $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/' . $file);
        }
        
    }

    /**
     * 1.5へのアップデートを実行します.
     * @param type $param
     */
    function update_1_5($arrPlugin) {
        $plugin_id = $arrPlugin['plugin_id'];

        // 変更のあったファイルを上書きします.
        $arrChangeFiles[] = 'Coupon.php';
        foreach ($arrChangeFiles AS $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/' . $file);
        }

        //テンプレート部分の更新
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "template/admin/contents/plg_Coupon_coupon_input.tpl", SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon_input.tpl');
        
        $admin_dir = (ADMIN_DIR) ? ADMIN_DIR : "admin";
        //htmlファイルの更新
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "html/admin/contents/plg_Coupon_coupon.php", HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_coupon.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "html/admin/contents/plg_Coupon_coupon_input.php", HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_coupon_input.php');
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "html/admin/contents/plg_Coupon_product_select.php", HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_product_select.php');
        
    }


    /**
     * 1.6へのアップデートを実行します.
     * @param type $param
     */
    function update_1_6($arrPlugin) {
        $plugin_id = $arrPlugin['plugin_id'];

        // 変更のあったファイルを上書きします.
        $arrChangeFiles[] = 'config.php';
        $arrChangeFiles[] = 'data/class/pages/admin/contents/plg_Coupon_LC_Page_Admin_Contents_Coupon.php';
        $arrChangeFiles[] = 'data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Payment.php';
        foreach ($arrChangeFiles AS $file) {
            copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . $file, PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/' . $file);
        }
        
        //テンプレート部分の更新
        copy(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . "template/admin/contents/plg_Coupon_coupon.tpl", SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon.tpl');
    }

    /**
     * dtb_pluginを更新します.
     *
     * @param int $plugin_id プラグインID
     * @return void
     */
    function updateDtbPluginVersion ($plugin_id, $plugin_version) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $table = "dtb_plugin";
        $sqlval['plugin_version'] = $plugin_version;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_id = ?";
        $objQuery->update($table, $sqlval, $where, array($plugin_id));
    }
}
