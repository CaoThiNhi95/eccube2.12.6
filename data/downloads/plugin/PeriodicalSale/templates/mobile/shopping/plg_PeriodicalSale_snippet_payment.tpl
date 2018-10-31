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
<!--{assign var=period_type_key value="period_type"}-->
<!--{assign var=period_delivery_time_key value="period_delivery_time"}-->
<!--{assign var=period_type_weekly value="PeriodicalSale::PERIOD_TYPE_WEEKLY"|constant}-->
<!--{assign var=period_type_biweekly value="PeriodicalSale::PERIOD_TYPE_BIWEEKLY"|constant}-->
<!--{assign var=period_type_monthly_day value="PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY"|constant}-->
<!--{assign var=period_type_monthly_date value="PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE"|constant}-->

<br />
<br />
■2回目以降のお届け日時<br />

お届け周期:<br />
<select id="periodType" name="<!--{$period_type_key}-->">
    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.weekly}-->
        <option value="<!--{$period_type_weekly}-->" <!--{if $arrForm[$period_type_key].value == $period_type_weekly}-->selected<!--{/if}-->>毎週</option>
    <!--{/if}-->
    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.biweekly}-->
        <option value="<!--{$period_type_biweekly}-->" <!--{if $arrForm[$period_type_key].value == $period_type_biweekly}-->selected<!--{/if}-->>隔週</option>
    <!--{/if}-->
    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.monthly_day}-->
        <option value="<!--{$period_type_monthly_day}-->" <!--{if $arrForm[$period_type_key].value == $period_type_monthly_day}-->selected<!--{/if}-->>毎月 (曜日指定)</option>
    <!--{/if}-->
    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.monthly_date}-->
        <option value="<!--{$period_type_monthly_date}-->" <!--{if $arrForm[$period_type_key].value == $period_type_monthly_date}-->selected<!--{/if}-->>毎月 (日付指定)</option>
    <!--{/if}-->
</select><br />
お届け週:<br /><font color="#FF0000" size="1"> [毎月 (曜日指定) の場合]</font><br />
<!--{assign var=key value="period_week"}-->
<select name="<!--{$key}-->">
    <!--{foreach from=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_weeks key=k item=i}-->
        <option value="<!--{$k}-->" <!--{if $arrForm[$key].value == $k}-->selected<!--{/if}-->>第<!--{$i|h}--></option>
    <!--{/foreach}-->
</select><br />
お届け曜日:<br /><font color="#FF0000" size="1"> [毎週/隔週/毎月 (曜日指定) の場合]</font><br />
<!--{assign var=key value="period_day"}-->
<select name="<!--{$key}-->">
    <!--{foreach from=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_days key=k item=i}-->
        <option value="<!--{$k}-->" <!--{if $arrForm[$key].value == $k}-->selected<!--{/if}-->><!--{$i|h}-->曜日</option>
    <!--{/foreach}-->
</select><br />
お届け日付指定:<br /><font color="#FF0000" size="1"> [毎月 (日付指定) の場合]</font><br />
<!--{assign var=key value="period_date"}-->
<select name="<!--{$key}-->">
    <!--{foreach from=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_dates key=k item=i}-->
        <option value="<!--{$k}-->" <!--{if $arrForm[$key].value == $k}-->selected<!--{/if}-->><!--{$i|h}-->日</option>
    <!--{/foreach}-->
</select><br />
お届け時間帯: <br />
<select name="<!--{$period_delivery_time_key}-->">
    <option value="" selected="">指定なし</option>
    <!--{html_options options=$arrDelivTime selected=$arrForm[$period_delivery_time_key].value}-->
</select>


<!--{if count($plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_payments) > 0}-->
<br /><br />
■2回目以降の支払い方法<br />
<!--{assign var=key value="period_payment_id"}-->
支払い方法:<br />
<select name="<!--{$key}-->">
    <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_payments selected=$arrForm[$key].value}-->
</select>
<!--{/if}-->
<!--{/if}-->