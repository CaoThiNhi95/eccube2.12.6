<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Mail_Ex.php';

/**
 * 受注メール管理 のページクラス.
 */
class plg_Coupon_LC_Page_Admin_Order_Mail extends LC_Page_Admin_Order_Mail_Ex {

	function plg_Coupon_LC_Page_Admin_Order_Mail(){

	}

	function exec(&$objPage){

		$this->action($objPage);
	}

    function action(&$objPage) {


        $post = $_POST;
        //一括送信用の処理
        if (array_key_exists('mail_order_id',$post) and $post['mode'] == 'mail_select'){
            $post['order_id_array'] = implode(',',$post['mail_order_id']);
        } else if(!array_key_exists('order_id_array',$post)){
            $post['order_id_array'] = $post['order_id'];
        }

        //一括送信処理変数チェック(ここですべきかは課題)
        if (preg_match("/^[0-9|\,]*$/",$post['order_id_array'])){
            $this->order_id_array = $post['order_id_array'];
        } else {
            //エラーで元に戻す
            SC_Response_Ex::sendRedirect(ADMIN_ORDER_URLPATH);
            SC_Response_Ex::actionExit();
        }


        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // POST値の取得
        $objFormParam->setParam($post);
        $objFormParam->convParam();
        $this->tpl_order_id = $objFormParam->getValue('order_id');

        // メールの送信
        // 注文受付メール(複数受注ID対応)
        $order_id_array = explode(',',$this->order_id_array);
        foreach ($order_id_array as $order_id){
            $objMail = new SC_Helper_Mail_Ex();
            $objMail->setPage($this);
            $objSendMail = $objMail->sfSendOrderMail($order_id,
            $objFormParam->getValue('template_id'),
            $objFormParam->getValue('subject'),
            $objFormParam->getValue('header'),
            $objFormParam->getValue('footer'),
            true,
            true);
        }
    }

    /**
     * パラメーター情報の初期化
     * @param SC_FormParam $objFormParam
     */
    function lfInitParam(&$objFormParam) {
    	// 検索条件のパラメーターを初期化
    	parent::lfInitParam($objFormParam);
    	$objFormParam->addParam('テンプレート', 'template_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
    	$objFormParam->addParam('メールタイトル', 'subject', STEXT_LEN, 'KVa',  array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
    	$objFormParam->addParam('ヘッダー', 'header', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
    	$objFormParam->addParam('フッター', 'footer', LTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK', 'SPTAB_CHECK'));
    }

}
