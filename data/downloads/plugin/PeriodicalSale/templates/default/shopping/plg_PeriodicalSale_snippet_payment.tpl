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


<style>
 #periodArea li{
	margin:0.5em 0;
	border-bottom-width: 1px;
	border-bottom-style: dotted;
	border-bottom-color: #CCC;
}
 #periodArea li p{
	margin-top:5px;
	text-indent: 2em;
}
 #periodArea p { margin-bottom:10px;}
</style>

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
                }
            }
        });
    });
    
    //定期タイプのradio
    var radios = $('input:radio[name="<!--{$period_type_key}-->"]')
    
    if(radios.filter(':checked').length == 0){
        radios.filter(':first').attr('checked', true);
    }
    if(radios.length == 1){
        radios.hide();
    }
    
    /**
     * disabledのON/OFFを切り替える
     */
    function togglePeriodForm(){
        radios.each(function(){
            if($(this).is(':checked')){
                $(this).closest('li').find('input, select').attr('disabled',false);
            }
            else{
                $(this).closest('li').find('input, select').attr('disabled','disabled');
                $(this).attr('disabled',false);
            }
        });
    }
    
    togglePeriodForm()
    radios.change(function(){
        togglePeriodForm();
    });
});


</script>


<div id="periodArea">
    <h3>
        2回目以降のお届け日時指定
    </h3>
    <p class="non-select-msg">
        まずはじめに、配送方法を選択ください。
    </p>
    <div id="period">
        <p>
            定期商品をご購入の方は、2回目以降のお届け周期・お届け時間をご指定ください。
        </p>
        <p>
            <span class="attention">
                <!--{$arrErr[$period_type_key]}-->
            </span>
        </p>

        <ul>
            <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.weekly}-->
            <li>
                <label>
                    <input type="radio" name="<!--{$period_type_key}-->" value="<!--{$period_type_weekly}-->" <!--{if $arrForm[$period_type_key].value == $period_type_weekly}-->checked<!--{/if}--> />
                    毎週
                </label>
                <p>
                    <em>
                        お届け曜日指定: 
                    </em>
                    <!--{assign var=key value="period_day"}-->
                    <label>
                        毎週
                        <select name="<!--{$key}-->">
                            <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_days selected=$arrForm[$key].value}-->
                        </select>
                        曜日
                    </label>
                    <label>
                        <em>
                            お届け時間指定: 
                        </em>
                        <select name="<!--{$period_delivery_time_key}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{html_options options=$arrDelivTime selected=$arrForm[$period_delivery_time_key].value}-->
                        </select>
                    </label>
                </p>
            </li>
            <!--{/if}-->
            <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.biweekly}-->
            <li>
                <label>
                    <input type="radio" name="<!--{$period_type_key}-->" value="<!--{$period_type_biweekly}-->" <!--{if $arrForm[$period_type_key].value == $period_type_biweekly}-->checked<!--{/if}--> />
                    隔週
                </label>
                <p>
                    <em>
                        お届け曜日指定: 
                    </em>
                    <!--{assign var=key value="period_day"}-->
                    <label>
                        隔週
                        <select name="<!--{$key}-->">
                            <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_days selected=$arrForm[$key].value}-->
                        </select>
                        曜日
                    </label>
                    <label>
                        <em>
                            お届け時間指定: 
                        </em>
                        <select name="<!--{$period_delivery_time_key}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{html_options options=$arrDelivTime selected=$arrForm[$period_delivery_time_key].value}-->
                        </select>
                    </label>
                </p>
            </li>
            <!--{/if}-->
            <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.monthly_day}-->
            <li>
                <label>
                    <input type="radio" name="<!--{$period_type_key}-->" value="<!--{$period_type_monthly_day}-->" <!--{if $arrForm[$period_type_key].value == $period_type_monthly_day}-->checked<!--{/if}--> />
                    毎月 (曜日指定)
                </label>
                <p>
                    <em>
                        お届け曜日指定: 
                    </em>
                    <!--{assign var=key value="period_week"}-->
                    <label>
                        各月第
                        <select name="<!--{$key}-->">
                            <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_weeks selected=$arrForm[$key].value}-->
                        </select>
                    </label>
                    <!--{assign var=key value="period_day"}-->
                    <label>
                        <select name="<!--{$key}-->">
                            <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_days selected=$arrForm[$key].value}-->
                        </select>
                        曜日
                    </label>
                    <label>
                        <em>
                            お届け時間指定: 
                        </em>
                        <select name="<!--{$period_delivery_time_key}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{html_options options=$arrDelivTime selected=$arrForm[$period_delivery_time_key].value}-->
                        </select>
                    </label>
                </p>
            </li>
            <!--{/if}-->
            <!--{if $plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_types.monthly_date}-->
            <li>
                <label>
                    <input type="radio" name="<!--{$period_type_key}-->" value="<!--{$period_type_monthly_date}-->" <!--{if $arrForm[$period_type_key].value == $period_type_monthly_date}-->checked<!--{/if}--> />
                    毎月 (日付指定)
                </label>
                <p>
                    <!--{assign var=key value="period_date"}-->
                    <em>
                        お届け日指定: 
                    </em>
                    <label>
                        各月
                        <select name="<!--{$key}-->">
                            <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_dates selected=$arrForm[$key].value}-->
                        </select>
                        日
                    </label>
                    <label>
                        <em>
                            お届け時間指定: 
                        </em>
                        <select name="<!--{$period_delivery_time_key}-->">
                            <option value="" selected="">指定なし</option>
                            <!--{html_options options=$arrDelivTime selected=$arrForm[$period_delivery_time_key].value}-->
                        </select>
                    </label>
                </p>
            </li>
            <!--{/if}-->
        </ul>

        <!--{if count($plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_payments) > 0}-->
        <!--{assign var=key value="period_payment_id"}-->
        <h3 style="margin-top:30px;">2回目以降のお支払い方法指定</h3>
        <p>
           2回目以降のお支払い方法を選択してください。
        </p>
        <p>
            <em>
                お支払方法: 
            </em>
            <select name="<!--{$key}-->">
                <!--{html_options options=$plg_PeriodicalSale_arrAvailablePeriodInfo.available_period_payments selected=$arrForm[$key].value}-->
            </select>
        </p>
        <!--{/if}-->



    </div>
</div>
<!--{/if}-->