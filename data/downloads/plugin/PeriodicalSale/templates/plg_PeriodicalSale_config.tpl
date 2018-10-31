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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
 
<style>
.dateCol{ width:32%;float:left;}
p{ margin-bottom:5px;}
th{ width:210px !important;}
</style>
 
<script type="text/javascript">
    
    function fnMySubmit(){
        
        document.form1.submit();return false;
    }
</script>
<h2>
    <!--{$tpl_subtitle}-->
</h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="edit" />
    <h2>販売周期</h2>
    <p>定期販売を行なう際の周期（２回以降の注文サイクル）を選択してください。</p>

    <table class="form">
        <!--{assign var=key value="available_period_types"}-->
        <tr>
            <th>
                周期
            </th>
            <td>
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <!--{foreach from=$arrPERIODTYPES key=type item=item}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$type}-->]" value="0" />
                    <label>
                        <input type="checkbox" name="<!--{$key}-->[<!--{$type}-->]" value="1" <!--{if $arrForm[$key][$type]}-->checked="checked"<!--{/if}--> />
                        <!--{$arrPERIODTYPES[$type]}-->
                    </label>
                    <br />
                <!--{/foreach}-->
            </td>
        </tr>
   </table>

    <h2>２回以降のお届け日の設定</h2>
    <p>２回目以降のお届け可能な曜日・日付を設定してください。</p>
    <table class="form">
        <!--{assign var=key value="available_period_weeks"}-->
        <tr>
            <th>
                <p>選択可能な週</p>
                <small class="attention">
                    ※毎月 (曜日指定) を選択した場合必須。
                </small>
            </th>
            <td>
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <!--{foreach from=$arrWEEKS key=week item=item}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$week}-->]" value="0" />
                    <label>
                        <input type="checkbox" name="<!--{$key}-->[<!--{$week}-->]" value="1" <!--{if $arrForm[$key][$week]}-->checked="checked"<!--{/if}--> />
                        第<!--{$arrWEEKS[$week]}-->週
                    </label>
                    　
                <!--{/foreach}-->
            </td>
        </tr>
        <!--{assign var=key value="available_period_days"}-->
        <tr>
            <th>
                <p>選択可能な曜日</p>
                <small class="attention">
                    ※毎週・隔週・毎月 (曜日指定) を選択した場合必須。
                </small>
            </th>
            <td>
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <!--{foreach from=$arrDAYS key=day item=item}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$day}-->]" value="0" />
                    <label>
                        <input type="checkbox" name="<!--{$key}-->[<!--{$day}-->]" value="1" <!--{if $arrForm[$key][$day]}-->checked="checked"<!--{/if}--> />
                        <!--{$arrDAYS[$day]}-->
                    </label>
                    　
                <!--{/foreach}-->
            </td>
        </tr>
        <!--{assign var=key value="available_period_dates"}-->
        <tr>
            <th>
                <p>選択可能な日付</p>
                <small class="attention">
                    ※毎月 (日付指定) を選択した場合必須。
                </small>
            </th>
            <td>
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <div class="dateCol">
                <!--{foreach from=$arrDATES key=date item=item name=dates}-->
                <!--{if $smarty.foreach.dates.index % 10 == 0 AND $smarty.foreach.dates.index != 0}-->
                </div>
                <div class="dateCol">
                <!--{/if}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$date}-->]" value="0" />
                    <label>
                        <input type="checkbox" name="<!--{$key}-->[<!--{$date}-->]" value="1" <!--{if $arrForm[$key][$date]}-->checked="checked"<!--{/if}--> />
                        <!--{$arrDATES[$date]}-->日
                    </label>
                    <br />
                <!--{/foreach}-->
                </div>
            </td>
        </tr>
    </table>
    <h2>２回目のお届け日までの期間</h2>
    <p>初回お届け日から２回目のお届け日までの最低期間を指定して下さい。</p>
    <table class="form">
        <!--{assign var=key1 value="period_weekly_offset_days"}-->
        <!--{assign var=key2 value="period_biweekly_offset_days"}-->
        <!--{assign var=key3 value="period_monthly_day_offset_days"}-->
        <!--{assign var=key4 value="period_monthly_date_offset_days"}-->
        <tr>
            <th>
                初回定期オフセット<span class="attention">*</span>
            </th>
            <td>
                <span class="attention">
                    <!--{$arrErr[$key1]}-->
                    <!--{$arrErr[$key2]}-->
                    <!--{$arrErr[$key3]}-->
                    <!--{$arrErr[$key4]}-->
                </span>
                <p><label>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]}-->" class="box6" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" /> 日
                    (毎週)
                </label></p>
                <p><label>
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]}-->" class="box6" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" /> 日
                    (隔週)
                </label></p>
                <p><label>
                    <input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]}-->" class="box6" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" /> 日
                    (毎月 (曜日))
                </label></p>
                <p><label>
                    <input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4]}-->" class="box6" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->" /> 日
                    (毎月 (日付))
                </label></p>
            </td>
        </tr>
    </table>
    <h2>２回目以降の支払い方法</h2>
    <p>定期販売を行なう際の支払い方法（２回以降の支払い方法）を選択してください。</p>
    <table class="form">
        <!--{assign var=key value="available_period_payments"}-->
        <tr>
            <th>
                選択可能な支払い方法
                <br />
                <small class="attention">
                    ※2回目以降に選択できる支払い方法を指定して下さい。<br />
                    ※選択しない場合、初回と同じ支払い方法になります。
                </small>
            </th>
            <td>
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <!--{foreach from=$arrPAYMENTS key=type item=item}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$type}-->]" value="0" />
                    <label>
                        <input type="checkbox" name="<!--{$key}-->[<!--{$type}-->]" value="1" <!--{if $arrForm[$key][$type]}-->checked="checked"<!--{/if}--> />
                        <!--{$arrPAYMENTS[$type]}-->
                    </label>
                    <br />
                <!--{/foreach}-->
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li>
                <a class="btn-action" href="javascript:void(0);" onclick="fnMySubmit();"><span class="btn-next">設定を保存する</span></a>
            </li>
        </ul>
    </div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->