<?php

require_once CLASS_EX_REALDIR . 'page_extends/shopping/LC_Page_Shopping_Complete_Ex.php';
require_once CLASS_EX_REALDIR . 'SC_FormParam_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/SC_Coupon.php';

/**
 * ご注文完了 のページクラス.
 *
 * @package Coupon
 */
class plg_Coupon_LC_Page_Shopping_Complete extends LC_Page_Shopping_Complete_Ex {

	function plg_Coupon_LC_Page_Shopping_Complete(){

	}

    //決済モジュールを使用している場合のみ処理を行う
	function exec(&$objPage){
	
	    if($_SESSION['order_id']){
	        $objQuery = new SC_Query_Ex();
	        
            //注文情報の取得
            $order_data = $objQuery->getRow("*","dtb_order","order_id = ?",array($_SESSION['order_id']));

	        
	        //決済モジュールを使用しているか？
            //$this->use_module = SC_Helper_Payment_Ex::useModule($order_data['payment_id']);
            $this->use_module = $this->useModule($order_data['payment_id']);
            
            //決済モジュールを使っている場合のみクーポン履歴作成とメール送信を行う
            if($this->use_module){

                //クーポン履歴の作成
                if($order_data["coupon_discount_price"]){
                    //クーポンコードの取得
                    $temp_data = $objQuery->getRow("*","dtb_order_temp","order_temp_id = ?",array($order_data["order_temp_id"]));
                    
                    //クーポン使用履歴の作成
                    $sqlVal = array();
                    $sqlVal["coupon_used_id"] = $objQuery->nextVal("dtb_coupon_used_coupon_used_id");
       	            $sqlVal["coupon_id"]      = $temp_data["coupon_id"];
       	            $sqlVal["customer_id"]    = $order_data["customer_id"];
       	            $sqlVal["order_id"]       = $order_data["order_id"];
       	            $sqlVal["create_date"]    = "CURRENT_TIMESTAMP";
               	    $objQuery->insert("dtb_coupon_used",$sqlVal);
               	    
                }
                //メールの送信
                $objHelperMail = new SC_Helper_Mail_Ex();
                $template_id = SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE ? 2 : 1;
                $objHelperMail->sfSendOrderMail($order_data['order_id'],$template_id,'','','',true,true);                
            }
	    }
	}
	
    /**
     * 決済モジュールを使用するかどうか.
     *
     * dtb_payment.memo03 に値が入っている場合は決済モジュールと見なす.
     *
     * @param integer $payment_id 支払い方法ID
     * @return boolean 決済モジュールを使用する支払い方法の場合 true
     */
    function useModule($payment_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $memo03 = $objQuery->get('memo03', 'dtb_payment', 'payment_id = ?', array($payment_id));
        return !SC_Utils_Ex::isBlank($memo03);
    }
}