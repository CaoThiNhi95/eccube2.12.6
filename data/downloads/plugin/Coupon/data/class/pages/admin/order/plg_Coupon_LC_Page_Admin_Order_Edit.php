<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Ex.php';

/**
 * 受注修正 のページクラス.
 */
class plg_Coupon_LC_Page_Admin_Order_Edit extends LC_Page_Admin_Order_Ex {

	function plg_Coupon_LC_Page_Admin_Order_Edit(){

	}

	function exec(&$objPage){

		if($objPage->arrForm["order_id"]["value"]){
			$this->action($objPage);
		}
	}

    function action(&$objPage) {

    	//dtb_orderの編集
    	$objQuery = new SC_Query();

    	//注文データの取得
    	$order_data = $objQuery->getRow("*","dtb_order","order_id = ?",array($objPage->arrForm["order_id"]["value"]));

    	if($order_data["coupon_discount_price"]){

	    	switch ($this->getMode()){
	    		case 'recalculate':
	   			case 'select_product_detail':
				case 'multiple':
	   			case 'edit':
            		//クーポン分を差し引く
					$objPage->arrForm["payment_total"]["value"] = $objPage->arrForm["payment_total"]["value"] - $order_data["coupon_discount_price"];
					$tmp_total = $objPage->arrForm["payment_total"]["value"];
					break;

				case 'pre_edit':
	    		case 'order_id':
				case 'append_shipping':
				case 'multiple_set_to':

					break;

				case 'add':
				case 'payment':
				case 'deliv':
				case 'delete_product':
				case 'search_customer':

	    			break;

	    	}

    		//クーポン分を差し引く
    		$sqlVal = array();
    		if($tmp_total>0){
    			$sqlVal["payment_total"] = $tmp_total;
    		}else{
    			$sqlVal["payment_total"] = $objPage->arrForm["payment_total"]["value"] - $order_data["coupon_discount_price"];
    		}

    		if($this->getMode()!="pre_edit"&&$this->getMode()!="order_id"){
    			//表示部分
    			//$objPage->arrForm["payment_total"]["value"] = $objPage->arrForm["payment_total"]["value"] - $order_data["coupon_discount_price"];
    		}

    		if($this->getMode()=="edit"){
    			//dtb_orderの更新
    			$objQuery->update("dtb_order",$sqlVal,"order_id = ?",array($objPage->arrForm["order_id"]["value"]));
    		}
    	}

    }
}
