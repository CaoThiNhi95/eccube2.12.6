<?php

require_once PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/AddProductColumns.php';
/**
 * プラグイン のアップデート用クラス.
 *
 * @package AddProductColumns
 * @author DAISY Inc.
 * @version $Id: $
 */
class plugin_update{
    
    static private $arrVersions = array(
        '1.0',
        '1.0.fix1',
        '1.0.fix2',
        '1.0.fix3',
        '1.1',
        '1.1.fix1',
        '1.1.fix2',
        '1.1.fix3',
        '2.0',
        '2.0.fix1',
        '2.0.fix2',
        '2.0.fix3',
        '2.0.fix4',
        '2.0.fix5',
    );
    
   /**
     * アップデート
     * updateはアップデート時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($arrPlugin) {
        
        self::copyFiles($arrPlugin);
        
        $arrOldPlugin = self::getOldPlugin($arrPlugin);
        $arrVersionKeys = array_flip(self::$arrVersions);
        $current_version_key = $arrVersionKeys[$arrPlugin['plugin_version']];

        if(is_numeric($current_version_key)){

            foreach(self::$arrVersions as $version_key => $version){

                if($current_version_key < $version_key){

                    switch($version){
                        
                        case '1.0.fix1':
                        case '1.0.fix2':
                        case '1.0.fix3':
                        case '1.1':
                        case '1.1.fix1':
                        case '1.1.fix2':
                        case '1.1.fix3':
                            break;

                        case '2.0':
                            self::deleteFiles_2_0($arrPlugin);
                            self::alterTables_2_0($arrPlugin);
                            break;
                        
                        case '2.0.fix1':
                            self::alterTables_2_0_fix1($arrPlugin);
                            break;
                        
                        case '2.0.fix2':
                            break;
                        
                        case '2.0.fix3':
                            break;
                        
                        case '2.0.fix4':
                            break;
                        
                        case '2.0.fix5':
                            break;
                    }
                }
            }
        }
        
        self::updatePluginRow($arrPlugin, '2.0.fix4');
        self::updateHookPoints($arrPlugin);
        
        if(isset($arrOldPlugin['enable']) && $arrOldPlugin['enable'] == PLUGIN_ENABLE_TRUE){
            
            SC_Utils_Ex::clearCompliedTemplate();
            $_SESSION['Message.AddProductColumns.Updated'] = 'プラグインをアップデートしました。';
            $objDisplay = new SC_Display_Ex();
            $objDisplay->reload();
        }
    }
    
    /**
     * アップデートに必要なファイルをコピーする
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    static function copyFiles($arrPlugin){
        
        //全上書き
        self::copy_recursive(DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR, PLUGIN_UPLOAD_REALDIR . 'AddProductColumns');
        //html
        self::copy_recursive(PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/html/', PLUGIN_HTML_REALDIR . 'AddProductColumns/');
        //アイコン
        self::copy_recursive(PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/logo.png', PLUGIN_HTML_REALDIR . 'AddProductColumns/logo.png');
    }
    
    /**
     * アップデートに伴い、不要なファイルを削除する。
     * 
     * @param array $arrPlugin 
     */
    static function deleteFiles_2_0($arrPlugin){
        
        //html
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . ADMIN_DIR . 'products/plg_AddProductColumns_add_product_columns.php');
    }
    
    /**
     * テーブル構造を変更する。
     * 
     * @param type $arrPlugin 
     */
    static function alterTables($arrPlugin){
        
        $arrOldPlugin = self::getOldPlugin($arrPlugin);
        $old_plugin_version = $arrOldPlugin['plugin_version'];
        
        //バージョン1系なら
        if(preg_match('/^1\./ui', $old_plugin_version)){
            
            self::alterTablesFor1($arrPlugin);
        }
        //バージョン2系なら
        if(preg_match('/^2\./ui', $old_plugin_version)){
            
            self::alterTablesFor2($arrPlugin);
        }
    }
    
    /**
     * バージョン2.0へのアップデート処理。
     * 
     * @param array $arrPlugin 
     */
    static function alterTables_2_0($arrPlugin){
        
        $arrDbSqls = array(
            'pgsql' => array(
                'ALTER TABLE dtb_add_product_columns RENAME TO plg_apc_dtb_columns',
                'ALTER TABLE dtb_add_product_data RENAME TO plg_apc_dtb_values',
                'ALTER TABLE plg_apc_dtb_columns RENAME COLUMN id TO column_id',
                'ALTER TABLE plg_apc_dtb_values RENAME COLUMN id TO value_id',
                'ALTER TABLE plg_apc_dtb_values RENAME COLUMN add_product_column_id TO column_id',
                'ALTER TABLE plg_apc_dtb_columns ADD PRIMARY KEY(column_id)',
                'ALTER TABLE plg_apc_dtb_values ADD PRIMARY KEY(value_id)',
                'CREATE SEQUENCE plg_apc_dtb_columns_column_id_seq',
                'CREATE SEQUENCE plg_apc_dtb_values_value_id_seq',
                "ALTER TABLE plg_apc_dtb_columns ALTER COLUMN column_id SET DEFAULT nextval('plg_apc_dtb_columns_column_id_seq')",
                "ALTER TABLE plg_apc_dtb_values ALTER COLUMN value_id SET DEFAULT nextval('plg_apc_dtb_values_value_id_seq')",
                "SELECT setval('plg_apc_dtb_columns_column_id_seq', (SELECT MAX(column_id) FROM plg_apc_dtb_columns))",
                "SELECT setval('plg_apc_dtb_values_value_id_seq', (SELECT MAX(value_id) FROM plg_apc_dtb_values))",
            ),
            'mysql' => array(
                'ALTER TABLE dtb_add_product_columns RENAME TO plg_apc_dtb_columns',
                'ALTER TABLE dtb_add_product_data RENAME TO plg_apc_dtb_values',
                'ALTER TABLE plg_apc_dtb_columns CHANGE COLUMN id column_id INTEGER NOT NULL',
                'ALTER TABLE plg_apc_dtb_values CHANGE COLUMN id value_id INTEGER NOT NULL',
                'ALTER TABLE plg_apc_dtb_values CHANGE COLUMN add_product_column_id column_id INTEGER NOT NULL',
                'ALTER TABLE plg_apc_dtb_columns ADD PRIMARY KEY(column_id)',
                'ALTER TABLE plg_apc_dtb_values ADD PRIMARY KEY(value_id)',
                'ALTER TABLE plg_apc_dtb_columns CHANGE column_id column_id INTEGER AUTO_INCREMENT',
                'ALTER TABLE plg_apc_dtb_values CHANGE value_id value_id INTEGER AUTO_INCREMENT',
            )
        );
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrSqls = $arrDbSqls[DB_TYPE];
        if(is_array($arrSqls)){
            
            foreach($arrSqls as $sql){
                
                $objQuery->exec($sql);
            }
        }
        
        if(DB_TYPE == 'mysql'){
            
            $max_column_id = (int)$objQuery->max('column_id', 'plg_apc_dtb_columns') + 1;
            $max_value_id  = (int)$objQuery->max('value_id', 'plg_apc_dtb_values') + 1;
            $objQuery->exec('ALTER TABLE plg_apc_dtb_columns AUTO_INCREMENT = ' . $max_column_id);
            $objQuery->exec('ALTER TABLE plg_apc_dtb_values  AUTO_INCREMENT = ' . $max_value_id);
        }
    }
    
    /**
     * バージョン2.0.fix1へのアップデート処理。
     * 
     * @param array $arrPlugin 
     */
    static function alterTables_2_0_fix1($arrPlugin){
        
        $arrDbSqls = array(
            'pgsql' => array(
            ),
            'mysql' => array(
                'ALTER TABLE plg_apc_dtb_columns CHANGE column_id column_id INTEGER AUTO_INCREMENT',
                'ALTER TABLE plg_apc_dtb_values CHANGE value_id value_id INTEGER AUTO_INCREMENT',
            )
        );
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrSqls = $arrDbSqls[DB_TYPE];
        if(is_array($arrSqls)){
            
            foreach($arrSqls as $sql){
                
                $objQuery->exec($sql);
            }
        }
        
        if(DB_TYPE == 'mysql'){
            
            $max_column_id = (int)$objQuery->max('column_id', 'plg_apc_dtb_columns') + 1;
            $max_value_id  = (int)$objQuery->max('value_id', 'plg_apc_dtb_values') + 1;
            $objQuery->exec('ALTER TABLE plg_apc_dtb_columns AUTO_INCREMENT = ' . $max_column_id);
            $objQuery->exec('ALTER TABLE plg_apc_dtb_values  AUTO_INCREMENT = ' . $max_value_id);
        }
    }
    
    /**
     * プラグインの情報をアップデートする
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @param integer $plugin_version プラグインのバージョン
     * @return void
     */
    static function updatePluginRow($arrPlugin, $plugin_version){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_plugin';
        $arrValues = array(
            'plugin_version' => $plugin_version, 
            'update_date' => 'CURRENT_TIMESTAMP'
        );
        $where = 'plugin_id = ?';
        $arrWhereValues = array($arrPlugin['plugin_id']);
        $objQuery->update($table, $arrValues, $where, $arrWhereValues);
    }
    
    /**
     * フックポイントを全削除・全登録する。
     * 
     * @param array $arrPlugin 
     */
    static function updateHookPoints($arrPlugin){
        
        require_once DOWNLOADS_TEMP_PLUGIN_UPDATE_DIR . '/plugin_info.php';
        $arrHookPoints = plugin_info::$HOOK_POINTS;
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_plugin_hookpoint';
        $temp_where = 'plugin_id = ?';
        $arrTempWhereValues = array($arrPlugin['plugin_id']);
        $objQuery->delete($table, $temp_where, $arrTempWhereValues);
        
        foreach($arrHookPoints as $arrHookPoint){
            
            $arrValues = array(
                'plugin_hookpoint_id' => $objQuery->nextVal('dtb_plugin_hookpoint_plugin_hookpoint_id'), 
                'plugin_id' => $arrPlugin['plugin_id'], 
                'hook_point' => $arrHookPoint[0], 
                'callback' => $arrHookPoint[1], 
                'create_date' => 'CURRENT_TIMESTAMP', 
                'update_date' => 'CURRENT_TIMESTAMP', 
            );
            $objQuery->insert($table, $arrValues);
        }
    }
    
    /**
     * ディレクトリとその内容を、再帰的にコピーする。
     * 
     * @param string $src コピー元パス
     * @param string $dst コピー先パス
     * @param boolean $delete_first trueの場合、$dstを削除してからコピーする。
     */
    static function copy_recursive($src, $dst, $delete_first = false) {

        if ($delete_first && file_exists($dst)) {
            
            rmdir_recursive($dst);
        }

        if (is_dir($src)) {
            
            if(!file_exists($dst) || !is_dir($dst)){
                
                mkdir($dst);
            }

            $files = scandir($src);
            foreach ($files as $file) {
                if (($file != ".") && ($file != "..")) {
                    self::copy_recursive("$src/$file", "$dst/$file");
                }
            }
        }
        else if (file_exists($src)) {
            
            copy($src, $dst);
        }
    }
    
    /**
     * 2.12.x判定
     * 
     * @return boolean 
     */
    static function is2_12($version = ECCUBE_VERSION){
        
        return preg_match('/^2\.12\./', $version);
    }
    
    /**
     * 以前のバージョンのプラグイン情報を取得する。
     * 
     * @param array $arrPlugin 
     */
    static function getOldPlugin($arrPlugin){
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_plugin';
        $cols = '*';
        $where = 'plugin_id = ?';
        $arrWhereValues = $arrPlugin['plugin_id'];
        $arrOldPlugin = $objQuery->getRow($cols, $table, $where, $arrWhereValues);
        return $arrOldPlugin;
    }
}