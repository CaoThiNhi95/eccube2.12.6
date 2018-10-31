<?php

/*
 * MemberPrice
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

/**
 * プラグインのメインクラス
 *
 * @package MemberPrice
 * @author Bratech CO.,LTD.
 * @version $Id: $
 */
class MemberPrice extends SC_Plugin_Base
{

    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo)
    {
        parent::__construct($arrSelfInfo);

        define('MEMBER_PRICE_TITLE', $this->getTitle());
        define('PLG_MEMBER_PRICE_LOGIN_DISP', $this->getLoginDisp());
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin)
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $objQuery->query("ALTER TABLE dtb_products_class ADD COLUMN plg_memberprice_price03 int DEFAULT NULL");

        $objQuery->update("dtb_plugin", array("free_field1" => "会員価格"), "plugin_code = ?", array("MemberPrice"));
        $objQuery->update("dtb_plugin", array("free_field2" => '0'), "plugin_code = ?", array("MemberPrice"));

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
    function uninstall($arrPlugin)
    {
        //テーブル削除
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->query("ALTER TABLE dtb_products_class DROP COLUMN plg_memberprice_price03");
    }

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        // dtb_csvテーブルにレコードを追加
        $sqlval_dtb_csv = array();
        $max = $objQuery->max('no', 'dtb_csv') + 1;
        $next = $objQuery->nextVal('dtb_csv_no');
        if ($max > $next) {
            $no = $max;
        } else {
            $no = $next;
        }
        $sqlval_dtb_csv['no'] = $no;
        $sqlval_dtb_csv['csv_id'] = 1;
        $sqlval_dtb_csv['col'] = 'plg_memberprice_price03';
        $sqlval_dtb_csv['disp_name'] = '会員価格';
        $sqlval_dtb_csv['rw_flg'] = 1;
        $sqlval_dtb_csv['status'] = 2;
        $sqlval_dtb_csv['create_date'] = "CURRENT_TIMESTAMP";
        $sqlval_dtb_csv['update_date'] = "CURRENT_TIMESTAMP";
        $sqlval_dtb_csv['mb_convert_kana_option'] = "n";
        $sqlval_dtb_csv['size_const_type'] = "PRICE_LEN";
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
    function disable($arrPlugin)
    {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        // dtb_csvテーブルからレコードを削除
        $objQuery->delete("dtb_csv", "col = ?", array('plg_memberprice_price03'));
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     */
    function register(SC_Helper_Plugin $objHelperPlugin)
    {
        $objHelperPlugin->addAction("prefilterTransform", array(&$this, "prefilterTransform"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("loadClassFileChange", array(&$this, "loadClassFileChange"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("SC_FormParam_construct", array(&$this, "addParam"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Products_Product_action_after", array(&$this, "admin_products_product_after"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Products_ProductClass_action_after", array(&$this, "admin_products_productclass_after"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_Admin_Order_Edit_action_after", array(&$this, "admin_order_edit_after"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_FrontParts_LoginCheck_action_login", array(&$this, "frontparts_logincheck_login"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("LC_Page_FrontParts_LoginCheck_action_logout", array(&$this, "frontparts_logincheck_logout"), $this->arrSelfInfo['priority']);
    }

    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        $objTransform = new SC_Helper_Transform($source);
        if ($this->getECCUBEVer() >= 2130) {
            $template_dir = PLUGIN_UPLOAD_REALDIR . "MemberPrice/templates/213/";
        } else {
            $template_dir = PLUGIN_UPLOAD_REALDIR . "MemberPrice/templates/212/";
        }
        switch ($objPage->arrPageLayout['device_type_id']) {
            case DEVICE_TYPE_PC:
                $template_dir .= "default/";
                if (strpos($filename, "products/list.tpl") !== false) {
                    $objTransform->select("span.price", 0, false)->insertAfter(file_get_contents($template_dir . "products/list.tpl"));
                    $objTransform->select("div#undercolumn", 0, false)->insertBefore(file_get_contents($template_dir . "products/js.tpl"));
                }
                if (strpos($filename, "products/detail.tpl") !== false) {
                    $objTransform->select("dl.sale_price", 0, false)->insertAfter(file_get_contents($template_dir . "products/detail.tpl"));
                    $objTransform->select("div.point", 0, false)->replaceElement(file_get_contents($template_dir . "products/detail_point.tpl"));
                    $objTransform->select("div#undercolumn", 0, false)->insertBefore(file_get_contents($template_dir . "products/js.tpl"));
                }
                break;
            case DEVICE_TYPE_SMARTPHONE:
                $template_dir .= "sphone/";
                if (strpos($filename, "products/list.tpl") !== false) {
                    $objTransform->select("span.price", 0, false)->insertAfter(file_get_contents($template_dir . "products/list.tpl"));
                    $objTransform->select("div.btn_area p", 0, false)->replaceElement(file_get_contents($template_dir . "products/list_btn_area.tpl"));
                    if ($this->getECCUBEVer() >= 2133) {
                        $objTransform->select("section", 0, false)->insertAfter(file_get_contents($template_dir . "products/list_js_2133.tpl"));
                    }else{
                        $objTransform->select("section", 0, false)->insertAfter(file_get_contents($template_dir . "products/list_js.tpl"));
                    }
                }
                if (strpos($filename, "products/detail.tpl") !== false) {
                    $objTransform->select("p.sale_price", 0, false)->insertAfter(file_get_contents($template_dir . "products/detail.tpl"));
                    $objTransform->select("p.sale_price", 1, false)->replaceElement(file_get_contents($template_dir . "products/detail_point.tpl"));
                    $objTransform->select("section", 0, false)->insertAfter(file_get_contents($template_dir . "products/js.tpl"));
                }
                break;
            case DEVICE_TYPE_MOBILE:
                $template_dir .= "mobile/";
                if (strpos($filename, "products/list.tpl") !== false) {
                    $objTransform->select("br", 3, false)->insertAfter(file_get_contents($template_dir . "products/list.tpl"));
                }
                if (strpos($filename, "products/detail.tpl") !== false) {
                    $objTransform->select("", 0, false)->replaceElement(file_get_contents($template_dir . "products/detail.tpl"));
                }
                break;
            case DEVICE_TYPE_ADMIN:
            default:
                $template_dir .= "admin/";
                if (strpos($filename, "products/product.tpl") !== false) {
                    $objTransform->select("table.form tr", 11)->insertAfter(file_get_contents($template_dir . "products/product.tpl"));
                }
                if (strpos($filename, "products/confirm.tpl") !== false) {
                    $objTransform->select("div.contents-main table tr", 9)->insertAfter(file_get_contents($template_dir . "products/confirm.tpl"));
                }
                if (strpos($filename, "products/product_class.tpl") !== false) {
                    $objTransform->select("table.list tr th", 6)->insertAfter(file_get_contents($template_dir . "products/product_class_th.tpl"));
                    $objTransform->select("table.list tr td", 6)->insertAfter(file_get_contents($template_dir . "products/product_class_td.tpl"));
                    $objTransform->select("h2", 0)->insertBefore(file_get_contents($template_dir . "products/product_class_js.tpl"));
                }
                if (strpos($filename, "products/product_class_confirm.tpl") !== false) {
                    $objTransform->select("table.list tr th", 5)->insertAfter(file_get_contents($template_dir . "products/product_class_th.tpl"));
                    $objTransform->select("table.list tr td", 5)->insertAfter(file_get_contents($template_dir . "products/product_class_confirm_td.tpl"));
                }
                break;
        }
        $source = $objTransform->getHTML();
    }

    function addParam($class_name, $param)
    {
        if (strpos($class_name, 'LC_Page_Admin_Products_Product') !== false) {
            $this->addMemberPriceParam($param);
        }
        if (strpos($class_name, 'LC_Page_Admin_Products_ProductClass') !== false) {
            $this->addMemberPriceParam($param);
        }
    }

    function loadClassFileChange(&$classname, &$classpath)
    {
        if ($this->getECCUBEVer() >= 2130) {
            $base_path = PLUGIN_UPLOAD_REALDIR . "MemberPrice/213/";
        } else {
            $base_path = PLUGIN_UPLOAD_REALDIR . "MemberPrice/212/";
        }
        if ($classname == 'SC_Product_Ex') {
            $classname = "plg_MemberPrice_SC_Product";
            $classpath = $base_path . $classname . ".php";
        }
        if ($classname == 'SC_CartSession_Ex') {
            $classname = "plg_MemberPrice_SC_CartSession";
            $classpath = $base_path . $classname . ".php";
        }
        if ($classname == 'SC_Helper_Purchase_Ex') {
            $classname = "plg_MemberPrice_SC_Helper_Purchase";
            $classpath = $base_path . $classname . ".php";
        }
    }

    /**
     * @param LC_Page_Admin_Products_Product $objPage 商品管理のページクラス
     * @return void
     */
    function admin_products_product_after($objPage)
    {
        $objFormParam = new SC_FormParam_Ex();
        switch ($objPage->getMode($objPage)) {
            case "pre_edit":
            case "copy" :
                $objPage->lfInitFormParam_PreEdit($objFormParam, $_POST);
                // エラーチェック
                $arrErr = $objFormParam->checkError();
                if (count($arrErr) == 0) {
                    $product_id = $objFormParam->getValue('product_id');
                    $objQuery = & SC_Query_Ex::getSingletonInstance();
                    $col = '*';
                    $table = <<< __EOF__
						dtb_products AS T1
						LEFT JOIN (
							SELECT product_id AS product_id_sub,
								plg_memberprice_price03
							FROM dtb_products_class
						) AS T2
							ON T1.product_id = T2.product_id_sub
__EOF__;
                    $where = 'product_id = ?';
                    $objQuery->setLimit('1');
                    $arrProduct = $objQuery->select($col, $table, $where, array($product_id));
                    $objPage->arrForm['plg_memberprice_price03'] = $arrProduct[0]['plg_memberprice_price03'];
                }
                break;
            case "edit":
            case "upload_image":
            case "delete_image":
            case "upload_down":
            case "delete_down":
            case "recommend_select":
            case "confirm_return":
                $this->addMemberPriceParam($objFormParam);
                $objPage->lfInitFormParam($objFormParam, $_POST);
                $arrForm = $objFormParam->getHashArray();
                $objPage->arrForm['plg_memberprice_price03'] = $arrForm['plg_memberprice_price03'];
                break;
            case "complete":
                if ($_POST['has_product_class'] != 1) {
                    $this->addMemberPriceParam($objFormParam);

                    $objPage->lfInitFormParam($objFormParam, $_POST);
                    $arrForm = $objPage->lfGetFormParam_Complete($objFormParam);
                    // エラーチェック
                    $arrErr = $objFormParam->checkError();
                    if (count($arrErr) == 0) {
                        $objQuery = & SC_Query_Ex::getSingletonInstance();
                        $table = "dtb_products_class";
                        $where = "product_class_id = ?";
                        $arrParam['plg_memberprice_price03'] = $arrForm['plg_memberprice_price03'];
                        $arrParam['update_date'] = "CURRENT_TIMESTAMP";
                        if ($arrForm['product_class_id'] > 0 && empty($arrForm['copy_product_id'])) {
                            $product_class_id = $arrForm['product_class_id'];
                        } else {
                            $product_class_id = SC_Utils_Ex::sfGetProductClassId($objPage->arrForm['product_id'], '0', '0');
                        }
                        $arrWhere[] = $product_class_id;
                        $objQuery->update($table, $arrParam, $where, $arrWhere);
                    }
                }
                break;
            default:
                break;
        }
    }

    /**
     * @param LC_Page_Admin_Products_ProductClass $objPage 商品管理のページクラス
     * @return void
     */
    function admin_products_productclass_after($objPage)
    {
        $objFormParam = new SC_FormParam_Ex();
        $objPage->initParam($objFormParam);
        $this->addMemberPriceParam($objFormParam);

        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        switch ($objPage->getMode($objPage)) {
            case "complete":
                $arrValues = $objFormParam->getHashArray();

                $objQuery = & SC_Query_Ex::getSingletonInstance();
                if (count($arrValues['plg_memberprice_price03']) > 0) {
                    foreach ($arrValues['plg_memberprice_price03'] as $key => $value) {
                        $product_class_id = $objQuery->get("product_class_id", "dtb_products_class", "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?", array($arrValues['product_id'], $arrValues['classcategory_id1'][$key], $arrValues['classcategory_id2'][$key]));
                        if ($product_class_id > 0) {
                            $objQuery->update('dtb_products_class', array('plg_memberprice_price03' => $value), 'product_class_id = ?', array($product_class_id));
                        }
                    }
                }
                break;
            default:
                break;
        }
    }

    /**
     * @param LC_Page_Admin_Order_Edit $objPage
     * @return void
     */
    function admin_order_edit_after($objPage)
    {
        $objFormParam = new SC_FormParam_Ex();

        // パラメーター情報の初期化
        $objPage->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        if ($objPage->getMode() == "select_product_detail") {
            if ($objPage->arrForm['customer_id']['value'] > 0) {
                $product_class_id = $objFormParam->getValue('add_product_class_id');
                if (SC_Utils_Ex::isBlank($product_class_id)) {
                    $product_class_id = $objFormParam->getValue('edit_product_class_id');
                    $changed_no = $objFormParam->getValue('no');
                }

                $objQuery = & SC_Query_Ex::getSingletonInstance();
                $price03 = $objQuery->get("plg_memberprice_price03", "dtb_products_class", "product_class_id = ?", array($product_class_id));

                if (!is_null($price03) && $price03 != "") {
                    $arrExistsProductClassIds = $objFormParam->getValue('product_class_id');

                    if (isset($changed_no)) {
                        $objPage->arrForm['price']['value'][$changed_no] = $price03;
                    } else {
                        $added_no = 0;
                        if (is_array($arrExistsProductClassIds)) {
                            $added_no = count($arrExistsProductClassIds);
                        }
                        $objPage->arrForm['price']['value'][$added_no] = $price03;
                    }
                }
            }
        }
    }

    /**
     * @param LC_Page_FrontParts_LoginCehck $objPage
     * @return void
     */
    function frontparts_logincheck_login($objPage)
    {
        $objCartSess = new SC_CartSession_Ex();
        $cartKeys = $objCartSess->getKeys();
        foreach ($cartKeys as $key) {
            $objCartSess->getCartList($key);
        }
    }

    /**
     * @param LC_Page_FrontParts_LoginCehck $objPage
     * @return void
     */
    function frontparts_logincheck_logout($objPage)
    {
        $objCartSess = new SC_CartSession_Ex();
        $cartKeys = $objCartSess->getKeys();
        foreach ($cartKeys as $key) {
            $objCartSess->getCartList($key);
        }
    }

    function addMemberPriceParam(&$objFormParam)
    {
        $objFormParam->addParam("会員価格", 'plg_memberprice_price03', PRICE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }

    function getTitle()
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        return $objQuery->get("free_field1", "dtb_plugin", "plugin_code = ?", array("MemberPrice"));
    }

    function getLoginDisp()
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        return $objQuery->get("free_field2", "dtb_plugin", "plugin_code = ?", array("MemberPrice"));
    }

    function getECCUBEVer()
    {
        return floor(str_replace('.', '', ECCUBE_VERSION));
    }

}

?>
