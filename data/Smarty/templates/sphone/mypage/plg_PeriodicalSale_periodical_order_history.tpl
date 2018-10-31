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

<style>
.periodicalFormBox dt{
    font-weight: bold;
    line-height: 1.2;
    padding: 0.75em;
    position: relative;
    vertical-align: middle; 
    border-radius: 7px 7px 0 0;
    border-top: 1px solid #FFFFFF;
    border-bottom: 1px solid #CCCCCC;
}
.periodicalFormBox dd{
    border-bottom: 1px solid #CCCCCC;
    display: block;
    font-size: 12px;
    line-height: 1.3;
    padding: 10px;
    padding:0.75em;
}
</style>

<section id="mypagecolumn">
    <h2 class="title">
        <!--{$tpl_title|h}-->
    </h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

    <div class="form_area">
        <div id="historyBox">
            <p>
                <em>定期注文番号: </em>
                <!--{$arrPeriodicalOrder.periodical_order_id}--><br />
                <em>申込日時: </em>
                <!--{$arrPeriodicalOrder.create_date|sfDispDBDate}--><br />
                <em>お支払い方法: </em><!--{$arrPAYMENTS[$arrPeriodicalOrder.payment_id]|h}-->
            </p>
        </div>

        <div class="formBox">
            <!--▼カートの中の商品一覧 -->
            <div class="cartinarea clearfix">

                <!--▼商品 -->
                <!--{foreach from=$arrPeriodicalOrder.periodical_order_details item=arrPeriodicalOrderDetail}-->
                    <!--{assign var=price value=`$arrPeriodicalOrderDetail.price`}-->
                    <!--{assign var=quantity value=`$arrPeriodicalOrderDetail.quantity`}-->
                    <div>
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrPeriodicalOrderDetail.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="<!--{$arrPeriodicalOrderDetail.product_name|h}-->" class="photoL" />
                        <div class="cartinContents">
                            <div>
                                <p>
                                    <em><!--→商品名-->
                                        <a<!--{if $arrPeriodicalOrderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrPeriodicalOrderDetail.product_id|u}-->"<!--{/if}--> rel="external">
                                            <!--{$arrPeriodicalOrderDetail.product_name|h}-->
                                        </a>
                                        <!--←商品名-->
                                    </em>
                                </p>
                                <p>
                                    <!--→金額-->
                                    <span class="mini">価格:</span><!--{$price|number_format|h}-->円
                                    <!--←金額-->
                                </p>

                                <!--→商品種別-->
                                <!--{if $arrPeriodicalOrderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                                    <p id="downloadable">
                                        <!--{if $arrPeriodicalOrderDetail.is_downloadable}-->
                                            <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$arrPeriodicalOrder.order_id}-->&amp;product_id=<!--{$arrPeriodicalOrderDetail.product_id}-->&amp;product_class_id=<!--{$arrPeriodicalOrderDetail.product_class_id}-->" rel="external">
                                                ダウンロード
                                            </a><br />
                                        <!--{else}-->
                                            <!--{if $arrPeriodicalOrderDetail.payment_date == "" && $arrPeriodicalOrderDetail.effective == "0"}-->
                                                <!--{$arrProductType[$arrPeriodicalOrderDetail.product_type_id]}--><br />（入金確認中）
                                            <!--{else}-->
                                                <!--{$arrProductType[$arrPeriodicalOrderDetail.product_type_id]}--><br />（期限切れ）
                                            <!--{/if}-->
                                        <!--{/if}-->
                                    </p>
                                <!--{/if}-->
                                <!--←商品種別-->
                            </div>

                            <ul>
                                <li>
                                    <span class="mini">数量: </span>
                                    <!--{$quantity|h}-->
                                </li>
                                <li class="result">
                                    <span class="mini">
                                        小計: 
                                    </span>
                                    <!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}-->円
                                </li>
                            </ul>
                        </div>
                    </div>
                <!--{/foreach}-->
                <!--▲商品 -->

            </div><!--{* /.cartinarea *}-->
            <!--▲ カートの中の商品一覧 -->

            <div class="total_area">
                <div><span class="mini">小計：</span><!--{$arrPeriodicalOrder.subtotal|number_format}-->円</div>
                <!--{if $arrPeriodicalOrder.discount != '' && $arrPeriodicalOrder.discount > 0}-->
                    <div><span class="mini">値引き：</span>&minus;<!--{$arrPeriodicalOrder.discount|number_format}-->円</div>
                <!--{/if}-->
                <div><span class="mini">送料：</span><!--{$arrPeriodicalOrder.deliv_fee|number_format}-->円</div>
                <div><span class="mini">手数料：</span><!--{$arrPeriodicalOrder.charge|number_format}-->円</div>
                <div><span class="mini">合計：</span><span class="price fb"><!--{$arrPeriodicalOrder.payment_total|number_format}-->円</span></div>
            </div>
        </div><!-- /.formBox -->
        
        <!--{foreach from=$arrPeriodicalOrder.periodical_shippings item=arrPeriodicalShipping}-->
            <div class="formBox periodicalFormBox">
                <dl class="shippingArea">
                    <dt>
                        お届け先情報
                    </dt>
                    <dd>
                        <p>
                            〒<!--{$arrPeriodicalShipping.shipping_zip01|h}-->-<!--{$arrPeriodicalShipping.shipping_zip02|h}--><br />
                            <!--{$arrPref[$arrPeriodicalShipping.shipping_pref]}--><!--{$arrPeriodicalShipping.shipping_addr01|cat:$arrPeriodicalShipping.shipping_addr02|h}-->
                        </p>
                        <p class="deliv_name">
                            <!--{$arrPeriodicalShipping.shipping_name01|cat:" "|cat:$arrPeriodicalShipping.shipping_name02|h}--><br />
                        </p>
                        <p>
                            <!--{$arrPeriodicalShipping.shipping_tel01|h}-->-<!--{$arrPeriodicalShipping.shipping_tel02|h}-->-<!--{$arrPeriodicalShipping.shipping_tel03|h}-->
                        </p>
                        <!--{if $arrPeriodicalShipping.shipping_fax01 > 0}-->
                        <p>
                            <!--{$arrPeriodicalShipping.shipping_fax01|h}-->-<!--{$arrPeriodicalShipping.shipping_fax02|h}-->-<!--{$arrPeriodicalShipping.shipping_fax03|h}-->
                        </p>
                        <!--{/if}-->
                    </dd>
                    <dd>
                        <em>
                            お届け周期: 
                        </em><br />
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
                        <br />
                        <em>
                            次回お届け予定日:
                        </em><br />
                        <!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
                        <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}-->
                        (<!--{$arrDAYS[$day]}-->)
                        <!--{$arrDELIVERYTIMES[$arrPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
                    </dd>
                </dl>
            </div>
        <!--{/foreach}-->
        
        <dl class="formBox periodicalFormBox">
            <dt>
                この定期注文と関連する注文 (最新の10件)
            </dt>
            <!--{foreach from=$arrPeriodicalOrder.orders item=arrOrder}-->
                <!--{if $smarty.foreach.loop.index < 10}-->
                <!--▼商品 -->
                <dd class="arrowBox">
                    <p>
                        <em>注文番号：</em><span class="order_id"><!--{$arrOrder.order_id}--><!--{assign var=payment_id value="`$arrOrder.payment_id`"}--></span><br />
                        <em>購入日時：</em><span class="create_date"><!--{$arrOrder.create_date|sfDispDBDate}--></span><br />
                        <em>お支払い方法：</em><span class="payment_id"><!--{$arrPAYMENTS[$payment_id]|h}--></span><br />
                        <em>合計金額：</em><span class="payment_total"><!--{$arrOrder.payment_total|number_format}--></span>円<br />
                        <em>ご注文状況：</em>
                        <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                            <!--{assign var=order_status_id value="`$arrOrder.status`"}-->
                            <!--{if $order_status_id != $smarty.const.ORDER_PENDING }-->
                            <span class="order_status"><!--{$arrORDERSTATUSES[$order_status_id]|h}--></span><br />
                            <!--{else}-->
                            <span class="order_status attention"><!--{$arrORDERSTATUSES[$order_status_id]|h}--></span><br />
                            <!--{/if}-->
                        <!--{/if}-->
                    </p>
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder.order_id}-->" rel="external"></a>
                </dd>
                <!--▲商品 -->
                <!--{/if}-->
            <!--{/foreach}-->
        </dl><!-- /.formBox -->

        <p>
            <a rel="external" class="btn_more" href="./plg_PeriodicalSale_periodical_order.php">
                定期購入履歴一覧に戻る
            </a>
        </p>

    </div><!-- /.form_area -->

</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->