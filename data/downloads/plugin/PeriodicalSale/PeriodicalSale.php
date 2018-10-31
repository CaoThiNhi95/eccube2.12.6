<?php
/*
 * PeriodicalSale
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/pages/admin/products/plg_PeriodicalSale_LC_Page_Admin_Products_Product.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/pages/shopping/plg_PeriodicalSale_LC_Page_Shopping_Confirm.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/pages/shopping/plg_PeriodicalSale_LC_Page_Shopping_Payment.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/pages/shopping/plg_PeriodicalSale_LC_Page_Shopping_Deliv.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/helper/plg_PeriodicalSale_SC_Helper_Purchase.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/helper/plg_PeriodicalSale_SC_Helper_Mail.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/sql/plg_PeriodicalSale_MySQL.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/sql/plg_PeriodicalSale_PostgreSQL.php';

/**
 * プラグインのメインクラス
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class PeriodicalSale extends SC_Plugin_Base {

    public static $plugin_name = 'PeriodicalSale';
    
    const PERIOD_TYPE_WEEKLY = 'weekly';
    const PERIOD_TYPE_BIWEEKLY = 'biweekly';
    const PERIOD_TYPE_MONTHLY_DATE = 'monthly_date';
    const PERIOD_TYPE_MONTHLY_DAY = 'monthly_day';

    /**
     * dtb_plugin.free_field1～4に対応するパラメータ名。
     * 2次元配列まで対応する。
     * array(
     *  'free_field1' => 'field_name',
     *  'free_field2' => 'my_field',
     *  'free_field4' => array(
     *      'name', 'of', 'field'
     *  )
     * )
     */
    static public $arrConfigFields = array(
        'free_field4' => array(
            'available_period_dates',
            'available_period_weeks',
            'available_period_days',
            'available_period_types',
            'default_mail_template_id',
            'mobile_mail_template_id',
            'period_weekly_offset',
            'period_biweekly_offset',
            'period_monthly_day_offset',
            'period_monthly_date_offset',
            'available_period_payments'
        )
    );

    /**
     * デフォルト設定値。
     * self::$arrConfigFieldsに対応するキーを使用する。
     * 
     * - period_weekly_offset: 次回定期までのオフセット秒数 (毎週)
     * - period_biweekly_offset: 次回定期までのオフセット秒数 (隔週)
     * - period_monthly_day_offset: 次回定期までのオフセット秒数 (毎月 (曜日))
     * - period_monthly_date_offset: 次回定期までのオフセット秒数 (毎月 (日付))
     */
    public static $arrDefault = array(
        'period_weekly_offset' => 432000,
        'period_biweekly_offset' => 604800,
        'period_monthly_day_offset' => 1296000,
        'period_monthly_date_offset' => 1296000
    );

    /**
     * インストール時に実行される処理を記述します.
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    function install($arrPlugin) {
        
        self::copyFiles($arrPlugin);
        self::createTable();
        self::initPluginRow($arrPlugin);
    }

    /**
     * 削除時に実行される処理を記述します.
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    function uninstall($arrPlugin) {
        
        self::deleteFiles($arrPlugin);
        self::dropTable();
    }

    /**
     * 有効にした際に実行される処理を記述します.
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    function enable($arrPlugin) {
        
        self::insertPage($arrPlugin);
        self::insertMailTemplate($arrPlugin);
    }

    /**
     * 無効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    function disable($arrPlugin) {
        
        self::deletePage($arrPlugin);
        self::deleteMailTemplate($arrPlugin);
    }

    /**
     * プラグイン用ファイルをコピー 
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    static function copyFiles($arrPlugin) {
        //アイコン
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/logo.png', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/logo.png');
        //html
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/admin/order/plg_PeriodicalSale_periodical_order.php', HTML_REALDIR . ADMIN_DIR . 'order/plg_PeriodicalSale_periodical_order.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/admin/order/plg_PeriodicalSale_periodical_order_edit.php', HTML_REALDIR . ADMIN_DIR . 'order/plg_PeriodicalSale_periodical_order_edit.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/admin/order/plg_PeriodicalSale_periodical_product_select.php', HTML_REALDIR . ADMIN_DIR . 'order/plg_PeriodicalSale_periodical_product_select.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/mypage/plg_PeriodicalSale_periodical_order.php', HTML_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/mypage/plg_PeriodicalSale_periodical_order_history.php', HTML_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/shopping/plg_PeriodicalSale_unmultiorderable.php', HTML_REALDIR . 'shopping/plg_PeriodicalSale_unmultiorderable.php');
        //tpl
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/default/mypage/plg_PeriodicalSale_periodical_order.tpl', TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/sphone/mypage/plg_PeriodicalSale_periodical_order.tpl', SMARTPHONE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/mobile/mypage/plg_PeriodicalSale_periodical_order.tpl', MOBILE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/default/mypage/plg_PeriodicalSale_periodical_order_history.tpl', TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/sphone/mypage/plg_PeriodicalSale_periodical_order_history.tpl', SMARTPHONE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/mobile/mypage/plg_PeriodicalSale_periodical_order_history.tpl', MOBILE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/templates/mobile/shopping/plg_PeriodicalSale_unmultiorderable.tpl', MOBILE_TEMPLATE_REALDIR . 'shopping/plg_PeriodicalSale_unmultiorderable.tpl');
    }

    /**
     * 本体を含むすべてのプラグイン用ファイルを削除
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    static function deleteFiles($arrPlugin) {
        //html
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . ADMIN_DIR . 'order/plg_PeriodicalSale_periodical_order.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . ADMIN_DIR . 'order/plg_PeriodicalSale_periodical_order_edit.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . ADMIN_DIR . 'order/plg_PeriodicalSale_periodical_product_select.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . 'shopping/plg_PeriodicalSale_unmultiorderable.php');
        //tpl
        SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.tpl');
        SC_Helper_FileManager_Ex::deleteFile(SMARTPHONE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.tpl');
        SC_Helper_FileManager_Ex::deleteFile(MOBILE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order.tpl');
        SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.tpl');
        SC_Helper_FileManager_Ex::deleteFile(SMARTPHONE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.tpl');
        SC_Helper_FileManager_Ex::deleteFile(MOBILE_TEMPLATE_REALDIR . 'mypage/plg_PeriodicalSale_periodical_order_history.tpl');
        SC_Helper_FileManager_Ex::deleteFile(MOBILE_TEMPLATE_REALDIR . 'shopping/plg_PeriodicalSale_unmultiorderable.tpl');
        //アイコン
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/logo.png');
        //プラグイン本体
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code']);
    }
    
    /**
     * RDBMS別のスキーマを取得する
     * 
     * @return array スキーマ配列 
     */
    static function getSchema(){
        
        $arrSchema = array();
        switch(DB_TYPE){
            case 'mysql':
                $arrSchema = plg_PeriodicalSale_MySQL::$arrSchema;
                break;
            case 'pgsql':
                $arrSchema = plg_PeriodicalSale_PostgreSQL::$arrSchema;
                break;
        }
        return $arrSchema;
    }

    /**
     * プラグイン用テーブルの作成
     */
    static function createTable() {
        
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        
        //テーブル作成
        foreach(self::getSchema() as $table => $arrFields){
           $fields = implode(',', $arrFields);
           $sql = sprintf('CREATE TABLE %s (%s)', $table, $fields);
           $objQuery->query($sql);
        }
        
        //日付登録
        $table = 'plg_ps_mtb_period_dates';
        //27日までとし、28日以降は末日として扱う
        foreach(range(1, 27) as $date){
            $arrKeys = array('id', 'name', 'rank');
            $arrValues = array_fill_keys($arrKeys, $date);
            $objQuery->insert($table, $arrValues);
        }
        //日付計算時に使うために、末日を99日とする
        $date = 99;
        $arrValues = array(
            'id' => $date,
            'name' => '末',
            'rank' => $date
        );
        $objQuery->insert($table, $arrValues);

        //週番号登録
        $table = 'plg_ps_mtb_period_weeks';
        //第4週までとし、第5週は選択不可
        foreach(range(1, 4) as $week){
            $arrKeys = array('id', 'name', 'rank');
            $arrValues = array_fill_keys($arrKeys, $week);
            $objQuery->insert($table, $arrValues);
        }

        //定期受注状態を登録
        $table = 'plg_ps_mtb_p_order_statuses';
        $arrStatuses = array(
            '継続',
            '休止',
            '解約'
        );
        foreach ($arrStatuses as $key => $value) {
            $arrValues = array(
                'id' => $key,
                'name' => $value,
                'rank' => $key
            );
            $objQuery->insert($table, $arrValues);
        }
    }

    /**
     * プラグイン用テーブルの削除
     */
    static function dropTable() {
        
        $objQuery = &SC_QUERY_Ex::getSingletonInstance();
        $arrTables = $objQuery->listTables();
        
        //テーブル削除
        foreach(self::getSchema() as $table => $arrFields){
            
            if(in_array($table, $arrTables)){
                
                $sql = sprintf('DROP TABLE %s', $table);
                $objQuery->query($sql);
            }
        }
    }
    
    /**
     * pagelayout関係のテーブルにプラグインの情報を登録する。
     *  
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    static function insertPage($arrPlugin){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrFiles = array(
            'mypage/plg_PeriodicalSale_periodical_order' => 'MYページ/定期購入履歴一覧',
            'mypage/plg_PeriodicalSale_periodical_order_history' => 'MYページ/定期購入詳細',
        );
        $file_name = 'mypage/plg_PeriodicalSale_periodical_order';
        
        $arrDeviceTypes = array(
            DEVICE_TYPE_PC, 
            DEVICE_TYPE_SMARTPHONE, 
            DEVICE_TYPE_MOBILE
        );
        $where = 'device_type_id = ?';
        
        foreach($arrDeviceTypes as $device_type){
            
            foreach($arrFiles as $file_name => $page_name){
            
                $table = 'dtb_pagelayout';
                $arrWhereValues = array($device_type);
                $page_id = $objQuery->max('page_id', $table, $where, $arrWhereValues) +1;
                $arrValues = array(
                    'device_type_id' => $device_type,
                    'page_id' => $page_id,
                    //ページ名
                    'page_name' => $page_name,
                    'url' => sprintf('%s.php', $file_name),
                    'filename' => $file_name,
                    'header_chk' => 1,
                    'footer_chk' => 1,
                    'edit_flg' => 2,
                    'create_date' => 'CURRENT_TIMESTAMP',
                    'update_date' => 'CURRENT_TIMESTAMP',
                );
                $table = 'dtb_pagelayout';
                $objQuery->insert($table, $arrValues);
            }
        }
        
        $table = 'dtb_pagelayout';
        $file_name = 'shopping/plg_PeriodicalSale_unmultiorderable';
        $page_name = '商品購入/複数配送先不可';
        $device_type = DEVICE_TYPE_MOBILE;
        $where = 'device_type_id = ?';
        $arrWhereValues = array($device_type);
        $page_id = $objQuery->max('page_id', $table, $where, $arrWhereValues) + 1;
        $arrValues = array(
            'device_type_id' => $device_type,
            'page_id' => $page_id,
            'page_name' => $page_name,
            'url' => sprintf('%s.php', $file_name),
            'filename' => $file_name,
            'header_chk' => 1,
            'footer_chk' => 1,
            'edit_flg' => 2,
            'create_date' => 'CURRENT_TIMESTAMP',
            'update_date' => 'CURRENT_TIMESTAMP'
        );
        $objQuery->insert($table,$arrValues);
    }

    /**
     * pagelayout関係のテーブルからプラグインの情報を削除する。
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    static function deletePage($arrPlugin){
        
        $objQuery =&SC_Query_Ex::getSingletonInstance();
        $file_name_like = 'plg_PeriodicalSale_';
        
        $arrDeviceTypes = array(
            DEVICE_TYPE_PC,
            DEVICE_TYPE_SMARTPHONE,
            DEVICE_TYPE_MOBILE
        );
        
        foreach($arrDeviceTypes as $device_type){
            
            //filenameの前方一致とデバイスタイプでpage_id取得
            $table = 'dtb_pagelayout';
            $arrPageIds = $objQuery->getCol('page_id', $table, 'filename ILIKE ? AND device_type_id = ?',array(sprintf('%%%s%%', $file_name_like), $device_type));
            
            foreach ($arrPageIds as $page_id){
                
                $where = 'page_id = ? AND device_type_id = ?';
                $arrWhereValues = array($page_id, $device_type);
                $table = 'dtb_pagelayout';
                $objQuery->delete($table, $where, $arrWhereValues);
                $table = 'dtb_blocposition';
                $objQuery->delete($table, $where, $arrWhereValues);
            }
        }
    }
    
    /**
     * メールテンプレートを登録し、プラグイン情報にtemplate_idを登録する。
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    static function insertMailTemplate($arrPlugin){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objMasterData = new SC_DB_MasterData_Ex();
        
        $arrDefaultMailValues = array(
            'creator_id' => $_SESSION['member_id'],
            'update_date' => 'CURRENT_TIMESTAMP'
        );
        $arrMailValues = array(
            array(
                'device_type' => DEVICE_TYPE_PC,
                'name' => '定期販売受付メール',
                'subject' => '定期販売のお申込ありがとうございます',
                'header' => file_get_contents(PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/db_mail_templates/default/header/periodical_order_mail.txt'),
                'footer' => file_get_contents(PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/db_mail_templates/default/footer/periodical_order_mail.txt'),
            ),
            array(
                'device_type' => DEVICE_TYPE_MOBILE,
                'name' => '定期販売受付メール(携帯)',
                'subject' => '定期販売のお申込ありがとうございます',
                'header' => file_get_contents(PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/db_mail_templates/mobile/header/periodical_order_mail.txt'),
                'footer' => file_get_contents(PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/db_mail_templates/mobile/footer/periodical_order_mail.txt'),
            )
        );
        
        $arrNamedPluginInfo = array();
        
        $arrMailTemplateIds = array();
        
        foreach($arrMailValues as $arrMailValue){
            
            //テンプレートのマスタデータ
            $table = 'mtb_mail_template';
            $template_id = $objQuery->max('id', $table) + 1;
            
            $arrMailTemplateIds[] = $template_id;
            $arrValues = array(
                'name' => $arrMailValue['name'],
                'id' => $template_id,
                'rank' => $objQuery->max('rank', $table) + 1
            );
            $objQuery->insert($table, $arrValues);
            
            $table = 'dtb_mailtemplate';
            $arrValues = $objQuery->extractOnlyColsOf($table, array_merge($arrDefaultMailValues, $arrMailValue));
            $arrValues['template_id'] = $template_id;
            $objQuery->insert($table, $arrValues);
            
            //テンプレートパスのマスタデータ
            $table = 'mtb_mail_tpl_path';
            $arrValues = array(
                'id' => $template_id,
                'rank' => $objQuery->max('rank', $table) + 1
            );
            switch($arrMailValue['device_type']){
                
                case DEVICE_TYPE_PC:
                    $arrNamedPluginInfo['default_mail_template_id'] = $template_id;
                    $arrValues['name'] = PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/default/mail_templates/plg_PeriodicalSale_periodical_order_mail.tpl';
                    break;
                
                case DEVICE_TYPE_MOBILE:
                    $arrNamedPluginInfo['mobile_mail_template_id'] = $template_id;
                    $arrValues['name'] = PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/mobile/mail_templates/plg_PeriodicalSale_periodical_order_mail.tpl';
                    break;
            }
            $objQuery->insert($table, $arrValues);
        }
        
        $table = 'mtb_mail_template';
        $objMasterData->clearCache($table);
        $table = 'mtb_mail_tpl_path';
        $objMasterData->clearCache($table);
        
        self::saveNamedPluginInfo($arrNamedPluginInfo);
    }
    
    
    /**
     * メールテンプレートを削除する。
     * 
     * @param array $arrPlugin dtb_pluginの情報配列
     */
    static function deleteMailTemplate($arrPlugin){
        
        $arrNamedPluginInfo = self::getNamedPluginInfo();

        $arrMailTemplateIds = array(
            $arrNamedPluginInfo['default_mail_template_id'],
            $arrNamedPluginInfo['mobile_mail_template_id']
        );
        
        if(!empty($arrMailTemplateIds)){
            
            $objQuery =& SC_Query_Ex::getSingletonInstance();
        
            $table = 'mtb_mail_template';
            $where = sprintf('id IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrMailTemplateIds)));
            $arrWhereValues = $arrMailTemplateIds;
            $objQuery->delete($table, $where, $arrWhereValues);
            
            $table = 'dtb_mailtemplate';
            $where = sprintf('template_id IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrMailTemplateIds)));
            $arrWhereValues = $arrMailTemplateIds;
            $objQuery->delete($table, $where, $arrWhereValues);
            
            $table = 'mtb_mail_tpl_path';
            $where = sprintf('id IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrMailTemplateIds)));
            $arrWhereValues = $arrMailTemplateIds;
            $objQuery->delete($table, $where, $arrWhereValues);
        }
        
        $objMasterData = new SC_DB_MasterData_Ex();
        $table = 'mtb_mail_template';
        $objMasterData->clearCache($table);
    }

    /**
     * データベース上のプラグイン情報を初期化する。
     * 
     * @param array $arrPlugin  プラグイン情報
     */
    static function initPluginRow($arrPlugin) {
        
        $arrDefault = self::$arrDefault;
        $objMasterData = new SC_DB_MasterData_Ex();
        $arrDays = $objMasterData->getMasterData('mtb_wday');
        $arrPeriodWeeks = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
        $arrPeriodDates = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
        $arrPayments = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $arrPeriodTypes = array(
            self::PERIOD_TYPE_WEEKLY, 
            self::PERIOD_TYPE_BIWEEKLY,
            self::PERIOD_TYPE_MONTHLY_DAY,
            self::PERIOD_TYPE_MONTHLY_DATE
        );

        $arrDefault['available_period_weeks'] = array_fill_keys(array_keys($arrPeriodWeeks), 1);
        $arrDefault['available_period_days'] = array_fill_keys(array_keys($arrDays), 1);
        $arrDefault['available_period_dates'] = array_fill_keys(array_keys($arrPeriodDates), 1);
        $arrDefault['available_period_types'] = array_fill_keys($arrPeriodTypes, 1);
        $arrDefault['available_period_payments'] = array_fill_keys(array_keys($arrPayments), 1);
        
        self::saveNamedPluginInfo($arrDefault);
    }

    /**
     * self::$arrConfigFieldsに応じて整形したプラグイン情報の連想配列を取得する。
     * 
     * @return array プラグイン情報
     */
    static function getNamedPluginInfo() {

        $arrPlugin = SC_Plugin_Util_Ex::getPluginByPluginCode('PeriodicalSale');
        $arrValues = array();
        foreach (self::$arrConfigFields as $field => $arrNames) {

            if (is_array($arrNames)) {

                $arrData = unserialize($arrPlugin[$field]);
                foreach ($arrNames as $name) {
                    $arrValues[$name] = isset($arrData[$name]) ? $arrData[$name] : '';
                }
            } 
            else {
                
                $name = $arrNames;
                $arrValues[$name] = $arrPlugin[$field];
            }
        }
        return $arrValues;
    }

    /**
     * 渡された連想配列を、self::$arrConfigFieldsに応じて保存する。
     * 
     * @param array $arrData 保存するデータ SC_FormParam_Ex::getHashArray()形式
     */
    static function saveNamedPluginInfo($arrData) {

        $arrValues = array();
        $arrMergedData = array_merge(self::getNamedPluginInfo(), $arrData);
        foreach (self::$arrConfigFields as $field => $arrNames) {

            if (is_array($arrNames)) {
                
                $arrFlipedNames = array_flip($arrNames);
                $arrValues[$field] = serialize(array_intersect_key($arrMergedData, $arrFlipedNames));
            } 
            else {
                
                $name = $arrNames;
                if (isset($arrMergedData[$name])) {
                    $arrValues[$field] = $arrMergedData[$name];
                }
            }
        }

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $where = 'plugin_code = ?';
        $objQuery->update('dtb_plugin', $arrValues, $where, array('PeriodicalSale'));
    }
    
    /**
     * 有効な定期間隔情報を取得する。
     * 
     * @return array 有効な定期間隔情報 
     * - dates: 日付
     * - weeks: 週番号
     * - days: 曜日
     */
    static function getAvailablePeriodInfo(){
        
        $arrNamedPluginInfo = self::getNamedPluginInfo();
        $objMasterData = new SC_DB_MasterData_Ex();
        $arrPeriodDates = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
        $arrPeriodWeeks = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
        $arrPeriodDays = $objMasterData->getMasterData('mtb_wday');
        $arrPeriodTypes = self::getPeriodTypes();
        $arrPayments = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        
        //有効な日付・週番号・曜日をフィルター -> マスターデータと共通するものに絞り込む
        $available_period_dates = array_intersect_key($arrPeriodDates, array_filter($arrNamedPluginInfo['available_period_dates']));
        $available_period_weeks = array_intersect_key($arrPeriodWeeks, array_filter($arrNamedPluginInfo['available_period_weeks']));
        $available_period_days = array_intersect_key($arrPeriodDays, array_filter($arrNamedPluginInfo['available_period_days']));
        $available_period_types = array_intersect_key($arrPeriodTypes, array_filter($arrNamedPluginInfo['available_period_types']));
        $available_period_payments = array_intersect_key($arrPayments, array_filter($arrNamedPluginInfo['available_period_payments']));
        $arrValues = compact('available_period_dates', 'available_period_weeks', 'available_period_days', 'available_period_types', 'available_period_payments');
        return $arrValues;
    }
    
    /**
     * 周期のキーと名前の配列を取得する。
     * XXX 面倒なので適当に書いた。本来は…マスタ管理？
     * 
     * @return string 
     */
    static function getPeriodTypes(){
        
        $arrValues = array(
            self::PERIOD_TYPE_WEEKLY => '毎週',
            self::PERIOD_TYPE_BIWEEKLY => '隔週',
            self::PERIOD_TYPE_MONTHLY_DAY => '毎月 (曜日指定)',
            self::PERIOD_TYPE_MONTHLY_DATE => '毎月 (日付指定)'
        );
        return $arrValues;
    }

    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    static function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {

        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/templates/';
        
        switch ($objPage->arrPageLayout['device_type_id']) {
            
            case DEVICE_TYPE_MOBILE:
                if (strpos($filename, 'shopping/payment.tpl') !== false) {
                    $objTransform->select('textarea', 0)->insertAfter(file_get_contents($template_dir . 'mobile/shopping/plg_PeriodicalSale_snippet_payment.tpl'));
                } 
                elseif (strpos($filename, 'mypage/index.tpl') !== false){
                    $objTransform->select('a', 0)->insertBefore(file_get_contents($template_dir . 'mobile/mypage/plg_PeriodicalSale_snippet_index.tpl'));
                }
                break;
                
            case DEVICE_TYPE_SMARTPHONE:
                if (strpos($filename, 'shopping/nonmember_input.tpl') !== false) {
                    $objTransform->select('#form1')->insertBefore(file_get_contents($template_dir . 'sphone/shopping/plg_PeriodicalSale_snippet_nonmember_input.tpl'));
                } 
                elseif (strpos($filename, 'shopping/deliv.tpl') !== false) {
                    $objTransform->select('#form1')->insertBefore(file_get_contents($template_dir . 'sphone/shopping/plg_PeriodicalSale_snippet_deliv.tpl'));
                } 
                elseif (strpos($filename, 'shopping/payment.tpl') !== false) {
                    $objTransform->select('#form1 .contact_area')->insertBefore(file_get_contents($template_dir . 'sphone/shopping/plg_PeriodicalSale_snippet_payment.tpl'));
                    //XXX ※テンプレートが壊れるので応急処置
                    $objTransform->select('#form1 .btn_area')->insertAfter('</form></section>');
                } 
                elseif (strpos($filename, 'shopping/confirm.tpl') !== false){
                    $objTransform->select('.otherconfirm_area')->insertAfter(file_get_contents($template_dir . 'sphone/shopping/plg_PeriodicalSale_snippet_confirm.tpl'));
                }
                elseif (strpos($filename, 'mypage/navi.tpl') !== false) {
                    $objTransform->select('#mypage_nav ul')->appendChild(file_get_contents($template_dir . 'sphone/mypage/plg_PeriodicalSale_snippet_navi.tpl'));
                }
                break;
                
            case DEVICE_TYPE_PC:
                if (strpos($filename, 'shopping/nonmember_input.tpl') !== false) {
                    $objTransform->select('#several')->insertBefore(file_get_contents($template_dir . 'default/shopping/plg_PeriodicalSale_snippet_nonmember_input.tpl'));
                } 
                elseif (strpos($filename, 'shopping/deliv.tpl') !== false) {
                    $objTransform->select('#address_area')->insertBefore(file_get_contents($template_dir . 'default/shopping/plg_PeriodicalSale_snippet_deliv.tpl'));
                } 
                elseif (strpos($filename, 'shopping/payment.tpl') !== false) {
                    $objTransform->select('#form1 .pay_area02')->insertAfter(file_get_contents($template_dir . 'default/shopping/plg_PeriodicalSale_snippet_payment.tpl'));
                } 
                elseif (strpos($filename, 'shopping/confirm.tpl') !== false) {
                    $objTransform->select('#form1 .btn_area', 1)->insertBefore(file_get_contents($template_dir . 'default/shopping/plg_PeriodicalSale_snippet_confirm.tpl'));
                }
                elseif (strpos($filename, 'mypage/navi.tpl') !== false) {
                    $objTransform->select('#mynavi_area .mynavi_list')->appendFirst(file_get_contents($template_dir . 'default/mypage/plg_PeriodicalSale_snippet_navi.tpl'));
                }
                break;
                
            default:
            case DEVICE_TYPE_ADMIN:
                if (strpos($filename, 'products/product.tpl') !== false) {
                    $objTransform->select('#products .form', 0)->appendChild(file_get_contents($template_dir . 'admin/products/plg_PeriodicalSale_product.tpl'));
                } 
                elseif (strpos($filename, 'products/confirm.tpl') !== false) {
                    $objTransform->select('#products table', 0)->appendChild(file_get_contents($template_dir . 'admin/products/plg_PeriodicalSale_confirm.tpl'));
                } 
                elseif (strpos($filename, 'order/subnavi.tpl') !== false) {
                    $objTransform->select('ul.level1')->appendChild(file_get_contents($template_dir . 'admin/plg_PeriodicalSale_snippet_admin_order_subnavi.tpl'));
                }
                elseif (strpos($filename, 'main_frame.tpl') !== false){
                    $objTransform->select('head')->appendChild(file_get_contents($template_dir . 'admin/plg_PeriodicalSale_snippet_admin_scripts_for_layout.tpl'));
                }
                break;
        }
        $source = $objTransform->getHTML();
    }
    
    function LC_Page_Shopping_Complete_action_before(LC_Page_Ex $objPage){
        
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $objMail = new plg_PeriodicalSale_SC_Helper_Mail();
        
        //受注IDがセッションに残っているので取得
        $order_id = $_SESSION['order_id'];
        
        if(!SC_Utils_Ex::isBlank($order_id)){
        
            //定期受注を取得
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $objQuery->andWhere('last_order.order_id = ?');
            $objQuery->arrWhereVal = array_merge($objQuery->arrWhereVal, array($order_id));
            $arrPeriodicalOrders = $objPeriodicalOrder
                ->fetch(true, $objQuery)
                ->get();

            //テンプレートIDを取得
            $arrNamedPluginInfo = self::getNamedPluginInfo();
            $template_id = (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) ? $arrNamedPluginInfo['mobile_mail_template_id'] : $arrNamedPluginInfo['default_mail_template_id'];

            foreach($arrPeriodicalOrders as $arrPeriodicalOrder){

                $periodical_order_id = $arrPeriodicalOrder['periodical_order_id'];
                
                //一時的なdel_flgを折る
                plg_PeriodicalSale_SC_Helper_Purchase::savePeriodicalOrder($periodical_order_id, array('del_flg' => 0));
                
                //メールを送信
                $objMail->sfSendPeriodicalOrderMail($periodical_order_id, $template_id);
            }
        }
    }

    /**
     * 入力内容の確認 confirm
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Shopping_Confirm_action_confirm(LC_Page_Ex $objPage) {

        $order_id = $objPage->arrForm['order_id'];
        //注文商品に定期商品が入っていたなら、
        if (plg_PeriodicalSale_SC_Helper_Purchase::hasPeriodicalProductByOrderId($order_id)) {

            //一時定期受注情報にIDを付与する。
            plg_PeriodicalSale_SC_Helper_Purchase::applyOrderIdToTempPeriodicalOrder($objPage->tpl_uniqid);
            
            //一時定期受注情報を確定する。
            $arrPeriodicalOrderIds = plg_PeriodicalSale_SC_Helper_Purchase::completePeriodicalOrder($order_id);
            
            //一時的にdel_flgを立てる
            foreach($arrPeriodicalOrderIds as $periodical_order_id){
                plg_PeriodicalSale_SC_Helper_Purchase::savePeriodicalOrder($periodical_order_id, array('del_flg' => 1));
            }
        }
    }

    /**
     * 入力内容の確認 after
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Shopping_Confirm_action_after(LC_Page_Ex $objPage) {
        
        if(plg_PeriodicalSale_SC_Helper_Purchase::hasPeriodicalCartItem()){
            
            $session = $_SESSION;
            $mode = $objPage->getMode();
            switch ($mode) {
                
                case 'return':
                case 'confirm':
                    break;

                default:
                    
                    $objPage->plg_PeriodicalSale_arrTempPeriodicalOrder = plg_PeriodicalSale_SC_Helper_Purchase::getTempPeriodicalOrder($objPage->tpl_uniqid);
                    
                    //モジュールから戻ってきた場合は一時定期受注が空になってしまう。
                    //モジュールにフックポイントがないため対応には要カスタマイズ
                    if(empty($objPage->plg_PeriodicalSale_arrTempPeriodicalOrder)){
                        
                        //リダイレクト
                        SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                        SC_Response_Ex::actionExit();
                    }
                    
                    //マスタデータをセット
                    $objPurchase = new SC_Helper_Purchase_Ex();
                    $objMasterData = new SC_DB_MasterData_Ex();

                    $objPage->plg_PeriodicalSale_arrPERIODDATES = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
                    $objPage->plg_PeriodicalSale_arrDAYS = $objMasterData->getMasterData('mtb_wday');
                    $objPage->plg_PeriodicalSale_arrPERIODWEEKS = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
                    $objPage->plg_PeriodicalSale_has_periodical_cart_item = plg_PeriodicalSale_SC_Helper_Purchase::hasPeriodicalCartItem($objPage->arrCartItems);
                    $objPage->plg_PeriodicalSale_arrPAYMENTS = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');

                    $delivery_id = $objPage->arrForm['deliv_id'];
                    $objPage->plg_PeriodicalSale_arrPeriodicalDeliveryTimes = $objPurchase->getDelivTime($delivery_id);
                    $objPage->plg_PeriodicalSale_arrShippings = plg_PeriodicalSale_LC_Page_Shopping_Confirm::sfGetShippingParams($objPage->plg_PeriodicalSale_arrTempPeriodicalOrder, $session['shipping']);
                    break;
            }
        }
    }

    /**
     * お支払い・お届け情報入力 confirm
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Shopping_Payment_action_confirm(LC_Page_Ex $objPage) {

        if (plg_PeriodicalSale_SC_Helper_Purchase::hasPeriodicalCartItem()) {

            $objFormParam = new SC_FormParam_Ex();
            $post = $_POST;

            plg_PeriodicalSale_LC_Page_Shopping_Payment::sfInitFormParam($objFormParam, $post);
            plg_PeriodicalSale_LC_Page_Shopping_Payment::sfSetFormParam($objFormParam, $post);
            $arrForm = $objFormParam->getHashArray();
            //一時定期受注情報を保存する
            plg_PeriodicalSale_SC_Helper_Purchase::saveTempPeriodicalOrder($objPage->tpl_uniqid, $arrForm);
        }
    }

    /**
     * お支払い・お届け情報入力 after
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Shopping_Payment_action_after(LC_Page_Ex $objPage) {

        $objPage->plg_PeriodicalSale_arrAvailablePeriodInfo = self::getAvailablePeriodInfo();
        $objPage->plg_PeriodicalSale_has_periodical_cart_item = plg_PeriodicalSale_SC_Helper_Purchase::hasPeriodicalCartItem();

        $objFormParam = new SC_FormParam_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $post = $_POST;
        $mode = $objPage->getMode();
        
        switch ($mode) {

            case 'confirm':
                plg_PeriodicalSale_LC_Page_Shopping_Payment::sfInitFormParam($objFormParam, $post);
                plg_PeriodicalSale_LC_Page_Shopping_Payment::sfSetFormParam($objFormParam, $post);
                $objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getFormParamList());
                break;

            case 'return':
                break;

            /**
             * XXX モバイルでselected処理を行うならここ、な気がする
             */
            case 'select_deliv':
                break;

            default:

                //一時受注データを取得
                $arrOrderTemp = $objPurchase->getOrderTemp($objPage->tpl_uniqid);
                $objPage->setFormParams($objFormParam, $arrOrderTemp, false, $objPage->arrShipping);

                //複数配送先でないなら
                if (!$objPage->is_single_deliv) {
                    $delivery_id = $objFormParam->getValue('deliv_id');
                }
                //複数配送先なら
                else {
                    $delivery_id = $objPage->arrDeliv[0]['deliv_id'];
                }

                if (!SC_Utils_Ex::isBlank($delivery_id)) {
                    //一時定期受注を取得
                    $arrTempPeriodicalOrder = plg_PeriodicalSale_SC_Helper_Purchase::getTempPeriodicalOrder($objPage->tpl_uniqid);
                    
                    //一時定期受注データが存在したら
                    if (!empty($arrTempPeriodicalOrder)) {
                        
                        plg_PeriodicalSale_LC_Page_Shopping_Payment::sfInitFormParam($objFormParam);
                        plg_PeriodicalSale_LC_Page_Shopping_Payment::sfSetFormParam($objFormParam, $arrTempPeriodicalOrder);
                        //フォーム情報にマージする
                        $objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getFormParamList());
                    }
                }
                break;
        }
    }

    /**
     * お客様情報入力(非会員) after
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Shopping_action_after(LC_Page_Ex $objPage) {

        $mode = $objPage->getMode();
        switch ($mode) {

            case 'login':
            case 'return':
            case 'multiple':
                break;

            default:
                $objPage->plg_PeriodicalSale_multi_orderable = plg_PeriodicalSale_SC_Helper_Purchase::isMultiOrderable();
                break;
        }
    }

    /**
     * お届け先の指定(会員) after
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Shopping_Deliv_action_after(LC_Page_Ex $objPage) {
        $objPage->plg_PeriodicalSale_multi_orderable = plg_PeriodicalSale_SC_Helper_Purchase::isMultiOrderable();
    }
    
    function LC_Page_Shopping_Deliv_action_before(LC_Page_Ex $objPage){
        
        $device_type_id = SC_Display_Ex::detectDevice();
        $mode = $objPage->getMode();
        
        switch($device_type_id){
            case DEVICE_TYPE_MOBILE:
                
                switch($mode){
                
                case 'multiple':
                    if(!plg_PeriodicalSale_SC_Helper_Purchase::isMultiOrderable()){
                        
                        $objPeriodicalSalePage = new plg_PeriodicalSale_LC_Page_Shopping_Deliv();
                        $objPeriodicalSalePage->sendRedirect(sprintf('%s/../plg_PeriodicalSale_unmultiorderable.php', SHOPPING_CONFIRM_URLPATH));
                        $objPeriodicalSalePage->actionExit();
                    }
                }
                
                break;
        }
    }

    /**
     * 商品編集ページ after
     * 
     * @param LC_Page_Ex $objPage 
     */
    function LC_Page_Admin_Products_Product_action_after(LC_Page_Ex $objPage) {

        $objPage->tpl_mainpage = 'products/product.tpl';
        $objFormParam = new SC_FormParam_Ex();
        plg_PeriodicalSale_LC_Page_Admin_Products_Product::sfInitFormParam($objFormParam);
        $mode = $objPage->getMode();
        $post = $_POST;

        switch ($mode) {

            case 'pre_edit':
            case 'copy':
                //フォーム情報に定期商品の情報をマージ
                plg_PeriodicalSale_LC_Page_Admin_Products_Product::sfSetPeriodicalProduct($objPage->arrForm);
                break;

            case 'edit':
            case 'complete':
                plg_PeriodicalSale_LC_Page_Admin_Products_Product::sfSetFormParam($objFormParam, $post);
                $objPage->arrErr = array_merge($objPage->arrErr, plg_PeriodicalSale_LC_Page_Admin_Products_Product::sfCheckError($objFormParam));
                $objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getHashArray());
                if (empty($objPage->arrErr)) {
                    switch ($mode) {

                        case 'edit':
                            $objPage->tpl_mainpage = 'products/confirm.tpl';
                            break;

                        case 'complete':
                            plg_PeriodicalSale_LC_Page_Admin_Products_Product::sfRegistPeriodicalProduct($objPage->arrForm);
                            $objPage->tpl_mainpage = 'products/complete.tpl';
                            break;
                    }
                }
                break;

            default:
                plg_PeriodicalSale_LC_Page_Admin_Products_Product::sfSetFormParam($objFormParam, $post);
                $objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getHashArray());
                break;
        }
    }
    
    /**
     * メール送信画面 after
     * 
     * @param LC_Page_Admin_Order_Mail_Ex $objPage 
     */
    function LC_Page_Admin_Order_Mail_action_after(LC_Page_Admin_Order_Mail_Ex $objPage){
        
        $arrNamedPluginInfo = self::getNamedPluginInfo();
        unset($objPage->arrMAILTEMPLATE[$arrNamedPluginInfo['default_mail_template_id']]);
        unset($objPage->arrMAILTEMPLATE[$arrNamedPluginInfo['mobile_mail_template_id']]);
    }
}