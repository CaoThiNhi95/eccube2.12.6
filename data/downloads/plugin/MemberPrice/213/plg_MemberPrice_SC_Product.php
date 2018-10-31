<?php

/*
 * MemberPrice
 * Copyright (C) 2012 Bratech CO.,LTD. All Rights Reserved.
 * http://wwww.bratech.co.jp/
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

class plg_MemberPrice_SC_Product extends SC_Product
{

    /**
     * SC_Queryインスタンスに設定された検索条件をもとに商品一覧の配列を取得する.
     *
     * 主に SC_Product::findProductIds() で取得した商品IDを検索条件にし,
     * SC_Query::setOrder() や SC_Query::setLimitOffset() を設定して, 商品一覧
     * の配列を取得する.
     *
     * @param SC_Query $objQuery SC_Query インスタンス
     * @return array 商品一覧の配列
     */
    public function lists(&$objQuery)
    {
        $col = <<< __EOS__
             product_id
            ,product_code_min
            ,product_code_max
            ,name
            ,comment1
            ,comment2
            ,comment3
            ,main_list_comment
            ,main_image
            ,main_list_image
            ,price01_min
            ,price01_max
            ,price02_min
            ,price02_max
            ,plg_memberprice_price03_min
            ,plg_memberprice_price03_max
            ,stock_min
            ,stock_max
            ,stock_unlimited_min
            ,stock_unlimited_max
            ,deliv_date_id
            ,status
            ,del_flg
            ,update_date
__EOS__;
        $res = $objQuery->select($col, $this->alldtlSQL());
        return $res;
    }

    /**
     * 商品IDに紐づく商品規格を自分自身に設定する.
     *
     * 引数の商品IDの配列に紐づく商品規格を取得し, 自分自身のフィールドに
     * 設定する.
     *
     * @param array $arrProductId 商品ID の配列
     * @param boolean $has_deleted 削除された商品規格も含む場合 true; 初期値 false
     * @return void
     */
    public function setProductsClassByProductIds($arrProductId, $has_deleted = false)
    {

        foreach ($arrProductId as $productId) {
            $arrProductClasses = $this->getProductsClassFullByProductId($productId, $has_deleted);

            $classCats1 = array();
            $classCats1['__unselected'] = '選択してください';

            // 規格1クラス名
            $this->className1[$productId] = isset($arrProductClasses[0]['class_name1']) ? $arrProductClasses[0]['class_name1'] : '';

            // 規格2クラス名
            $this->className2[$productId] = isset($arrProductClasses[0]['class_name2']) ? $arrProductClasses[0]['class_name2'] : '';

            // 規格1が設定されている
            $this->classCat1_find[$productId] = $arrProductClasses[0]['classcategory_id1'] > 0; // 要変更ただし、他にも改修が必要となる
            // 規格2が設定されている
            $this->classCat2_find[$productId] = $arrProductClasses[0]['classcategory_id2'] > 0; // 要変更ただし、他にも改修が必要となる

            $this->stock_find[$productId] = false;
            $classCategories = array();
            $classCategories['__unselected']['__unselected']['name'] = '選択してください';
            $classCategories['__unselected']['__unselected']['product_class_id'] = $arrProductClasses[0]['product_class_id'];
            // 商品種別
            $classCategories['__unselected']['__unselected']['product_type'] = $arrProductClasses[0]['product_type_id'];
            $this->product_class_id[$productId] = $arrProductClasses[0]['product_class_id'];
            // 商品種別
            $this->product_type[$productId] = $arrProductClasses[0]['product_type_id'];
            foreach ($arrProductClasses as $arrProductsClass) {
                $arrClassCats2 = array();
                $classcategory_id1 = $arrProductsClass['classcategory_id1'];
                $classcategory_id2 = $arrProductsClass['classcategory_id2'];
                // 在庫
                $stock_find_class = ($arrProductsClass['stock_unlimited'] || $arrProductsClass['stock'] > 0);

                $arrClassCats2['classcategory_id2'] = $classcategory_id2;
                $arrClassCats2['name'] = $arrProductsClass['classcategory_name2'] . ($stock_find_class ? '' : ' (品切れ中)');

                $arrClassCats2['stock_find'] = $stock_find_class;

                if ($stock_find_class) {
                    $this->stock_find[$productId] = true;
                }

                if (!in_array($classcat_id1, $classCats1)) {
                    $classCats1[$classcategory_id1] = $arrProductsClass['classcategory_name1']
                            . ($classcategory_id2 == 0 && !$stock_find_class ? ' (品切れ中)' : '');
                }

                // 価格
                // TODO: ここでprice01,price02を税込みにしてよいのか？ _inctax を付けるべき？要検証
                $arrClassCats2['price01'] = strlen($arrProductsClass['price01']) ? number_format(SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductsClass['price01'], $productId, $arrProductsClass['product_class_id'])) : '';

                $arrClassCats2['price02'] = strlen($arrProductsClass['price02']) ? number_format(SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductsClass['price02'], $productId, $arrProductsClass['product_class_id'])) : '';

                $arrClassCats2['plg_memberprice_price03'] = strlen($arrProductsClass['plg_memberprice_price03']) ? number_format(SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProductsClass['plg_memberprice_price03'], $productId, $arrProductsClass['product_class_id'])) : '';

                // ポイント
                $objCustomer = new SC_Customer_Ex();
                if ($arrProductsClass['plg_memberprice_price03'] != "" && !empty($arrProductsClass['plg_memberprice_price03'])) {
                    $arrClassCats2['point'] = number_format(SC_Utils_Ex::sfPrePoint($arrProductsClass['plg_memberprice_price03'], $arrProductsClass['point_rate']));
                } else {
                    $arrClassCats2['point'] = number_format(SC_Utils_Ex::sfPrePoint($arrProductsClass['price02'], $arrProductsClass['point_rate']));
                }

                // 商品コード
                $arrClassCats2['product_code'] = $arrProductsClass['product_code'];
                // 商品規格ID
                $arrClassCats2['product_class_id'] = $arrProductsClass['product_class_id'];
                // 商品種別
                $arrClassCats2['product_type'] = $arrProductsClass['product_type_id'];

                // #929(GC8 規格のプルダウン順序表示不具合)対応のため、2次キーは「#」を前置
                if (!$this->classCat1_find[$productId]) {
                    $classcategory_id1 = '__unselected2';
                }
                $classCategories[$classcategory_id1]['#'] = array(
                    'classcategory_id2' => '',
                    'name' => '選択してください',
                );
                $classCategories[$classcategory_id1]['#' . $classcategory_id2] = $arrClassCats2;
            }

            $this->classCategories[$productId] = $classCategories;

            // 規格1
            $this->classCats1[$productId] = $classCats1;
        }
    }

    /**
     * SC_Query インスタンスに設定された検索条件を使用して商品規格を取得する.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $params 検索パラメーターの配列
     * @return array 商品規格の配列
     */
    public function getProductsClassByQuery(&$objQuery, $params)
    {
        // 末端の規格を取得
        $col = <<< __EOS__
            T1.product_id,
            T1.stock,
            T1.stock_unlimited,
            T1.sale_limit,
            T1.price01,
            T1.price02,
            T1.plg_memberprice_price03,
            T1.point_rate,
            T1.product_code,
            T1.product_class_id,
            T1.del_flg,
            T1.product_type_id,
            T1.down_filename,
            T1.down_realfilename,
            T3.name AS classcategory_name1,
            T3.rank AS rank1,
            T4.name AS class_name1,
            T4.class_id AS class_id1,
            T1.classcategory_id1,
            T1.classcategory_id2,
            dtb_classcategory2.name AS classcategory_name2,
            dtb_classcategory2.rank AS rank2,
            dtb_class2.name AS class_name2,
            dtb_class2.class_id AS class_id2
__EOS__;
        $table = <<< __EOS__
            dtb_products_class T1
            LEFT JOIN dtb_classcategory T3
                ON T1.classcategory_id1 = T3.classcategory_id
            LEFT JOIN dtb_class T4
                ON T3.class_id = T4.class_id
            LEFT JOIN dtb_classcategory dtb_classcategory2
                ON T1.classcategory_id2 = dtb_classcategory2.classcategory_id
            LEFT JOIN dtb_class dtb_class2
                ON dtb_classcategory2.class_id = dtb_class2.class_id
__EOS__;

        $objQuery->setOrder('T3.rank DESC, dtb_classcategory2.rank DESC'); // XXX
        $arrRet = $objQuery->select($col, $table, '', $params);

        return $arrRet;
    }

    /**
     * 商品情報の配列に, 税込金額を設定して返す.
     *
     * この関数は, 主にスマートフォンで使用します.
     *
     * @param array $arrProducts 商品情報の配列
     * @return array 税込金額を設定した商品情報の配列
     */
    static function setPriceTaxTo(&$arrProducts)
    {
        foreach ($arrProducts as &$arrProduct) {
            $arrProduct['price01_min_format'] = number_format($arrProduct['price01_min']);
            $arrProduct['price01_max_format'] = number_format($arrProduct['price01_max']);
            $arrProduct['price02_min_format'] = number_format($arrProduct['price02_min']);
            $arrProduct['price02_max_format'] = number_format($arrProduct['price02_max']);
            $arrProduct['plg_memberprice_price03_min_format'] = number_format($arrProduct['plg_memberprice_price03_min']);
            $arrProduct['plg_memberprice_price03_max_format'] = number_format($arrProduct['plg_memberprice_price03_max']);

            SC_Product_Ex::setIncTaxToProduct($arrProduct);

            $arrProduct['price01_min_inctax_format'] = number_format($arrProduct['price01_min_inctax']);
            $arrProduct['price01_max_inctax_format'] = number_format($arrProduct['price01_max_inctax']);
            $arrProduct['price02_min_inctax_format'] = number_format($arrProduct['price02_min_inctax']);
            $arrProduct['price02_max_inctax_format'] = number_format($arrProduct['price02_max_inctax']);
            $arrProduct['plg_memberprice_price03_min_inctax_format'] = number_format($arrProduct['plg_memberprice_price03_min_inctax']);
            $arrProduct['plg_memberprice_price03_max_inctax_format'] = number_format($arrProduct['plg_memberprice_price03_max_inctax']);

            // @deprecated 2.12.4
            // 旧バージョン互換用
            // 本来は、税額の代入で使用すべきキー名。
            $arrProduct['price01_min_tax_format'] = & $arrProduct['price01_min_inctax_format'];
            $arrProduct['price01_max_tax_format'] = & $arrProduct['price01_max_inctax_format'];
            $arrProduct['price02_min_tax_format'] = & $arrProduct['price02_min_inctax_format'];
            $arrProduct['price02_max_tax_format'] = & $arrProduct['price02_max_inctax_format'];
            $arrProduct['plg_memberprice_price03_min_tax_format'] = & $arrProduct['plg_memberprice_price03_min_inctax_format'];
            $arrProduct['plg_memberprice_price03_max_tax_format'] = & $arrProduct['plg_memberprice_price03_max_inctax_format'];
        }
        // @deprecated 2.12.4
        // 旧バージョン互換用
        // 現在は参照渡しで戻せる
        return $arrProducts;
    }

    /**
     * 商品情報の配列に税込金額を設定する
     *
     * @param array $arrProducts 商品情報の配列
     * @return void
     */
    static function setIncTaxToProduct(&$arrProduct)
    {
        $arrProduct['price01_min_inctax'] = isset($arrProduct['price01_min']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price01_min'], $arrProduct['product_id']) : null;
        $arrProduct['price01_max_inctax'] = isset($arrProduct['price01_max']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price01_max'], $arrProduct['product_id']) : null;
        $arrProduct['price02_min_inctax'] = isset($arrProduct['price02_min']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price02_min'], $arrProduct['product_id']) : null;
        $arrProduct['price02_max_inctax'] = isset($arrProduct['price02_max']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['price02_max'], $arrProduct['product_id']) : null;
        $arrProduct['plg_memberprice_price03_min_inctax'] = isset($arrProduct['plg_memberprice_price03_min']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['plg_memberprice_price03_min'], $arrProduct['product_id']) : null;
        $arrProduct['plg_memberprice_price03_max_inctax'] = isset($arrProduct['plg_memberprice_price03_max']) ? SC_Helper_TaxRule_Ex::sfCalcIncTax($arrProduct['plg_memberprice_price03_max'], $arrProduct['product_id']) : null;
    }

    /**
     * 商品詳細の SQL を取得する.
     *
     * @param string $where_products_class 商品規格情報の WHERE 句
     * @return string 商品詳細の SQL
     */
    public function alldtlSQL($where_products_class = '')
    {
        if (!SC_Utils_Ex::isBlank($where_products_class)) {
            $where_products_class = 'AND (' . $where_products_class . ')';
        }
        /*
         * point_rate, deliv_fee は商品規格(dtb_products_class)ごとに保持しているが,
         * 商品(dtb_products)ごとの設定なので MAX のみを取得する.
         */
        $sql = <<< __EOS__
            (
                SELECT
                     dtb_products.product_id
                    ,dtb_products.name
                    ,dtb_products.maker_id
                    ,dtb_products.status
                    ,dtb_products.comment1
                    ,dtb_products.comment2
                    ,dtb_products.comment3
                    ,dtb_products.comment4
                    ,dtb_products.comment5
                    ,dtb_products.comment6
                    ,dtb_products.note
                    ,dtb_products.main_list_comment
                    ,dtb_products.main_list_image
                    ,dtb_products.main_comment
                    ,dtb_products.main_image
                    ,dtb_products.main_large_image
                    ,dtb_products.sub_title1
                    ,dtb_products.sub_comment1
                    ,dtb_products.sub_image1
                    ,dtb_products.sub_large_image1
                    ,dtb_products.sub_title2
                    ,dtb_products.sub_comment2
                    ,dtb_products.sub_image2
                    ,dtb_products.sub_large_image2
                    ,dtb_products.sub_title3
                    ,dtb_products.sub_comment3
                    ,dtb_products.sub_image3
                    ,dtb_products.sub_large_image3
                    ,dtb_products.sub_title4
                    ,dtb_products.sub_comment4
                    ,dtb_products.sub_image4
                    ,dtb_products.sub_large_image4
                    ,dtb_products.sub_title5
                    ,dtb_products.sub_comment5
                    ,dtb_products.sub_image5
                    ,dtb_products.sub_large_image5
                    ,dtb_products.sub_title6
                    ,dtb_products.sub_comment6
                    ,dtb_products.sub_image6
                    ,dtb_products.sub_large_image6
                    ,dtb_products.del_flg
                    ,dtb_products.creator_id
                    ,dtb_products.create_date
                    ,dtb_products.update_date
                    ,dtb_products.deliv_date_id
                    ,T4.product_code_min
                    ,T4.product_code_max
                    ,T4.price01_min
                    ,T4.price01_max
                    ,T4.price02_min
                    ,T4.price02_max
                    ,T4.plg_memberprice_price03_min
                    ,T4.plg_memberprice_price03_max
                    ,T4.stock_min
                    ,T4.stock_max
                    ,T4.stock_unlimited_min
                    ,T4.stock_unlimited_max
                    ,T4.point_rate
                    ,T4.deliv_fee
                    ,T4.class_count
                    ,dtb_maker.name AS maker_name
                FROM dtb_products
                    JOIN (
                        SELECT product_id,
                            MIN(product_code) AS product_code_min,
                            MAX(product_code) AS product_code_max,
                            MIN(price01) AS price01_min,
                            MAX(price01) AS price01_max,
                            MIN(price02) AS price02_min,
                            MAX(price02) AS price02_max,
                            MIN(plg_memberprice_price03) AS plg_memberprice_price03_min,
                            MAX(plg_memberprice_price03) AS plg_memberprice_price03_max,
                            MIN(stock) AS stock_min,
                            MAX(stock) AS stock_max,
                            MIN(stock_unlimited) AS stock_unlimited_min,
                            MAX(stock_unlimited) AS stock_unlimited_max,
                            MAX(point_rate) AS point_rate,
                            MAX(deliv_fee) AS deliv_fee,
                            COUNT(*) as class_count
                        FROM dtb_products_class
                        WHERE del_flg = 0 $where_products_class
                        GROUP BY product_id
                    ) AS T4
                        ON dtb_products.product_id = T4.product_id
                    LEFT JOIN dtb_maker
                        ON dtb_products.maker_id = dtb_maker.maker_id
            ) AS alldtl
__EOS__;
        return $sql;
    }

    /**
     * 商品規格詳細の SQL を取得する.
     *
     * MEMO: 2.4系 vw_product_classに相当(?)するイメージ
     *
     * @param string $where 商品詳細の WHERE 句
     * @return string 商品規格詳細の SQL
     */
    public function prdclsSQL($where = '')
    {
        $where_clause = '';
        if (!SC_Utils_Ex::isBlank($where)) {
            $where_clause = ' WHERE ' . $where;
        }
        $sql = <<< __EOS__
        (
            SELECT dtb_products.*,
                dtb_products_class.product_class_id,
                dtb_products_class.product_type_id,
                dtb_products_class.product_code,
                dtb_products_class.stock,
                dtb_products_class.stock_unlimited,
                dtb_products_class.sale_limit,
                dtb_products_class.price01,
                dtb_products_class.price02,
                dtb_products_class.plg_memberprice_price03,
                dtb_products_class.deliv_fee,
                dtb_products_class.point_rate,
                dtb_products_class.down_filename,
                dtb_products_class.down_realfilename,
                dtb_products_class.classcategory_id1 AS classcategory_id, /* 削除 */
                dtb_products_class.classcategory_id1,
                dtb_products_class.classcategory_id2 AS parent_classcategory_id, /* 削除 */
                dtb_products_class.classcategory_id2,
                Tcc1.class_id as class_id,
                Tcc1.name as classcategory_name,
                Tcc2.class_id as parent_class_id,
                Tcc2.name as parent_classcategory_name
            FROM dtb_products
                LEFT JOIN dtb_products_class
                    ON dtb_products.product_id = dtb_products_class.product_id
                LEFT JOIN dtb_classcategory as Tcc1
                    ON dtb_products_class.classcategory_id1 = Tcc1.classcategory_id
                LEFT JOIN dtb_classcategory as Tcc2
                    ON dtb_products_class.classcategory_id2 = Tcc2.classcategory_id
            $where_clause
        ) as prdcls
__EOS__;
        return $sql;
    }

}
