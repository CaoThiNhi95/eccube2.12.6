<?php
/*
 * Sale
 *
 * Copyright(c) 2009-2012 CUORE CO.,LTD. All Rights Reserved.
 *
 * http://ec.cuore.jp/
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
 * セール管理 のページクラス.
 *
 * @package Page
 * @author CUORE CO.,LTD.
 */
class plg_Sale_LC_Page_Admin_Products_Sale extends LC_Page_Admin_Ex {

    const CATEGORY_SALE = '1';
    const CATEGORY_BARGAIN = '2';
    const DIVISION_PERCENT = '1';
    const DIVISION_YEN = '2';

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/plg_Sale_sale.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'sale';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = 'セール管理';
        // 日付データ取得
        $objDate = new SC_Date();
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        // マスタデータ取得
        $masterData = new SC_DB_MasterData_Ex();
        // 分類
        $this->arrCategory = $masterData->getMasterData("plg_sale_mtb_sale_category");
        // 区分
        $this->arrDivision = $masterData->getMasterData("plg_sale_mtb_sale_division");
        // 有効状態
        $this->arrStatus = $masterData->getMasterData("plg_sale_mtb_sale_status");
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

        // 要求未指定の場合、初期表示
        if(!isset($_POST['mode'])) $_POST['mode'] = "";

        // 分類未指定の場合、セール
        if(!isset($_POST['category'])) {
            $this->category = "1";
        } else {
            $this->category = $_POST['category'];
        }

        // 要求判定
        switch($this->getMode()) {
        // 登録処理
        case 'confirm':
            // POST値の引き継ぎ
            $arrPost = $_POST;
            // 入力文字の変換
            $arrPost = $this->lfConvertParam($arrPost);

            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($arrPost);
            // エラー件数判定
            if(count($this->arrErr) <= 0) {
                // エラーなし
                // IDの有無で登録処理振り分け
                if($_POST['sale_id'] == "") {
                    // 新規登録
                    // IDの発行
                    $objQuery =& SC_Query_Ex::getSingletonInstance();
                    $arrPost['sale_id'] = $objQuery->nextVal('plg_sale_dtb_sale_sale_id');
                    // セール基本情報の登録
                    $this->lfInsertSale($arrPost);
                    // セール対象情報の登録
                    $this->lfInsertGoods($arrPost);
                } else {
                    // 更新
                    // セール基本情報の更新
                    $this->lfUpdateSale($arrPost);
                    // セール対象情報の更新
                    $this->lfUpdateGoods($arrPost);
                }
                $this->tpl_onload = "alert('登録が完了しました。');";
            } else {
                // エラーあり
                // POSTデータを引き継ぐ
                $this->arrForm = $arrPost;
                $this->tpl_sale_id = $_POST['sale_id'];
            }
            break;
        // 削除
        case 'delete':
            // セール基本情報の削除
            $this->lfDeleteSale($_POST['sale_id']);
            // セール対象情報の削除
            $this->lfDeleteGoods($_POST['sale_id']);
            break;
        // 編集処理
        case 'edit':
            // 編集項目をDBより取得
            $arrRet = $this->lfGetSaleData($_POST['sale_id']);
            // 編集項目値の埋め込み
            // 名称
            $this->arrForm['name'] = $arrRet[0]['name'];
            // 分類
            $this->arrForm['category'] = $arrRet[0]['category'];
            // 区分
            $this->arrForm['division'] = $arrRet[0]['division'];
            // 値
            $this->arrForm['value'] = $arrRet[0]['value'];
            // 開始日
            if(isset($arrRet[0]['start_date'])){
                $this->arrForm['start_year'] = substr($arrRet[0]['start_date'], 0, 4);
                $this->arrForm['start_month'] = substr($arrRet[0]['start_date'], 5, 2);
                $this->arrForm['start_day'] = substr($arrRet[0]['start_date'], 8, 2);
            }
            // 終了日
            if(isset($arrRet[0]['end_date'])){
                $this->arrForm['end_year'] = substr($arrRet[0]['end_date'], 0, 4);
                $this->arrForm['end_month'] = substr($arrRet[0]['end_date'], 5, 2);
                $this->arrForm['end_day'] = substr($arrRet[0]['end_date'], 8, 2);
            }
            // 有効状態
            $this->arrForm['status'] = $arrRet[0]['status'];
            // コメント
            $this->arrForm['comment'] = $arrRet[0]['comment'];

            // 対象情報をDBより取得
            $this->category = $this->arrForm['category'];
            $this->arrForm['object_id'] = $this->lfGetGoodsData($_POST['sale_id'], $this->category);

            // POSTデータを引き継ぐ
            $this->tpl_sale_id = $_POST['sale_id'];
            break;
        // 部類変更処理
        case 'chg_category':
            // POST値の引き継ぎ
            $this->arrForm = $_POST;
            // 対象情報は初期化
            unset($this->arrForm['object_id']);
            break;
        default:
            break;
        }

        // 分類毎の対象リストの取得
        if($this->category == self::CATEGORY_SALE) {
            // カテゴリ
            $this->arrObject = $this->lfGetCategory();
        } else {
            // 商品
            list($this->arrObjVal, $this->arrObjOut) = $this->lfGetProductList(true);
            // onLoad で対象選択切り替えファンクション実行
            $this->tpl_onload .= "fnMoveSelect('object_id_unselect', 'object_id');";
        }

        // 登録済みデータリストの取得
        $this->arrSale = $this->lfGetSaleList();
        // データ毎の対象情報リストの取得
        foreach($this->arrSale as $key => $val) {
            $this->arrSale[$key]['goods'] = $this->lfGetGoodsList($val['sale_id'], $val['category']);
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

    /* セール基本情報の登録 */
    function lfInsertSale($arrData) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // INSERTする値を作成する。
        $sqlval['sale_id'] = $arrData['sale_id'];
        $sqlval['name'] = $arrData['name'];
        $sqlval['category'] = $arrData['category'];
        $sqlval['division'] = $arrData['division'];
        $sqlval['value'] = $arrData['value'];
        $sqlval['start_date'] = $arrData['start_date'];
        $sqlval['end_date'] = $arrData['end_date'];
        $sqlval['status'] = $arrData['status'];
        $sqlval['comment'] = $arrData['comment'];

        // INSERTの実行
        $ret = $objQuery->insert("plg_sale_dtb_sale", $sqlval);

        return $ret;
    }

    /* セール対象情報の登録 */
    function lfInsertGoods($arrData) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $id_name = "";
        if($arrData['category'] == self::CATEGORY_SALE) {
            $id_name = "category_id";
        } else {
            $id_name = "product_id";
        }

        // INSERTする値を作成する。
        $sqlval['sale_id'] = $arrData['sale_id'];
        foreach($arrData['object_id'] as $key => $val) {
            // IDの発行
            $sqlval['goods_id'] = $objQuery->nextVal('plg_sale_dtb_sale_goods_goods_id');

            $sqlval[$id_name] = $val;
            // INSERTの実行
            $ret = $objQuery->insert("plg_sale_dtb_sale_goods", $sqlval);
        }

        return $ret;
    }

    /* セール基本情報の更新 */
    function lfUpdateSale($arrData) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // UPDATEする値を作成する。
        $sqlval['name'] = $arrData['name'];
        $sqlval['division'] = $arrData['division'];
        $sqlval['value'] = $arrData['value'];
        $sqlval['start_date'] = $arrData['start_date'];
        $sqlval['end_date'] = $arrData['end_date'];
        $sqlval['status'] = $arrData['status'];
        $sqlval['comment'] = $arrData['comment'];

        // UPDATEの実行
        $ret = $objQuery->update("plg_sale_dtb_sale", $sqlval, "sale_id = ?", array($arrData['sale_id']));

        return $ret;
    }

    /* セール対象情報の更新 */
    function lfUpdateGoods($arrData) {
        // 削除
        $ret = $this->lfDeleteGoods($arrData['sale_id']);
        // 登録
        $ret = $this->lfInsertGoods($arrData);

        return $ret;
    }

    /* セール基本情報の削除 */
    function lfDeleteSale($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // DELETEする値を作成する。
        $sqlval[] = $id;

        // DELETEの実行
        $ret = $objQuery->delete("plg_sale_dtb_sale", "sale_id = ?", $sqlval);

        return $ret;
    }

    /* セール対象情報の削除 */
    function lfDeleteGoods($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // DELETEする値を作成する。
        $sqlval[] = $id;

        // DELETEの実行
        $ret = $objQuery->delete("plg_sale_dtb_sale_goods", "sale_id = ?", $sqlval);

        return $ret;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */
        // 文字変換
        $arrConvList['name'] = "KVa";
        $arrConvList['comment'] = "KVa";

        foreach($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }

        // 開始日情報作成
        $array['start_date'] = $array['start_year'] . "/" . $array['start_month'] . "/" . $array['start_day'];
        // 終了日情報作成
        $array['end_date'] = $array['end_year'] . "/" . $array['end_month'] . "/" . $array['end_day'];

        return $array;
    }

    /* 入力エラーチェック */
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError_Ex();

        $objErr->doFunc(array("名称", "name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("分類", "category"), array("EXIST_CHECK"));

        $objErr->doFunc(array("値引き区分", "division"), array("EXIST_CHECK"));

        $objErr->doFunc(array("値引き値", "value", PRICE_LEN), array("EXIST_CHECK", "NUM_CHECK", "ZERO_CHECK", "MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("値引き開始日", "start_year", "start_month", "start_day"), array("FULL_EXIST_CHECK", "CHECK_DATE"));

        $objErr->doFunc(array("値引き終了日", "end_year", "end_month", "end_day"), array("FULL_EXIST_CHECK", "CHECK_DATE"));

        $objErr->doFunc(array("有効状態", "status"), array("EXIST_CHECK"));

        $objErr->doFunc(array("コメント", "comment", LTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        if(!isset($array['object_id'])) $objErr->arrErr['object'] = "※ セール対象" . ($this->category == self::CATEGORY_SALE ? "カテゴリ" : "商品") . "が選択されていません。<br />";

        if(!isset($objErr->arrErr['value'])) {
            if($array['division'] == self::DIVISION_PERCENT) {
                if($array['value'] > 99) {
                    $objErr->arrErr['value'] = "※ 値引き区分が「%」の場合、100以上の値は入力できません。<br />";
                }
            } else {
                if(!isset($objErr->arrErr['object'])) {
                    if(!$this->lfCheckValue($array['value'], $array['category'], $array['object_id'])) {
                        $objErr->arrErr['value'] = "※ 販売価格が１円未満になる商品が存在します。<br />";
                    }
                }
            }
        }

        if(!isset($objErr->arrErr['start_year']) && !isset($objErr->arrErr['end_year']) && !isset($objErr->arrErr['object'])) {
            $objErr->doFunc(array("値引き開始日", "値引き終了日", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
            if(!isset($objErr->arrErr['start_year'])) {
                if(!$this->lfCheckSaleDate($array)) {
                    $objErr->arrErr['object'] = "※ 他の" . $this->arrCategory[$this->category] . "に含まれているものが選択されています。<br />";
                }
            }
        }

        return $objErr->arrErr;
    }

    /* 期間重複チェック */
    function lfCheckSaleDate($array) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "s1.sale_id";
        $table = "plg_sale_dtb_sale s1, plg_sale_dtb_sale_goods s2";
        $where = "s1.sale_id = s2.sale_id";
        // IDの有無で新規か編集か判定
        if($array['sale_id'] != "") {
            // 編集の場合、編集中のIDは除外する
            $where .= " AND s1.sale_id <> ?";
            $sqlval[] = $array['sale_id'];
        }

        $where .= " AND (s1.start_date <= ? AND s1.end_date >= ?) AND s2.";
        // 分類の判定
        if($this->category == "1") {
            // セールの場合
            $where .=  "category_id";
        } else {
            // 特価品の場合
            $where .=  "product_id";
        }
        $where .= " IN(";
        for($i = 0; $i < count($array['object_id']); $i++) {
            if($i != 0) $where .= ", ";
            $where .= "?";
        }
        $where .= ")";

        $sqlval[] = $array['end_date'];
        $sqlval[] = $array['start_date'];
        $sqlval = array_merge($sqlval, $array['object_id']);

        return $objQuery->count($table, $where, $sqlval) > 0 ? false : true;
    }

    /* 値引き値チェック */
    function lfCheckValue($value, $category, $arrObject) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "MIN(price02) AS price02_min";
        $table = "dtb_products_class";
        $where = "product_id IN(";
        if($category == self::CATEGORY_SALE) $where .= "SELECT product_id FROM dtb_product_categories WHERE category_id IN(";
        for($i = 0; $i < count($arrObject); $i++) {
            if($i != 0) $where .= ", ";
            $where .= "?";
        }
        $where .= ")";
        if($category == self::CATEGORY_SALE) $where .= ")";

        $result = $objQuery->select($column, $table, $where, $arrObject);

        if(($result[0]['price02_min'] - $value) < 1) {
            return false;
        }

        return true;
    }

    /* カテゴリリスト取得 */
    function lfGetCategory() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "category_id, category_name, level";
        $table = "dtb_category";
        $where = "del_flg = 0";
        $order = "rank DESC";

        $objQuery->setorder($order);
        $ret = $objQuery->select($column, $table, $where);

        $id_val = "";
        $bef_level = "";
        for($i = 0; $i < count($ret); $i++) {
            if($ret[$i]['level'] == "1") {
                $id_val = sprintf("%010s", $ret[$i]['category_id']);
            } elseif($ret[$i]['level'] <= $bef_level) {
                $id_val = substr($id_val, 0, strlen($id_val) - ($bef_level - $ret[$i]['level'] + 1) * 10) . sprintf("%010s", $ret[$i]['category_id']);
            } else {
                $id_val .= sprintf("%010s", $ret[$i]['category_id']);
            }
            $ret[$i]['id'] = $id_val;
            $bef_level = $ret[$i]['level'];
        }

        return $ret;
    }

    /* 登録済みセール基本情報取得 */
    function lfGetSaleData($id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "*";
        $table = "plg_sale_dtb_sale";
        $where = "sale_id = ?";

        $sqlval[] = $id;

        $ret = $objQuery->select($column, $table, $where, $sqlval);

        return $ret;
    }

    /* 登録済みセール基本情報リスト取得 */
    function lfGetSaleList() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "sale_id, name, category, start_date, end_date";
        $table = "plg_sale_dtb_sale";
        $order = "sale_id ASC";

        $objQuery->setorder($order);
        $ret = $objQuery->select($column, $table);

        return $ret;
    }

    /* 登録済みセール対象情報取得 */
    function lfGetGoodsData($id, $category) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "";
        // 分類の判定
        if($category == self::CATEGORY_SALE) {
            // セールの場合
            $column = "category_id";
        } else {
            // 特価品の場合
            $column = "product_id";
        }
        $column .= " AS id";
        $table = "plg_sale_dtb_sale_goods";
        $where = "sale_id = ?";
        $order = "goods_id ASC";

        $sqlval[] = $id;

        $objQuery->setorder($order);
        $result = $objQuery->select($column, $table, $where, $sqlval);
        foreach($result as $key => $val) {
            $ret[$key] = $val['id'];
        }

        return $ret;
    }

    /* 登録済みセール対象情報リスト取得 */
    function lfGetGoodsList($id, $category) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 分類の判定
        $column = "";
        $table = "";
        $where = "";
        if($category == self::CATEGORY_SALE) {
            // セールの場合
            $column = "s.category_id AS id, c.category_name AS name";
            $table = "plg_sale_dtb_sale_goods s, dtb_category c";
            $where = "s.category_id = c.category_id AND s.sale_id = ?";
        } else {
            // 特価品の場合
            $column = "s.product_id AS id, p.name AS name";
            $table = "plg_sale_dtb_sale_goods s, dtb_products p";
            $where = "s.product_id = p.product_id AND s.sale_id = ?";
        }
        $order = "s.goods_id ASC";

        $sqlval[] = $id;

        $objQuery->setorder($order);
        $ret = $objQuery->select($column, $table, $where, $sqlval);

        return $ret;
    }

    /**
     * 商品リストの取得を行う.
     *
     * @return array 商品リストの配列
     */
    function lfGetProductList() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $col = "p1.product_id, p1.name, c1.category_id, c1.parent_category_id, c1.category_name, c1.level";
        $table = "dtb_products p1, dtb_product_categories p2, dtb_category c1";
        $where = "p1.product_id = p2.product_id AND p2.category_id = c1.category_id AND p1.del_flg = 0";
        $order = "c1.rank DESC, p2.rank DESC";

        $objQuery->setorder($order);
        $arrRet = $objQuery->select($col, $table, $where);
        $max = count($arrRet);
        $arrTemp = array("");
        for($cnt = 0; $cnt < $max; $cnt++) {
            // 重複商品削除処理
            if (isset($arrTemp[$arrRet[$cnt]['product_id']])) {
                // 既に存在する場合は処理を行わない
                continue;
            }
            $arrTemp[$arrRet[$cnt]['product_id']] = $arrRet[$cnt]['product_id'];// 記録
            $arrValue[$cnt] = $arrRet[$cnt]['product_id'];
            $arrOutput[$cnt] = "";
            // 子カテゴリから親カテゴリを検索
            $parent_category_id = $arrRet[$cnt]['parent_category_id'];
            for($cat_cnt = $arrRet[$cnt]['level']; $cat_cnt > 1; $cat_cnt--) {
                foreach($arrRet as $arrCat) {
                    // 親が見つかったら順番に代入
                    if ($arrCat['category_id'] == $parent_category_id) {
                        $arrOutput[$cnt] = CATEGORY_HEAD
                            . $arrCat['category_name'] . $arrOutput[$cnt];
                        $parent_category_id = $arrCat['parent_category_id'];
                    }
                }
            }
            $arrOutput[$cnt].= CATEGORY_HEAD . $arrRet[$cnt]['category_name'] . CATEGORY_HEAD . $arrRet[$cnt]['name'];
        }

        return array($arrValue, $arrOutput);
    }

}
?>
