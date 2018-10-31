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

class plg_Sale_SC_Helper_CSV extends SC_Helper_CSV {
    /**
     * CSVファイルを送信する
     *
     * @param integer $csv_id CSVフォーマットID
     * @param string $where WHERE条件文
     * @param array $arrVal プリペアドステートメントの実行時に使用される配列。配列の要素数は、クエリ内のプレースホルダの数と同じでなければなりません。
     * @param string $order ORDER文
     * @param boolean $is_download true:ダウンロード用出力までさせる false:CSVの内容を返す(旧方式、メモリを食います。）
     * @return mixed $is_download = true時 成功失敗フラグ(boolean) 、$is_downalod = false時 string
     */
    function sfDownloadCsv($csv_id, $where = '', $arrVal = array(), $order = '', $is_download = false) {
        // 実行時間を制限しない
        @set_time_limit(0);

        // CSV出力タイトル行の作成
        $arrOutput = SC_Utils_Ex::sfSwapArray($this->sfGetCsvOutput($csv_id, 'status = ' . CSV_COLUMN_STATUS_FLG_ENABLE));
        if (count($arrOutput) <= 0) return false; // 失敗終了
        $arrOutputCols = $arrOutput['col'];

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder($order);
        $cols = SC_Utils_Ex::sfGetCommaList($arrOutputCols, true);

        // TODO: 固有処理 なんかエレガントな処理にしたい
        if ($csv_id == '1') {
            //商品の場合
            $objProduct = new SC_Product_Ex();
            // このWhereを足さないと無効な規格も出力される。現行仕様と合わせる為追加。
            $inner_where = 'dtb_products_class.del_flg = 0';
/* CUORECUSTOM START */
            $sql = $objQuery->getSql($cols, $objProduct->prdclsSQL($inner_where, "csv"), $where);
/* CUORECUSTOM END */
        } else if ($csv_id == '2') {
            // 会員の場合
            $sql = 'SELECT ' . $cols . ' FROM dtb_customer ' . $where;
        } else if ($csv_id == '3') {
            // 注文の場合
            $sql = 'SELECT ' . $cols . ' FROM dtb_order ' . $where;
        } else if ($csv_id == '4') {
            // レビューの場合
            $sql = 'SELECT ' . $cols . ' FROM dtb_review AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ' . $where;
        } else if ($csv_id == '5') {
            // カテゴリの場合
            $sql = 'SELECT ' . $cols . ' FROM dtb_category ' . $where;
        }
        // 固有処理ここまで
        return $this->sfDownloadCsvFromSql($sql, $arrVal, $this->arrSubnavi[$csv_id], $arrOutput['disp_name'], $is_download);
    }
}
?>
