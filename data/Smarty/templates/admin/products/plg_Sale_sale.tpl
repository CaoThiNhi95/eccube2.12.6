<!--{*
/*
 * Sale
 *
 * Copyright(c) 2009-2012 CUORE CO.,LTD. All Rights Reserved.
 *
 * http://ec.cuore.jp/
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
 */
*}-->
<script type="text/javascript">
// チェックボックスの子要素の自動チェック切り替え
function fnAutoCheck(p_id, p_level) {
    var check = document.getElementById(p_id).checked;
    var element = null;
    for(var i = 0; i < document.form1.length; i++) {
        element = document.form1.elements[i];
        if(element.type == "checkbox" &&
            element.id.slice(0, 10 * p_level) == p_id) {
            element.checked = check;
        }
    }
}

// セレクトボックスのリストを移動
//（移動元セレクトボックスID, 移動先セレクトボックスID）
function fnMoveSelect(select, target) {
    $('#' + select).children().each(function() {
        if (this.selected) {
            $('#' + target).append(this);
            $(this).attr({selected: false});
        }
    });
}

// target の子要素を選択状態にする
function selectAll(target) {
    $('#' + target).children().attr({selected: true});
}
</script>

<!--★★メインコンテンツ★★-->
<form name="form1" id="form1" method="post" action="./plg_Sale_sale.php">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="">
    <input type="hidden" name="sale_id" value="<!--{$tpl_sale_id}-->">
    <div id="products" class="contents-main">

        <h2>セール情報詳細</h2>
        <table class="form">
            <tr>
                <th>名称<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.name}--></span>
                    <input type="text" name="name" value="<!--{$arrForm.name|escape}-->" size="60" class="box60" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /><span class="attention"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
                </td>
            </tr>
            <tr>
                <th>分類<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.category}--></span>
                    <!--{if $tpl_sale_id == ""}-->
                    <input type="radio" name="category" value="1" onclick="fnModeSubmit('chg_category', 'category', '1'); return false;"<!--{if $category == "1"}--> checked<!--{/if}--> />セール&nbsp;&nbsp;<input type="radio" name="category" value="2" onclick="fnModeSubmit('chg_category', 'category', '2'); return false;"<!--{if $category == "2"}--> checked<!--{/if}--> />特価品
                    <!--{else}-->
                    <!--{$arrCategory[$category]}-->
                    <input type="hidden" name="category" value="<!--{$category}-->" />
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>値引き区分<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.division}--></span>
                    <input type="radio" name="division" value="1"<!--{if $arrForm.division == "1" || $arrForm.division == ""}--> checked<!--{/if}-->/>%&nbsp;&nbsp;<input type="radio" name="division" value="2"<!--{if $arrForm.division == "2"}--> checked<!--{/if}--> />円
                </td>
            </tr>
            <tr>
                <th>値引き値<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.value}--></span>
                    <input type="text" name="value" value="<!--{$arrForm.value|escape}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.value != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" /><span class="attention"> （半角数字で入力）</span>
                </td>
            </tr>
            <tr>
                <th>値引き開始日<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.start_year}--></span>
                    <select name="start_year" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrYear selected=$arrForm.start_year}-->
                    </select>年
                    <select name="start_month" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.start_month}-->
                    </select>月
                    <select name="start_day" style="<!--{$arrErr.start_year|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.start_day}-->
                    </select>日
                </td>
            </tr>
            <tr>
                <th>値引き終了日<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.end_year}--></span>
                    <select name="end_year" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
                        <option value="">----</option>
                        <!--{html_options options=$arrYear selected=$arrForm.end_year}-->
                    </select>年
                    <select name="end_month" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrMonth selected=$arrForm.end_month}-->
                    </select>月
                    <select name="end_day" style="<!--{$arrErr.end_year|sfGetErrorColor}-->">
                        <option value="">--</option>
                        <!--{html_options options=$arrDay selected=$arrForm.end_day}-->
                    </select>日
                </td>
            </tr>
<!-- 有効状態無効化：Hiddenで1固定にしておく。有効にしたい場合はコメント化を解除して下のHiddenタグを削除
            <tr>
                <th>有効状態<span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.status}--></span>
                    <input type="radio" name="status" value="1"<!--{if $arrForm.status == "1" || $arrForm.status == ""}--> checked<!--{/if}-->/>有効&nbsp;&nbsp;<input type="radio" name="status" value="0"<!--{if $arrForm.status == "0"}--> checked<!--{/if}--> />無効
                </td>
            </tr>
-->
            <input type="hidden" name="status" value="1" />
            <tr>
                <th>対象<!--{if $category == "1"}-->カテゴリ<!--{else}-->商品<!--{/if}--><span class="attention"> *</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.object}--></span>
                    <!--{if $category == "1"}-->
                    <!--{section name=objectCnt loop=$arrObject}-->
                    <!--{section name=levelCnt loop=$arrObject[objectCnt].level start=1}-->　　<!--{/section}--><input type="checkbox" name="object_id[]" id="<!--{$arrObject[objectCnt].id}-->" value="<!--{$arrObject[objectCnt].category_id}-->" onClick="fnAutoCheck('<!--{$arrObject[objectCnt].id}-->', '<!--{$arrObject[objectCnt].level}-->');"<!--{section name=valueCnt loop= $arrForm.object_id}--><!--{if $arrObject[objectCnt].category_id == $arrForm.object_id[valueCnt]}--> checked<!--{/if}--><!--{/section}--> /><!--{$arrObject[objectCnt].category_name}--><br />
                    <!--{/section}-->
                    <!--{else}-->
                    <table class="layout">
                        <tr>
                            <td>
                                <select name="object_id[]" id="object_id" style="<!--{$arrErr.object|sfGetErrorColor}-->" size="10" style="height: 120px; min-width: 200px;" multiple>
                                </select>
                            </td>
                            <td style="padding: 30px;">
                                <a class="btn-normal" href="javascript:;" name="on_select" onClick="fnMoveSelect('object_id_unselect', 'object_id'); return false;">&nbsp;&nbsp;&lt;-&nbsp;登録&nbsp;&nbsp;</a><br /><br />
                                <a class="btn-normal" href="javascript:;" name="un_select" onClick="fnMoveSelect('object_id', 'object_id_unselect'); return false;">&nbsp;&nbsp;削除&nbsp;-&gt;&nbsp;&nbsp;</a>
                            </td>
                            <td>
                                <select name="object_id_unselect[]" id="object_id_unselect" onchange="" size="10" style="height: 120px; min-width: 200px;" multiple>
                                    <!--{html_options values=$arrObjVal output=$arrObjOut selected=$arrForm.object_id}-->
                                </select>
                            </td>
                        </tr>
                    </table>
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>コメント</th>
                <td>
                    <span class="attention"><!--{$arrErr.comment}--></span>
                    <textarea name="comment" cols="60" rows="8" class="area60" wrap="soft" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="<!--{$arrErr.comment|sfGetErrorColor}-->"><!--{$arrForm.comment|escape}--></textarea><br/><span class="attention"> （上限3000文字）</span>
                </td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="<!--{if $category == "2"}-->selectAll('object_id'); <!--{/if}-->fnModeSubmit('confirm', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            </ul>
        </div>

        <h2>セール情報一覧</h2>
        <table class="list">
            <colgroup width="30%">
            <colgroup width="10%">
            <colgroup width="15%">
            <colgroup width="25%">
            <colgroup width="10%">
            <colgroup width="10%">
            <tr>
                <th>名称</th>
                <th>分類</th>
                <th>期間</th>
                <th>カテゴリ/商品</th>
                <th class="edit">編集</th>
                <th class="delete">削除</th>
            </tr>
            <!--{section name=cnt loop=$arrSale}-->
            <tr style="background:<!--{if $arrSale[cnt].sale_id != $tpl_sale_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;" class="center">
                <td><!--{* 名称 *}--><!--{$arrSale[cnt].name|mb_strimwidth:0:40:"..."|escape}--></td>
                <td align="center">
                    <!--{assign var=catVal value=$arrSale[cnt].category}-->
                    <!--{$arrCategory[$catVal]}-->
                </td>
                <td align="center">
                    <!--{$arrSale[cnt].start_date|escape}--><br />
                    ～<br />
                    <!--{$arrSale[cnt].end_date|escape}-->
                </td>
                <td>
                    <!--{section name=goodsCnt loop=$arrSale[cnt].goods}-->
                    <!--{assign var=goods value=$arrSale[cnt].goods}-->
                    <!--{$goods[goodsCnt].name|escape}--><br />
                    <!--{/section}-->
                </td>
                <td align="center">
                    <!--{if $tpl_sale_id != $arrSale[cnt].sale_id}-->
                    <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('edit', 'sale_id', <!--{$arrSale[cnt].sale_id}-->); return false;">編集</a>
                    <!--{else}-->
                    編集中
                    <!--{/if}-->
                </td>
                <td align="center">
                    <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'sale_id', <!--{$arrSale[cnt].sale_id}-->); return false;">削除</a>
                </td>
            </tr>
            <!--{sectionelse}-->
            <tr class="center">
                <td colspan="6">現在データはありません。</td>
            </tr>
            <!--{/section}-->
        </table>

    </div>
</form>
<!--★★メインコンテンツ★★-->
