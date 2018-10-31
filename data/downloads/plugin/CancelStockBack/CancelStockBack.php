<?php
/*
 * CancelStockBack
 * Copyright (C) 2012 Bratech CO.,LTD. All Rights Reserved.
 * http://www.bratech.co.jp/
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
 

require_once PLUGIN_UPLOAD_REALDIR . "CancelStockBack/plg_CancelStockBack_SC_Helper_Purchase_Ext.php";

/**
 * プラグインのメインクラス
 *
 * @package CancelStockBack
 * @author Bratech CO.,LTD.
 * @version $Id: $
 */
class CancelStockBack extends SC_Plugin_Base {

    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }
    
    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        // ロゴファイルをhtmlディレクトリにコピーします.
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/logo.png", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");
		
		$objQuery =& SC_Query_Ex::getSingletonInstance();
		$objQuery->update("dtb_plugin", array("free_field1" => ORDER_CANCEL),"plugin_code = ?",array("CancelStockBack"));
    }
    
    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
		//nop
    }
    
    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
		//nop
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
		//nop
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     * 
     * @param SC_Helper_Plugin $objHelperPlugin 
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
		$objHelperPlugin->addAction("prefilterTransform",array(&$this,"prefilterTransform"),$this->arrSelfInfo['priority']);
		$objHelperPlugin->addAction("loadClassFileChange", array(&$this, "loadClassFileChange"), $this->arrSelfInfo['priority']);
		$objHelperPlugin->addAction("LC_Page_Admin_Order_Edit_action_before", array(&$this, "admin_order_edit_before"), $this->arrSelfInfo['priority']);
    }
	
	function loadClassFileChange(&$classname,&$classpath){
		if($classname == 'SC_Helper_Purchase_Ex'){
			$classpath = PLUGIN_UPLOAD_REALDIR . "CancelStockBack/plg_CancelStockBack_SC_Helper_Purchase.php";
			
			$classname = "plg_CancelStockBack_SC_Helper_Purchase";
		}	
	}
	
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        switch($objPage->arrPageLayout['device_type_id']) {
            case DEVICE_TYPE_PC:
            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_MOBILE:
				break;
            case DEVICE_TYPE_ADMIN:
			default:
                // 受注管理・編集画面
                if(strpos($filename, "order/edit.tpl") !== false) {
                    $objTransform->select("div#order table.form tr td span.attention",1,false)->replaceElement("");
				}
                if(strpos($filename, "order/status.tpl") !== false) {
                    $objTransform->select("div#order span.attention",0,false)->replaceElement("");
				}
                break;
        }
        $source = $objTransform->getHTML();
    }
	
	
    /**
     * @param LC_Page_Admin_Order_Edit $objPage 受注管理のページクラス
     * @return void
     */
    function admin_order_edit_before($objPage) {
		$orderId = $_POST['order_id'];

        if($objPage->getMode() == 'edit' && $orderId > 0) {
			$arrCancelPrevStatus = plg_CancelStockBack_SC_Helper_Purchase_Ext::getCancelStatus('prev');
			$arrCancelPostStatus = plg_CancelStockBack_SC_Helper_Purchase_Ext::getCancelStatus('post');
			$newStatus = $_POST['status'];
			
	        $objQuery =& SC_Query_Ex::getSingletonInstance();
    	    $arrOrderOld = $objQuery->getRow('status, add_point, use_point, customer_id', 'dtb_order', 'order_id = ?', array($orderId));
			if (in_array($arrOrderOld['status'],$arrCancelPostStatus) && ((in_array($newStatus,$arrCancelPrevStatus) && count($arrCancelPrevStatus) > 0) || (!in_array($newStatus,$arrCancelPostStatus) && count($arrCancelPrevStatus) == 0))) {
				if(plg_CancelStockBack_SC_Helper_Purchase_Ext::checkStock($orderId) === false){
					$objPage->arrErr['status'] = "在庫が不足しているため変更できません。<br>";
					$_POST['mode'] = $_GET['mode'] = $_REQUEST['mode'] = "order_id";
				}
			}	
        }
    }	
	
}
?>
