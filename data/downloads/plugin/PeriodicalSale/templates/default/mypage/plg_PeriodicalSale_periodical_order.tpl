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
#periodicalOrders th{
    text-align:center !important;
}
#periodicalOrders td{
    text-align:center !important;
}
</style>

<div id="mypagecolumn">
    <h2 class="title">
        <!--{$tpl_title|h}-->
    </h2>
    <!--{if $tpl_navi != ""}-->
        <!--{include file=$tpl_navi}-->
    <!--{else}-->
        <!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
    <!--{/if}-->
    
    <div id="mycontents_area">
        <form name="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="periodical_order_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
            <h3>
                <!--{$tpl_subtitle|h}-->
            </h3>
            <p>
                <span class="attention">
                    <!--{$objNavi->all_row}-->件
                </span>
                の定期購入履歴があります。
            </p>
            <div class="pagenumber_area">
                <!--{$objNavi->strnavi}-->
            </div>
            <table id="periodicalOrders">
                <thead>
                    <tr>
                        <th>
                            申込日時
                        </th>
                        <th>
                            定期注文番号
                        </th>
                        <th>
                            お支払い方法
                        </th>
                        <th>
                            合計金額
                        </th>
                        <th>
                            継続状況
                        </th>
                        <th>
                            次回お届け予定日
                        </th>
                        <th>
                            詳細
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!--{foreach from=$arrPeriodicalOrders item=arrPeriodicalOrder}-->
                    <tr>
                        <td class="createDate">
                            <!--{$arrPeriodicalOrder.create_date|sfDispDBDate}-->
                        </td>
                        <td class="periodicalOrderId">
                            <!--{$arrPeriodicalOrder.periodical_order_id}-->
                        </td>
                        <td class="paymentId">
                            <!--{$arrPAYMENTS[$arrPeriodicalOrder.payment_id]}-->
                        </td>
                        <td class="paymentTotal">
                            <!--{$arrPeriodicalOrder.payment_total|number_format}-->円
                        </td>
                        <td class="periodicalStatus">
                            <!--{$arrPERIODICALORDERSTATUSES[$arrPeriodicalOrder.periodical_status]}-->
                        </td>
                        <td class="nextPeriod">
                            <!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
                            <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}-->
                            (<!--{$arrDAYS[$day]}-->)
                        </td>
                        <td class="detail">
                            <a href="plg_PeriodicalSale_periodical_order_history.php?periodical_order_id=<!--{$arrPeriodicalOrder.periodical_order_id}-->">
                                詳細
                            </a>
                        </td>
                    </tr>
                    <!--{/foreach}-->
                </tbody>
            </table>
        </form>
    </div>
</div>