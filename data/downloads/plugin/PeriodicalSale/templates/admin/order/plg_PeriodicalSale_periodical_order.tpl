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
 
<script type="text/javascript">
$(function(){
    var checkboxes = $('#periodicalOrderList .periodicalOrderId');
    var fnChecked = function(){
        if(checkboxes.filter(':checked').length > 0){
            return true;
        }
        else{
            alert('定期受注が選択されていません。');
            return false;
        }
    };
    
    $('#selectAll')
        .click(function(){
            if(this.checked)    checkboxes.attr({checked:'checked'});
            else                checkboxes.attr({checked:false});
        })
    $('#commitOrdersButton')
        .click(function(){
            if(fnChecked()){
                if(confirm('選択した定期受注を受注として発行しますか？')){
                    fnModeSubmit('commit','','');
                }
            }
        })
})
</script>
 
<style>
#periodicalOrderList .periodicalOrderId,
#periodicalOrderList .periodicalStatus,
#periodicalOrderList .orderName,
#periodicalOrderList .periodicalTimes,
#periodicalOrderList .payment,
#periodicalOrderList .nextPeriod,
#periodicalOrderList .lastOrder,
#periodicalOrderList .checkbox,
#periodicalOrderList .actions,
#periodicalOrderList .status{
    text-align:center;
}
</style>
 
<div id="admin-contents" class="contents-main">
    <form name="search_form" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <h2>検索条件設定</h2>
        <table>
            <tr>
                <!--{assign var=key1 value="search_periodical_order_id1"}-->
                <!--{assign var=key2 value="search_periodical_order_id2"}-->
                <th>
                    定期ID
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                    </span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                    ～
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                </td>
                <!--{assign var=key value="search_last_order_status"}-->
                <th>
                    最終注文対応状況
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <select name="<!--{$key}-->" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value=""></option>
                        <!--{html_options  options=$arrORDERSTATUSES selected=$arrForm[$key].value}-->
                    </select>
                </td>
            </tr>
            <tr>
                <!--{assign var=key value="search_order_name"}-->
                <th>
                    お名前
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                </td>
                <!--{assign var=key value="search_periodical_status"}-->
                <th>
                    定期継続状況
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <select name="<!--{$key}-->" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value=""></option>
                        <!--{html_options  options=$arrPERIODICALORDERSTATUSES selected=$arrForm[$key].value}-->
                    </select>
                </td>
            </tr>
            <tr>
                <!--{assign var=key value="search_order_kana"}-->
                <th>
                    お名前 (フリガナ)
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                </td>
                <!--{assign var=key value="search_order_tel"}-->
                <th>
                    TEL
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                </td>
            </tr>
            <tr>
                <!--{assign var=key value="search_order_email"}-->
                <th>
                    メールアドレス
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
                </td>
                <!--{assign var=key value="search_order_sex"}-->
                <th>
                    性別
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <!--{html_checkboxes options=$arrSEX name=$key selected=$arrForm[$key].value}-->
                </td>
            </tr>
            <!--{assign var=key1 value="search_order_birth1"}-->
            <!--{assign var=key2 value="search_order_birth2"}-->
            <tr>
                <th>
                    生年月日
                </th>
                <td colspan="3">
                    <!--{assign var=key1 value="search_birth_year_start"}-->
                    <!--{assign var=key2 value="search_birth_month_start"}-->
                    <!--{assign var=key3 value="search_birth_date_start"}-->
                    <!--{assign var=key4 value="search_birth_year_end"}-->
                    <!--{assign var=key5 value="search_birth_month_end"}-->
                    <!--{assign var=key6 value="search_birth_date_end"}-->
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                        <!--{$arrErr[$key3]}-->
                        <!--{$arrErr[$key4]}-->
                        <!--{$arrErr[$key5]}-->
                        <!--{$arrErr[$key6]}-->
                    </span>
                    <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrBIRTHYEAR selected=$arrForm[$key1].value}-->
                    </select>年
                    <select name="<!--{$key2}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key2].value}-->
                    </select>月
                    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key3].value}-->
                    </select>日
                    ～
                    <span class="attention">
                    </span>
                    <select name="<!--{$key4}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrBIRTHYEAR selected=$arrForm[$key4].value}-->
                    </select>年
                    <select name="<!--{$key5}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key5].value}-->
                    </select>月
                    <select name="<!--{$key6}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key6].value}-->
                    </select>日
                </td>
            </tr>
            <tr>
                <th>
                    初回購入日
                </th>
                <td colspan="3">
                    <!--{assign var=key1 value="search_first_order_year_start"}-->
                    <!--{assign var=key2 value="search_first_order_month_start"}-->
                    <!--{assign var=key3 value="search_first_order_date_start"}-->
                    <!--{assign var=key4 value="search_first_order_year_end"}-->
                    <!--{assign var=key5 value="search_first_order_month_end"}-->
                    <!--{assign var=key6 value="search_first_order_date_end"}-->
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                        <!--{$arrErr[$key3]}-->
                        <!--{$arrErr[$key4]}-->
                        <!--{$arrErr[$key5]}-->
                        <!--{$arrErr[$key6]}-->
                    </span>
                    <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrORDERYEAR selected=$arrForm[$key1].value}-->
                    </select>年
                    <select name="<!--{$key2}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key2].value}-->
                    </select>月
                    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key3].value}-->
                    </select>日
                    ～
                    <span class="attention">
                    </span>
                    <select name="<!--{$key4}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrORDERYEAR selected=$arrForm[$key4].value}-->
                    </select>年
                    <select name="<!--{$key5}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key5].value}-->
                    </select>月
                    <select name="<!--{$key6}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key6].value}-->
                    </select>日
                </td>
            </tr>
            <!--{assign var=key value="search_payment_id"}-->
            <tr>
                <th>
                    支払方法
                </th>
                <td colspan="3">
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                        <!--{html_checkboxes options=$arrPAYMENTS name=$key selected=$arrForm[$key].value}-->
                </td>
            </tr>
            <!--{assign var=key1 value="search_total_periodical_times1"}-->
            <!--{assign var=key2 value="search_total_periodical_times2"}-->
            <tr>
                <th>
                    定期回数
                </th>
                <td colspan="3">
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                    </span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                    回 ～
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                    回
                </td>
            </tr>
            <!--{assign var=key1 value="search_periodical_next_date1"}-->
            <!--{assign var=key2 value="search_periodical_next_date2"}-->
            <tr>
                <th>
                    次回お届け予定日
                </th>
                <td colspan="3">
                    <!--{assign var=key1 value="search_next_period_year_start"}-->
                    <!--{assign var=key2 value="search_next_period_month_start"}-->
                    <!--{assign var=key3 value="search_next_period_date_start"}-->
                    <!--{assign var=key4 value="search_next_period_year_end"}-->
                    <!--{assign var=key5 value="search_next_period_month_end"}-->
                    <!--{assign var=key6 value="search_next_period_date_end"}-->
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                        <!--{$arrErr[$key3]}-->
                        <!--{$arrErr[$key4]}-->
                        <!--{$arrErr[$key5]}-->
                        <!--{$arrErr[$key6]}-->
                    </span>
                    <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrORDERYEAR selected=$arrForm[$key1].value}-->
                    </select>年
                    <select name="<!--{$key2}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key2].value}-->
                    </select>月
                    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key3].value}-->
                    </select>日
                    ～
                    <span class="attention">
                    </span>
                    <select name="<!--{$key4}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrORDERYEAR selected=$arrForm[$key4].value}-->
                    </select>年
                    <select name="<!--{$key5}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key5].value}-->
                    </select>月
                    <select name="<!--{$key6}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key6].value}-->
                    </select>日
                </td>
            </tr>
            <!--{assign var=key1 value="search_last_order_commit_date1"}-->
            <!--{assign var=key2 value="search_last_order_commit_date2"}-->
            <tr>
                <th>
                    最終受注発送日<br />
                    <small class="attention">
                        最終受注が未発送の定期受注は除外されます。
                    </small>
                </th>
                <td colspan="3">
                    <!--{assign var=key1 value="search_last_order_commit_year_start"}-->
                    <!--{assign var=key2 value="search_last_order_commit_month_start"}-->
                    <!--{assign var=key3 value="search_last_order_commit_date_start"}-->
                    <!--{assign var=key4 value="search_last_order_commit_year_end"}-->
                    <!--{assign var=key5 value="search_last_order_commit_month_end"}-->
                    <!--{assign var=key6 value="search_last_order_commit_date_end"}-->
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                        <!--{$arrErr[$key3]}-->
                        <!--{$arrErr[$key4]}-->
                        <!--{$arrErr[$key5]}-->
                        <!--{$arrErr[$key6]}-->
                    </span>
                    <select name="<!--{$key1}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrORDERYEAR selected=$arrForm[$key1].value}-->
                    </select>年
                    <select name="<!--{$key2}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key2].value}-->
                    </select>月
                    <select name="<!--{$key3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key3].value}-->
                    </select>日
                    ～
                    <span class="attention">
                    </span>
                    <select name="<!--{$key4}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrORDERYEAR selected=$arrForm[$key4].value}-->
                    </select>年
                    <select name="<!--{$key5}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMONTHS selected=$arrForm[$key5].value}-->
                    </select>月
                    <select name="<!--{$key6}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDATES selected=$arrForm[$key6].value}-->
                    </select>日
                </td>
            </tr>
        </table>

        <div class="btn">
            <p class="page_rows">
                <!--{assign var=key value="search_page_max"}-->
                検索結果表示件数:
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <!--{html_options options=$arrPAGEMAX selected=$arrForm[$key].value}-->
                </select>
                    
                <!--{assign var=key value="search_order_by"}-->
                <span class="attention">
                    <!--{$arrErr[$key]}-->
                </span>
                <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option <!--{if $arrForm[$key].value == "next_period_asc"}-->selected<!--{/if}--> value="next_period_asc">次回お届け予定日の昇順</option>
                    <option <!--{if $arrForm[$key].value == "next_period_desc"}-->selected<!--{/if}--> value="next_period_desc">次回お届け予定日の降順</option>
                    <option <!--{if $arrForm[$key].value == "periodical_order_id_asc"}-->selected<!--{/if}--> value="periodical_order_id_asc">定期受注IDの昇順</option>
                    <option <!--{if $arrForm[$key].value == "periodical_order_id_desc"}-->selected<!--{/if}--> value="periodical_order_id_desc">定期受注IDの降順</option>
                    <option <!--{if $arrForm[$key].value == "total_periodical_times_asc"}-->selected<!--{/if}--> value="total_periodical_times_asc">定期回数の昇順</option>
                    <option <!--{if $arrForm[$key].value == "total_periodical_times_desc"}-->selected<!--{/if}--> value="total_periodical_times_desc">定期回数の降順</option>
                    <option <!--{if $arrForm[$key].value == "update_date_asc"}-->selected<!--{/if}--> value="update_date_asc">更新日時の昇順</option>
                    <option <!--{if $arrForm[$key].value == "update_date_desc"}-->selected<!--{/if}--> value="update_date_desc">更新日時の降順</option>
                </select>
            </p>
            <div class="btn-area">
                <ul>
                    <li>
                        <a class="btn-action" href="javascript:void(0);" onclick="fnFormModeSubmit('search_form','search','','');return false;">
                            <span class="btn-next">
                                この条件で検索する
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </form>
                
    <!--{if count($arrErr) == 0}-->
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="periodical_order_id" value="" />
        <input type="hidden" name="edit_customer_id" value="" />
        <!--{foreach key=key item=item from=$arrHidden}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->
        <h2>
            検索結果一覧
        </h2>
        <div class="btn">
            <span class="attention">
                <!--{$tpl_linemax}-->件
            </span>
            が該当しました。
            <a id="commitOrdersButton" class="btn-normal" href="javascript:void(0);">
                受注一括発行
            </a>
        </div>
        <!--{if count($arrResults) > 0}-->
        <!--{include file=$tpl_pager}-->
        <table id="periodicalOrderList" class="list">
            <colgroup>
                <col style="width:4%;"/>
                <col style="width:5%;"/>
                <col style="width:6%;"/>
                <col style="width:10%;"/>
                <col style="width:7.5%;"/>
                <col style="width:10%;"/>
                <col style="width:10%;"/>
                <col style="width:10%;"/>
                <col style="width:7.5%;"/>
                <col style="width:10%;"/>
            </colgroup>
            <thead>
                <tr>
                    <th class="checkbox">
                        <input type="checkbox" id="selectAll" />
                    </th>
                    <th class="periodicalOrderId">
                        定期ID
                    </th>
                    <th>
                        継続状況
                    </th>
                    <th class="orderName">
                        <small>
                            フリガナ
                        </small>
                        <br />
                        お名前
                    </th>
                    <th class="periodicalTimes">
                        定期回数
                    </th>
                    <th class="payment">
                        支払方法
                    </th>
                    <th class="nextPeriod">
                        次回お届け予定日
                    </th>
                    <th class="lastOrder">
                        <small>
                            最新注文番号<br />
                            / 対応状況
                        </small>
                    </th>
                    <th class="status">
                        状態
                    </th>
                    <th class="delete">
                        操作
                    </th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$arrResults item=arrResult}-->
                <tr>
                    <td class="checkbox">
                        <input type="checkbox" name="arrPeriodicalOrderIds[]" class="periodicalOrderId" value="<!--{$arrResult.periodical_order_id}-->" />
                    </td>
                    <td class="periodicalOrderId">
                        <!--{$arrResult.periodical_order_id}-->
                    </td>
                    <td class="periodicalStatus">
                        <!--{$arrPERIODICALORDERSTATUSES[$arrResult.periodical_status]}-->
                    </td>
                    <td class="orderName">
                        <!--{if $arrResult.customer_id > 0}-->
                            <a href="javascript:void(0);" onclick="fnChangeAction('<!--{$smarty.const.ROOT_URLPATH|cat:$smarty.const.ADMIN_DIR|cat:"customer/edit.php"}-->'); fnModeSubmit('edit_search','edit_customer_id','<!--{$arrResult.customer_id}-->');">
                                <small>
                                    <!--{$arrResult.order_kana01|cat:" "|cat:$arrResult.order_kana02|h}-->
                                </small>
                                <br />
                                <!--{$arrResult.order_name01|cat:" "|cat:$arrResult.order_name02|h}-->
                            </a>
                        <!--{else}-->
                                <small>
                                    <!--{$arrResult.order_kana01|cat:" "|cat:$arrResult.order_kana02|h}-->
                                </small>
                                <br />
                                <!--{$arrResult.order_name01|cat:" "|cat:$arrResult.order_name02|h}-->
                        <!--{/if}-->
                    </td>
                    <td class="periodicalTimes">
                        <a href="javascript:void(0);" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH|cat:"plg_PeriodicalSale_periodical_order_edit.php#orders"}-->');fnModeSubmit('pre_edit','periodical_order_id','<!--{$arrResult.periodical_order_id}-->')">
                            <!--{$arrResult.total_periodical_times}-->回目
                        </a>
                    </td>
                    <td class="payment">
                        <!--{$arrPAYMENTS[$arrResult.payment_id]}-->
                    </td>
                    <td class="nextPeriod">
                        <!--{assign var=day value=$arrResult.next_period|date_format:"%w"}-->
                        <!--{$arrResult.next_period|date_format:"%Y/%m/%d"}-->
                        (<!--{$arrDAYS[$day]}-->)
                    </td>
                    <td class="lastOrder">
                        <a href="javascript:void(0);" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_EDIT_URLPATH}-->');fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResult.last_order.order_id}-->');">
                            <!--{$arrResult.last_order.order_id}-->
                        </a>
                        /
                        <!--{$arrORDERSTATUSES[$arrResult.last_order.status]}-->
                        <!--{if $arrResult.last_order.status == $smarty.const.ORDER_DELIV}-->
                            <br />
                            <small>
                                (<!--{$arrResult.last_order.commit_date|sfDispDBDate}-->)
                            </small>
                        <!--{/if}-->
                    </td>
                    <td class="status">
                        <!--{if $arrResult.periodical_status.value != 0}-->
                            <!--{assign var=class value="plgPeriodicalSaleUncommittable"}-->
                        <!--{elseif $arrResult.last_order.status == $smarty.const.ORDER_DELIV || $arrResult.last_order.status == $smarty.const.ORDER_CANCEL}-->
                            <!--{assign var=class value="plgPeriodicalSaleCommittable"}-->
                        <!--{elseif strlen($arrResult.last_order.status) > 0}-->
                            <!--{assign var=class value="plgPeriodicalSaleCommitted"}-->
                        <!--{else}-->
                            <!--{assign var=class value=""}-->
                        <!--{/if}-->
                        <div class="<!--{$class}-->">
                        </div>
                    </td>
                    <td class="actions">
                        <a class="btn-normal" href="javascript:void(0);" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH|cat:"plg_PeriodicalSale_periodical_order_edit.php"}-->');fnModeSubmit('pre_edit','periodical_order_id','<!--{$arrResult.periodical_order_id}-->')">
                            編集
                        </a>
                        <a class="btn-normal" href="javascript:void(0);" onclick="fnModeSubmit('delete','periodical_order_id','<!--{$arrResult.periodical_order_id}-->');">
                            削除
                        </a>
                    </td>
                </tr>
                <!--{/foreach}-->
            </tbody>
        </table>
        <!--{/if}-->
    </form>
    <!--{/if}-->
</div>