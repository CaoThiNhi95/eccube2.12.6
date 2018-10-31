<?php
/*
 * FreeShipping
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
 
require_once PLUGIN_UPLOAD_REALDIR . "FreeShipping/plg_FreeShipping_Util.php";

/**
 * プラグインのメインクラス
 *
 * @package FreeShipping
 * @author Bratech CO.,LTD.
 * @version $Id: $
 */
class FreeShipping extends SC_Plugin_Base {

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
		$objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->query("ALTER TABLE dtb_products ADD COLUMN plg_freeshipping_flg smallint DEFAULT 0");

        // ロゴファイルをhtmlディレクトリにコピーします.
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/logo.png", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");
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
		//テーブル削除
		$objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->query("ALTER TABLE dtb_products DROP COLUMN plg_freeshipping_flg");
		

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
		$objQuery = SC_Query_Ex::getSingletonInstance();

        // dtb_csvテーブルにレコードを追加
		$sqlval_dtb_csv = array();
		$max = $objQuery->max('no','dtb_csv')+1;
		$next = $objQuery->nextVal('dtb_csv_no');
		if($max > $next){
			$no = $max;
		}else{
			$no = $next;
		}
		$sqlval_dtb_csv['no'] = $no;
		$sqlval_dtb_csv['csv_id'] = 1;
		$sqlval_dtb_csv['col'] = 'plg_freeshipping_flg';
		$sqlval_dtb_csv['disp_name'] = '送料無料対象商品設定';
		$sqlval_dtb_csv['rw_flg'] = 1;
		$sqlval_dtb_csv['status'] = 2;
		$sqlval_dtb_csv['create_date'] = 'CURRENT_TIMESTAMP';
		$sqlval_dtb_csv['update_date'] = 'CURRENT_TIMESTAMP';
		$sqlval_dtb_csv['mb_convert_kana_option'] = "n";
		$sqlval_dtb_csv['size_const_type'] = "INT_LEN";
		$sqlval_dtb_csv['error_check_types'] = "NUM_CHECK,MAX_LENGTH_CHECK";
		$objQuery->insert("dtb_csv", $sqlval_dtb_csv);
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
		$objQuery = SC_Query_Ex::getSingletonInstance();

		// dtb_csvテーブルからレコードを削除
		$objQuery->delete("dtb_csv","col = ?",array('plg_freeshipping_flg'));
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
		$objHelperPlugin->addAction("SC_FormParam_construct",array(&$this,"addParam"),$this->arrSelfInfo['priority']);
		$objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_after",array(&$this,"admin_products_product_after"),$this->arrSelfInfo['priority']);
    }

	
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
		$template_dir = PLUGIN_UPLOAD_REALDIR . "FreeShipping/templates/";
        switch($objPage->arrPageLayout['device_type_id']) {
			case DEVICE_TYPE_PC:
			case DEVICE_TYPE_SMARTPHONE:
			case DEVICE_TYPE_MOBILE:
				break;		
            // 端末種別：管理画面
            case DEVICE_TYPE_ADMIN:
			default:
				$template_dir .= "admin/";
                // 受注管理・編集画面
                if(strpos($filename, "products/product.tpl") !== false) {
					if(plg_FreeShipping_Util::getECCUBEVer() >= 2130){
                    	$objTransform->select("table.form tr",14)->insertAfter(file_get_contents($template_dir ."products/product.tpl"));
					}else{
                    	$objTransform->select("table.form tr",13)->insertAfter(file_get_contents($template_dir ."products/product.tpl"));
					}
				}
                if(strpos($filename, "products/confirm.tpl") !== false) {
					if(plg_FreeShipping_Util::getECCUBEVer() >= 2130){
                    	$objTransform->select("div.contents-main table tr",12)->insertAfter(file_get_contents($template_dir ."products/confirm.tpl"));
					}else{
						$objTransform->select("div.contents-main table tr",11)->insertAfter(file_get_contents($template_dir ."products/confirm.tpl"));
					}
				}				
                break;
        }
        $source = $objTransform->getHTML();
    }
	
	function addParam($class_name,$param){
		if(strpos($class_name,'LC_Page_Admin_Products_Product') !== false){
			$this->addFreeShippingParam($param);
		}
	}
	
	function loadClassFileChange(&$classname,&$classpath){
		if($classname == 'SC_CartSession_Ex'){
			$classpath = PLUGIN_UPLOAD_REALDIR . "FreeShipping/plg_FreeShipping_SC_CartSession.php";
			
			$classname = "plg_FreeShipping_SC_CartSession";
		}
	}
	
    /**
     * @param LC_Page_Admin_Products_Product $objPage 商品管理のページクラス
     * @return void
     */
    function admin_products_product_after($objPage) {
		$objFormParam = new SC_FormParam_Ex();
        switch($objPage->getMode($objPage)) {
            case "pre_edit":
            case "copy" :
                // 何もしない
                break;
            case "edit":
            case "upload_image":
            case "delete_image":
            case "upload_down":
            case "delete_down":
            case "recommend_select":
            case "confirm_return":
				$this->addFreeShippingParam($objFormParam);
                $objPage->lfInitFormParam($objFormParam, $_POST);
				$arrForm = $objFormParam->getHashArray();
                $objPage->arrForm['plg_freeshipping_flg'] = $arrForm['plg_freeshipping_flg'];
                break;
            case "complete":
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                $table = "dtb_products";
                $where = "product_id = ?";
                $arrParam['plg_freeshipping_flg'] = $_POST['plg_freeshipping_flg'];
                $arrParam['update_date'] = "CURRENT_TIMESTAMP";
                $arrWhere[] = $objPage->arrForm['product_id'];
                $objQuery->update($table, $arrParam, $where, $arrWhere);
                break;
            default:
                break;
        }
    }
	
	function addFreeShippingParam(&$objFormParam){
		$objFormParam->addParam("送料無料対象設定", 'plg_freeshipping_flg', INT_LEN, 'n', array('NUM_CHECK','MAX_LENGTH_CHECK'));
	}
	
}
?>
