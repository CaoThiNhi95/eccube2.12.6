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

class plg_MemberPrice_SC_Helper_Purchase extends SC_Helper_Purchase
{

    /**
     * 配送商品を設定する.
     *
     * @param  integer $shipping_id      配送先ID
     * @param  integer $product_class_id 商品規格ID
     * @param  integer $quantity         数量
     * @return void
     */
    public function setShipmentItemTemp($shipping_id, $product_class_id, $quantity)
    {
        // 配列が長くなるので, リファレンスを使用する
        $arrItems = & $_SESSION['shipping'][$shipping_id]['shipment_item'][$product_class_id];

        $arrItems['shipping_id'] = $shipping_id;
        $arrItems['product_class_id'] = $product_class_id;
        $arrItems['quantity'] = $quantity;

        $objProduct = new SC_Product_Ex();

        // カート情報から読みこめば済むと思うが、一旦保留。むしろ、カート情報も含め、セッション情報を縮小すべきかもしれない。
        /*
          $objCartSession = new SC_CartSession_Ex();
          $cartKey = $objCartSession->getKey();
          // 詳細情報を取得
          $cartItems = $objCartSession->getCartList($cartKey);
         */

        if (empty($arrItems['productsClass'])) {
            $product = & $objProduct->getDetailAndProductsClass($product_class_id);
            $arrItems['productsClass'] = $product;
        }

        $arrItems['price'] = $arrItems['productsClass']['price02'];

        $objCustomer = new SC_Customer_Ex();
        if ($objCustomer->isLoginSuccess(true) && isset($arrItems['productsClass']['plg_memberprice_price03'])) {
            $arrItems['price'] = $arrItems['productsClass']['plg_memberprice_price03'];
        }


        $inctax = SC_Helper_TaxRule_Ex::sfCalcIncTax($arrItems['price'], $arrItems['productsClass']['product_id'], $arrItems['productsClass']['product_class_id']);
        $arrItems['total_inctax'] = $inctax * $arrItems['quantity'];
    }

}
