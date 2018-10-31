<?php
/*
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * カテゴリコンテンツの設定クラス
 *
 * @package TopicPath
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class LC_Page_Plugin_DetailFacebookComment_Config extends LC_Page_Admin_Ex {
    
    var $arrForm = array();

    var $arrNumList = array(
        5 => 5,
        10 => 10,
        15 => 15,
        20 => 20,
        25 => 25,
        30 => 30,
    );
    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR ."DetailFacebookComment/templates/config.tpl";
        $this->tpl_subtitle = "Facebookコメント設定";
    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        
        $arrForm = array();
        
        switch ($this->getMode()) {
        case 'edit':
            $arrForm = $objFormParam->getHashArray();
            $this->arrErr = $objFormParam->checkError();

            if (count($this->arrErr) == 0) {
                // データ更新
                $this->arrErr = $this->updateData($arrForm);
                if (count($this->arrErr) == 0) {
                    $this->tpl_onload = "alert('登録が完了しました。');";
                }
            }
            break;
        default:
            // プラグイン情報を取得.
            $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("DetailFacebookComment");
            $arrForm['free_field1'] = $plugin['free_field1'];
            $arrForm['free_field2'] = $plugin['free_field2'];
            $arrForm['free_field3'] = empty($plugin['free_field3'])
                ? 5
                : $plugin['free_field3'];
            
            break;
        }

        $this->arrForm = $arrForm;
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
    
    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('app_id', 'free_field1', 80, null, array('EXIST_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('data-width',  'free_field2', 80, null, array('EXIST_CHECK','MAX_LENGTH_CHECK'));
        $objFormParam->addParam('data-num-post',  'free_field3', 80, null, array('EXIST_CHECK','NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }
    
    /**
     *
     * @param type $arrData
     * @return type 
     */
    function updateData($arrData) {
        $arrErr = array();
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        // UPDATEする値を作成する。
        $sqlval = array();
        $sqlval['free_field1'] = $arrData['free_field1'];
        $sqlval['free_field2'] = $arrData['free_field2'];
        $sqlval['free_field3'] = $arrData['free_field3'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = 'DetailFacebookComment'";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where);
        $objQuery->commit();
        return $arrErr;
    }
}
?>
