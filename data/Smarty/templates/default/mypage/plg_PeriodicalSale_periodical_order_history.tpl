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
#orders td{
    text-align:center;
}
</style>

<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->
    <div id="mycontents_area">
        <h3><!--{$tpl_subtitle|h}--></h3>
        <div class="mycondition_area clearfix">
            <p>
                <span class="st">申込日時: </span>
                <!--{$arrPeriodicalOrder.create_date|sfDispDBDate}--><br />
                <span class="st">定期注文番号: </span>
                <!--{$arrPeriodicalOrder.periodical_order_id}--><br />
                <span class="st">お支払い方法: </span>
                <!--{$arrPAYMENTS[$arrPeriodicalOrder.payment_id]|h}-->
            </p>
        </div>

        <table id="periodicalOrderDetails" summary="購入商品詳細">
            <col width="15%" />
            <col width="25%" />
            <col width="20%" />
            <col width="15%" />
            <col width="10%" />
            <col width="15%" />
            <tr>
                <th class="alignC">商品コード</th>
                <th class="alignC">商品名</th>
                <th class="alignC">商品種別</th>
                <th class="alignC">単価</th>
                <th class="alignC">数量</th>
                <th class="alignC">小計</th>
            </tr>
            <!--{foreach from=$arrPeriodicalOrder.periodical_order_details item=periodicalOrderDetail}-->
                <tr>
                    <td><!--{$periodicalOrderDetail.product_code|h}--></td>
                    <td><a<!--{if $periodicalOrderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$periodicalOrderDetail.product_id|u}-->"<!--{/if}-->><!--{$periodicalOrderDetail.product_name|h}--></a><br />
                        <!--{if $periodicalOrderDetail.classcategory_name1 != ""}-->
                            <!--{$periodicalOrderDetail.classcategory_name1|h}--><br />
                        <!--{/if}-->
                        <!--{if $periodicalOrderDetail.classcategory_name2 != ""}-->
                            <!--{$periodicalOrderDetail.classcategory_name2|h}-->
                        <!--{/if}-->
                    </td>
                    <td class="alignC">
                    <!--{if $periodicalOrderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
                        <!--{if $periodicalOrderDetail.is_downloadable}-->
                            <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$arrPeriodicalOrder.order_id}-->&product_id=<!--{$periodicalOrderDetail.product_id}-->&product_class_id=<!--{$periodicalOrderDetail.product_class_id}-->">ダウンロード</a>
                        <!--{else}-->
                            <!--{if $periodicalOrderDetail.payment_date == "" && $periodicalOrderDetail.effective == "0"}-->
                                <!--{$arrProductType[$periodicalOrderDetail.product_type_id]}--><BR />（入金確認中）
                            <!--{else}-->
                                <!--{$arrProductType[$periodicalOrderDetail.product_type_id]}--><BR />（期限切れ）
                            <!--{/if}-->
                        <!--{/if}-->
                    <!--{else}-->
                        <!--{$arrProductType[$periodicalOrderDetail.product_type_id]}-->
                    <!--{/if}-->
                    </td>
                    <!--{assign var=price value=`$periodicalOrderDetail.price`}-->
                    <!--{assign var=quantity value=`$periodicalOrderDetail.quantity`}-->
                    <td class="alignR"><!--{$price|sfCalcIncTax|number_format|h}-->円</td>
                    <td class="alignR"><!--{$quantity|h}--></td>
                    <td class="alignR"><!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}-->円</td>
                </tr>
            <!--{/foreach}-->
            <tr>
                <th colspan="5" class="alignR">小計</th>
                <td class="alignR"><!--{$arrPeriodicalOrder.subtotal|number_format}-->円</td>
            </tr>
            <!--{assign var=key value="discount"}-->
            <!--{if $arrPeriodicalOrder[$key] != "" && $arrPeriodicalOrder[$key] > 0}-->
            <tr>
                <th colspan="5" class="alignR">値引き</th>
                <td class="alignR">&minus;<!--{$arrPeriodicalOrder[$key]|number_format}-->円</td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="alignR">送料</th>
                <td class="alignR"><!--{assign var=key value="deliv_fee"}--><!--{$arrPeriodicalOrder[$key]|number_format|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="alignR">手数料</th>
                <!--{assign var=key value="charge"}-->
                <td class="alignR"><!--{$arrPeriodicalOrder[$key]|number_format|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="alignR">合計</th>
                <td class="alignR"><span class="price"><!--{$arrPeriodicalOrder.payment_total|number_format}-->円</span></td>
            </tr>
        </table>
            
        <h3>
            お届け先情報
        </h3>
        <!--{foreach from=$arrPeriodicalOrder.periodical_shippings item=arrShipping}-->
        <table class="shipping">
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th>
                    お名前
                </th>
                <td>
                    <!--{assign var=key1 value="shipping_name01"}-->
                    <!--{assign var=key2 value="shipping_name02"}-->
                    <!--{$arrShipping[$key1]|h}-->
                    <!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>
                    お名前 (カナ)
                </th>
                <td>
                    <!--{assign var=key1 value="shipping_kana01"}-->
                    <!--{assign var=key2 value="shipping_kana02"}-->
                    <!--{$arrShipping[$key1]|h}-->
                    <!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>
                    郵便番号
                </th>
                <td>
                    <!--{assign var=key1 value="shipping_zip01"}-->
                    <!--{assign var=key2 value="shipping_zip02"}-->
                    〒<!--{$arrShipping[$key1]|h}-->-<!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>
                    住所
                </th>
                <td colspan="3">
                    <!--{assign var=key1 value="shipping_addr01"}-->
                    <!--{assign var=key2 value="shipping_addr02"}-->
                    <!--{assign var=key3 value="shipping_pref"}-->
                    <!--{assign var=pref value=$arrShipping[$key3]}-->
                    <!--{$arrPREFS[$pref]}--><!--{$arrShipping[$key1]|h}--><!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>
                    電話番号
                </th>
                <td>
                    <!--{assign var=key1 value="shipping_tel01"}-->
                    <!--{assign var=key2 value="shipping_tel02"}-->
                    <!--{assign var=key3 value="shipping_tel03"}-->
                    <!--{$arrShipping[$key1]|h}-->-<!--{$arrShipping[$key2]|h}-->-<!--{$arrShipping[$key3]|h}-->
                </td>
            </tr>
        </table>
        <!--{/foreach}-->
        
        <h3>
            定期情報
        </h3>
        <table id="period">
            <col width="30%" />
            <col width="70%" />
            <tr>
                <th class="alignL">
                    お届け周期
                </th>
                <td>
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
                </td>
            </tr>
            <tr>
                <th class="alignL">
                    次回お届け予定日
                </th>
                <td>
                    <!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
                    <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}-->
                    (<!--{$arrDAYS[$day]}-->)
                    <!--{$arrDELIVERYTIMES[$arrPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
                </td>
            </tr>
        </table>
                
        <h3>
            この定期注文と関連する注文 (最新の10件)
        </h3>
        <table id="orders">
            <thead>
                <tr>
                    <th class="alignC">購入日時</th>
                    <th class="alignC">注文番号</th>
                    <th class="alignC">お支払い方法</th>
                    <th class="alignC">合計金額</th>
                    <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                    <th class="alignC">ご注文状況</th>
                    <!--{/if}-->
                    <th class="alignC">詳細</th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$arrPeriodicalOrder.orders item=arrOrder name=loop}-->
                    <!--{if $smarty.foreach.loop.index < 10}-->
                    <tr>
                        <td class="">
                            <!--{$arrOrder.create_date|sfDispDBDate}-->
                        </td>
                        <td>
                            <!--{$arrOrder.order_id}-->
                        </td>
                        <!--{assign var=payment_id value="`$arrOrder.payment_id`"}-->
                        <td class="">
                            <!--{$arrPAYMENTS[$payment_id]|h}-->
                        </td>
                        <td class="">
                            <!--{$arrOrder.payment_total|number_format}-->円
                        </td>
                        
                        <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                            <!--{assign var=order_status_id value="`$arrOrder.status`"}-->
                            <!--{if $order_status_id != $smarty.const.ORDER_PENDING }-->
                            <td class="">
                                <!--{$arrORDERSTATUSES[$order_status_id]|h}-->
                            </td>
                            <!--{else}-->
                            <td class="attention">
                                <!--{$arrORDERSTATUSES[$order_status_id]|h}-->
                            </td>
                            <!--{/if}-->
                        <!--{/if}-->
                        <td class="">
                            <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder.order_id}-->">
                                詳細
                            </a>
                        </td>
                    </tr>
                    <!--{/if}-->
                <!--{/foreach}-->
            </tbody>
        </table>
    </div>
</div>