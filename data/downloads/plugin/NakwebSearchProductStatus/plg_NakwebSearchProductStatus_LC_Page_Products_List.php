<?php
/*
 * NakwebSearchProductStatus
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

require_once CLASS_EX_REALDIR . 'page_extends/products/LC_Page_Products_List_Ex.php';

/**
 * プラグインのメインクラス
 *
 * @package NakwebSearchProductStatus
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class plg_NakwebSearchProductStatus_LC_Page_Products_List extends LC_Page_Products_List_Ex {


    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init($plg_head) {
        parent::init();
        // プラグイン情報の取得（フォルダ名からプラグインコードを取得する）
        $this->plugin_code  = basename(dirname(__FILE__));
        $this->plg_head     = $plg_head;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        exit();

    }

    // 検索結果の取得
    //2.13系と2.12系で一部処理が違うのでifでの条件分岐で対応arrEcVersion = explode('.',ECCUBE_VERSION,3);
    function action() {
      $arrEcVersion = explode('.',ECCUBE_VERSION,3);
      if($arrEcVersion[1]=='13'){

        //決済処理中ステータスのロールバック
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);

        $objProduct = new SC_Product_Ex();
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // 値の設定
        $objFormParam->setParam($_REQUEST);

        // 入力値の変換
        $objFormParam->convParam();

        // 値の取得
        $this->arrForm = $objFormParam->getHashArray();

        //modeの取得
        $this->mode = $this->getMode();

        }else{

        $objProduct = new SC_Product_Ex();

        $this->arrForm = $_REQUEST;//時間が無いのでコレで勘弁してください。 tao_s
        //modeの取得
        $this->mode = $this->getMode();
        }

        //表示条件の取得
        $this->arrSearchData = array(
            'category_id'                          => $this->lfGetCategoryId(intval($this->arrForm['category_id'])),
            'maker_id'                             => intval($this->arrForm['maker_id']),
            'name'                                 => $this->arrForm['name'],
            $this->plg_head . '_product_status_id' => ($this->arrForm[$this->plg_head . '_product_status_id'])
        );
        
        $this->orderby = $this->arrForm['orderby'];

        //ページング設定
        $this->tpl_pageno   = $this->arrForm['pageno'];
        $this->disp_number  = $this->lfGetDisplayNum($this->arrForm['disp_number']);

        // 画面に表示するサブタイトルの設定
        $this->tpl_subtitle = $this->lfGetPageTitle($this->mode, $this->arrSearchData['category_id']);

        // 画面に表示する検索条件を設定
        $this->arrSearch    = $this->lfGetSearchConditionDisp($this->arrSearchData);

        // 商品一覧データの取得
        $arrSearchCondition = $this->lfGetSearchCondition($this->arrSearchData);
        $this->tpl_linemax  = $this->lfGetProductAllNum($arrSearchCondition);
        $urlParam           = "category_id={$this->arrSearchData['category_id']}&pageno=#page#";
        // モバイルの場合に検索条件をURLの引数に追加
        //// 使用していないが今後のため残す
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $searchNameUrl = urlencode(mb_convert_encoding($this->arrSearchData['name'], 'SJIS-win', 'UTF-8'));
            $urlParam .= "&mode={$this->mode}&name={$searchNameUrl}&orderby={$this->orderby}";
        }
        if($arrEcVersion[1]=='13'){ /*2.13系と2.12系で一部objNabiに代入する値が違うので分岐、ここで分岐させないとwarning発生。*/
        $this->objNavi      = new SC_PageNavi_Ex($this->tpl_pageno, $this->tpl_linemax, $this->disp_number, 'eccube.movePage', NAVI_PMAX, $urlParam, SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);}
        else{
        $this->objNavi      = new SC_PageNavi_Ex($this->tpl_pageno, $this->tpl_linemax, $this->disp_number, 'fnNaviPage', NAVI_PMAX, $urlParam, SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);}
        $this->arrProducts  = $this->lfGetProductsList($arrSearchCondition, $this->disp_number, $this->objNavi->start_row, $this->tpl_linemax, $objProduct);

         /*doFefaultへと送る値が2.13系と2.12系で違うので分岐します。*/
         switch ($this->getMode()) {

             case 'json':
                 // 使用していないが今後のため残す
                 $this->doJson($objProduct);
                 break;

             default:
              if($arrEcVersion[1]=='13'){
                 $this->doDefault($objProduct, $objFormParam);
                /* break;*/
              }

              else{
                $this->doDefault($objProduct);
              }
                break;
        }

        $this->tpl_rnd          = SC_Utils_Ex::sfGetRandomString(3);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        $arrEcVersion = explode('.',ECCUBE_VERSION,3);
        if($arrEcVersion[1]=='12'){
            parent::destroy();
        }
    }

   /**
     * パラメーター情報の初期化
     *
     * @param  array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitParam(&$objFormParam)
    {
        //2.12系にもobjFormParamがあり、このプラグインの関係上今後直接的に利用するかもしれないので2.13系のみならず新たなパラメータをセットします。*/

        // 抽出条件
        // XXX カートインしていない場合、チェックしていない
        $objFormParam->addParam('カテゴリID', 'category_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メーカーID', 'maker_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品名', 'name', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('表示順序', 'orderby', STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ページ番号', 'pageno', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('表示件数', 'disp_number', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ステータスID', 'plg_nakweb_00003_product_status_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        // カートイン
        $objFormParam->addParam('規格1', 'classcategory_id1', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('規格2', 'classcategory_id2', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('数量', 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('商品規格ID', 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /* 商品一覧の表示 */
    function lfGetProductsList($searchCondition, $disp_number, $startno, $linemax, &$objProduct) {

        $arrOrderVal = array(); 
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 表示順序
        switch ($this->orderby) {
            // 販売価格が安い順
            case 'price':
                $objProduct->setProductsOrder('price02', 'dtb_products_class', 'ASC');
                break;

            // 新着順
            case 'date':
                $objProduct->setProductsOrder('create_date', 'dtb_products', 'DESC');
                break;

            default:
                if (strlen($searchCondition['where_category']) >= 1) {
                    $dtb_product_categories = '(SELECT * FROM dtb_product_categories WHERE '.$searchCondition['where_category'].')';
                    $arrOrderVal            = $searchCondition['arrvalCategory'];
                } else {
                    $dtb_product_categories = 'dtb_product_categories';
                }
                $order = <<< __EOS__
                    (
                        SELECT
                            T3.rank * 2147483648 + T2.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                              ON T2.category_id = T3.category_id
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,product_id DESC
__EOS__;
                    $objQuery->setOrder($order);
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($disp_number, $startno);
        $objQuery->setWhere($searchCondition['where']);

        // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProductId = $this->findProductIdsOrder($objProduct->arrOrderData, $objQuery, array_merge($searchCondition['arrval'], $arrOrderVal));

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrProducts = $objProduct->getListByProductIds($objQuery, $arrProductId);

        // 規格を設定
        $objProduct->setProductsClassByProductIds($arrProductId);
        $arrProducts['productStatus'] = $objProduct->getProductStatus($arrProductId);





        return $arrProducts;
    }

    /**
     * 表示用検索条件の設定
     *
     * @return array
     */
    function lfGetSearchConditionDisp($arrSearchData) {

        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $arrSearch  = array('product_status' => '指定なし', 'category' => '指定なし', 'maker' => '指定なし', 'name' => '指定なし');

        // カテゴリ検索条件
        if ($arrSearchData['category_id'] > 0) {
            $arrSearch['category']       = $objQuery->get('category_name', 'dtb_category', 'category_id = ?', array($arrSearchData['category_id']));
        }

        // メーカー検索条件
        if (strlen($arrSearchData['maker_id']) > 0) {
            $arrSearch['maker']          = $objQuery->get('name', 'dtb_maker', 'maker_id = ?', array($arrSearchData['maker_id']));
        }

        // 商品ステータス
        // 選択中の商品ステータスIDを判定する
        if ($arrSearchData[$this->plg_head . '_product_status_id']) {
            $searchNameCnt = 0;
            $arrSearch['product_status'] = "";
            foreach($arrSearchData[$this->plg_head . '_product_status_id'] as $serchStatusData){
                //条件としているステータスをカンマ区切りで並べるようにする
                if($searchNameCnt == 0){
                    $arrSearch['product_status'] .= $this->arrSTATUS[$serchStatusData];
                } else {
                    $arrSearch['product_status'] .= "," . $this->arrSTATUS[$serchStatusData];
                }
                $searchNameCnt++;
            }
        }

        // 商品名検索条件
        if (strlen($arrSearchData['name']) > 0) {
            $arrSearch['name']           = $arrSearchData['name'];
        }
        
        return $arrSearch;
    }

    /**
     * 該当件数の取得
     *
     * @return int
     */
    function lfGetProductAllNum($searchCondition) {
        // 検索結果対象となる商品の数を取得
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($searchCondition['where']);
        return $this->findProductCount($objQuery, $searchCondition['arrval']);
    }

    /**
     * 検索条件のwhere文とかを取得
     *
     * @return array
     */
    function lfGetSearchCondition($arrSearchData) {

        // 条件の使用状態取得
        $plugin_data   = SC_Plugin_Util_Ex::getPluginByPluginCode($this->plugin_code);
        $arrPluginData = unserialize($plugin_data['free_field1']);

        $searchCondition = array(
            'where'             => '',
            'arrval'            => array(),
            'where_category'    => '',
            'arrvalCategory'    => array()
        );

        // カテゴリからのWHERE文字列取得
        if ($arrSearchData['category_id'] != 0) {
            list($searchCondition['where_category'], $searchCondition['arrvalCategory']) = SC_Helper_DB_Ex::sfGetCatWhere($arrSearchData['category_id']);
        }
        // ▼対象商品IDの抽出
        // 商品検索条件の作成（未削除、表示）
        $searchCondition['where'] = 'alldtl.del_flg = 0 AND alldtl.status = 1 ';

        if (strlen($searchCondition['where_category']) >= 1) {
            $searchCondition['where'] .= ' AND EXISTS (SELECT * FROM dtb_product_categories WHERE ' . $searchCondition['where_category'] . ' AND product_id = alldtl.product_id)';
            $searchCondition['arrval'] = array_merge($searchCondition['arrval'], $searchCondition['arrvalCategory']);
        }

        // 商品名をwhere文に
        $name = $arrSearchData['name'];
        $name = str_replace(',', '', $name);
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split('/ +/', $name);
        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if (strlen($val) > 0) {
                if ($arrPluginData['product_code'] == 1) {
                    // 商品コードの設定有り
                    $searchCondition['where']    .= ' AND ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ? Or alldtlpc.product_code ILIKE ?) ';
                    $searchCondition['arrval'][]  = "%$val%";
                    $searchCondition['arrval'][]  = "%$val%";
                    $searchCondition['arrval'][]  = "%$val%";
                } else {
                    // 通常
                    $searchCondition['where']    .= ' AND ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ?) ';
                    $searchCondition['arrval'][]  = "%$val%";
                    $searchCondition['arrval'][]  = "%$val%";
                }
            }
        }

        // メーカーらのWHERE文字列取得
        if ($arrSearchData['maker_id']) {
            $searchCondition['where']   .= ' AND alldtl.maker_id = ? ';
            $searchCondition['arrval'][] = $arrSearchData['maker_id'];
        }


        // 商品ステータスのWHERE文字列取得
        if ($arrPluginData['product_status_id'] == 1) {
            if ($arrSearchData[$this->plg_head . '_product_status_id']) {
                $searchStatusCnt = 0;
                //チェックボックスにて選択されたステータスを含むもののみするように絞込み用のＳＱＬ文を作成する。
                foreach($arrSearchData[$this->plg_head . '_product_status_id'] as $searchStatusValue){
                    if($searchStatusCnt == 0){
                        $searchCondition['where']   .= ' AND ( alldtlps.product_status_id = ' . $searchStatusValue;
                    } else {
                        $searchCondition['where']   .= ' AND alldtlps.product_id IN (SELECT product_id FROM dtb_product_status WHERE product_status_id = ' . $searchStatusValue;
                    }
                    $searchStatusCnt++;
                }
                //追加した文かっこを閉じるようにする。
                for($endCnt = 1; $endCnt <= $searchStatusCnt; $endCnt++){
                    $searchCondition['where']   .= ')';
                }
            }
        }


        $searchCondition['where_for_count'] = $searchCondition['where'];

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN) {
            $searchCondition['where'] .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = alldtl.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
            $searchCondition['where_for_count'] .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = alldtl.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
        }


        return $searchCondition;
    }



    /**
     * SC_Queryインスタンスに設定された検索条件を元に並び替え済みの検索結果商品IDの配列を取得する。
     *
     * 検索条件は, SC_Query::setWhere() 関数で設定しておく必要があります.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param array $arrVal 検索パラメーターの配列
     * @return array 商品IDの配列
     */
    function findProductIdsOrder($arrOrderData, &$objQuery, $arrVal = array()) {
        // product_id で Group By を行うためここでは全件を取得する
        $table = <<< __EOS__
            dtb_products AS alldtl
  LEFT JOIN dtb_products_class AS alldtlpc
         ON alldtlpc.product_id = alldtl.product_id
  LEFT JOIN dtb_product_status AS alldtlps
         ON alldtlps.product_id = alldtl.product_id
        AND alldtlps.del_flg = 0
__EOS__;
        $objQuery->setGroupBy('alldtl.product_id');
        if (is_array($arrOrderData) and $objQuery->order == '') {
            $o_col = $arrOrderData['col'];
            $o_table = $arrOrderData['table'];
            $o_order = $arrOrderData['order'];
            $order = <<< __EOS__
                    (
                        SELECT $o_col
                        FROM
                            $o_table as T2
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T2.$o_col $o_order
                        LIMIT 1
                    ) $o_order, product_id
__EOS__;
            $objQuery->setOrder($order);
        }
        $results = $objQuery->select('alldtl.product_id', $table, '', $arrVal, MDB2_FETCHMODE_ORDERED);
        $resultValues = array();
        foreach ($results as $val) {
            $resultValues[] = $val[0];
        }
        return $resultValues;
    }

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに対象商品数を取得する.
     *
     * 検索条件は, SC_Query::setWhere() 関数で設定しておく必要があります.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @param array $arVal 検索パラメーターの配列
     * @return array 対象商品ID数
     */
    function findProductCount(&$objQuery, $arrVal = array()) {
        $table = <<< __EOS__
            dtb_products AS alldtl
  LEFT JOIN dtb_products_class AS alldtlpc
         ON alldtlpc.product_id = alldtl.product_id
  LEFT JOIN dtb_product_status AS alldtlps
         ON alldtlps.product_id = alldtl.product_id
        AND alldtlps.del_flg = 0
__EOS__;
        $rows = $objQuery->select('DISTINCT alldtl.product_id', $table, '', $arrVal);
        return count($rows);
    }




  /**
      * カートに入れる商品情報にエラーがあったら戻す
      *
      * @return str
      */
     public function lfSetSelectedData(&$arrProducts, $arrForm, $arrErr, $product_id)
     {
         $js_fnOnLoad = '';
         foreach (array_keys($arrProducts) as $key) {
             if ($arrProducts[$key]['product_id'] == $product_id) {
                 $arrProducts[$key]['product_class_id']  = $arrForm['product_class_id'];
                 $arrProducts[$key]['classcategory_id1'] = $arrForm['classcategory_id1'];
                 $arrProducts[$key]['classcategory_id2'] = $arrForm['classcategory_id2'];
                 $arrProducts[$key]['quantity']          = $arrForm['quantity'];
                 $arrProducts[$key]['product_Status_id']     = $arrForm['plg_nakweb_00003_product_status_id'];
                 $arrProducts[$key]['arrErr']            = $arrErr;
                 $classcategory_id2 = SC_Utils_Ex::jsonEncode($arrForm['classcategory_id2']);
                 $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProducts[$key]['product_id']}, {$classcategory_id2});";
}
         }
         return $js_fnOnLoad;
     }
}
?>
