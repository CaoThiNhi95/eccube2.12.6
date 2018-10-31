<?php
/*
 * NakwebBlocNewProductStatus
 * Copyright (C) 2012 NAKWEB CO.,LTD. All Rights Reserved.
 * http://www.nakweb.com/
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

/**
 * プラグインの設定クラス
 *
 * @package NakwebPluginBase
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class plg_NakwebBlocNewProductStatus_SC_Plugin_Bloc_DB_Set {

    /** DB更新用オブジェクト **/
    var $objQuery;

    /** プラグインステータス **/
    var $arrPlugin;

    /** 商品ステータス **/
    var $arrStatusId;

    /**
     * コンストラクト.
     *
     * @return void
     */
    function __construct() {

        // SQL実行用オブジェクト取得
        $this->objQuery    =& SC_Query_Ex::getSingletonInstance();

        // プラグインステータス初期化
        $this->arrPlugin    = array();

        // 商品ステータスID取得
        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrStatusId  = $masterData->getMasterData('mtb_status');

    }

    /**
     * 商品ステータスブロック用 ブロックデータの作成
     *
     * @param array  $arrPlugin   プラグインID
     * @param array  $arrDbSetFlg データベース更新対象
     * @return void
     */
    function sfProductStatusBlocDatabase($arrPlugin, $arrDbSetFlg) {

        // プラグイン情報の取込
        $this->arrPlugin = $arrPlugin;

        // トランザクション開始
        $this->objQuery->begin();

        // ブロックデータの作成
        if ($arrDbSetFlg['Insert'] == true) {

            if ($this->lfProductStatusBlocInsert() == false) {
                // ロールバック
                $this->objQuery->rollback();
                return false;
            }

        }

        // ブロックデータの作成（更新用）
        if ($arrDbSetFlg['Update'] == true) {

            if ($this->lfProductStatusBlocUpdate() == false) {
                // ロールバック
                $this->objQuery->rollback();
                return false;
            }

        }

        // ブロックデータの削除
        if ($arrDbSetFlg['Delete'] == true) {

            // ブロックポジションの削除
            $this->lfProductStatusBlocPositionDelete();
            // ブロックデータの削除
            $this->lfProductStatusBlocDelete();

        }

        // コミット
        $this->objQuery->commit();
        return true;

    }



    /**
     * 商品ステータスブロック用 ブロックデータの作成
     *
     * @return void
     */
    function lfProductStatusBlocInsert() {

        // ブロックが1件でも存在している場合は登録処理を中断する
        $device_type = DEVICE_TYPE_PC;
        $bloc_row_cnt = $this->objQuery->count('dtb_bloc', 'device_type_id = ? AND plugin_id = ?', array($device_type, $this->arrPlugin['plugin_id']));
        if ($bloc_row_cnt > 0) {
            return false;
        }


        foreach ($this->arrStatusId As $status_id => $status_name) {
            if ($this->lfOneBlocInsert($status_id, $status_name) == false) {
                // ブロックのインサート失敗
                return false;
            }

            // テンプレートファイルをコピーする
            $tpl_file_base = PLUGIN_UPLOAD_REALDIR . $this->arrPlugin['plugin_code'] . '/templates/plg_' . $this->arrPlugin['plugin_code'] . '_Base.tpl';
            $tpl_file_copy = TEMPLATE_REALDIR . 'frontparts/bloc/plg_' . $this->arrPlugin['plugin_code'] . '_' . $status_id . '.tpl';
            copy($tpl_file_base, $tpl_file_copy);

        }

        // ブロック呼出用のphpファイルをコピーする
        $php_file_base = PLUGIN_UPLOAD_REALDIR . $this->arrPlugin['plugin_code'] . '/bloc/plg_' . $this->arrPlugin['plugin_code'    ] . '.php';
        $php_file_copy = HTML_REALDIR . 'frontparts/bloc/plg_' . $this->arrPlugin['plugin_code'] . '.php';
        copy($php_file_base, $php_file_copy);

        return true;

    }

    /**
     * 商品ステータスブロック用 ブロックデータの更新
     *
     * @return void
     */
    function lfProductStatusBlocUpdate() {

        return true;

    }

    /**
     * 商品ステータスブロック用 ブロックポジションデータの削除
     *
     * @return void
     */
    function lfProductStatusBlocPositionDelete() {

        $device_type = DEVICE_TYPE_PC;

        // 削除対象のブロックを取得する
        $arrBlocId = $this->objQuery->getCol('bloc_id', 'dtb_bloc', 'device_type_id = ? AND plugin_id = ?', array($device_type, $this->arrPlugin['plugin_id']));

        // ブロックポジションを削除する
        foreach ($arrBlocId As $bloc_cnt => $bloc_id) {
            // 指定なしの場合があるため削除件数が 0 の場合もエラーとしない
            $where = 'bloc_id = ? AND device_type_id = ? ';
            $this->objQuery->delete('dtb_blocposition', $where, array($bloc_id, $device_type));
        }

        return true;

    }

    /**
     * 商品ステータスブロック用 ブロックデータの削除
     *
     * @return void
     */
    function lfProductStatusBlocDelete() {

        $device_type = DEVICE_TYPE_PC;

        // 登録したファイルを削除する
        // 削除対象のブロックを取得する
        $arrTplPath = $this->objQuery->getCol('tpl_path', 'dtb_bloc', 'device_type_id = ? AND plugin_id = ?', array($device_type, $this->arrPlugin['plugin_id']));
        // ブロックのテンプレートファイルを削除する
        foreach ($arrTplPath As $tpl_cnt => $tpl_path) {
            SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . 'frontparts/bloc/' . $tpl_path);
        }
        // ブロック呼出用のphpファイルを削除する
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR  . 'frontparts/bloc/plg_' . $this->arrPlugin['plugin_code'] . '.php');

        // ブロックを削除する.
        $where = 'plugin_id = ?';
        $this->objQuery->delete('dtb_bloc', $where, array($this->arrPlugin['plugin_id']));

        return true;

    }

    /**
     * 商品ステータス別ブロック情報インサート
     *
     * @param string $status_id 商品ステータスID
     * @param string $status_name 商品ステータス名称
     * @return integer|DB_Error|boolean
     */
    function lfOneBlocInsert($status_id, $status_name) {

        $device_type = DEVICE_TYPE_PC;

        // dtb_blocにブロックを追加する.
        $sql_bloc = array();
        $sql_bloc['device_type_id'] = $device_type;
        $sql_bloc['bloc_id']        = $this->objQuery->max('bloc_id', 'dtb_bloc', 'device_type_id = ' . $device_type) + 1;
        $sql_bloc['bloc_name']      = '商品ステータス別新着商品（' . $status_name . '）';
        $sql_bloc['tpl_path']       = 'plg_' . $this->arrPlugin['plugin_code'] . '_' . $status_id . '.tpl';
        $sql_bloc['filename']       = 'plg_' . $this->arrPlugin['plugin_code'] . '_' . $status_id;
        $sql_bloc['create_date']    = 'CURRENT_TIMESTAMP';
        $sql_bloc['update_date']    = 'CURRENT_TIMESTAMP';
        $sql_bloc['php_path']       = 'frontparts/bloc/plg_' . $this->arrPlugin['plugin_code'] . '.php';
        $sql_bloc['deletable_flg']  = 0;
        $sql_bloc['plugin_id']      = $this->arrPlugin['plugin_id'];

        $ret = $this->objQuery->insert("dtb_bloc", $sql_bloc);

        return $ret;

    }


}
?>
