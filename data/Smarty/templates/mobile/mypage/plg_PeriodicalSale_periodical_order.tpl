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
    ■定期購入履歴一覧<br>
    <!--{if $objNavi->all_row > 0}-->
        <!--{$objNavi->all_row}-->件の購入履歴があります。<br>
        <br>
        <!--{foreach from=$arrPeriodicalOrders item=arrPeriodicalOrder}-->
            <hr>
            ▽申込日時<br>
            <!--{$arrPeriodicalOrder.create_date|sfDispDBDate}--><br>
            ▽定期注文番号<br>
            <!--{$arrPeriodicalOrder.periodical_order_id}--><br>
            <!--{assign var=payment_id value="`$arrPeriodicalOrder.payment_id`"}-->
            ▽お支払い方法<br>
            <!--{$arrPAYMENTS[$payment_id]|h}--><br>
            ▽合計金額<br>
            <font color="#ff0000"><!--{$arrPeriodicalOrder.payment_total|number_format}-->円</font><br>
            ▽継続状況<br>
            <!--{$arrPERIODICALORDERSTATUSES[$arrPeriodicalOrder.periodical_status]}--><br />
            ▽次回お届け予定日<br />
            <!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
            <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}-->
            (<!--{$arrDAYS[$day]}-->)<br />
            <div align="right"><a href="./plg_PeriodicalSale_periodical_order_history.php?periodical_order_id=<!--{$arrPeriodicalOrder.periodical_order_id}-->">→詳細を見る</a></div><br>
        <!--{/foreach}-->
        <hr>
    <!--{else}-->
        購入履歴はありません。<br>
    <!--{/if}-->

    <!--{if $objNavi->strnavi != ""}-->
        <!--{$objNavi->strnavi}-->
        <br>
    <!--{/if}-->
<!--{/strip}-->
