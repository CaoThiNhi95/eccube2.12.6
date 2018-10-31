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
 
 
<!--{if $plg_PeriodicalSale_has_periodical_cart_item}-->
<!--{assign var=period_type value=$plg_PeriodicalSale_arrTempPeriodicalOrder.period_type}-->
<section id="periodArea">
    <h3 class="subtitle">
        2回目以降のお届け日時指定 
    </h3>
    <div class="form_area">
        <div class="formBox">
            <div class="innerBox">
                <em>お届け日：</em>
                <!--{if $period_type == constant("PeriodicalSale::PERIOD_TYPE_WEEKLY")}-->
                    毎週
                    <!--{$plg_PeriodicalSale_arrDAYS[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_day]}-->曜日
                <!--{elseif $period_type == constant("PeriodicalSale::PERIOD_TYPE_BIWEEKLY")}-->
                    隔週
                    <!--{$plg_PeriodicalSale_arrDAYS[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_day]}-->曜日
                <!--{elseif $period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY")}-->
                    第<!--{$plg_PeriodicalSale_arrPERIODWEEKS[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_week]}-->
                    <!--{$plg_PeriodicalSale_arrDAYS[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_day]}-->曜日
                <!--{elseif $period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE")}-->
                    毎月<!--{$plg_PeriodicalSale_arrPERIODDATES[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_date]}-->日
                <!--{/if}-->
                <!--{$plg_PeriodicalSale_arrPeriodicalDeliveryTimes[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
            </div>
            <div class="innerBox">
                <em>2回目以降の支払い方法：</em>
                <!--{$plg_PeriodicalSale_arrPAYMENTS[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_payment_id]}-->
            </div>
        </div>
        <div class="formBox">
            <dl class="deliv_confirm">
                <!--{foreach from=$plg_PeriodicalSale_arrShippings|@array_values key=index item=arrShipping}-->
                <dt>
                    <em>配送先<!--{$index+1}-->：</em>
                </dt>
                <dd>
                    <p>
                        〒<!--{$arrShipping.shipping_zip01|h}-->-<!--{$arrShipping.shipping_zip02|h}--><br />
                        <!--{$arrPref[$arrShipping.shipping_pref]}--><!--{$arrShipping.shipping_addr01|cat:$arrShipping.shipping_addr02|h}-->
                    </p>
                    <p class="deliv_name">
                        <!--{$arrShipping.shipping_name01|cat:" "|cat:$arrShipping.shipping_name02|h}--><br />
                    </p>
                    <p>
                        TEL: <!--{$arrShipping.shipping_tel01|h}-->-<!--{$arrShipping.shipping_tel02|h}-->-<!--{$arrShipping.shipping_tel03|h}-->
                    </p>
                    <!--{if $arrShipping.shipping_fax01 > 0}-->
                    <p>
                        FAX: <!--{$arrShipping.shipping_fax01|h}-->-<!--{$arrShipping.shipping_fax02|h}-->-<!--{$arrShipping.shipping_fax03|h}-->
                    </p>
                    <!--{/if}-->
                </dd>
                <dd>
                    <em>次回お届け予定日：</em>
                    <!--{assign var=day value=$arrShipping.next_period|date_format:"%w"}-->
                    <!--{$arrShipping.next_period|date_format:"%Y/%m/%d"}-->
                    (<!--{$plg_PeriodicalSale_arrDAYS[$day]}-->)
                    <!--{$plg_PeriodicalSale_arrPeriodicalDeliveryTimes[$plg_PeriodicalSale_arrTempPeriodicalOrder.period_delivery_time]|default:"指定なし"}-->
                </dd>
                <!--{/foreach}-->
            </dl>
        </div>
            
            
    </div>
</section>
<!--{/if}-->