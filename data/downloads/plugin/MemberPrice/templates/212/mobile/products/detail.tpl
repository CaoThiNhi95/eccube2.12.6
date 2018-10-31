<!--{*
 * MemberPrice
 * Copyright (C) 2012 Bratech CO.,LTD. All Rights Reserved.
 * http://wwww.bratech.co.jp/
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
 <!--{strip}-->
    <!--★商品画像★-->
    <!--{if $smarty.get.image != ''}-->
        <!--{assign var=key value="`$smarty.get.image`"}-->
    <!--{else}-->
        <!--{assign var=key value="main_image"}-->
    <!--{/if}-->
    <center><img src="<!--{$arrFile[$key].filepath}-->"></center>

    <!--★商品サブ画像★-->
    <center>
        <!--{if $subImageFlag == true}-->
            <br>画像
            <!--{if ($smarty.get.image == "" || $smarty.get.image == "main_image")}-->
                [1]
            <!--{else}-->
                [<a href="?product_id=<!--{$smarty.get.product_id}-->&amp;image=main_image">1</a>]
            <!--{/if}-->

            <!--{assign var=num value="2"}-->
            <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
                <!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
                <!--{if $arrFile[$key].filepath != ""}-->
                    <!--{if $key == $smarty.get.image}-->
                        [<!--{$num}-->]
                    <!--{else}-->
                        [<a href="?product_id=<!--{$smarty.get.product_id}-->&amp;image=<!--{$key}-->"><!--{$num}--></a>]
                    <!--{/if}-->
                    <!--{assign var=num value="`$num+1`"}-->
                <!--{/if}-->
            <!--{/section}-->
            <br>
        <!--{/if}-->
        <br>
    </center>

    <!--{* オペビルダー用 *}-->
    <!--{if "sfViewDetailOpe"|function_exists === TRUE}-->
        <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/detail_ope_mb_view.tpl}-->
    <!--{/if}-->

    <!--★詳細メインコメント★-->
    [emoji:76]<!--{$arrProduct.main_comment|nl2br_html}--><br>
    <br>

    <!--★商品コード★-->
    商品コード：
    <!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
        <!--{$arrProduct.product_code_min|h}-->
    <!--{else}-->
        <!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
    <!--{/if}-->
    <br>
    
<!--{if strlen($arrProduct.plg_memberprice_price03_min) > 0 && (($smarty.session.customer && $smarty.const.PLG_MEMBER_PRICE_LOGIN_DISP == 1) || $smarty.const.PLG_MEMBER_PRICE_LOGIN_DISP == 0)}-->
        <!--★会員価格★-->
        <font color="#0000FF"><!--{$smarty.const.MEMBER_PRICE_TITLE}-->(税込)：
            <!--{if $arrProduct.plg_memberprice_price03_min_inctax == $arrProduct.plg_memberprice_price03_max_inctax}-->
                <!--{$arrProduct.plg_memberprice_price03_min_inctax|number_format}-->
            <!--{else}-->
                <!--{$arrProduct.plg_memberprice_price03_min_inctax|number_format}-->～<!--{$arrProduct.plg_memberprice_price03_max_inctax|number_format}-->
            <!--{/if}-->
            円</font>
        <br>
<!--{/if}-->    

    <!--★販売価格★-->
    <font color="#FF0000"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：
        <!--{if $arrProduct.price02_min_inctax == $arrProduct.price02_max_inctax}-->
        	<!--{$arrProduct.price02_min_inctax|number_format}-->
        <!--{else}-->
        	<!--{$arrProduct.price02_min_inctax|number_format}-->～<!--{$arrProduct.price02_max_inctax|number_format}-->
        <!--{/if}-->
        円</font>
    <br>

    <!--{if $arrProduct.price01_max_inctax > 0}-->
        <!--★通常価格★-->
        <font color="#FF0000"><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)：
            <!--{if $arrProduct.price01_min_inctax == $arrProduct.price01_max_inctax}-->
                <!--{$arrProduct.price01_min_inctax|number_format}-->
            <!--{else}-->
                <!--{$arrProduct.price01_min_inctax|number_format}-->～<!--{$arrProduct.price01_max_inctax|number_format}-->
            <!--{/if}-->
            円</font>
        <br>
    <!--{/if}-->

    <!--★ポイント★-->
    <!--{if $smarty.const.USE_POINT !== false}-->
        ポイント：
        <!--{if strlen($arrProduct.plg_memberprice_price03_min) > 0 && (($smarty.session.customer && $smarty.const.PLG_MEMBER_PRICE_LOGIN_DISP == 1) || $smarty.const.PLG_MEMBER_PRICE_LOGIN_DISP == 0)}-->
        	<!--{if $arrProduct.plg_memberprice_price03_min == $arrProduct.plg_memberprice_price03_max}-->
                    <!--{$arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
            <!--{else}-->
            	<!--{if $arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.plg_memberprice_price03_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
                        <!--{$arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                <!--{else}-->                  <!--{$arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->～<!--{$arrProduct.plg_memberprice_price03_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                <!--{/if}-->
            <!--{/if}-->
        <!--{else}-->        
            <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate|number_format}-->
            <!--{else}-->
                <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate}-->
                    <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate|number_format}-->
                <!--{else}-->
                    <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate|number_format}-->～<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate|number_format}-->
                <!--{/if}-->
            <!--{/if}-->
        <!--{/if}-->
        Pt<br>
    <!--{/if}-->
    <br>

    <!--★関連カテゴリ★-->
    関連カテゴリ：<br>
    <!--{section name=r loop=$arrRelativeCat}-->
        <!--{section name=s loop=$arrRelativeCat[r]}-->
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=<!--{$arrRelativeCat[r][s].category_id}-->"><!--{$arrRelativeCat[r][s].category_name}--></a>
            <!--{if !$smarty.section.s.last}--><!--{$smarty.const.SEPA_CATNAVI}--><!--{/if}-->
        <!--{/section}-->
        <br>
    <!--{/section}-->
    <br>

    <form name="form1" method="post" action="?">
    	<input type="hidden" name="mode" value="select">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">

    	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
        <!--{if $tpl_stock_find}-->
        	<!--★商品を選ぶ★-->
        	<center><input type="submit" name="select" id="cart" value="この商品を選ぶ"></center>
        <!--{else}-->
        	<font color="#FF0000">申し訳ございませんが､只今品切れ中です｡</font>
        <!--{/if}-->
    </form>
<!--{/strip}-->