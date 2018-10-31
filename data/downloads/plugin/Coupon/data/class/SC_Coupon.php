<?php

/**
 * クーポン管理の共通クラス.
 *
 * 主に static 参照するユーティリティ系の関数群
 *
 */

class SC_Coupon {

    // }}}
    // {{{ functions

    /* DBに渡す数値のチェック
     * 10桁以上はオーバーフローエラーを起こすので。
     */
    function sfCheckNumLength( $value ){
        if ( ! is_numeric($value)  ){
            return false;
        }

        if ( strlen($value) > 9 ) {
            return false;
        }

        return true;
    }

    // 2013.03.04 SEED クーポン割引金額を返す
    function sfCouponDiscount($target_price, $total_price, $arrCouponInfo, $discount_rule) {

        if( $arrCouponInfo["discount_type"] == 0 ) {
            //定額
            $discount_price = $arrCouponInfo["discount_price"];

            if( ($total_price-$discount_price) < 1 ) {
                $discount_price = $total_price -1;
            } elseif ( ($target_price - $discount_price) < 1 ) {
                $discount_price = $target_price;
            }
            $real_discount = (float)($discount_price) / $total_price;
            $discount_percent = round($real_discount * 100.0, 1);
        } else {
            //定率
            $discount_percent = $arrCouponInfo["discount_percent"];
            $real_discount =  $discount_percent / 100;
            $discount_price = $target_price * $real_discount;
        }

        switch($discount_rule) {
            case 1: // 四捨五入
                    $discount_price = round($discount_price);
                    break;
            case 2: // 切捨て
                    $discount_price = floor($discount_price);
                    break;
            case 3: // 切り上げ
                    $discount_price = ceil($discount_price);
                    break;
            default: // デフォルト：切り上げ
                    $discount_price = ceil($discount_price);
                    break;
        }

        return array($discount_price, $discount_percent);
    }


    /**
     * 合計金額による割引金額を返す
     * 2013.03.04 SEED 
     *
     * @param  integer $amount  割引金額
     * @param  integer $percent 割引率（％）
     * @param  integer $total   割引対象金額
     * @return なし（参照渡しで返す）
     */
    function sfGetDiscountData( &$amount, &$percent, $total ) {

        $col = "amount, percent, rule";
        $from = "cst_dtb_amount_discount";
        $objQuery = new SC_Query_Ex();
        $objQuery->setorder("amount");
        $arrAmountDiscount = $objQuery->select($col, $from);

        $cnt = count($arrAmountDiscount) ;
        if( $cnt>0 ) {
            $max_amount = 0 ;
            for($i=0; $i<$cnt; $i++ ) {
                if( $max_amount <= $total
                 && $arrAmountDiscount[$i]['amount'] <= $total ) {
                    $amount = ( $total / 100 ) * $arrAmountDiscount[$i]['percent'] ;
                    // 割引額端数処理
                    switch($arrAmountDiscount[$i]['rule']) {
                        case 1:  $amount = round($amount); break; // 四捨五入
                        case 2:  $amount = floor($amount); break; // 切り捨て
                        case 3:  $amount = ceil($amount);  break; // 切り上げ
                        default: $amount = ceil($amount);  break; // デフォルト:切り上げ
                    }
                    $percent = $arrAmountDiscount[$i]['percent'] ;
                    $max_amount = $arrAmountDiscount[$i]['amount'] ;
                }
            }
        }

        return ;
    }


}
?>
