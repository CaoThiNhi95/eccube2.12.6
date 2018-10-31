<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/SC_Coupon.php';


/**
 * クーポン管理 のページクラス.
 *
 * @package Page
 * @author SEED
 * @version $Id: plg_Coupon_LC_Page_Admin_Contents_Coupon.php 21232 2011-09-04 15:44:01Z Seasoft $
 */
class plg_Coupon_LC_Page_Admin_Contents_Coupon extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/plg_Coupon_coupon.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subno = 'coupon';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'クーポン管理';
    }

    /**
     * Page のプロセス.
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

        $objQuery = new SC_Query_Ex();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_POST['coupon_id'])) $_POST['coupon_id'] = "";

        if ( $_POST['mode'] == "delete" && SC_Coupon::sfCheckNumLength($_POST['coupon_id'])===true ){

            // 登録削除
            $sql = "UPDATE dtb_coupon SET del_flg = 1, update_date = NOW() WHERE coupon_id = ?";
            $objQuery->query($sql, array($_POST['coupon_id']));

           //$this->reload(null, true);
            $this->objDisplay->reload(null, true);
        }

        $col = "*";
        $from = "dtb_coupon";
        $where = "del_flg=0";
        $arrval = array();

        // 行数の取得
        $linemax = $objQuery->count($from, $where);
        $this->tpl_linemax = $linemax;
        // ページナビ用
        $this->tpl_pageno = isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";
        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($this->tpl_pageno,
                                      $this->tpl_linemax, SEARCH_PMAX,
                                      'fnNaviSearchPage', NAVI_PMAX);
        $this->arrPagenavi = $objNavi->arrPagenavi;
        $startno = $objNavi->start_row;
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setlimitoffset(SEARCH_PMAX, $startno);
        // 表示順序
        $order = "create_date DESC, coupon_id DESC" ;
        $objQuery->setorder($order);
        // データの取得
        $this->list_data = $objQuery->select($col, $from, $where);
                
        //使用回数を取得
        $tmp_list_data = array();
        foreach($this->list_data as $key=>$data){
            $tmp_list_data[$data["coupon_id"]] = $data;
            if($coupon_ids){
                $coupon_ids = $coupon_ids.",".$data["coupon_id"];
            }else{
                $coupon_ids = $data["coupon_id"];
            }
        }
        if($coupon_ids){
            $sql = "select coupon_id,count(*) as cnt from dtb_coupon_used where coupon_id in(".$coupon_ids.") group by coupon_id";
            $result = $objQuery->getAll($sql);

            foreach($result as $key=>$data){
                $tmp_list_data[$data["coupon_id"]]["used_num"] = $data["cnt"];
            }
            $this->list_data = $tmp_list_data;
        }

        // 比較のため終了日付をタイムスタンプへ変更
        foreach($this->list_data as $key => $val) {
            $this->list_data[$key]['end_date_timestamp'] = strtotime($val['end_date']);
        }
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
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->count('dtb_coupon', $where, $arrValues);
    }

}
?>
