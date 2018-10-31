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

<!--{assign var=period_type_key value="period_type"}-->
<!--{assign var=payment_id_key value="payment_id"}-->
<!--{assign var=period_delivery_time_key value="period_delivery_time"}-->

<script type="text/javascript">
    
function fnChangeFormAction(form, url) {
    document.forms[form].action = url;
} 

function fnEditOrder(order_id, button){
    var form = $(button).closest('form');
    var originalAction = form.attr('action');
    var action = '<!--{$smarty.const.ADMIN_ORDER_EDIT_URLPATH}-->';
    var name = form.attr('name');
    fnChangeFormAction(name, action)
    fnFormModeSubmit(name,'pre_edit','order_id',order_id);
    fnChangeFormAction(name, originalAction);
}

function fnCopyFromPeriodicalOrderData() {
    df = document.form1;
    df['shipping_name01[0]'].value = df.order_name01.value;
    df['shipping_name02[0]'].value = df.order_name02.value;
    df['shipping_kana01[0]'].value = df.order_kana01.value;
    df['shipping_kana02[0]'].value = df.order_kana02.value;
    df['shipping_zip01[0]'].value = df.order_zip01.value;
    df['shipping_zip02[0]'].value = df.order_zip02.value;
    df['shipping_tel01[0]'].value = df.order_tel01.value;
    df['shipping_tel02[0]'].value = df.order_tel02.value;
    df['shipping_tel03[0]'].value = df.order_tel03.value;
    df['shipping_pref[0]'].value = df.order_pref.value;
    df['shipping_addr01[0]'].value = df.order_addr01.value;
    df['shipping_addr02[0]'].value = df.order_addr02.value;
}

$(function(){
    /**
     * 通信エラー表示.
     */
    function remoteException(XMLHttpRequest, textStatus, errorThrown) {
        alert('通信中にエラーが発生しました。');
    }
    
    $('#deliv_id').change(function() {
        var data = {};
        data.mode = 'select_delivery';
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
                    // 支払方法を生成
                    var payment_id_select = $('select[name="<!--{$payment_id_key}-->"]');
                    payment_id_select.empty();
                    for(var i in data.arrPayment){
                        var option = $('<option />').val(data.arrPayment[i].payment_id).text(data.arrPayment[i].payment_method);
                        payment_id_select.append(option);
                    }
                    // お届け時間を生成
                    var deliv_time_id_select = $('select[name="<!--{$period_delivery_time_key}-->"]');
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
    
    /**
     * disabledのON/OFFを切り替える
     */
    function togglePeriodForm(){
        radios.each(function(){
            if($(this).is(':checked')){
                $(this).closest('tr').find('input, select').attr('disabled',false);
            }
            else{
                $(this).closest('tr').find('input, select').attr('disabled','disabled');
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
 
<style>
#periodicalOrderDetails th{
    width:auto;
}
#periodicalOrderDetails tfoot th{
    text-align:right;
}
#orders td{
    text-align:center;
}
</style>
 
<div class="contents-main">
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="<!--{$tpl_mode|default:"edit"|h}-->" />
        <input type="hidden" name="anchor_key" value="" />
        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="edit_customer_id" value="" />
        <input type="hidden" id="add_product_id" name="add_product_id" value="" />
        <input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
        <input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
        <input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
        <input type="hidden" id="no" name="no" value="" />
        <input type="hidden" id="delete_no" name="delete_no" value="" />
        <!--{assign var=key value="periodical_order_id"}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
        
        <!--{foreach key=key item=item from=$arrSearchHidden}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

        <!--{if strlen($arrForm.periodical_order_id.value) > 0}-->
        <div id="periodicalOrderHeader">
            <table class="form">
                <tr>
                    <th>
                        定期ID
                    </th>
                    <td>
                        <!--{assign var=key value="periodical_order_id"}-->
                        <!--{$arrForm[$key].value|h}-->
                    </td>
                </tr>
                <tr>
                    <th>
                        定期継続状況
                    </th>
                    <td>
                        <!--{assign var=key value="periodical_status"}-->
                        <!--{assign var=periodical_status value=$arrForm[$key].value|h}-->
                        <!--{$arrPERIODICALORDERSTATUSES[$periodical_status]|h}-->
                    </td>
                </tr>
                <tr>
                    <th>
                        定期回数
                    </th>
                    <td>
                        <!--{assign var=key value="total_periodical_times"}-->
                        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                        <!--{$arrForm[$key].value|h}--> 回目
                    </td>
                </tr>
                <tr>
                    <th>
                        定期受注日
                    </th>
                    <td>
                        <!--{assign var=key value="create_date"}-->
                        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                        <!--{$arrForm[$key].value|sfDispDBDate|h}-->
                    </td>
                </tr>
                <tr>
                    <th>
                        次回発行分お届け日
                    </th>
                    <td>
                        <!--{assign var=key value="next_period"}-->
                        <!--{assign var=day value=$arrForm[$key].value|date_format:"%w"}-->
                        <!--{assign var=delivery_time value=$arrForm[$period_delivery_time_key].value|h}-->
                        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                        <!--{$arrForm[$key].value|date_format:"%Y/%m/%d"}-->
                        (<!--{$arrDAYS[$day]}-->)
                        <!--{$arrDELIVERYTIMES[$delivery_time]|default:"指定なし"}-->
                    </td>
                </tr>
                <tr>
                    <!--{if $arrForm.periodical_status.value != 0}-->
                        <!--{assign var=class value="plgPeriodicalSaleUncommittable"}-->
                    <!--{elseif $arrLastOrder.status == $smarty.const.ORDER_DELIV || $arrLastOrder.status == $smarty.const.ORDER_CANCEL}-->
                        <!--{assign var=class value="plgPeriodicalSaleCommittable"}-->
                    <!--{elseif strlen($arrLastOrder.status) > 0}-->
                        <!--{assign var=class value="plgPeriodicalSaleCommitted"}-->
                    <!--{else}-->
                        <!--{assign var=class value=""}-->
                    <!--{/if}-->
                    <th>
                        最終注文番号 / 対応状況
                    </th>
                    <td>
                        <a href="javascript:void(0);" onclick="fnEditOrder('<!--{$arrLastOrder.order_id}-->',this);">
                            <!--{$arrLastOrder.order_id}-->
                        </a>
                        /
                        <!--{$arrORDERSTATUSES[$arrLastOrder.status]}-->
                        <div class="<!--{$class}-->">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="btn-area">
            <a class="btn-action" href="javascript:void(0);" onclick="if(confirm('受注データを発行してもよろしいですか？'))fnModeSubmit('commit_order','','');">
                <span class="btn-next">
                    受注データを発行する
                </span>
            </a>
        </div>
        <!--{/if}-->
        <div id="periodicalOrder">
            <h2>
                お客様情報
                <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnOpenWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/search_customer.php','search','600','650'); return false;">会員検索</a>
            </h2>
            <table class="form">
                <tr>
                    <th>
                        会員ID
                    </th>
                    <td colspan="3">
                        <!--{assign var=key value="customer_id"}-->
                        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                        <!--{if $arrForm[$key].value > 0}-->
                        <a href="javascript:void(0);" onclick="fnChangeAction('<!--{$smarty.const.ROOT_URLPATH|cat:$smarty.const.ADMIN_DIR|cat:"customer/edit.php"}-->'); fnModeSubmit('edit_search','edit_customer_id','<!--{$arrForm[$key].value}-->');">
                            <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                            <!--{$arrForm[$key].value|h}-->
                        </a>
                        <!--{else}-->
                        (非会員)
                        <!--{/if}-->
                    </td>
                </tr>
                <tr>
                    <th>
                        お名前
                    </th>
                    <td>
                        <!--{assign var=key1 value="order_name01"}-->
                        <!--{assign var=key2 value="order_name02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1]}-->
                            <!--{$arrErr[$key2]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                    </td>
                    <th>
                        お名前 (フリガナ)
                    </th>
                    <td>
                        <!--{assign var=key1 value="order_kana01"}-->
                        <!--{assign var=key2 value="order_kana02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1]}-->
                            <!--{$arrErr[$key2]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
                    </td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td>
                        <!--{assign var=key value="order_email"}-->
                        <span class="attention">
                            <!--{$arrErr[$key]}-->
                        </span>
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                    </td>
                    <th>電話番号</th>
                    <td>
                        <!--{assign var=key1 value="order_tel01"}-->
                        <!--{assign var=key2 value="order_tel02"}-->
                        <!--{assign var=key3 value="order_tel03"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1]}-->
                            <!--{$arrErr[$key2]}-->
                            <!--{$arrErr[$key3]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
                    </td>
                </tr>
                <tr>
                    <th>郵便番号</th>
                    <td>
                        <!--{assign var=key1 value="order_zip01"}-->
                        <!--{assign var=key2 value="order_zip02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1]}-->
                            <!--{$arrErr[$key2]}-->
                        </span>
                        〒
                        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                        -
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                        <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;">
                            住所入力
                        </a>
                    </td>
                    <th>FAX</th>
                    <td>
                        <!--{assign var=key1 value="order_fax01"}-->
                        <!--{assign var=key2 value="order_fax02"}-->
                        <!--{assign var=key3 value="order_fax03"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1]}-->
                            <!--{$arrErr[$key2]}-->
                            <!--{$arrErr[$key3]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                        <input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
                    </td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td colspan="3">
                        <!--{assign var=key value="order_pref"}-->
                        <span class="attention">
                            <!--{$arrErr[$key]}-->
                        </span>
                        <select class="top" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <option value="" selected="">都道府県を選択</option>
                            <!--{html_options options=$arrPREFS selected=$arrForm[$key].value|h}-->
                        </select>
                        <br />

                        <!--{assign var=key value="order_addr01"}-->
                        <span class="attention">
                            <!--{$arrErr[$key]}-->
                        </span>
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60 top" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />

                        <!--{assign var=key value="order_addr02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key]}-->
                        </span>
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td colspan="3">
                        <!--{assign var=key value="message"}-->
                        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
                        <!--{$arrForm[$key].value|h|nl2br}-->
                    </td>
                </tr>
            </table>
        </div>

        <div id="periodicalShippings">
            <h2>
                お届け先情報
                <a class="btn-normal" href="javascript:;" onclick="fnCopyFromPeriodicalOrderData();">お客様情報へお届けする</a>
            </h2>
            <!--{foreach from=$arrForm.shipping_name01.value key=index item=item}-->
            <!--{assign var=key value="shipping_id"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
            <!--{assign var=key value="periodical_shipping_id"}-->
            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
            <table>
                <tr>
                    <th>
                        お名前
                    </th>
                    <td>
                        <!--{assign var=key1 value="shipping_name01"}-->
                        <!--{assign var=key2 value="shipping_name02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1][$index]}-->
                            <!--{$arrErr[$key2][$index]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" size="15" class="box15" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$index]|sfGetErrorColor}-->" />
                        <input type="text" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" size="15" class="box15" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$index]|sfGetErrorColor}-->" />
                    </td>
                    <th>
                        お名前 (カナ)
                    </th>
                    <td>
                        <!--{assign var=key1 value="shipping_kana01"}-->
                        <!--{assign var=key2 value="shipping_kana02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1][$index]}-->
                            <!--{$arrErr[$key2][$index]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" size="15" class="box15" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$index]|sfGetErrorColor}-->" />
                        <input type="text" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" size="15" class="box15" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$index]|sfGetErrorColor}-->" />
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
                        <span class="attention">
                            <!--{$arrErr[$key1][$index]}-->
                            <!--{$arrErr[$key2][$index]}-->
                            <!--{$arrErr[$key3][$index]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$index]|sfGetErrorColor}-->" />
                        -
                        <input type="text" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$index]|sfGetErrorColor}-->" />
                        -
                        <input type="text" name="<!--{$key3}-->[<!--{$index}-->]" value="<!--{$arrForm[$key3].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3][$index]|sfGetErrorColor}-->" />
                    </td>
                    <th>
                        FAX番号
                    </th>
                    <td>
                        <!--{assign var=key1 value="shipping_fax01"}-->
                        <!--{assign var=key2 value="shipping_fax02"}-->
                        <!--{assign var=key3 value="shipping_fax03"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1][$index]}-->
                            <!--{$arrErr[$key2][$index]}-->
                            <!--{$arrErr[$key3][$index]}-->
                        </span>
                        <input type="text" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$index]|sfGetErrorColor}-->" />
                        -
                        <input type="text" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$index]|sfGetErrorColor}-->" />
                        -
                        <input type="text" name="<!--{$key3}-->[<!--{$index}-->]" value="<!--{$arrForm[$key3].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3][$index]|sfGetErrorColor}-->" />
                    </td>
                </tr>
                <tr>
                
                    <th>
                        郵便番号
                    </th>
                    <td colspan="3">
                        <!--{assign var=key1 value="shipping_zip01"}-->
                        <!--{assign var=key2 value="shipping_zip02"}-->
                        <span class="attention">
                            <!--{$arrErr[$key1][$index]}-->
                            <!--{$arrErr[$key2][$index]}-->
                        </span>
                        〒
                        <input type="text" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$index]|sfGetErrorColor}-->" />
                        -
                        <input type="text" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$index]|sfGetErrorColor}-->" />
                        <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01[<!--{$index}-->]', 'shipping_zip02[<!--{$index}-->]', 'shipping_pref[<!--{$index}-->]', 'shipping_addr01[<!--{$index}-->]'); return false;">
                            住所入力
                        </a>
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
                        <span class="attention">
                            <!--{$arrErr[$key3][$index]}-->
                            <!--{$arrErr[$key1][$index]}-->
                            <!--{$arrErr[$key2][$index]}-->
                        </span>
                        <select class="top" name="<!--{$key3}-->[<!--{$index}-->]" style="<!--{$arrErr[$key3][$index]|sfGetErrorColor}-->">
                            <option value="" selected="">都道府県を選択</option>
                            <!--{html_options options=$arrPREFS selected=$arrForm[$key3].value|h}-->
                        </select>
                        <br />
                        <input type="text" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" size="60" class="box60 top" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$index]|sfGetErrorColor}-->" />
                        <br />
                        <input type="text" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$index]|sfGetErrorColor}-->" />
                    </td>
                </tr>
            </table>
            <!--{/foreach}-->
        </div>

        <div id="periodicalOrderDetails">
            <h2>
                定期受注商品情報
                <a class="btn-normal" href="javascript:void(0);" onclick="fnModeSubmit('recalculate','anchor_key','periodicalOrderDetails');">計算結果の確認</a>
                <a class="btn-normal" href="javascript:void(0);" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/plg_PeriodicalSale_periodical_product_select.php?periodical_order_id=<!--{$arrForm.periodical_order_id.value|h}-->', 'search', '615', '500'); return false;">商品の追加</a>
            </h2>
            <span class="attention">
                <!--{if $arrErr.product_id}-->
                ※商品が選択されていません。
                <!--{/if}-->
            </span>
            <table class="list">
                <thead>
                    <tr>
                        <th class="productId">
                            商品コード
                        </th>
                        <th class="name">
                            商品名 / 規格1 / 規格2
                        </th>
                        <th class="price">
                            単価
                        </th>
                        <th class="quantity">
                            数量
                        </th>
                        <th class="taxIncludedPrice">
                            税込価格
                        </th>
                        <th class="subtotal">
                            小計
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="5">
                            小計
                        </th>
                        <td class="right">
                            <!--{assign var=key value="subtotal"}-->
                            <!--{$arrForm[$key].value|number_format}--> 円
                        </td>
                    </tr>
                    <tr>
                        <th colspan="5">
                            値引き
                        </th>
                        <td class="right">
                            <!--{assign var=key value="discount"}-->
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <input class="right" type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                            円
                        </td>
                    </tr>
                    <tr>
                        <th colspan="5">
                            送料
                        </th>
                        <td class="right">
                            <!--{assign var=key value="deliv_fee"}-->
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <input class="right" type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                            円
                        </td>
                    </tr>
                    <tr>
                        <th colspan="5">
                            手数料
                        </th>
                        <td class="right">
                            <!--{assign var=key value="charge"}-->
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <input class="right" type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                            円
                        </td>
                    </tr>
                    <tr>
                        <th colspan="5">
                            合計
                        </th>
                        <td class="right">
                            <!--{assign var=key value="total"}-->
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <!--{$arrForm[$key].value|number_format}-->
                            円
                        </td>
                    </tr>
                    <tr>
                        <th colspan="5">
                            お支払い合計
                        </th>
                        <td class="right">
                            <!--{assign var=key value="payment_total"}-->
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <!--{$arrForm[$key].value|number_format}-->
                            円
                        </td>
                    </tr>
                    <!--{if $smarty.const.USE_POINT}-->
                    <tr>
                        <th colspan="5">
                            加算ポイント
                        </th>
                        <td class="right">
                            <!--{assign var=key value="add_point"}-->
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <input class="right" type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                            pt
                        </td>
                    </tr>
                    <!--{/if}-->
                </tfoot>
                <tbody>
                    <!--{if is_array($arrForm.quantity.value)}-->
                    <!--{foreach from=$arrForm.quantity.value key=index item=item}-->
                    <tr>
                        <td class="productCode">
                            <!--{assign var=key value="periodical_shipment_item_id"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="periodical_order_detail_id"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />
                            <!--{assign var=key value="product_id"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" />

                            <!--{assign var=key value="product_code"}-->
                            <!--{$arrForm[$key].value[$index]}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]}-->" id="<!--{$key}-->_<!--{$index}-->" />
                        </td>
                        <td class="productName">
                            <!--{assign var=key1 value="product_name"}-->
                            <!--{assign var=key2 value="classcategory_name1"}-->
                            <!--{assign var=key3 value="classcategory_name2"}-->
                            <!--{assign var=key4 value="point_rate"}-->
                            <!--{assign var=key5 value="product_class_id"}-->
                            <!--{$arrForm[$key1].value[$index]|h}-->
                            <!--{if strlen($arrForm[$key2].value[$index]) > 0}-->
                            / <!--{$arrForm[$key2].value[$index]|h}-->
                            <!--{/if}-->
                            <!--{if strlen($arrForm[$key3].value[$index]) > 0}-->
                            / <!--{$arrForm[$key3].value[$index]|h}-->
                            <!--{/if}-->
                            <input type="hidden" name="<!--{$key1}-->[<!--{$index}-->]" value="<!--{$arrForm[$key1].value[$index]|h}-->" id="<!--{$key1}-->_<!--{$index}-->" />
                            <input type="hidden" name="<!--{$key2}-->[<!--{$index}-->]" value="<!--{$arrForm[$key2].value[$index]|h}-->" id="<!--{$key2}-->_<!--{$index}-->" />
                            <input type="hidden" name="<!--{$key3}-->[<!--{$index}-->]" value="<!--{$arrForm[$key3].value[$index]|h}-->" id="<!--{$key3}-->_<!--{$index}-->" />
                            <input type="hidden" name="<!--{$key4}-->[<!--{$index}-->]" value="<!--{$arrForm[$key4].value[$index]|h}-->" id="<!--{$key4}-->_<!--{$index}-->" />
                            <input type="hidden" name="<!--{$key5}-->[<!--{$index}-->]" value="<!--{$arrForm[$key5].value[$index]|h}-->" id="<!--{$key5}-->_<!--{$index}-->" />
                            <br />
                            <a class="btn-normal" href="javascript:;" name="change" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/plg_PeriodicalSale_periodical_product_select.php?no=<!--{$index}-->&amp;periodical_order_id=<!--{$arrForm.periodical_order_id.value|h}-->', 'search', '615', '500'); return false;">変更</a>
                            <!--{if count($arrForm.quantity.value) > 1}-->
                                <a class="btn-normal" href="javascript:;" name="delete" onclick="fnSetFormVal('form1', 'delete_no', <!--{$index}-->); fnModeSubmit('delete_product','anchor_key','order_products'); return false;">削除</a>
                            <!--{/if}-->
                        </td>
                        <td class="price right">
                            <!--{assign var=key value="price"}-->
                            <!--{assign var=price value=`$arrForm[$key].value[$index]`}-->
                            <span class="attention">
                                <!--{$arrErr[$key][$index]}-->
                            </span>
                            <input class="right" type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$index}-->" /> 円
                        </td>
                        <td class="quantity right">
                            <!--{assign var=key value="quantity"}-->
                            <!--{assign var=quantity value=`$arrForm[$key].value[$index]`}-->
                            <span class="attention">
                                <!--{$arrErr[$key][$index]}-->
                            </span>
                            <input class="right" type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$index}-->" />
                        </td>
                        <td class="taxIncludedPrice right">
                            <!--{$price|sfCalcIncTax|number_format}--> 円
                        </td>
                        <td class="subtotal right">
                            <!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}--> 円
                        </td>
                    </tr>
                    <!--{/foreach}-->
                    <!--{/if}-->
                </tbody>
            </table>
        </div>

        <div id="delivery">
            <table class="form">
                <tr>
                    <th>
                        配送業者<br />
                        <span class="attention">
                            (配送業者の変更に伴う送料の変更は手動にておねがいします。)
                        </span>
                    </th>
                    <td>
                        <!--{assign var=key value="deliv_id"}-->
                        <select id="<!--{$key}-->" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrDELIVERIES selected=$arrForm[$key].value|h}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        お支払方法<br />
                        <span class="attention">
                            (お支払方法の変更に伴う送料の変更は手動にておねがいします。)
                        </span>
                    </th>
                    <td>
                        <select name="<!--{$payment_id_key}-->" style="<!--{$arrErr[$payment_id_key]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrPAYMENTS selected=$arrForm[$payment_id_key].value|h}-->
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <!--{assign var=period_type value=$arrForm[$period_type_key].value}-->
        <div id="period">
            <table class="form">
                <tr>
                    <th>
                        <label>
                            <input type="radio" name="<!--{$period_type_key}-->" value="<!--{"PeriodicalSale::PERIOD_TYPE_WEEKLY"|constant}-->" <!--{if $period_type == constant("PeriodicalSale::PERIOD_TYPE_WEEKLY") || strlen($period_type) == 0}-->checked<!--{/if}-->/>
                            毎週
                        </label>
                    </th>
                    <td>
                        <!--{assign var=key value="period_week"}-->
                        <!--{assign var=key value="period_day"}-->
                        <label>
                            毎週
                            <select name="<!--{$key}-->">
                                <!--{html_options options=$arrDAYS selected=$arrForm[$key].value|h}-->
                            </select>
                            曜日
                        </label>
                        <label>
                            <em>
                                お届け時間指定:
                            </em>
                            <select name="<!--{$period_delivery_time_key}-->">
                                <option value="" selected="">指定なし</option>
                                <!--{html_options options=$arrDELIVERYTIMES selected=$arrForm[$period_delivery_time_key].value|h}-->
                            </select>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label>
                            <input type="radio" name="<!--{$period_type_key}-->" value="<!--{"PeriodicalSale::PERIOD_TYPE_BIWEEKLY"|constant}-->" <!--{if $period_type == constant("PeriodicalSale::PERIOD_TYPE_BIWEEKLY")}-->checked<!--{/if}-->/>
                            隔週
                        </label>
                    </th>
                    <td>
                        <!--{assign var=key value="period_week"}-->
                        <!--{assign var=key value="period_day"}-->
                        <label>
                            隔週
                            <select name="<!--{$key}-->">
                                <!--{html_options options=$arrDAYS selected=$arrForm[$key].value|h}-->
                            </select>
                            曜日
                        </label>
                        <label>
                            <em>
                                お届け時間指定:
                            </em>
                            <select name="<!--{$period_delivery_time_key}-->">
                                <option value="" selected="">指定なし</option>
                                <!--{html_options options=$arrDELIVERYTIMES selected=$arrForm[$period_delivery_time_key].value|h}-->
                            </select>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label>
                            <input type="radio" name="<!--{$period_type_key}-->" value="<!--{"PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY"|constant}-->" <!--{if $period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DAY")}-->checked<!--{/if}-->/>
                            毎月 (曜日指定)
                        </label>
                    </th>
                    <td>
                        <!--{assign var=key value="period_week"}-->
                        <label>
                            各月第
                            <select name="<!--{$key}-->">
                                <!--{html_options options=$arrWEEKS selected=$arrForm[$key].value|h}-->
                            </select>
                        </label>
                        <!--{assign var=key value="period_day"}-->
                        <label>
                            <select name="<!--{$key}-->">
                                <!--{html_options options=$arrDAYS selected=$arrForm[$key].value|h}-->
                            </select>
                            曜日
                        </label>
                        <label>
                            <em>
                                お届け時間指定:
                            </em>
                            <select name="<!--{$period_delivery_time_key}-->">
                                <option value="" selected="">指定なし</option>
                                <!--{html_options options=$arrDELIVERYTIMES selected=$arrForm[$period_delivery_time_key].value|h}-->
                            </select>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label>
                            <input type="radio" name="<!--{$period_type_key}-->" value="<!--{"PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE"|constant}-->" <!--{if $period_type == constant("PeriodicalSale::PERIOD_TYPE_MONTHLY_DATE")}-->checked<!--{/if}-->/>
                            毎月 (日付指定)
                        </label>
                    </th>
                    <td>
                        <!--{assign var=key value="period_date"}-->
                        <label>
                            各月
                            <select name="<!--{$key}-->">
                                <!--{html_options options=$arrPERIODDATES selected=$arrForm[$key].value|h}-->
                            </select>
                            日
                        </label>
                        <label>
                            <em>
                                お届け時間指定:
                            </em>
                            <select name="<!--{$period_delivery_time_key}-->">
                                <option value="" selected="">指定なし</option>
                                <!--{html_options options=$arrDELIVERYTIMES selected=$arrForm[$period_delivery_time_key].value|h}-->
                            </select>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        定期継続状況
                    </th>
                    <td>
                        <!--{assign var=key value="periodical_status"}-->
                        <span class="attention">
                            <!--{$arrErr[$key]}-->
                        </span>
                        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                            <option value="">選択してください</option>
                            <!--{html_options options=$arrPERIODICALORDERSTATUSES selected=$arrForm[$key].value|h}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>
                        次回受注お届け日
                    </th>
                    <td>
                        <!--{assign var=next_period_key value="next_period"}-->
                        <!--{assign var=key1 value="next_shipping_year"}-->
                        <!--{assign var=key2 value="next_shipping_month"}-->
                        <!--{assign var=key3 value="next_shipping_date"}-->
                        <span class="attention">
                            <!--{$arrErr[$key3]}-->
                        </span>
                        <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrSHIPPINGYEAR selected=$arrForm[$key1].value}-->
                        </select> 年
                        <select name="<!--{$key2}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrSHIPPINGMONTH selected=$arrForm[$key2].value}-->
                        </select> 月
                        <select name="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrSHIPPINGDATE selected=$arrForm[$key3].value}-->
                        </select> 日
                    </td>
                </tr>
                <tr>
                    <th>
                        メモ<br />
                        <span class="attention">
                            (発行した受注データにも反映されます。)
                        </span>
                    </th>
                    <td>
                        <!--{assign var=key value="note"}-->
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                        <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area80" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea>
                    </td>
                </tr>
            </table>
        </div>

        <div class="btn-area">
            <ul>
                <!--{if strlen($arrForm.periodical_order_id.value) > 0}-->
                <li>
                    <a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH|cat:"plg_PeriodicalSale_periodical_order.php"}-->'); fnModeSubmit('search','',''); return false;">
                        <span class="btn-prev">検索画面に戻る</span>
                    </a>
                </li>
                <!--{/if}-->
                <li>
                    <a class="btn-action" href="javascript:;" onclick="if(fnConfirm()) document.form1.submit(); return false;">
                        <span class="btn-next">この内容で登録する</span>
                    </a>
                </li>
            </ul>
        </div>
    </form>

    <!--{if count($arrOrders) > 0}-->
    <form name="form2" id="form2" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="pre_edit" />
        <input type="hidden" name="order_id" value="" />
        <h2>
            定期発送履歴
        </h2>
        <table id="orders" class="list">
            <thead>
                <tr>
                    <th>
                        回数
                    </th>
                    <th>
                        受注ID
                    </th>
                    <th>
                        受注確定日
                    </th>
                    <th>
                        お届け(予定)日
                    </th>
                    <th>
                        受注対応状況<br />
                        <small>
                            発送日
                        </small>
                    </th>
                    <th>
                        支払方法
                    </th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$arrOrders item=arrOrder}-->
                <tr style="background-color:<!--{$arrORDERSTATUSCOLORS[$arrOrder.status]}-->;">
                    <td>
                        <!--{$arrOrder.periodical_times}-->
                    </td>
                    <td>
                        <a href="javascript:void(0);" onclick="fnEditOrder('<!--{$arrOrder.order_id}-->',this);">
                            <!--{$arrOrder.order_id}-->
                        </a>
                    </td>
                    <td>
                        <!--{$arrOrder.create_date|sfDispDBDate}-->
                    </td>
                    <td>
                        <!--{assign var=day value=$arrOrder.shipping_date|date_format:"%w"}-->
                        <!--{if strlen($arrOrder.shipping_date) > 0}-->
                        <!--{$arrOrder.shipping_date|date_format:"%Y/%m/%d"}-->
                        (<!--{$arrDAYS[$day]}-->)
                        <!--{else}-->
                        指定なし
                        <!--{/if}-->
                    </td>
                    <td>
                        <!--{$arrORDERSTATUSES[$arrOrder.status]}-->
                        <!--{if $arrOrder.status == 5}-->
                        <!--{assign var=day value=$arrOrder.commit_date|date_format:"%w"}-->
                        <br />
                        <small>
                            <!--{$arrOrder.commit_date|date_format:"%Y/%m/%d"}-->
                            (<!--{$arrDAYS[$day]}-->)
                        </small>
                        <!--{/if}-->
                    </td>
                    <td>
                        <!--{$arrOrder.payment_method}-->
                    </td>
                </tr>
                <!--{/foreach}-->
            </tbody>
        </table>
    </form>
    <!--{/if}-->
</div>