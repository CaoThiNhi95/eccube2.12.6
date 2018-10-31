<!--{*
 * WpPost
 * Copyright(c) 2000-2014 GIZMO CO.,LTD. All Rights Reserved.
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
<!--PLG:WpPost-->
<script type="text/javascript">
$(function(){
	document.title = "<!--{$arrSiteInfo.shop_name|h}--><!--{if $wp_posts[0].title}--> | <!--{$wp_posts[0].title}--><!--{elseif $wp_catname}--> | <!--{$wp_catname}--><!--{elseif $tpl_subtitle|strlen >= 1}--> | <!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}--> | <!--{$tpl_title|h}--><!--{/if}-->";
});
</script>
<!--PLG:WpPost-->