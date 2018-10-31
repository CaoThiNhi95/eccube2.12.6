<!--{*
 * NakwebBlocNewProductStatus
 * Copyright (C) 2012 NAKWEB CO.,LTD. All Rights Reserved.
 * http://www.nakweb.com/
 *_
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *_
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *_
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*}-->

<!-- start NakwebBlocNewProductStatus -->
<!--{if count($arrProductStatusNew) > 0}-->
<div class="block_outer clearfix">
    <div id="recommend_area">
        <h2><!--{$bloc_title_main}-->（<!--{$bloc_title_sub}-->）</h2>
        <div class="block_body clearfix">
        <!--{foreach from=$arrProductStatusNew item=arrProductStatusNew name="plg_nakweb_00004_products"}-->
            <div class="product_item clearfix">
                <div class="productImage">
                    <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProductStatusNew.product_id|u}-->">
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProductStatusNew.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$arrProductStatusNew.name|h}-->" />
                    </a>
                </div>
                <div class="productContents">
                    <h3>
                        <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProductStatusNew.product_id|u}-->"><!--{$arrProductStatusNew.name|h}--></a>
                    </h3>

                    <!--{assign var=price01 value=`$arrProductStatusNew.price01_min_inctax`}-->
                    <!--{assign var=price02 value=`$arrProductStatusNew.price02_min_inctax`}-->
                    <p class="sale_price">
                        <!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)： <span class="price"><!--{$price02|number_format}--> 円</span>
                    </p>
                    <p class="mini comment"><!--{$arrProductStatusNew.comment|h|nl2br}--></p>
                </div>
            </div>
            <!--{if $smarty.foreach.plg_nakweb_00004_products.iteration % 2 === 0}-->
                <div class="clear"></div>
            <!--{/if}-->
        <!--{/foreach}-->
        </div>
    </div>
</div>
<!--{/if}-->
<!-- end   NakwebBlocNewProductStatus -->
