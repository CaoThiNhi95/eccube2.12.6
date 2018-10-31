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

require_once CLASS_REALDIR . 'SC_CartSession.php';

class plg_MemberPrice_SC_CartSession extends SC_CartSession
{

    /**
     * セッション中の商品情報データの調整。
     * productsClass項目から、不必要な項目を削除する。
     */
    public function adjustSessionProductsClass(&$arrProductsClass)
    {
        $arrNecessaryItems = array(
            'product_id' => true,
            'product_class_id' => true,
            'name' => true,
            'price02' => true,
            'plg_memberprice_price03' => true,
            'point_rate' => true,
            'main_list_image' => true,
            'main_image' => true,
            'product_code' => true,
            'stock' => true,
            'stock_unlimited' => true,
            'sale_limit' => true,
            'class_name1' => true,
            'classcategory_name1' => true,
            'class_name2' => true,
            'classcategory_name2' => true,
        );

        // 必要な項目以外を削除。
        foreach ($arrProductsClass as $key => $value) {
            if (!isset($arrNecessaryItems[$key])) {
                unset($arrProductsClass[$key]);
            }
        }
    }

    /**
     * 商品種別ごとにカート内商品の一覧を取得する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return array カート内商品一覧の配列
     */
    public function getCartList($productTypeId, $pref_id = 0, $country_id = 0)
    {
        $objProduct = new SC_Product_Ex();
        $max = $this->getMax($productTypeId);
        $arrRet = array();
        /*

          $const_name = '_CALLED_SC_CARTSESSION_GETCARTLIST_' . $productTypeId;
          if (defined($const_name)) {
          $is_first = true;
          } else {
          define($const_name, true);
          $is_first = false;
          }

         */
        for ($i = 0; $i <= $max; $i++) {
            if (isset($this->cartSession[$productTypeId][$i]['cart_no']) && $this->cartSession[$productTypeId][$i]['cart_no'] != '') {

                // 商品情報は常に取得
                // TODO: 同一インスタンス内では1回のみ呼ぶようにしたい
                // TODO: ここの商品の合計処理は getAllProductsTotalや getAllProductsTaxとで類似重複なので統一出来そう
                /*
                  // 同一セッション内では初回のみDB参照するようにしている
                  if (!$is_first) {
                  $this->setCartSession4getCartList($productTypeId, $i);
                  }
                 */

                $this->cartSession[$productTypeId][$i]['productsClass'] = & $objProduct->getDetailAndProductsClass($this->cartSession[$productTypeId][$i]['id']);
                $objCustomer = new SC_Customer_Ex();
                if ($objCustomer->isLoginSuccess(true) === true && strlen($this->cartSession[$productTypeId][$i]['productsClass']['plg_memberprice_price03']) > 0) {
                    $price = $this->cartSession[$productTypeId][$i]['productsClass']['plg_memberprice_price03'];
                } else {
                    $price = $this->cartSession[$productTypeId][$i]['productsClass']['price02'];
                }
                $this->cartSession[$productTypeId][$i]['price'] = $price;

                $this->cartSession[$productTypeId][$i]['point_rate'] = $this->cartSession[$productTypeId][$i]['productsClass']['point_rate'];

                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

                $arrTaxRule = SC_Helper_TaxRule_Ex::getTaxRule(
                                $this->cartSession[$productTypeId][$i]['productsClass']['product_id'], $this->cartSession[$productTypeId][$i]['productsClass']['product_class_id'], $pref_id, $country_id);
                $incTax = $price + SC_Helper_TaxRule_Ex::calcTax($price, $arrTaxRule['tax_rate'], $arrTaxRule['tax_rule'], $arrTaxRule['tax_adjust']);

                $total = $incTax * $quantity;
                $this->cartSession[$productTypeId][$i]['price_inctax'] = $incTax;
                $this->cartSession[$productTypeId][$i]['total_inctax'] = $total;
                $this->cartSession[$productTypeId][$i]['tax_rate'] = $arrTaxRule['tax_rate'];
                $this->cartSession[$productTypeId][$i]['tax_rule'] = $arrTaxRule['tax_rule'];
                $this->cartSession[$productTypeId][$i]['tax_adjust'] = $arrTaxRule['tax_adjust'];

                $arrRet[] = $this->cartSession[$productTypeId][$i];

                // セッション変数のデータ量を抑制するため、一部の商品情報を切り捨てる
                // XXX 上で「常に取得」するのだから、丸ごと切り捨てて良さそうにも感じる。
                $this->adjustSessionProductsClass($this->cartSession[$productTypeId][$i]['productsClass']);
            }
        }
        return $arrRet;
    }

}
