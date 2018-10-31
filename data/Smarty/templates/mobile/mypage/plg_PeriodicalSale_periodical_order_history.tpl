<!--{*
 * PeriodicalSale
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
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

<!--{strip}-->
    申込日時: <!--{$arrPeriodicalOrder.create_date|sfDispDBDate}--><br>
    定期注文番号: <!--{$arrPeriodicalOrder.periodical_order_id}--><br>
    お支払い方法: <!--{$arrPAYMENTS[$arrPeriodicalOrder.payment_id]|h}--><br>
    <br>
    ■購入商品詳細<br>
    <!--{foreach from=$arrPeriodicalOrder.periodical_order_details item=arrPeriodicalOrderDetail}-->
        <hr>
        商品コード：<!--{$arrPeriodicalOrderDetail.product_code|h}--><br>
        商品名：<a<!--{if $arrPeriodicalOrderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrPeriodicalOrderDetail.product_id|u}-->"<!--{/if}-->><!--{$arrPeriodicalOrderDetail.product_name|h}--></a><br>
        商品種別：
        <!--{if $arrPeriodicalOrderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <!--{if $arrPeriodicalOrderDetail.is_downloadable}-->
                <!--{if $isAU == false}-->
                    <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$arrPeriodicalOrder.order_id}-->&amp;product_id=<!--{$arrPeriodicalOrderDetail.product_id}-->&amp;product_class_id=<!--{$arrPeriodicalOrderDetail.product_class_id}-->">ダウンロード</a><br>
                <!--{else}-->
                    <object data="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$arrPeriodicalOrder.order_id}-->&amp;product_id=<!--{$arrPeriodicalOrderDetail.product_id}-->&amp;product_class_id=<!--{$arrPeriodicalOrderDetail.product_class_id}-->&amp;<!--{$smarty.const.SID}-->" copyright="no" standby="ダウンロード" type="<!--{$arrPeriodicalOrderDetail.mime_type}-->">
                        <param name="title" value="<!--{$arrPeriodicalOrderDetail.down_filename}-->" valuetype="data">
                    </object><br>
                <!--{/if}-->
            <!--{else}-->
                <!--{if $arrPeriodicalOrderDetail.payment_date == "" && $arrPeriodicalOrderDetail.effective == "0"}-->
                    <!--{$arrProductType[$arrPeriodicalOrderDetail.product_type_id]}--><br>（入金確認中）<br>
                <!--{else}-->
                    <!--{$arrProductType[$arrPeriodicalOrderDetail.product_type_id]}--><br>（期限切れ）<br>
                <!--{/if}-->
            <!--{/if}-->
        <!--{else}-->
            <!--{$arrProductType[$arrPeriodicalOrderDetail.product_type_id]}--><br>
        <!--{/if}-->
        単価：
        <!--{assign var=price value=`$arrPeriodicalOrderDetail.price`}-->
        <!--{assign var=quantity value=`$arrPeriodicalOrderDetail.quantity`}-->
        <!--{$price|sfCalcIncTax|number_format|h}-->円<br>
        数量：<!--{$quantity|h}--><br>
        小計：<!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}-->円<br>
    <!--{/foreach}-->
    <hr>
    小計：<!--{$arrPeriodicalOrder.subtotal|number_format}-->円<br>
    <!--{assign var=point_discount value="`$arrPeriodicalOrder.use_point*$smarty.const.POINT_VALUE`"}-->
    <!--{assign var=key value="discount"}-->
    <!--{if $arrPeriodicalOrder[$key] != "" && $arrPeriodicalOrder[$key] > 0}-->
        値引き：<!--{$arrPeriodicalOrder[$key]|number_format}-->円<br>
    <!--{/if}-->
    送料：<!--{assign var=key value="deliv_fee"}--><!--{$arrPeriodicalOrder[$key]|number_format|h}-->円<br>
    手数料：
    <!--{assign var=key value="charge"}-->
    <!--{$arrPeriodicalOrder[$key]|number_format|h}-->円<br>
    合計：<!--{$arrPeriodicalOrder.payment_total|number_format}-->円<br>
    <hr>

    <!--{foreach from=$arrPeriodicalOrder.periodical_shippings item=arrShipping}-->
        ▼お届け先<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--><br>
        <!--{if $isMultiple}-->
            <!--{foreach item=item from=$arrShipping.shipment_item}-->
                商品コード：<!--{$item.productsClass.product_code|h}--><br>
                商品名：<!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br>
                <!--{if $item.productsClass.classcategory_name1 != ""}-->
                    <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br>
                <!--{/if}-->
                <!--{if $item.productsClass.classcategory_name2 != ""}-->
                    <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br>
                <!--{/if}-->
                単価：<!--{$item.price|sfCalcIncTax|number_format}-->円<br>
                数量：<!--{$item.quantity}--><br>
                <br>
            <!--{/foreach}-->
        <!--{/if}-->
        ●お名前<br>
        <!--{$arrShipping.shipping_name01|h}-->&nbsp;<!--{$arrShipping.shipping_name02|h}--><br>
        ●お名前(フリガナ)<br>
        <!--{$arrShipping.shipping_kana01|h}-->&nbsp;<!--{$arrShipping.shipping_kana02|h}--><br>
        ●住所<br>
        〒<!--{$arrShipping.shipping_zip01}-->-<!--{$arrShipping.shipping_zip02}--><br>
        <!--{assign var=key1 value="shipping_addr01"}-->
        <!--{assign var=key2 value="shipping_addr02"}-->
        <!--{assign var=key3 value="shipping_pref"}-->
        <!--{assign var=pref value=$arrShipping[$key3]}-->
        <!--{$arrPREFS[$pref]}--><!--{$arrShipping[$key1]|h}--><!--{$arrShipping[$key2]|h}--><br />
        ●電話番号<br>
        <!--{$arrShipping.shipping_tel01}-->-<!--{$arrShipping.shipping_tel02}-->-<!--{$arrShipping.shipping_tel03}--><br>
        <!--{if $arrShipping.shipping_fax01 > 0}-->
            ●FAX番号<br>
            <!--{$arrShipping.shipping_fax01}-->-<!--{$arrShipping.shipping_fax02}-->-<!--{$arrShipping.shipping_fax03}--><br>
        <!--{/if}-->
        <br>
    <!--{/foreach}-->

    <hr>

    ■お届け周期<br>
    <!--{$arrPERIODTYPES[$arrPeriodicalOrder.period_type]|h}-->
    <!--{if $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_WEEKLY")}-->
        <!--{$arrDAYS[$arrPeriodicalOrder.period_day]}-->曜日
    <!--{elseif $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_BIWEEKLY")}-->
        <!--{$arrDAYS[$arrPeriodicalOrder.period_day]}-->曜日
    <!--{elseif $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY")}-->
        第<!--{$arrWEEKS[$arrPeriodicalOrder.period_week]}--><!--{$arrDAYS[$arrPeriodicalOrder.period_day]}-->曜日
    <!--{elseif $arrPeriodicalOrder.period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE")}-->
        <!--{$arrDATES[$arrPeriodicalOrder.period_date]}-->日
    <!--{/if}-->
    <!--{$arrDELIVERYTIMES[$arrPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
    <br>
    ■次回お届け予定日<br>
    <!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
    <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}-->
    (<!--{$arrDAYS[$day]}-->)
    <!--{$arrDELIVERYTIMES[$arrPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
<!--{/strip}-->
