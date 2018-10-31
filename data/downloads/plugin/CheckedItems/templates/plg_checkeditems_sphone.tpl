<!--{*
 * CheckedItems
 * Copyright(c) 2014 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
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

<!--{* こちらはお客様ごとに編集してください*}-->
<style type="text/css">
#arrCheckItems{margin-bottom:10px;}
#arrCheckItems h2,#arrCheckItems ul li p{margin-bottom:5px;}
#arrCheckItems ul {width:99%; margin: 0px auto;}
#arrCheckItems ul li {float:left; width:33%; text-align:center;}
#arrCheckItems ul li p.item_image{text-align:center;}
#arrCheckItems ul li p.item_image img {width:98%;}
#arrCheckItems ul li p.checkItemname,
#arrCheckItems ul li p.price{ text-align:left;}
#arrCheckItems ul li p.price{ font-size:90%;}
#arrCheckItems ul li p.price em{ color:#FF0000;}
</style>
<!--{if $arrCheckItems}-->
<!-- CheckedItems -->
<section id="arrCheckItems">
<h2 class="title_block">最近チェックした商品</h2>
<ul class="clearfix">
<!--{section name=cnt loop=$arrCheckItems}-->
<li>
<p class="item_image">
<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrCheckItems[cnt].product_id}-->">
<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrCheckItems[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=130&amp;height=130" alt="<!--{$arrCheckItems[cnt].name|h}-->" style="max-width:130px;max-height:130px;" /></a>
</p>
<p class="checkItemname"><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrCheckItems[cnt].product_id}-->"><!--{$arrCheckItems[cnt].name}--></a></p>
<p class="price"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)<br /><em><!--{if $arrCheckItems[cnt].price02_min_inctax == $arrCheckItems[cnt].price02_max_inctax}--><!--{$arrCheckItems[cnt].price02_min_inctax|number_format}--><!--{else}--><!--{$arrCheckItems[cnt].price02_min_inctax|number_format}-->〜<!--{$arrCheckItems[cnt].price02_max_inctax|number_format}--><!--{/if}-->円</em></p>
</li>
<!--{/section}-->
</ul>
</section>
<!-- / CheckedItems END -->
<!--{/if}-->