<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright (C) 2013 INA Corporation. All Rights Reserved.
 *
 * http://www.e-ina.co.jp/
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

require_once CLASS_REALDIR . 'SC_Product.php';

class SC_Product_Dev extends SC_Product
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
	function lists(&$objQuery)
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
			,sub_comment1
			,price01_min
			,price01_max
			,price02_min
			,price02_max
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
}
