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


<script type="text/javascript">
$(function(){

    
    /**
     * 通信エラー表示.
     */
    function remoteException(XMLHttpRequest, textStatus, errorThrown) {
        alert('通信中にエラーが発生しました。カート画面に移動します。');
        location.href = '<!--{$smarty.const.CART_URLPATH}-->';
    }

    /**
     * 配送方法の選択状態により表示を切り替える
     */
    function showForm(show) {
        if (show) {
            $('#period').show();
        }
        else {
            $('#period').hide();
        }
    }
    
    if ($('input[name=deliv_id]:checked').val()
        || $('#deliv_id').val()) {
        showForm(true);
    }
    else {
        showForm(false);
    }
    
    $('input[id^=deliv_]').click(function() {
        showForm(true);
        var data = {};
        data.mode = 'select_deliv';
        data.deliv_id = $(this).val();
        data['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = '<!--{$transactionid}-->';
        $.ajax({
            type : 'POST',
            url : location.pathname,
            data: data,
            cache : false,
            dataType : 'json',
            error : remoteException,
            success : function(data, dataType) {
                if (data.error) {
                    remoteException();
                }
                else {
                    // お届け時間を生成
                    var deliv_time_id_select = $('select[name="period_delivery_time"]');
                    deliv_time_id_select.empty();
                    deliv_time_id_select.append($('<option />').text('指定なし').val(''));
                    for (var i in data.arrDelivTime) {
                        var option = $('<option />')
                            .val(i)
                            .text(data.arrDelivTime[i])
                            .appendTo(deliv_time_id_select);
                    }
                    var deliv_time_ui_button = $('#periodDeliveryTime .ui-btn-text');
                    deliv_time_ui_button.text('指定なし');
                }
            }
        });
    });
    
    var periodType = $('#periodType');
    var periodList = $('#periodList');
    
    function fnUpdatePeriodList(){
        
        var periodTypeText = periodType.val();
        periodList.find('li').each(function(){
            
            if($(this).hasClass(periodTypeText)){
                $(this).show().find('input, select').attr('disabled', false);
            }
            else{
                $(this).hide().find('input, select').attr('disabled', 'disabled');
            }
        });
    }
    
    fnUpdatePeriodList();
    periodType.change(function(){
        fnUpdatePeriodList();
    });
});
</script>

<section id="periodArea">
    <h3 class="subtitle">
        2回目以降のお届け日時指定
    </h3>
    <div class="form_area">
        <p class="non-select-msg">
            まずはじめに、配送方法を選択ください。
        </p>
        <div id="period">
            <p class="fb">
                定期商品をご購入の方は、2回目以降のお届け周期・お届け時間をご指定ください。
            </p>
            <p>
                <span class="attention">
                    <!--{$arrErr[$period_type_key]}-->
                </span>
            </p>
            <!--{if count($plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_payments) > 0}-->
            <!--{assign var=key value="period_payment_id"}-->
            <label>
                <p class="fb">2回目以降の支払い方法</p>
                <select name="<!--{$key}-->">
                    <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_payments selected=$arrForm[$key].value}-->
                </select>
            </label>
            <!--{/if}-->
            <label>
                <p class="fb">お届け周期</p>
                <select id="periodType" name="<!--{$period_type_key}-->">
                    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.weekly}-->
                        <option <!--{if $arrForm[$period_type_key].value == $period_type_weekly}-->selected<!--{/if}--> value="<!--{$period_type_weekly}-->">毎週</option>
                    <!--{/if}-->
                    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.biweekly}-->
                        <option <!--{if $arrForm[$period_type_key].value == $period_type_biweekly}-->selected<!--{/if}--> value="<!--{$period_type_biweekly}-->">隔週</option>
                    <!--{/if}-->
                    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.monthly_day}-->
                        <option <!--{if $arrForm[$period_type_key].value == $period_type_monthly_day}-->selected<!--{/if}--> value="<!--{$period_type_monthly_day}-->">毎月 (曜日指定)</option>
                    <!--{/if}-->
                    <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.monthly_date}-->
                        <option <!--{if $arrForm[$period_type_key].value == $period_type_monthly_date}-->selected<!--{/if}--> value="<!--{$period_type_monthly_date}-->">毎月 (日付指定)</option>
                    <!--{/if}-->
                </select>
            </label>
            <ul id="periodList">
                <li id="periodTypeWeekly" class="<!--{$period_type_monthly_day}-->">
                    <!--{assign var=key value="period_week"}-->
                    <select name="<!--{$key}-->">
                        <!--{foreach from=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_weeks key=k item=i}-->
                            <option <!--{if $arrForm[$key].value == $k}-->selected<!--{/if}--> value="<!--{$k}-->">第<!--{$i|h}--></option>
                        <!--{/foreach}-->
                    </select>
                </li>
                <li id="periodTypeBiweekly" class="<!--{$period_type_weekly}--> <!--{$period_type_biweekly}--> <!--{$period_type_monthly_day}-->">
                    <!--{assign var=key value="period_day"}-->
                    <select name="<!--{$key}-->">
                        <!--{foreach from=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_days key=k item=i}-->
                            <option <!--{if $arrForm[$key].value == $k}-->selected<!--{/if}--> value="<!--{$k}-->"><!--{$i|h}-->曜日</option>
                        <!--{/foreach}-->
                    </select>
                </li>
                <li id="periodTypeMonthlyDate" class="<!--{$period_type_monthly_date}-->">
                    <!--{assign var=key value="period_date"}-->
                    <select name="<!--{$key}-->">
                        <!--{foreach from=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_dates key=k item=i}-->
                            <option <!--{if $arrForm[$key].value == $k}-->selected<!--{/if}--> value="<!--{$k}-->"><!--{$i|h}-->日</option>
                        <!--{/foreach}-->
                    </select>
                </li>
                <li id="periodDeliveryTime" class="<!--{$period_type_weekly}--> <!--{$period_type_biweekly}--> <!--{$period_type_monthly_day}--> <!--{$period_type_monthly_date}-->">
                    <label>
                        <p class="fb">お届け時間帯</p>
                        <select name="<!--{$period_delivery_time_key}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{html_options options=$arrDelivTime selected=$arrForm[$period_delivery_time_key].value}-->
                        </select>
                    </label>
                </li>
            </ul>
        </div>
    </div>
</section>
            
<!--{/if}-->