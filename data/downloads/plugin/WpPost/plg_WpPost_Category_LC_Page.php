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
 * WordPressPost取得のクラス
 *
 * @package WpPost
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class LC_Page_WpPost_Category extends LC_Page_Ex {

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        $wp_incat_text = $plugin['free_field2'];
        $wp_total_excludecat = $plugin['free_field3'];

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $wppost_postlist = $objQuery->select("*",plg_wppost_postlist);
        $wppost_postlist = $wppost_postlist[0];
        $postnum = $wppost_postlist['postlist_num'];
        $format = $wppost_postlist['postlist_format'];
        $category = $wppost_postlist['postlist_include'];
        $expost = $wppost_postlist['postlist_exclude'];

        /**
         * カテゴリー
         * $catid : 親カテゴリID
         *
         */
        if ($_GET["catid"]) {
            $catid = $_GET["catid"];
            $this->catid = $catid;
            //$this->wp_catname = get_category($catid, ARRAY_A)['cat_name'];
            $wp_catname = get_category($catid, ARRAY_A);
            if (!is_wp_error($wp_catname)) {
                $this->wp_catname = $wp_catname['cat_name'];
            }
        } else {
            $catid = '';
        }

        if ($catid){

            // 表示除外カテゴリ子孫含む
            if ($wp_total_excludecat){
                $wp_total_excludecat_all_arr = $this->hide_allcats($wp_total_excludecat);
            }

            // カテゴリーが除外の場合
            if (in_array($catid, $wp_total_excludecat_all_arr) == true){
                $this->catlist = "このカテゴリーは表示できません。";
            } else {
                // パンクズ
                $this->wp_breadcrumb = $this->wp_get_cat_breadcrumb($catid, $wp_install_dir, $category, $wp_total_excludecat);
                $wp_total_excludecat_arr = explode(",", $wp_total_excludecat);
                // カテゴリーの説明を取得
                $wp_cat_description =  strip_tags(category_description($catid));
                $this->wp_cat_description =  str_replace(array("\r\n","\r","\n"), '', $wp_cat_description);

                $cat_post_args = array(
                    'show_option_all'    => '',
                    'orderby'            => 'name',
                    'order'              => 'ASC',
                    'show_last_update'   => 0,
                    'style'              => 'list',
                    'show_count'         => 0,
                    'hide_empty'         => 1,
                    'use_desc_for_title' => 1,
                    'child_of'           => $catid,
                    'feed'               => '',
                    'feed_type'          => '',
                    'feed_image'         => '',
                    'exclude'            => $wp_total_excludecat,
                    'exclude_tree'       => '',
                    'include'            => '',
                    'hierarchical'       => 1,
                    'title_li'           => '',
                    'number'             => NULL,
                    'echo'               => 0,
                    'depth'              => 0,
                    'current_category'   => 0,
                    'pad_counts'         => 0,
                    'taxonomy'           => 'category',
                    'walker'             => 'Walker_Category'
                );
                $catlist = wp_list_categories($cat_post_args);

                $wp_url = site_url();
                if (mb_strpos($catlist, 'カテゴリーなし')){
                    $catlist = NULL;
                } else {
                    $catlist = str_replace($wp_url."/?cat", ROOT_URLPATH."wppost/plg_WpPost_category.php?catid", $catlist);
                    $catlist = str_replace('<a href', '<div class="subcategory"><a href', $catlist);
                    $catlist = str_replace('</a>', '</a></div>', $catlist);
                    // カテゴリーに属するポスト一覧を追加
                    $postin_count = substr_count($catlist, "cat-item-");
                    $catidpos_init = 0;

                    for($a = 0; $a < $postin_count; $a++) {
                        //カテゴリーID抽出
                        $catidpos_pre = mb_strpos($catlist, 'cat-item-', $catidpos_init)+9;
                        $catidpos_post = mb_strpos($catlist, '">', $catidpos_pre);
                        $catid_num = (int)mb_substr($catlist, $catidpos_pre, $catidpos_post-$catidpos_pre);

                        //カテゴリーに属するポスト取得
                        if ($wp_total_excludecat){

                            if (strpos($wp_total_excludecat, ',') != false){
                                $wp_total_ex_tmp = str_replace (",", ",-", $wp_total_excludecat);

                                $wp_total_ex_tmp = "-".$wp_total_ex_tmp;

                            } else {
                                $wp_total_ex_tmp = "-".$wp_total_excludecat;
                            }
                            if ($category){
                                $category_tmp = $category.','.$wp_total_ex_tmp;
                            } else {
                                $category_tmp = $wp_total_ex_tmp;
                            }
                        }

                        $post_args = array(
                            'posts_per_page'   => -1,
                            'offset'           => 0,
                            //'category'         => $category_tmp,
                            'category'         => '',
                            'orderby'          => 'date',
                            'order'            => 'DESC',
                            'year'             => '',
                            'monthnum'         => '',
                            'w'                => '',
                            'day'              => '',
                            'tag'              => '',
                            'include'          => '',
                            'exclude'          => $expost,
                            'meta_key'         => '',
                            'meta_value'       => '',
                            'post_type'        => 'any',
                            'post_mime_type'   => '',
                            'post_parent'      => '',
                            'post_status'      => 'publish',
                            'comment_count'    => '',
                            'suppress_filters' => 1,
                            'category__in'     => array($catid_num),
                            'category__not_in' => array($wp_total_excludecat)
                        );
                        if ($post_args['post_type']){
                            unset ($args['post_type']);
                        }
                        if ($format == 1) {
                            $post_args['post_type'] = "post";
                        } elseif ($format == 2) {
                            $post_args['post_type'] = "page";
                        } else {
                            $post_args['post_type'] = "any";
                        }
                        $cat_post_lists = get_posts($post_args);

                        if ($cat_post_lists){
                            // 記事詳細の配列から記事を取得
                            $postin = $this -> wp_get_catposts($cat_post_lists, $wp_install_dir, $wp_incat_text, $category, $wp_total_excludecat);
                            $postin = $postin["html"];
                            $postin_str = implode("",$postin);
                        } else {
                            $postin_str = NULL;
                            $tags = NULL;
                        }

                        $catidpos_init = $catidpos_post;
                        $postin_front_pos = mb_strpos($catlist, '</a></div>', $catidpos_post+2);
                        $postin_front_str = mb_substr($catlist, 0, $postin_front_pos+10);

                        $postin_back_str = mb_substr($catlist, $postin_front_pos+10);
                        $catlist = $postin_front_str.$postin_str.$postin_back_str;
                    }
                }
                //親カテゴリーの記事を取得
                $prent_post_args = array(
                    'posts_per_page'   => -1,
                    'offset'           => 0,
                    'category'         => '',
                    'orderby'          => 'date',
                    'order'            => 'DESC',
                    'year'             => '',
                    'monthnum'         => '',
                    'w'                => '',
                    'day'              => '',
                    'tag'              => '',
                    'include'          => '',
                    'exclude'          => $expost,
                    'meta_key'         => '',
                    'meta_value'       => '',
                    'post_type'        => 'any',
                    'post_mime_type'   => '',
                    'post_parent'      => '',
                    'post_status'      => 'publish',
                    'comment_count'    => '',
                    'suppress_filters' => 1,
                    'category__in'     => array($catid),
                    'category__not_in' => array($wp_total_excludecat)
                );
                if ($prent_post_args['post_type']){
                    unset ($args['post_type']);
                }
                if ($format == 1) {
                    $prent_post_args['post_type'] = "post";
                } elseif ($format == 2) {
                    $prent_post_args['post_type'] = "page";
                } else {
                    $prent_post_args['post_type'] = "any";
                }
                $parent_post_lists = get_posts($prent_post_args);
                if ($parent_post_lists){
                    // 記事詳細の配列から記事を取得
                    $pre_posts = $this -> wp_get_catposts($parent_post_lists, $wp_install_dir, $wp_incat_text, $category, $wp_total_excludecat);
                    $pre_tags[] = $pre_posts["tag"];
                    $pre_posts = $pre_posts["html"];
                    $pre_posts_str = implode("",$pre_posts);
                } else {
                    $pre_posts_str = NULL;
                }
                $tag_total = array();
                foreach ($tags as $tag) {
                    $tag_total = array_merge($tag_total, $tag);
                }
                foreach ($pre_tags as $pre_tag) {
                    $tag_total = array_merge($tag_total, $pre_tag);
                }
                $this->cat_tags = implode(",", array_unique($tag_total));

                $this->catlist = $pre_posts_str.$catlist;
            }
        } else {
            $this->catlist = "カテゴリーが指定されていません。";
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 除外カテゴリ合成
     * $wp_total_excludecat　全体の除外カテゴリID
     * $catlist_excat 項目別に設定した除外カテゴリID
     * @return $cat_exclude
     * 除外カテゴリーの文字列を返す
     */
    function get_expload_cat ($wp_total_excludecat, $catlist_excat){
        if ($wp_total_excludecat != ''){
            if ($catlist_excat != ''){
                $cat_exclude = $wp_total_excludecat.','.$catlist_excat;
            } else {
                $cat_exclude = $catlist_excat;
            }
        } else {
            $cat_exclude = $wp_total_excludecat;
        }
        $cat_exclude_arr = explode(",", $cat_exclude);
        $cat_exclude_arr = array_unique($cat_exclude_arr);
        $cat_exclude_arr = sort($cat_exclude_arr, SORT_NUMERIC);
        $cat_exclude = implode(",",$cat_exclude_arr);
        return $cat_exclude;
    }

    /**
     * 記事取得
     * $cat_post_lists : 記事詳細情報の配列
     * $wp_install_dir : WordPressのインストール位置
     * return $postin : カテゴリページ用の記事HTMLを配列で返す
     * HTMLの中身は $products_str_tmp で決定
     */
    function wp_get_catposts($cat_post_lists, $wp_install_dir, $wp_incat_text, $category, $wp_total_excludecat){
        $postin = array();
        $tags_arr = array();
        foreach ($cat_post_lists as $key => $cat_post) : setup_postdata($cat_post);

            $wp_cats = get_the_category($cat_post->ID);

            // ポストが含まれるカテゴリーを取得
            $include_cats = $this -> wp_get_post_inc_categories($wp_cats, $wp_install_dir, $category, $wp_total_excludecat);

            $posttags = get_the_tags($cat_post->ID);

            foreach ($posttags as $tmp_tags) {
                array_push($tags_arr, $tmp_tags->name);
            }
            $postin["tag"] = $tags_arr;
            // contentからproduct_idを取得
            $src = mb_convert_kana($cat_post->post_content, "as"); //全角英数と全角スペースを半角に変換
            $src = str_replace("\ ", "", $src); //半角スペースを削除
            $pattern = '/products_id_list(.*?)products_id_list/';
            $result =  preg_match_all($pattern,$src,$dest,PREG_SET_ORDER);
            $products_str = NULL;

            // 記事データ
            $postin["html"][$key] =
            '<div class="cat_posts catid-'.$cat_post->ID.'">'."\n"
            .'<div class="post_block">'."\n"
            .'<div class="post_title"><a href="./plg_WpPost_post.php?postid='.$cat_post->ID.'">'.$cat_post->post_title.'</a></div>'."\n"
            .'<div class="post_date">'.date('Y年m月d日', strtotime($cat_post->post_date))."</div>"."\n"
            .'<div class="post_summary">'.mb_substr(get_the_excerpt(), 0, 100).'<a href="./plg_WpPost_post.php?postid='.$cat_post->ID.'">&nbsp;&gt;&gt;</a></div>'."\n"
            .'<div class="post_cats">'.$wp_incat_text.'&nbsp;'.$include_cats."</div>"."\n"
            .'<div class="products_box clearfix">'.$products_str.'</div>'."\n"
            .'</div>'."\n"
            ."</div>";
        endforeach;
        return $postin;
    }

    /**
     * パンクズ取得
     * $catid : 親カテゴリーID
     * $wp_install_dir : WordPressのインストール位置
     * return $breadcrumb : パンクズ用HTMLを文字列で返す
     */
    function wp_get_cat_breadcrumb($catid, $wp_install_dir, $category, $wp_total_excludecat){
        $breadcrumb = '';

        //　表示するカテゴリー
        $show_cat_arr = $this->show_allcats($category);

        //　表示しないカテゴリー
        $hide_cat_arr = $this->hide_allcats($wp_total_excludecat);


        // カテゴリIDが表示に含まれていたら処理を行う
        if (in_array($catid, $show_cat_arr) || $show_cat_arr[0] == 0){
            // 親までのリンクつきHTML取得
            $breadcrumb_tmp = get_category_parents($catid, true, '', false);
            // 非表示カテゴリーを削除
            foreach ($hide_cat_arr as $hide_cat) {
                $hide_cat_name = NULL;
                $hide_cat_name = get_the_category_by_ID($hide_cat);
                $hide_cat_pos = mb_strpos($breadcrumb_tmp, $hide_cat_name);

                if ($hide_cat_pos == false){
                    continue;
                } else {
                    $remove_spos = mb_strrpos($breadcrumb_tmp, '<a href', $hide_cat_pos-mb_strlen($breadcrumb_tmp));
                    $remove_epos = mb_strpos($breadcrumb_tmp, '</a>', $hide_cat_pos)+4;
                    $tmp_front = mb_substr($breadcrumb_tmp, 0, $remove_spos);
                    $tmp_back = mb_substr($breadcrumb_tmp, $remove_epos);
                    $breadcrumb_tmp = $tmp_front.$tmp_back;

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

        $wp_url = site_url();
        $breadcrumb = str_replace($wp_url."/?cat", ROOT_URLPATH."wppost/plg_WpPost_category.php?catid", $breadcrumb_tmp);
        $breadcrumb = str_replace('<a href', '<li><a href', $breadcrumb);
        $breadcrumb = str_replace('</a>', '</a></li>', $breadcrumb);
        $last_pos = mb_strrpos($breadcrumb, '<a href');
        $breadcrumb_front_str = mb_substr($breadcrumb, 0, $last_pos);
        $breadcrumb_back_str = strip_tags(mb_substr($breadcrumb, $last_pos), '<li>');
        $breadcrumb = $breadcrumb_front_str.$breadcrumb_back_str;
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
        sort($show_cat_arr, SORT_NUMERIC);
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
        sort($hide_cat_arr, SORT_NUMERIC);
        return $hide_cat_arr;
    }

}
?>
