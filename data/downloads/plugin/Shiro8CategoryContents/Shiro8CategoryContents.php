<?php
/*
 * Shiro8CategoryContents
 * Copyright (C) 2012 Shiro8. All Rights Reserved.
 * http://www.shiro8.net/
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


/* 
 * カテゴリ毎にコンテンツを設定する事ができます。
 */
class Shiro8CategoryContents extends SC_Plugin_Base {

    /**
     * コンストラクタ
     * プラグイン情報(dtb_plugin)をメンバ変数をセットします.
     * @param array $arrSelfInfo dtb_pluginの情報配列
     * @return void
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function install($arrPlugin) {
    	$objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        
    	// dtb_categoryに必要なカラムを追加します.
        $objQuery->query("ALTER TABLE dtb_category ADD plg_shiro8_categorycontents_category_contents TEXT");
        
        $objQuery->commit();
        
        // ロゴファイルをhtmlディレクトリにコピーします.
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/logo.png", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");
        // 画像検索ページをhtmlディレクトリにコピーします.
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plg_shiro8CategoryContents_imageCreateTag.php", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/plg_shiro8CategoryContents_imageCreateTag.php");
        // 画像検索ページ用テンプレートファイルをsmartyディレクトリにコピーします.
        if (!file_exists(DATA_REALDIR . "Smarty/templates/admin/" . $arrPlugin['plugin_code'])) {
            mkdir(DATA_REALDIR . "Smarty/templates/admin/" . $arrPlugin['plugin_code'], 0755);
        }
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/plg_shiro8CategoryContents_imgtag_search.tpl", DATA_REALDIR . "Smarty/templates/admin/" . $arrPlugin['plugin_code'] . "/plg_shiro8CategoryContents_imgtag_search.tpl");
        
    }

    /**
     * 削除時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function uninstall($arrPlugin) {
    	$objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        
        // dtb_categoryから不要なカラムを削除します.
        $objQuery->query("ALTER TABLE dtb_category DROP plg_shiro8_categorycontents_category_contents");
        
        //csv出力に不要なレコードを削除します.
        $objQuery->delete('dtb_csv', "col = ? ", 'plg_shiro8_categorycontents_category_contents');
        
        $objQuery->commit();
        
        // 画像検索ページ用テンプレートファイルを削除します.
        if (file_exists(DATA_REALDIR . "Smarty/templates/admin/" . $arrPlugin['plugin_code'] . "/plg_shiro8CategoryContents_imgtag_search.tpl")) {
            unlink(DATA_REALDIR . "Smarty/templates/admin/" . $arrPlugin['plugin_code'] . "/plg_shiro8CategoryContents_imgtag_search.tpl");
            rmdir(DATA_REALDIR . "Smarty/templates/admin/" . $arrPlugin['plugin_code']);
        }
    }
    
    /**
     * 有効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function enable($arrPlugin) {
        //csv出力に必要なレコードを追加します.
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $no = $objQuery->nextVal('dtb_csv_no');
        $sqlval['no'] = $no;
        $sqlval['csv_id'] = 5;
        $sqlval['col'] = 'plg_shiro8_categorycontents_category_contents';
        $sqlval['disp_name'] = 'フリーエリア';
        $sqlval['rank'] = $no;
        $sqlval['rw_flg'] = '1';
        $sqlval['status'] = '2';
        $sqlval['create_date'] = date('Y-m-d');
        $sqlval['update_date'] = date('Y-m-d');
        $sqlval['mb_convert_kana_option'] = 'KVa';
        $sqlval['size_const_type'] = 'LLTEXT_LEN';
        $sqlval['error_check_types'] = 'SPTAB_CHECK,MAX_LENGTH_CHECK,HTML_TAG_CHECK';
        $objQuery->insert('dtb_csv', $sqlval);
    }

    /**
     * 無効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function disable($arrPlugin) {
        //csv出力に不要なレコードを削除します.
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->delete('dtb_csv', "col = ? ", 'plg_shiro8_categorycontents_category_contents');
    }

    /**
     * カテゴリ毎にコンテンツの登録をします.
     * @param LC_Page_Admin_Products_Category $objPage <管理画面>カテゴリ登録.
     * @return void
     */
    function contents_set($objPage) {
        $post = $_POST;
        switch ($post['mode']) {
            // 編集押下時
            case 'pre_edit':
                $category_id = $objPage->arrForm['category_id'];
                $array_category = Shiro8CategoryContents::getCategoryByCategoryId($category_id);
                $objPage->arrForm['plg_shiro8_categorycontents_category_contents'] = $array_category['plg_shiro8_categorycontents_category_contents'];
                break;
            // 登録押下時
            case 'edit':
                $category_id = $post['category_id'];
                $category_contents = $post['plg_shiro8_categorycontents_category_contents'];
                // 新規登録
                if(empty($category_id)){
                    $category_name = $post['category_name'];
                    $array_category = Shiro8CategoryContents::getCategoryByCategoryName($category_name);
                    $category_id = $array_category['category_id'];
                }
                Shiro8CategoryContents::updateCategoryContents($category_id,$category_contents);
                break;
            default:
                break;
        }
    }

    /**
     * 商品一覧でカテゴリが指定されている場合、関連するコンテンツをページオブジェクトにセットします.
     * 
     * @param LC_Page_Products_List $objPage 商品一覧のページオブジェクト
     * @return void
     */
    function disp_contents($objPage) {
        // 選択されたカテゴリーIDを取得.
        $category_id = $objPage->arrSearchData['category_id'];
        if(!empty($category_id)){
            // カテゴリIDからカテゴリ情報を取得
            $array_category = Shiro8CategoryContents::getCategoryByCategoryId($category_id);
            // コンテンツをPageオブジェクトにセットします.
            $objPage->plg_shiro8_categorycontents_category_contents = $array_category['plg_shiro8_categorycontents_category_contents'];
        }
    }
    
    /**
     * prefilterコールバック関数
     * テンプレートの変更処理を行います.
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        // SC_Helper_Transformのインスタンスを生成.
        $objTransform = new SC_Helper_Transform($source);
        // 呼び出し元テンプレートを判定します.
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE: // モバイル
            case DEVICE_TYPE_SMARTPHONE: // スマホ
                break;
            case DEVICE_TYPE_PC: // PC
                // 商品一覧画面
                if (strpos($filename, 'products/list.tpl') !== false) {
                    // h2タグのclass=title要素の前にプラグイン側で用意したテンプレートを挿入します.
                    $template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/';
                    $objTransform->select('h2.title')->insertAfter(file_get_contents($template_dir . 'shiro8_categorycontents_products_list_add.tpl'));
                }
                break;
            case DEVICE_TYPE_ADMIN: // 管理画面
            default:
                // カテゴリ登録画面
                if (strpos($filename, 'products/category.tpl') !== false) {
                    // divタグのclass=now_dir要素をプラグイン側で用意したテンプレートと置き換えます.
                    $template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/';
                    $objTransform->select('div.now_dir')->replaceElement(file_get_contents($template_dir . 'shiro8_categorycontents_admin_basis_category_add.tpl'));
                }
                break;
        }

        // 変更を実行します
        $source = $objTransform->getHTML();
    }
    
    /**
     * カテゴリIDからカテゴリ情報を取得します
     * 
     * @param int $category_id カテゴリID
     * @return array カテゴリ情報
     */
    function getCategoryByCategoryId ($category_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = "*";
        $from = "dtb_category"; 
        $where = "category_id = ?";
        $array_categorys = $objQuery->select($col, $from, $where, array($category_id));
        return $array_categorys[0];
    }

    /**
     * カテゴリ名からカテゴリ情報を取得します
     * 
     * @param string $category_name カテゴリ名
     * @return array カテゴリ情報
     */
    function getCategoryByCategoryName ($category_name) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = "*";
        $from = "dtb_category"; 
        $where = "category_name = ?";
        $array_categorys = $objQuery->select($col, $from, $where, array($category_name));
        return $array_categorys[0];
    }
    
    /**
     * dtb_categoryのcategory_contentsを更新します.
     * 
     * @param int $category_id カテゴリID
     * @param string $category_contents カテゴリコンテンツ
     * @return void
     */
    function updateCategoryContents ($category_id, $category_contents) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $table = "dtb_category";
        $sqlval['plg_shiro8_categorycontents_category_contents'] = $category_contents;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "category_id = ?";
        $objQuery->update($table, $sqlval, $where, array($category_id));        
    }
    
}

?>