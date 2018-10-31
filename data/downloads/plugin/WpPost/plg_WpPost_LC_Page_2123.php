<?php
/*
 * WPPost
 * Copyright (C) 2014 GIZMO CO.,LTD. All Rights Reserved.
 * http://www.gizmo.co.jp/
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
$plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("WpPost");
$wp_install_dir = $plugin['free_field1'];
require_once(HTML_REALDIR.$wp_install_dir.'/wp-load.php' );

/**
 * WpPost 記事一覧のページクラス.
 *
 * @package WpPost
 * @author GIZMO CO.,LTD.
 * @version $Id: plg_WpPost_LC_Page.php 22206 2014-02-01 09:30:10Z maru $
 */
class LC_Page_WpPost extends LC_Page_Ex {

    // {{{ properties

    /** テンプレートクラス名1 */
    var $tpl_class_name1 = array();

    /** テンプレートクラス名2 */
    var $tpl_class_name2 = array();

    /** JavaScript テンプレート */
    var $tpl_javascript;

    var $orderby;

    var $mode;

    /** 検索条件(内部データ) */
    var $arrSearchData = array();

    /** 検索条件(表示用) */
    var $arrSearch = array();

    var $tpl_subtitle = '';

    /** ランダム文字列 **/
    var $tpl_rnd = '';

    // }}}
    // {{{ functions

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("WpPost");
        $wp_install_dir = $plugin['free_field1'];
        $this->wp_root = substr($wp_install_dir, 0);

        $masterData                 = new SC_DB_MasterData_Ex();
        $this->arrSTATUS            = $masterData->getMasterData('mtb_status');
        $this->arrSTATUS_IMAGE      = $masterData->getMasterData('mtb_status_image');
        $this->arrDELIVERYDATE      = $masterData->getMasterData('mtb_delivery_date');
        $this->arrPRODUCTLISTMAX    = $masterData->getMasterData('mtb_product_list_max');
        $this->arrPRODUCTLIST_MinData    = (int)min($masterData->getMasterData('mtb_product_list_max'));
    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        //表示条件取得
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("WpPost");
        $wp_install_dir = $plugin['free_field1'];
        $this->wp_incat_text = $plugin['free_field2'];
        $wp_total_excludecat = $plugin['free_field3'];

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $wppost_postlist = $objQuery->select("*",plg_wppost_postlist);
        $wppost_postlist = $wppost_postlist[0];
        $format = $wppost_postlist['postlist_format'];
        $category = $wppost_postlist['postlist_include'];
        $expost = $wppost_postlist['postlist_exclude'];

        //ID
        if ($_GET["postid"]) {
            $postid = $_GET["postid"];
            $this->postid = $postid;
        }

        $args = array(
            'posts_per_page'   => -1,
            'offset'           => 0,
            'category'         => '',
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'year'             => '',
            'monthnum'         => '',
            'w'                => '',
            'day'              => '',
            'tag'              => '',
            'include'          => $postid,
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'any',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish',
            'comment_count'    => '',
            'suppress_filters' => 1
        );
        if ($args['post_type']){
            unset ($args['post_type']);
        }
        if ($format == 1) {
            $args['post_type'] = "post";
        } elseif ($format == 2) {
            $args['post_type'] = "page";
        } else {
            $args['post_type'] = "any";
        }

        //記事の取得
        $postlist = get_posts($args);

        if (count($postlist) == 1){
            $postid = $postlist[0]->ID;
        }

        $wp_posts = array();
        $idx=0;
        foreach ($postlist as $post) : setup_postdata($post);
            $wp_posts[$idx]["ID"] = $post->ID;
            $wp_posts[$idx]["date"] = date('Y/m/d', strtotime($post->post_date));
            $wp_posts[$idx]["title"] = $post->post_title;
            $wp_posts[$idx]["content"] = apply_filters('the_content',$post->post_content);
            $wp_posts[$idx]["meta"] = get_post_meta($post->ID);
            $wp_posts[$idx]["comment_status"] = $post->comment_status;
            $wp_posts[$idx]["comment_count"] = $post->comment_count;
            $posttags  = get_the_tags($post->ID);
            foreach ($posttags as $posttag) {
                $posttag_arr[] = $posttag -> name;
            }
            $wp_posts[$idx]["tags"] = implode(",", $posttag_arr);
            $wp_posts[$idx]["post_summary"] =  mb_substr(get_the_excerpt(), 0, 100);

            // contentからproduct_idを取得
            $src = mb_convert_kana($wp_posts[$idx]["content"], "as"); //全角英数と全角スペースを半角に変換
            $src = str_replace("\ ", "", $src); //半角スペースを削除
            $pattern = '/products_id_list(.*?)products_id_list/';
            $result =  preg_match_all($pattern,$src,$dest,PREG_SET_ORDER);

            //product_idが存在する場合 商品ここから
            if($result!==0){
                // 文字列を数値に変換
                $arrProductId_string = explode(",", $dest[0][1]);

                foreach ($arrProductId_string as &$value) {
                    $value = (int)$value;
                    $arrProductId[] = $value;
                }
                unset($value); // 最後の要素への参照を解除

                //ここからLC_Page_Products_List.phpと同じ
                $objProduct = new SC_Product_Ex();

                $this->arrForm = $_REQUEST;
                //modeの取得
                $this->mode = $this->getMode();

                //表示条件の取得
                $this->WparrSearchData = array(
                    'category_id'   => $this->lfGetCategoryId(intval($this->arrForm['category_id'])),
                    'maker_id'      => intval($this->arrForm['maker_id']),
                    'name'          => $this->arrForm['name'],
                    'product_id'     => $arrProductId
                );

                $this->orderby = $this->arrForm['orderby'];

                //ページング設定
                $this->tpl_pageno   = $this->arrForm['pageno'];
                $this->disp_number  = $this->lfGetDisplayNum($this->arrForm['disp_number']);

                // 画面に表示するサブタイトルの設定 $this->WparrSearchData
                $this->tpl_subtitle = $this->lfGetPageTitle($this->mode, $this->WparrSearchData['category_id']);

                // 画面に表示する検索条件を設定 $this->WparrSearchData
                $this->arrSearch    = $this->lfGetSearchConditionDisp($this->WparrSearchData);

                // 商品一覧データの取得 $this->WparrSearchData
                $arrSearchCondition = $this->lfGetSearchCondition($this->WparrSearchData);
                $this->tpl_linemax  = $this->lfGetProductAllNum($arrSearchCondition);
                // WpPost用に修正
                $urlParam           = "postid={$this->postid}&m={$this->m}&w={$this->w}&tag={$this->tag}&pageno=#page#";
                // モバイルの場合に検索条件をURLの引数に追加 $this->WparrSearchData
                if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
                    $searchNameUrl = urlencode(mb_convert_encoding($this->WparrSearchData['name'], 'SJIS-win', 'UTF-8'));
                    $urlParam .= "&mode={$this->mode}&name={$searchNameUrl}&orderby={$this->orderby}";
                }
                $this->objNavi      = new SC_PageNavi_Ex($this->tpl_pageno, $this->tpl_linemax, $this->disp_number, 'fnNaviPage', NAVI_PMAX, $urlParam, SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);
                $this->arrProducts  = $this->lfGetProductsList($arrSearchCondition, $this->disp_number, $this->objNavi->start_row, $this->tpl_linemax, $objProduct);

                // WpPost用の設定
                if ($this->arrProducts){
                    $wp_posts[$idx]['prductsExist']  = 1;
                } else {
                    unset($wp_posts[$idx]['prductsExist']);
                }

                switch ($this->getMode()) {

                    case 'json':
                        $this->doJson($objProduct);
                        break;

                    default:
                        $this->doDefault($objProduct);
                        break;
                }

                $this->tpl_rnd          = SC_Utils_Ex::sfGetRandomString(3);

            } // //product_idが存在する場合 商品ここまで
            $idx++;

            //取得した記事が1つの場合
            if (count($postlist) == 1){
                
                // パンクズ
                $wp_cats = get_the_category($post->ID);

                // 関連カテゴリー
                $this->wp_post_breadcrumbs = $this -> wp_get_post_breadcrumb($wp_cats, $wp_install_dir, $category, $wp_total_excludecat);

                // 記事が含まれるカテゴリ
                $this->wp_catposts = $this -> wp_get_post_inc_categories($wp_cats, $wp_install_dir, $category, $wp_total_excludecat);

                //コメント設定の取得
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                $wppost_comment = $objQuery->select("*",plg_wppost_comment);
                $wppost_comment = $wppost_comment[0];

                //ログイン判定
                $objCustomer = new SC_Customer();
                if($objCustomer->isLoginSuccess()) {
                    $this->tpl_login = true;
                }
                //コメントにログイン必要か
                $this->wppost_comment_login = $wppost_comment["comment_login"];

                //EC-CUBE会員認証
                $this->wppost_comment_login_ec = $wppost_comment["comment_login_ec"];

                // Facebook認証
                if ($wppost_comment["comment_login_fb"] == 1){
                    $this->wppost_comment_login_fb = $wppost_comment["comment_login_fb"];
                    switch ($this->getMode()) {
                        case 'fb_start':
                            $fb_url = "http://www.facebook.com/dialog/oauth?client_id=" . $wppost_comment["fb_appid"] . "&redirect_uri=". urlencode("http://" . $_SERVER["HTTP_HOST"] . $_SERVER['PHP_SELF'] ."?postid=" . $post->ID);
                            header('Location: ' . $fb_url);
                            break;

                        case 'fb_stop':
                            unset($_SESSION["fb_code"]);
                            $this->fb_auth = 0;
                            break;

                        default:
                            //Facebookのコールバックにトークンがある場合
                            $fb_code = $_REQUEST["code"];
                            if ($fb_code) {
                                $_SESSION["fb_code"] = $fb_code;
                                $this->fb_auth = 1;
                            } else {
                                if ($_SESSION["fb_code"]) {
                                    $this->fb_auth = 1;
                                } else {
                                    $this->fb_auth = 0;
                                }
                            }
                            break;
     
                    } //switch ($this->getMode())
                } // Facebook認証
                
                // Twitter認証
                if ($wppost_comment["comment_login_tw"] == 1){
                    $this->wppost_comment_login_tw = $wppost_comment["comment_login_tw"];
                    require_once 'twitteroauth/twitteroauth.php';

                    switch ($this->getMode()) {
                        case 'tw_start':
                            // TwitterのOAuth関係
                            $tw_consumer_key = $wppost_comment["tw_consumer_key"];
                            $tw_consumer_secret = $wppost_comment["tw_consumer_secret"];
                            $tw_oauth_callback = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER['PHP_SELF'] ."?postid=" . $post->ID . "&tw_status=start";
                            //OAuthトークンがなければ取得する
                            if (  empty($_SESSION['tw_oauth_token']) || empty($_SESSION['tw_oauth_token_secret']) || empty($_REQUEST['oauth_verifier']) ) {
                                /* 1.認証リクエストを行い、仮のトークンを取得する*/
                                $con  = new TwitterOAuth($tw_consumer_key, $tw_consumer_secret);
                                $request_token = $con->getRequestToken($tw_oauth_callback);
                                // 仮のアクセストークンをセットする
                                $_SESSION['tw_oauth_token'] = $token = $request_token['oauth_token'];
                                $_SESSION['tw_oauth_token_secret'] = $request_token['oauth_token_secret'];

                                switch ($con->http_code) {
                                    case 200:
                                        /* 2. Twitter認証用のURLを取得し、ユーザの承認を得るページに遷移 */
                                        $url = $con->getAuthorizeURL($token);
                                        header('Location: ' . $url);
                                        break;
                                    default:
                                         //HTTP ステータスが200でなければエラー
                                        $this->tpl_onload = "alert('接続に失敗しました。');";
                                        exit;
                                }
                            }
                            break;

                        case 'tw_stop':
                            $this->tw_auth = 0;
                            unset($_SESSION['tw_oauth_token']);
                            unset($_SESSION['tw_oauth_token_secret']);
                            unset($_SESSION['tw_access_token']);
                            unset($_SESSION['tw_auth_status']);
                            break;

                        default:
                            if ($_SESSION['tw_oauth_token'] && $_SESSION['tw_oauth_token_secret'] && empty($_SESSION['tw_access_token'])){
                                /* 3. アクセストークン、トークンシークレット、ユーザ認証済みのパラメータがそろったので、コネクションを作成*/
                                $con = new TwitterOAuth($tw_consumer_key, $tw_consumer_secret, $_SESSION['tw_oauth_token'], $_SESSION['tw_oauth_token_secret']);
                                //ユーザが承認した印のverifier を取得して、正式のアクセストークンを取得する 
                                $tw_access_token = $con->getAccessToken($_REQUEST['oauth_verifier']);
                                //正式のアクセストークンをセッションにセットする
                                $_SESSION['tw_access_token'] = $tw_access_token;

                                /* 4. アクセストークンが取得できたらセッションにセットし、処理用ページにリダイレクト*/
                                if (200 == $con->http_code) {
                                /*後処理*/
                                    $_SESSION['tw_auth_status'] = 'authed';
                                    $this->tw_auth = 1;

                                /* HTTPのステータスコードが200でなければ */
                                } else {
                                    /* エラーー*/
                                    $this->tw_auth = 0;
                                    $this->tpl_onload = "alert('認証に失敗しました。');";
                                }
                            }

                            //セッションが認証済みとなっていたら認証とする
                            if ($_SESSION['tw_auth_status'] == "authed"){
                                $this->tw_auth = 1;
                            } else {
                                $this->tw_auth = 0;
                            }
                            break;
                    } // switch ($this->getMode())
                } // Twitter認証

                //コメント表示
                //全体設定&ページ毎の設定
                if ($wppost_comment["show_comment"] == 1 and $wp_posts[0]["comment_status"] == "open"){
                    $this->wppost_comment_show = 1;
                    // カレントページの取得
                    if ($_GET["cpage"]){
                        $comment_current_page = $_GET["cpage"];
                    }
                    //表示コメント数
                    $this->comment_num = (int)$wppost_comment["comment_num"];
                    // コメント取得
                    $this->wp_comments = $this->wp_get_comments($postid, $wp_install_dir, $wppost_comment["comment_num"], $comment_current_page, $wppost_comment["comment_turn"], $wppost_comment["comment_avatar_size"], $wppost_comment["comment_restext"]);
                } else {
                    $this->wppost_comment_show = 0;
                }
     
            } //取得した記事が1つの場合

        endforeach; //foreach ($postlist as $post)

        $this->wp_posts = $wp_posts;

    } //pageのアクション

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * カテゴリIDの取得
     *
     * @return integer カテゴリID
     */
    function lfGetCategoryId($category_id) {

        // 指定なしの場合、0 を返す
        if (empty($category_id)) return 0;

        // 正当性チェック
        if (!SC_Utils_Ex::sfIsInt($category_id)
            || SC_Utils_Ex::sfIsZeroFilling($category_id)
            || !SC_Helper_DB_Ex::sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')
            ) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        // 指定されたカテゴリIDを元に正しいカテゴリIDを取得する。
        $arrCategory_id = SC_Helper_DB_Ex::sfGetCategoryId('', $category_id);

        if (empty($arrCategory_id)) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        return $arrCategory_id[0];
    }

    /**
     * 商品一覧の表示
     *
     * 
     */
    function lfGetProductsList($searchCondition, $disp_number, $startno, $linemax, &$objProduct) {

        $arrOrderVal = array();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 表示順序
        switch ($this->orderby) {
            // 販売価格が安い順
            case 'price':
                $objProduct->setProductsOrder('price02', 'dtb_products_class', 'ASC');
                break;

            // 新着順
            case 'date':
                $objProduct->setProductsOrder('create_date', 'dtb_products', 'DESC');
                break;

            default:
                if (strlen($searchCondition['where_category']) >= 1) {
                    $dtb_product_categories = '(SELECT * FROM dtb_product_categories WHERE '.$searchCondition['where_category'].')';
                    $arrOrderVal           = $searchCondition['arrvalCategory'];
                } else {
                    $dtb_product_categories = 'dtb_product_categories';
                }
                $order = <<< __EOS__
                    (
                        SELECT
                            T3.rank * 2147483648 + T2.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                              ON T2.category_id = T3.category_id
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,product_id DESC
__EOS__;
                    $objQuery->setOrder($order);
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($disp_number, $startno);
        $objQuery->setWhere($searchCondition['where']);

        // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProductId = $objProduct->findProductIdsOrder($objQuery, array_merge($searchCondition['arrval'], $arrOrderVal));

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrProducts = $objProduct->getListByProductIds($objQuery, $arrProductId);

        // 規格を設定
        $objProduct->setProductsClassByProductIds($arrProductId);
        $arrProducts['productStatus'] = $objProduct->getProductStatus($arrProductId);
        return $arrProducts;
    }

    /**
     * 入力内容のチェック
     *
     * 
     */
    function lfCheckError($product_id, &$arrForm, $tpl_classcat_find1, $tpl_classcat_find2) {

        // 入力データを渡す。
        $objErr = new SC_CheckError_Ex($arrForm);

        // 複数項目チェック
        if ($tpl_classcat_find1[$product_id]) {
            $objErr->doFunc(array('規格1', 'classcategory_id1', INT_LEN), array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        }
        if ($tpl_classcat_find2[$product_id]) {
            $objErr->doFunc(array('規格2', 'classcategory_id2', INT_LEN), array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        }

        $objErr->doFunc(array('商品規格ID', 'product_class_id', INT_LEN), array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objErr->doFunc(array('数量', 'quantity', INT_LEN), array('EXIST_CHECK', 'ZERO_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * パラメーターの読み込み
     *
     * @return void
     */
    function lfGetDisplayNum($display_number) {
        // 表示件数
        return (SC_Utils_Ex::sfIsInt($display_number))
            ? $display_number
            : current(array_keys($this->arrPRODUCTLISTMAX));
    }

    /**
     * ページタイトルの設定
     *
     * @return str
     */
    function lfGetPageTitle($mode, $category_id = 0) {
        if ($mode == 'search') {
            return '検索結果';
        } elseif ($category_id == 0) {
            return '全商品';
        } else {
            $arrCat = SC_Helper_DB_Ex::sfGetCat($category_id);
            return $arrCat['name'];
        }
    }

    /**
     * 表示用検索条件の設定
     *
     * @return array
     */
    function lfGetSearchConditionDisp($arrSearchData) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $arrSearch  = array('category' => '指定なし', 'maker' => '指定なし', 'name' => '指定なし');
        // カテゴリ検索条件
        if ($arrSearchData['category_id'] > 0) {
            $arrSearch['category']  = $objQuery->get('category_name', 'dtb_category', 'category_id = ?', array($arrSearchData['category_id']));
        }

        // メーカー検索条件
        if (strlen($arrSearchData['maker_id']) > 0) {
            $arrSearch['maker']     = $objQuery->get('name', 'dtb_maker', 'maker_id = ?', array($arrSearchData['maker_id']));
        }

        // 商品名検索条件
        if (strlen($arrSearchData['name']) > 0) {
            $arrSearch['name']      = $arrSearchData['name'];
        }

        /*// 商品ID検索条件
        if (strlen($arrSearchData['product_id']) > 0) {
            $arrSearch['product_id']      = $arrSearchData['product_id'];
        }*/
        return $arrSearch;
    }

    /**
     * 該当件数の取得
     *
     * @return int
     */
    function lfGetProductAllNum($searchCondition) {
        // 検索結果対象となる商品の数を取得
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($searchCondition['where_for_count']);
        $objProduct = new SC_Product_Ex();
        return $objProduct->findProductCount($objQuery, $searchCondition['arrval']);
    }

    /**
     * 検索条件のwhere文とかを取得
     *
     * @return array
     */
    function lfGetSearchCondition($arrSearchData) {
        $searchCondition = array(
            'where'             => '',
            'arrval'            => array(),
            'where_category'    => '',
            'arrvalCategory'    => array()
        );

        // カテゴリからのWHERE文字列取得
        if ($arrSearchData['category_id'] != 0) {
            list($searchCondition['where_category'], $searchCondition['arrvalCategory']) = SC_Helper_DB_Ex::sfGetCatWhere($arrSearchData['category_id']);
        }
        // ▼対象商品IDの抽出
        // 商品検索条件の作成（未削除、表示）
        //$searchCondition['where'] = 'alldtl.del_flg = 0 AND alldtl.status = 1 ';
        if ($arrSearchData['product_id'] != 0) {
            
            foreach ($arrSearchData['product_id'] as $value) {
                if ($value === reset($arrSearchData['product_id'])) {
                    //最初
                    $SearchConditionProducts = 'AND (alldtl.product_id = '.$value;
                } else {
                    $SearchConditionProducts .= ' OR alldtl.product_id = '.$value;
                }
            }
            $SearchConditionProducts .= ')';
            $searchCondition['where'] = 'alldtl.del_flg = 0 AND alldtl.status = 1 '.$SearchConditionProducts;
        } else {
            $searchCondition['where'] = 'alldtl.del_flg = 0 AND alldtl.status = 1 ';
        }

        if (strlen($searchCondition['where_category']) >= 1) {
            $searchCondition['where'] .= ' AND EXISTS (SELECT * FROM dtb_product_categories WHERE ' . $searchCondition['where_category'] . ' AND product_id = alldtl.product_id)';
            $searchCondition['arrval'] = array_merge($searchCondition['arrval'], $searchCondition['arrvalCategory']);
        }

        // 商品名をwhere文に
        $name = $arrSearchData['name'];
        $name = str_replace(',', '', $name);
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split('/ +/', $name);
        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if (strlen($val) > 0) {
                $searchCondition['where']    .= ' AND ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ?) ';
                $searchCondition['arrval'][]  = "%$val%";
                $searchCondition['arrval'][]  = "%$val%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($arrSearchData['maker_id']) {
            $searchCondition['where']   .= ' AND alldtl.maker_id = ? ';
            $searchCondition['arrval'][] = $arrSearchData['maker_id'];
        }

        $searchCondition['where_for_count'] = $searchCondition['where'];

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN) {
            $searchCondition['where'] .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = alldtl.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
            $searchCondition['where_for_count'] .= ' AND EXISTS(SELECT * FROM dtb_products_class WHERE product_id = alldtl.product_id AND del_flg = 0 AND (stock >= 1 OR stock_unlimited = 1))';
        }

        return $searchCondition;
    }

    /**
     * カートに入れる商品情報にエラーがあったら戻す
     *
     * @return str
     */
    function lfSetSelectedData(&$arrProducts, $arrForm, $arrErr, $product_id) {
        $js_fnOnLoad = '';
        foreach ($arrProducts as $key => $value) {
            if ($arrProducts[$key]['product_id'] == $product_id) {

                $arrProducts[$key]['product_class_id']  = $arrForm['product_class_id'];
                $arrProducts[$key]['classcategory_id1'] = $arrForm['classcategory_id1'];
                $arrProducts[$key]['classcategory_id2'] = $arrForm['classcategory_id2'];
                $arrProducts[$key]['quantity']          = $arrForm['quantity'];
                $arrProducts[$key]['arrErr']            = $arrErr;
                $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProducts[$key]['product_id']}, '{$arrForm['classcategory_id2']}');";
            }
        }
        return $js_fnOnLoad;
    }

    /**
     * カートに商品を追加
     *
     * @return void
     */
    function lfAddCart($arrForm, $referer) {
        $product_class_id = $arrForm['product_class_id'];
        $objCartSess = new SC_CartSession_Ex();
        $objCartSess->addProduct($product_class_id, $arrForm['quantity']);
    }

    /**
     * 商品情報配列に商品ステータス情報を追加する
     *
     * @param Array $arrProducts 商品一覧情報
     * @param Array $arrStatus 商品ステータス配列
     * @param Array $arrStatusImage スタータス画像配列
     * @return Array $arrProducts 商品一覧情報
     */
    function setStatusDataTo($arrProducts, $arrStatus, $arrStatusImage) {

        foreach ($arrProducts['productStatus'] as $product_id => $arrValues) {
            for ($i = 0; $i < count($arrValues); $i++) {
                $product_status_id = $arrValues[$i];
                if (!empty($product_status_id)) {
                    $arrProductStatus = array(
                        'status_cd' => $product_status_id,
                        'status_name' => $arrStatus[$product_status_id],
                        'status_image' =>$arrStatusImage[$product_status_id],
                    );
                    $arrProducts['productStatus'][$product_id][$i] = $arrProductStatus;
                }
            }
        }
        return $arrProducts;
    }

    /**
     *
     * @param type $objProduct 
     * @return void
     */
    function doJson(&$objProduct) {
        $this->arrProducts = $this->setStatusDataTo($this->arrProducts, $this->arrSTATUS, $this->arrSTATUS_IMAGE);
        $this->arrProducts = $objProduct->setPriceTaxTo($this->arrProducts);

        // 一覧メイン画像の指定が無い商品のための処理
        foreach ($this->arrProducts as $key=>$val) {
            $this->arrProducts[$key]['main_list_image'] = SC_Utils_Ex::sfNoImageMainList($val['main_list_image']);
        }

        echo SC_Utils_Ex::jsonEncode($this->arrProducts);
        SC_Response_Ex::actionExit();
    }

    /**
     *
     * @param type $objProduct 
     * @return void
     */
    function doDefault(&$objProduct) {
        //商品一覧の表示処理
        $strnavi            = $this->objNavi->strnavi;

        // 表示文字列
        $this->tpl_strnavi  = empty($strnavi) ? '&nbsp;' : $strnavi;

        // 規格1クラス名
        $this->tpl_class_name1  = $objProduct->className1;

        // 規格2クラス名
        $this->tpl_class_name2  = $objProduct->className2;

        // 規格1
        $this->arrClassCat1     = $objProduct->classCats1;

        // 規格1が設定されている
        $this->tpl_classcat_find1 = $objProduct->classCat1_find;
        // 規格2が設定されている
        $this->tpl_classcat_find2 = $objProduct->classCat2_find;

        $this->tpl_stock_find       = $objProduct->stock_find;
        $this->tpl_product_class_id = $objProduct->product_class_id;
        $this->tpl_product_type     = $objProduct->product_type;

        // 商品ステータスを取得
        $this->productStatus = $this->arrProducts['productStatus'];
        unset($this->arrProducts['productStatus']);
        $this->tpl_javascript .= 'var productsClassCategories = ' . SC_Utils_Ex::jsonEncode($objProduct->classCategories) . ';';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_PC) {
            //onloadスクリプトを設定. 在庫ありの商品のみ出力する
            foreach ($this->arrProducts as $arrProduct) {
                if ($arrProduct['stock_unlimited_max'] || $arrProduct['stock_max'] > 0) {
                    $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});";
                }
            }
        }

        //カート処理
        $target_product_id = intval($this->arrForm['product_id']);
        if ($target_product_id > 0) {
            // 商品IDの正当性チェック
            if (!SC_Utils_Ex::sfIsInt($this->arrForm['product_id'])
                || !SC_Helper_DB_Ex::sfIsRecord('dtb_products', 'product_id', $this->arrForm['product_id'], 'del_flg = 0 AND status = 1')) {
                SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
            }

            // 入力内容のチェック
            $arrErr = $this->lfCheckError($target_product_id, $this->arrForm, $this->tpl_classcat_find1, $this->tpl_classcat_find2);
            if (empty($arrErr)) {
                $this->lfAddCart($this->arrForm, $_SERVER['HTTP_REFERER']);


                SC_Response_Ex::sendRedirect(CART_URLPATH);
                SC_Response_Ex::actionExit();
            }
            $js_fnOnLoad .= $this->lfSetSelectedData($this->arrProducts, $this->arrForm, $arrErr, $target_product_id);

        } else {
            // カート「戻るボタン」用に保持
            $netURL = new Net_URL();
            //該当メソッドが無いため、$_SESSIONに直接セット
            $_SESSION['cart_referer_url'] = $netURL->getURL();
        }

        $this->tpl_javascript   .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
        $this->tpl_onload       .= 'fnOnLoad(); ';
    }

    /**
     * コメント取得
     * @param $postid
     * $postid 記事ID
     * $wp_install_dir WordPressインストール位置
     * $comment_num 1ページに表示するコメント数（入れ子なので親コメント数）
     * $comment_current_page 複数ページで表示している場合、表示中のページ
     * $comment_turn 新規:0 古いものから:1
     * $comment_avatar_size アバターサイズ
     * $comment_restext 返信のリンクに表示するテキスト
     * @return コメント情報を配列で返す
     */
    function wp_get_comments($postid, $wp_install_dir, $comment_num, $comment_current_page, $comment_turn, $comment_avatar_size, $comment_restext){
        $comments = get_comments(array(
            'post_id' => $postid,
            'status' => 'approve'/*,
            'count' => true*/
        ));
        $wp_url = site_url();
        //コメント残体の親コメント数
        foreach ($comments as $key => $comment) {
            if ($comment->comment_parent == 0){
                $parents[$key] = $comment->comment_parent;
            }
        }
        $parent_count = count($parents);

        $args = array(
            //'walker'            => new Walker_Comment(),
            'walker'            => '',
            'max_depth'         => '',
            'style'             => 'ul',
            'callback'          => null,
            'end-callback'      => null,
            'type'              => 'all',
            'reply_text'        => $comment_restext,
            'page'              => $comment_current_page,
            'per_page'          => $comment_num,
            'avatar_size'       => $comment_avatar_size,
            'reverse_top_level' => $comment_turn,
            'reverse_children'  => $comment_turn,
            'format'            => 'html5', //or xhtml if no HTML5 theme support
            'short_ping'        => false, // @since 3.6
            'echo'              => false
        );
        $comments_list = wp_list_comments($args, $comments);
        // 取得したページでのコメント総数
        $comments_pagecount = substr_count($comments_list, '</article>');
        $comments_pageparent = substr_count($comments_list, 'depth-1');
        $src_init = 0;
        for ($i=0; $i < $comments_pagecount; $i++) {
            //日付部分をリンクごと取り出し、current pageを設定
            $date_start = mb_strpos($comments_list, '<div class="comment-metadata">', $src_init)+31;
            $date_end = mb_strpos($comments_list, '</div>', $date_start);
            $date_link = mb_substr($comments_list, $date_start, $date_end-$date_start);
            if ($comment_current_page){
                $date_link = str_replace('&#038;cpage', '&#038;cpage='.$comment_current_page, $date_link);
            } else {
                $date_link = str_replace('&#038;cpage', '', $date_link);
            }

            // 日付からリンク削除
            $date_link = strip_tags($date_link);

            $date_link_fpos = mb_strpos($comments_list, '<div class="comment-metadata">', $src_init);
            $date_link_bpos = mb_strpos($comments_list, '</div>', $date_start)+6;
            $date_link_fstr = mb_substr($comments_list, 0, $date_link_fpos);
            $date_link_bstr = mb_substr($comments_list, $date_link_bpos);
            $comments_list = $date_link_fstr.$date_link_bstr;

            // コメントID抽出
            $comment_id_start = mb_strpos($comments_list, '<article id="div-comment-', $src_init)+25;
            $comment_id_end = mb_strpos($comments_list, '" class="comment-body">', $comment_id_start);
            $comment_id = mb_substr($comments_list, $comment_id_start, $comment_id_end-$comment_id_start);

            // ～よりを削除し日付を挿入
            $date_rep_fpos = mb_strpos($comments_list, '<span class="says">', $src_init);
            $date_rep_bpos = mb_strpos($comments_list, '</span>', $date_rep_fpos)+7;
            $date_rep_f_str = mb_substr($comments_list, 0, $date_rep_fpos);
            $date_rep_b_str = mb_substr($comments_list, $date_rep_bpos);
            $comments_list = $date_rep_f_str.$date_link.$date_rep_b_str;

            $rep_fpos = mb_strpos($comments_list, '<div class="reply">', $src_init)+19;
            $rep_bpos = mb_strpos($comments_list, '>', $rep_fpos)+1;
            $rep_f_str = mb_substr($comments_list, 0, $rep_fpos);
            $rep_b_str = mb_substr($comments_list, $rep_bpos);
            $rep_link = '<a href="#" onclick="return comment_bloc(\''.ROOT_URLPATH.'\', \''.substr($wp_install_dir, 1).'\', '.$postid.', '.$comment_id.')">';

            $comments_list = $rep_f_str.$rep_link.$rep_b_str;

            $post_end = mb_strpos($comments_list, '</article>', $rep_fpos);

            $src_init = $post_end;
        }
        $comments_list = str_replace($wp_url."/?p", ROOT_URLPATH."wppost/plg_WpPost_post.php?postid", $comments_list);

        // ブラウザ判定
        require_once(dirname(__FILE__) . "/plg_WpPost_BrowserType.php");
        if (BrowserType::isLegacyBrowser()){
            // IEで10未満はarticleをdivに置換
            $comments_list = str_replace("article", "div", $comments_list);
        }
        $wp_comments = array();
        $wp_comments['html'] = $comments_list; // コメント表示HTML
        $wp_comments['total_comments_num'] = (int)count($comments); // コメント総数

        // ページ切り替え生成
        $comment_num = (int)$comment_num;
        $page_count = ceil((int)$parent_count/(int)$comment_num);
        if ($comment_current_page > $page_count){$comment_current_page = 1;}
        if ($parent_count > $comment_num and $comment_num != 0){
            $page_count = ceil((int)$parent_count/(int)$comment_num);

            if (!$comment_current_page){$comment_current_page = 1;}
            if ($comment_current_page == 1){
                $pchange_str = '<div class="pchange clearfix"><div class="com_before"><a href="'.ROOT_URLPATH.'wppost/plg_WpPost_post.php?postid='.$postid.'&#038;cpage='.((int)$comment_current_page+1).'">&lt;以前のコメント'.((int)$comment_current_page+1).'/'.$page_count.'ページ</a></div></div>';
            } elseif ($comment_current_page == $page_count){
                $pchange_str = '<div class="pchange clearfix"><div class=com_new><a href="'.ROOT_URLPATH.'wppost/plg_WpPost_post.php?postid='.$postid.'&#038;cpage='.((int)$comment_current_page-1).'">&gt;新しいコメント'.((int)$comment_current_page-1).'/'.$page_count.'ページ</a></div></div>';
            } else {
                $pchange_str = '<div class="pchange clearfix"><div class="com_before"><a href="'.ROOT_URLPATH.'wppost/plg_WpPost_post.php?postid='.$postid.'&#038;cpage='.((int)$comment_current_page+1).'">&lt以前のコメント'.((int)$comment_current_page+1).'/'.$page_count.'ページ</a></div><div class=com_new><a href="'.ROOT_URLPATH.'wppost/plg_WpPost_post.php?postid='.$postid.'&#038;cpage='.((int)$comment_current_page-1).'">&gt;新しいコメント'.((int)$comment_current_page-1).'/'.$page_count.'ページ</a></div></div>';
            }
        } else {
            $pchange_str = '';
        }
        
        $wp_comments['page_count'] = (int)$page_count; // コメント表示ページ数
        $wp_comments['parent_count'] = (int)$parent_count; // 全体の親コメントの数
        $wp_comments['page_comments_count'] = (int)$comments_pagecount; //取得した全コメント数
        $wp_comments['page_parents_count'] = (int)$comments_pageparent; //取得した親コメント数
        $wp_comments['page_reply_count'] = (int)($comments_pagecount - $comments_pageparent); //取得した返信コメント数
        $wp_comments['pchange'] = $pchange_str; //ページ切り替えHTML

        return $wp_comments;

        
    }

    /**
     * パンクズ取得
     * $catid : 親カテゴリーID
     * $wp_install_dir : WordPressのインストール位置
     * return $breadcrumb : パンクズ用HTMLを文字列で返す
     */
    function wp_get_post_breadcrumb($catids, $wp_install_dir, $category, $wp_total_excludecat){
        $breadcrumb = '';
        $wp_url = site_url();

        //　表示するカテゴリー
        $show_cat_arr = $this->show_allcats($category);

        //　表示しないカテゴリー
        $hide_cat_arr = $this->hide_allcats($wp_total_excludecat);

        foreach ($catids as $i => $tmp_cat) {
            // カテゴリIDが表示に含まれていたら処理を行う
            if (in_array($tmp_cat->cat_ID, $show_cat_arr) || $show_cat_arr[0] == 0){
                // 親までのリンクつきHTML取得
                $breadcrumb_tmp[$i] = get_category_parents($tmp_cat->cat_ID, true, '', false);

                // 非表示カテゴリーを削除
                if ($hide_cat_arr[0] != 0){
                    foreach ($hide_cat_arr as $hide_cat) {
                        $hide_cat_name = NULL;
                        $hide_cat_name = get_the_category_by_ID($hide_cat);
                        $hide_cat_pos = mb_strpos($breadcrumb_tmp[$i], $hide_cat_name);

                        if ($hide_cat_pos == false){
                            continue;
                        } else {
                            $remove_spos = mb_strrpos($breadcrumb_tmp[$i], '<a href', $hide_cat_pos-mb_strlen($breadcrumb_tmp[$i]));
                            $remove_epos = mb_strpos($breadcrumb_tmp[$i], '</a>', $hide_cat_pos)+4;
                            $tmp_front = mb_substr($breadcrumb_tmp[$i], 0, $remove_spos);
                            $tmp_back = mb_substr($breadcrumb_tmp[$i], $remove_epos);
                            $breadcrumb_tmp[$i] = $tmp_front.$tmp_back;

                        }
                    }
                }
                
            }
        }
        // 重複があったら削除
        sort($breadcrumb_tmp);
        $include_cat_count = count($breadcrumb_tmp);
        for ($i=0; $i < $include_cat_count; $i++) {
            for ($j=$i+1; $j < $include_cat_count; $j++) { 
                $del_tmp = mb_strpos($breadcrumb_tmp[$j], $breadcrumb_tmp[$i]);
                if ($del_tmp !== false){
                    $del_keys[] = $i;
                }
            }
        }
        foreach ($del_keys as $del_key) {
            unset($breadcrumb_tmp[$del_key]);
        }

        $breadcrumb = str_replace($wp_url."/?cat", ROOT_URLPATH."wppost/plg_WpPost_category.php?catid", $breadcrumb_tmp);
        $breadcrumb = str_replace('<a href', '<li><a href', $breadcrumb);
        $breadcrumb = str_replace('</a>', '</a></li>', $breadcrumb);
        return $breadcrumb;
    }

    /**
     * ポストが含まれるカテゴリーを取得
     * $catid : 記事のカテゴリーID一覧
     * $wp_install_dir : WordPressのインストール位置
     * return $postin : カテゴリページ用の記事HTMLを配列で返す
     * HTMLの中身は $products_str_tmp で決定
     */
    function wp_get_post_inc_categories($catids, $wp_install_dir, $category, $wp_total_excludecat){
        $exclude_arr = explode(",", $wp_total_excludecat);
        $include_arr = explode(",", $category);
        $include_cats = '';
        $wp_url = site_url();

        //　表示するカテゴリー
        $show_cat_arr = $this->show_allcats($category);

        //　表示しないカテゴリー
        $hide_cat_arr = $this->hide_allcats($wp_total_excludecat);

        foreach ($catids as $i => $tmp_cat) {
            // カテゴリIDが表示に含まれていたら処理を行う
            if (in_array($tmp_cat->cat_ID, $show_cat_arr) || $show_cat_arr[0] == 0){
                // 親までのリンクつきHTML取得
                $parent_tmp= get_category_parents($tmp_cat->cat_ID, false, ',', false);
                $parent_tmp_arr = explode(",", $parent_tmp);
                $include_cats_tmp[$i] = get_category_parents($tmp_cat->cat_ID, true, '&gt;', false);

                // 非表示カテゴリーを削除
                if ($hide_cat_arr[0] != 0){
                    foreach ($hide_cat_arr as $hide_cat) {
                        $hide_cat_name = NULL;
                        $hide_cat_name = get_the_category_by_ID($hide_cat);
                        $hide_cat_pos = mb_strpos($include_cats_tmp[$i], $hide_cat_name);

                        if ($hide_cat_pos == false){
                            continue;
                        } else {
                            $remove_spos = mb_strrpos($include_cats_tmp[$i], '<a href', $hide_cat_pos-mb_strlen($include_cats_tmp[$i]));
                            $remove_epos = mb_strpos($include_cats_tmp[$i], '</a>&gt;', $hide_cat_pos)+8;
                            $tmp_front = mb_substr($include_cats_tmp[$i], 0, $remove_spos);
                            $tmp_back = mb_substr($include_cats_tmp[$i], $remove_epos);
                            $include_cats_tmp[$i] = $tmp_front.$tmp_back;                    
                        }
                    }
                }
                $include_cats_tmp[$i] = rtrim($include_cats_tmp[$i], '&gt;');
                $include_cats_tmp[$i] = str_replace($wp_url."/?cat", ROOT_URLPATH."wppost/plg_WpPost_category.php?cat_exclude=".$cat_exclude."&catid", $include_cats_tmp[$i]);
            }
        }
        // 重複があったら削除
        sort($include_cats_tmp);
        $include_cat_count = count($include_cats_tmp);
        for ($i=0; $i < $include_cat_count; $i++) {
            for ($j=$i+1; $j < $include_cat_count; $j++) { 
                $del_tmp = mb_strpos($include_cats_tmp[$j], $include_cats_tmp[$i]);
                if ($del_tmp !== false){
                    $del_keys[] = $i;
                }
            }
        }
        foreach ($del_keys as $del_key) {
            unset($include_cats_tmp[$del_key]);
        }

        if (count($include_cats_tmp) > 1){
            return $include_cats = implode("|", $include_cats_tmp);
        } else {
            return $include_cats = implode("", $include_cats_tmp);
        }
        
    }

    /**
     * 表示カテゴリーIDを子孫含め取得
     * 
     * 
     * 
     */
    function  show_allcats($category){
        //表示するカテゴリーIDを子孫含め取得
        $include_arr = explode(",", $category);
        $show_cat_arr = array();
        foreach ($include_arr as $key => $include) {
            if ($show_cat_arr == NULL){
                $show_cat_arr = get_term_children($include, "category");
            } else {
                $show_cat_arr = array_merge($show_cat_arr, get_term_children($include, "category"));
            }
            array_unshift($show_cat_arr, (int)$include);
        }
        return $show_cat_arr;
    }

    /**
     * 表示除外カテゴリーIDを子孫含め取得
     * 
     * 
     * 
     */
    function  hide_allcats($wp_total_excludecat){
        //表示しないカテゴリーIDを子孫含め取得
        $exclude_arr = explode(",", $wp_total_excludecat);
        $hide_cat_arr = array();
        foreach ($exclude_arr as $i => $exclude) {
            if ($hide_cat_arr == NULL){
                $hide_cat_arr = get_term_children($exclude, "category");
            } else {
                $hide_cat_arr = array_merge($hide_cat_arr, get_term_children($exclude, "category"));
            }
            array_unshift($hide_cat_arr, (int)$exclude);
        }
        return $hide_cat_arr;
    }







}
?>
