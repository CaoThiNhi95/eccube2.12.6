<!--{*
 * WPPost
 * Copyright(c) 2000-2012 GIZMO CO.,LTD. All Rights Reserved.
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
 *}-->

<!--▼ WpPost Category-->
<div id="undercolumn">
    <div id="wpcategory">
        <!--▼パンクズ-->
        <script type="text/javascript">
            <!--
            if ($("#topicpath_area").length){
                var breadcrumbs
                breadcrumbs = '<ul class="breadcrumbs clearfix"><li><a href="<!--{$smarty.const.TOP_URLPATH}-->">ホーム</a></li><!--{if $wp_breadcrumb}--><!--{$wp_breadcrumb}--><!--{else}--><li><!--{$wp_catname}--></li><!--{/if}--></ul>';
                $("#topicpath_area").html(breadcrumbs);
            }
            -->
        </script>
        <!--▲パンクズ-->

        <!--{if $catlist}-->
            <h2 class="title"><!--{$wp_catname}--></h2>

            <div id="wpcategory_content">
                <!--{*$wp_catlist*}-->
                <!--{$catlist}-->
            </div>
        <!--{else}-->
            <!--{if $wp_catname}-->
                <h2 class="title"><!--{$wp_catname}--></h2>
                <div id="wpcategory_content">
                    記事がありません。
                </div>
            <!--{else}-->
                <div class="error">カテゴリがありません。</div>
            <!--{/if}-->
        <!--{/if}-->

    </div><!--#wpcategory-->
</div><!--#undercolumn-->
<!--▲ WpPost Category-->