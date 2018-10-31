<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_FileManager_Ex.php");

/**
 * キャンペーンタグ生成 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class LC_Page_Admin_Contents_ImgCreateTag extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainno = 'contents';
        $this->tpl_subno = '';
        $this->tpl_subtitle = '画像設定';
        
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }
    
    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

            // POST値の引き継ぎ
            $this->arrForm = $_POST;
            // 入力文字の強制変換
            $this->lfConvertParam();

            //画像ファイルの取得
            // ルートディレクトリ /user_data/
        	$top_dir = USER_REALDIR;
            // 現在のディレクトリ配下のファイル一覧を取得
            $objFileManager = new SC_Helper_FileManager_Ex();
        	$imgArray = $objFileManager->sfGetFileList($top_dir . 'img/');
        	$imgData = array();
        	foreach ($imgArray as $img) {
        		$imgData[] = array('url' => ROOT_URLPATH.'user_data/img/' . $img['file_name'], 'file_name' => $img['file_name'],
        			'tag' => '<img src="'.ROOT_URLPATH.'user_data/img/'.$img['file_name'].'" alt="" />');
        	}

        	$this->tpl_linemax = count($imgData);				// 何件が該当しました。表示用
			// ページ送りの処理
            if(is_numeric($_POST['search_page_max'])) {
                $page_max = $_POST['search_page_max'];
            } else {
                $page_max = SEARCH_PMAX;
            }

            // ページ送りの取得
            $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
            $this->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
            $startno = $objNavi->start_row;

            // 検索結果の取得
            $this->arrProducts = $imgData;
            
            $this->setTemplate('Shiro8CategoryContents/plg_shiro8CategoryContents_imgtag_search.tpl');

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
     * 取得する文字数の変換を行うメソッド
     *
     * @return void
     */
    function lfConvertParam() {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */
        $arrConvList['search_name'] = "KVa";
        $arrConvList['search_product_code'] = "KVa";

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($this->arrForm[$key])) {
                $this->arrForm[$key] = mb_convert_kana($this->arrForm[$key] ,$val);
            }
        }
    }

}
?>
