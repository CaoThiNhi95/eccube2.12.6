<?php
/*
 * FreeShipping
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
 
require_once PLUGIN_UPLOAD_REALDIR . "FreeShipping/plg_FreeShipping_Util.php";

class plg_FreeShipping_SC_CartSession extends SC_CartSession{
    /**
     * 送料無料条件を満たすかどうかチェックする
     *
     * @param integer $productTypeId 商品種別ID
     * @return boolean 送料無料の場合 true
     */
    function isDelivFree($productTypeId) {
        $objDb = new SC_Helper_DB_Ex();

        $subtotal = $this->getAllProductsTotal($productTypeId);

        // 送料無料の購入数が設定されている場合
        if (DELIV_FREE_AMOUNT > 0) {
            // 商品の合計数量
            $total_quantity = $this->getTotalQuantity($productTypeId);

            if ($total_quantity >= DELIV_FREE_AMOUNT) {
                return true;
            }
        }

        // 送料無料条件が設定されている場合
        $arrInfo = $objDb->sfGetBasisData();
        if ($arrInfo['free_rule'] > 0) {
            // 小計が送料無料条件以上の場合
            if ($subtotal >= $arrInfo['free_rule']) {
                return true;
            }
        }
		
		// 送料無料対象商品の判定
		if($productTypeId != PRODUCT_TYPE_DOWNLOAD){
			$method = plg_FreeShipping_Util::getConfig("method");
			$objQuery =& SC_Query_Ex::getSingletonInstance();
			
			$max = $this->getMax($productTypeId);
			$flg_cnt = 0;
			for ($i = 1; $i <= $max; $i++) {
				// 商品送料
				if($this->cartSession[$productTypeId][$i]['productsClass']['product_id'] > 0){
					$freeshipping_flg = $objQuery->get('plg_freeshipping_flg','dtb_products', 'product_id = ?', array($this->cartSession[$productTypeId][$i]['productsClass']['product_id']));
					if($freeshipping_flg == 1)$flg_cnt++;
				}
			}
			if($method == 0){
				if($flg_cnt > 0){
					return true;
				}
			}elseif($method == 1){
				$max_cnt = count($this->getAllProductClassID($productTypeId));
				if($flg_cnt == $max_cnt){
					return true;
				}
			}
		}

        return false;
    }
}