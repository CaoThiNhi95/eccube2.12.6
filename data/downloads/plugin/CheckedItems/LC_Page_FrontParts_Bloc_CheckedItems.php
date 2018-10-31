<?php
/*
 * CheckedItems
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

// {{{ requires
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';
require_once PLUGIN_UPLOAD_REALDIR . 'CheckedItems/CheckedItems.php';

/**
 * 最近チェックした商品ブロックのブロッククラス
 */
class LC_Page_FrontParts_Bloc_CheckedItems extends LC_Page_FrontParts_Bloc {

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        //商品情報取得
        $this->arrCheckItems = $this->getItemList();
    }
    
    /**
     * 最近チェック商品の情報を取得
     *
     * @return array
     * @setcookie array
     */
    function getItemList() {
        $cnt = 0;
        $arrItemList = array();

        // プラグイン情報を取得.
        $plugin     = CheckedItems::getPlgInfo();
        //保存件数
        $save_count = $plugin['item_count'];
        
        $arrItem = CheckedItems::getCookieArray();
        $arrItem = array_reverse($arrItem, true);
        foreach ($arrItem as $name => $value) {
            $objQuery = new SC_Query();
            $objProduct = new SC_Product_Ex();

            // 商品情報取得
            $arrRet = $objProduct->getDetail($value);
            if( $arrRet['product_id'] ){
                $arrItemList[$cnt] = $arrRet;
                $cnt = $cnt+1;
            }
            if( $save_count <= $cnt ) break;
        }
        return $arrItemList;
        
    }

}
?>
