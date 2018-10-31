<?php
/*
 * CancelStockBack
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

class plg_CancelStockBack_SC_Helper_Purchase_Ext extends SC_Helper_Purchase{
	function backStock($order_id){
		$objQuery =& SC_Query_Ex::getSingletonInstance();
		
        $arrOrderDetail = $this->getOrderDetail($order_id);
        foreach ($arrOrderDetail as $arrDetail) {
            $objQuery->update('dtb_products_class', array(),
                              'product_class_id = ? AND stock_unlimited <> ?', array($arrDetail['product_class_id'],1),
                              array('stock' => 'stock + ?'), array($arrDetail['quantity']));
        }
	}
	
	function reduceStock($order_id){
		$objQuery =& SC_Query_Ex::getSingletonInstance();
		
        $arrOrderDetail = $this->getOrderDetail($order_id);
        foreach ($arrOrderDetail as $arrDetail) {
            $objQuery->update('dtb_products_class', array(),
                              'product_class_id = ? AND stock_unlimited <> ?', array($arrDetail['product_class_id'],1),
                              array('stock' => 'stock - ?'), array($arrDetail['quantity']));
        }
	}
	
	function checkStock($order_id){
		$objQuery =& SC_Query_Ex::getSingletonInstance();
		
		$ret = true;
        $arrOrderDetail = SC_Helper_Purchase::getOrderDetail($order_id);
        foreach ($arrOrderDetail as $arrDetail) {
            $cnt = $objQuery->get('count(product_class_id)','dtb_products_class',
                              'product_class_id = ? AND stock_unlimited <> ? AND stock < ?', array($arrDetail['product_class_id'],1,$arrDetail['quantity']));
			if($cnt > 0){
				$ret = false;
				break;
			}
        }
		return $ret;
	}		
	
	function getCancelStatus($mode='post'){
		$objQuery =& SC_Query_Ex::getSingletonInstance();
		if($mode == 'prev'){
			$cancel_status = $objQuery->get("free_field2","dtb_plugin","plugin_code = ?",array('CancelStockBack'));
		}else{
			$cancel_status = $objQuery->get("free_field1","dtb_plugin","plugin_code = ?",array('CancelStockBack'));
		}
		if(!is_null($cancel_status) && $cancel_status != ''){
			return explode(',',$cancel_status);
		}else{
			return array();
		}
	}
}