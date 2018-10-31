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
 * Foundation, Inc., 59 Temple Placae, Suite 330, Boston, MA  02111-1307  USA
 */

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Ex.php';
$plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("WpPost");
$wp_install_dir = $plugin['free_field1'];
require_once(HTML_REALDIR.$wp_install_dir.'/wp-load.php' );

/**
 * WordPressPost取得のブロッククラス
 *
 * @package WpPost
 * @author GIZMO.,LTD.
 * @version $Id: $
 */
class LC_Page_FrontParts_Bloc_WpPost_postlist extends LC_Page_FrontParts_Bloc_Ex {

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

        /**
         * 表示条件取得
         * 
         * 
         */
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("WpPost");
        $wp_install_dir = $plugin['free_field1'];
        $wp_total_excludecat = $plugin['free_field3'];

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $wppost_postlist = $objQuery->select("*",plg_wppost_postlist);
        $wppost_postlist = $wppost_postlist[0];
        $wppost_list_title = $wppost_postlist['postlist_title'];
        $postnum = $wppost_postlist['postlist_num'];
        $format = $wppost_postlist['postlist_format'];
        $category = $wppost_postlist['postlist_include'];
        $expost = $wppost_postlist['postlist_exclude'];

        /**
         * コメント条件の取得
         * 
         * 
         */
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $wppost_comment = $objQuery->select("*",plg_wppost_comment);
        $wppost_comment = $wppost_comment[0];
        $this->comment_num = (int)$wppost_comment["comment_num"];

        /**
         * 記事一覧
         * 
         * 
         */
        $this->wppost_list_title = $wppost_list_title;

        if ($wp_total_excludecat){
            if ($category){
                str_replace(",", ",-", $wp_total_excludecat);
                $category = "-".str_replace(",", ",-", $wp_total_excludecat).",".$category;
            } else {
                $category = "-".str_replace(",", ",-", $wp_total_excludecat);
            }
        }

        $args = array(
            'posts_per_page'   => $postnum,
            'offset'           => 0,
            'category'         => $category,
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => $expost,
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => '',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish',
            'comment_count'    => '',
            'suppress_filters' => true
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
        $postlist = get_posts( $args );

        $wp_postlist = array();
        $idx=0;
        foreach ($postlist as $post) {
            $wp_postlist[$idx]["ID"] = $post->ID;
            $wp_postlist[$idx]["date"] = date('Y/m/d', strtotime($post->post_date));
            $wp_postlist[$idx]["title"] = $post->post_title;
            $wp_postlist[$idx]["content"] = apply_filters('the_content',$post->post_content);
            $wp_postlist[$idx]["meta"] = get_post_meta($post->ID);
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            $wp_postlist[$idx]["comment_status"] = $post->comment_status;
            $wp_postlist[$idx]["comment_count"] = $post->comment_count;

            $idx++;
        }
        $this->wp_postlist = $wp_postlist;

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
