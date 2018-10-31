<?php

/*
 * MemberPrice
 * Copyright (C) 2012 Bratech CO.,LTD. All Rights Reserved.
 * http://www.bratech.co.jp/
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 会員価格設定プラグイン
 *
 * @package MemberPrice
 * @author Bratech CO.,LTD.
 * @version $Id: $
 */
class LC_Page_Plugin_MemberPrice_Config extends LC_Page_Admin_Ex
{

    var $arrForm = array();

    /**
     * 初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR . "MemberPrice/templates/config.tpl";
        $this->tpl_subtitle = "会員価格タイトル設定";
    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {
        $this->arrLoginDisp = array('0' => '常に表示', '1' => 'ログイン中のみ表示');
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $arrForm = array();

        switch ($this->getMode()) {
            case 'edit':
                $arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                // エラーなしの場合にはデータを更新
                if (count($this->arrErr) == 0) {
                    // データ更新
                    $this->updateData($arrForm);
                    if (count($this->arrErr) == 0) {
                        $this->tpl_onload = "alert('登録が完了しました。');";
                        $this->tpl_onload .= 'window.close();';
                    }
                }
                break;
            default:
                break;
        }
        if (empty($arrForm)) {
            $objQuery = & SC_Query_Ex::getSingletonInstance();
            $ret = $objQuery->select("free_field1,free_field2", "dtb_plugin", "plugin_code = ?", array('MemberPrice'));
            foreach ($ret as $item) {
                $arrForm['memberprice_title'] = $item['free_field1'];
                $arrForm['login_disp'] = $item['free_field2'];
            }
        }
        $this->arrForm = $arrForm;
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        if (method_exists('LC_Page_Admin_Ex', 'destroy')) {
            parent::destroy();
        }
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam)
    {
        $objFormParam->addParam('会員価格タイトル', 'memberprice_title', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('会員価格の表示設定', 'login_disp', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    function updateData($arrData)
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $objQuery->update("dtb_plugin", array("free_field1" => $arrData['memberprice_title']), "plugin_code = ?", array('MemberPrice'));
        $objQuery->update("dtb_plugin", array("free_field2" => $arrData['login_disp']), "plugin_code = ?", array('MemberPrice'));
    }

}

?>
