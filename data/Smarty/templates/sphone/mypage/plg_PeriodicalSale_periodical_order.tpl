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

<script>
$(function(){
    $('#btn_more_history').remove();
})
</script>


<section id="mypagecolumn">

    <h2 class="title">
        <!--{$tpl_title|h}-->
    </h2>
    <!--{if $tpl_navi != ""}-->
        <!--{include file=$tpl_navi}-->
    <!--{else}-->
        <!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
    <!--{/if}-->

    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="periodical_order_id" value="" />
        <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

        <h3 class="title_mypage">
            <!--{$tpl_subtitle|h}-->
        </h3>
        <!--{if $objNavi->all_row > 0}-->

            <!--★インフォメーション★-->
            <div class="information">
                <p>
                    <span class="attention">
                        <span id="historycount">
                            <!--{$objNavi->all_row}-->
                        </span>
                        件
                    </span>の購入履歴があります。
                </p>
            </div>

            <div class="form_area">

                <!--▼フォームボックスここから -->
                <div class="formBox">
                    <!--{foreach from=$arrPeriodicalOrders item=arrPeriodicalOrder}-->
                    <div class="arrowBox">
                        <p>
                            <em>定期注文番号: </em>
                            <span class="periodicalOrderId">
                                <!--{$arrPeriodicalOrder.periodical_order_id}-->
                            </span><br />
                            <em>申込日時: </em>
                            <span class="createDate">
                                <!--{$arrPeriodicalOrder.create_date|sfDispDBDate}-->
                            </span><br />
                            <em>お支払い方法: </em>
                            <span class="payment">
                                <!--{$arrPAYMENTS[$arrPeriodicalOrder.payment_id]}-->
                            </span><br />
                            <em>合計金額: </em>
                            <span class="paymentTotal">
                                <!--{$arrPeriodicalOrder.payment_total|number_format}-->円
                            </span><br />
                            <em>継続状況: </em>
                            <span class="periodicalStatus">
                                <!--{$arrPERIODICALORDERSTATUSES[$arrPeriodicalOrder.periodical_status]}-->
                            </span><br />
                            <em>次回お届け予定日: </em>
                            <span class="nextPeriod">
                                <!--{assign var=day value=$arrPeriodicalOrder.next_period|date_format:"%w"}-->
                                <!--{$arrPeriodicalOrder.next_period|date_format:"%Y/%m/%d"}-->
                                (<!--{$arrDAYS[$day]}-->)
                            </span>
                        </p>
                        <a rel="external" href="plg_PeriodicalSale_periodical_order_history.php?periodical_order_id=<!--{$arrPeriodicalOrder.periodical_order_id}-->">
                        
                        </a>
                    </div>
                    <!--{/foreach}-->
                </div><!-- /.formBox -->
            </div><!-- /.form_area-->
            <div class="btn_area">
                <!--{if $objNavi->all_row > $dispNumber}-->
                    <p><a href="javascript: void(0);" class="btn_more" id="btn_more_history" onClick="getHistory(5); return false;" rel="external">もっとみる(＋<!--{$dispNumber}-->件)</a></p>
                <!--{/if}-->
            </div>
        <!--{else}-->
            <div class="form_area">
                <div class="information">
                    <p>購入履歴はありません。</p>
                </div>
            </div><!-- /.form_area-->
        <!--{/if}-->
    </form>
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