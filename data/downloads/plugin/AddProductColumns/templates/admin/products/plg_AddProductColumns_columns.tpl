<!--{*
 * AddProductColumns
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
 
<style type="text/css">
.column-id{
    text-align: center;
}
</style>
 
<div class="contents-main">
    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="add" />
        <input type="hidden" name="column_id" value="<!--{$arrForm.column_id.value|h}-->" />
        <table id="column">
            <!--{assign var=key value="name"}-->
            <tr id="column-name">
                <th>
                    <!--{$arrForm[$key].disp_name}-->
                    <span class="attention">*</span>
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" class="box60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                    <small class="attention">
                        (上限<!--{$arrForm[$key].length}-->文字)
                    </small>
                </td>
            </tr>
            <!--{assign var=key value="type"}-->
            <tr id="column-type">
                <th>
                    <!--{$arrForm[$key].disp_name}-->
                    <span class="attention">*</span>
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key]}-->
                    </span>
                    <label>
                        <input type="radio" name="<!--{$key}-->" value="<!--{$smarty.const.COLUMN_TYPE_TEXT}-->" <!--{if $arrForm[$key].value == $smarty.const.COLUMN_TYPE_TEXT || !$arrForm[$key].value}-->checked="checked"<!--{/if}--> />
                        テキストフォーム
                    </label>
                    <label>
                        <input type="radio" name="<!--{$key}-->" value="<!--{$smarty.const.COLUMN_TYPE_TEXTAREA}-->" <!--{if $arrForm[$key].value == $smarty.const.COLUMN_TYPE_TEXTAREA}-->checked="checked"<!--{/if}--> />
                        テキストエリア (HTMLタグ利用可)
                    </label>
                </td>
            </tr>
            <!--{assign var=key1 value="required"}-->
            <!--{assign var=key2 value="max_length"}-->
            <tr id="column-restriction">
                <th>
                    入力制限
                    <span class="attention">*</span>
                </th>
                <td>
                    <span class="attention">
                        <!--{$arrErr[$key1]}-->
                        <!--{$arrErr[$key2]}-->
                    </span>
                    <input type="hidden" name="<!--{$key1}-->" value="0" />
                    <label>
                        <input type="checkbox" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1].value == "1"}-->checked="checked"<!--{/if}--> />
                        入力必須
                    </label>
                    <label>
                        <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" class="box40" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" />
                        文字以内
                    </label>
                </td>
            </tr>
        </table>
        <div class="btn-area">
            <ul>
                <li>
                    <a class="btn-action" href="javascript:;" onclick="document.form1.submit();">
                        <span class="btn-next">この内容で登録する</span>
                    </a>
                </li>
            </ul>
        </div>
        <table id="columns" class="list">
            <colgroup>
                <col width="5%" />
                <col width="20%" />
                <col width="20%" />
                <col width="20%" />
                <col width="15%" />
                <col width="20%" />
            </colgroup>
            <thead>
                <tr>
                    <th class="column-id">
                        ID
                    </th>
                    <th class="name">
                        項目名<br />
                        <small class="attention">(*は必須)</small>
                    </th>
                    <th class="name-tag">
                        項目名タグ
                    </th>
                    <th class="name-tag">
                        入力内容タグ
                    </th>
                    <th class="type">
                        入力パターン<br />
                        <small>(入力規則)</small>
                    </th>
                    <th class="actions center">
                        操作
                    </th>
                </tr>
            </thead>
            <tbody>
                <!--{foreach from=$arrColumns item=arrColumn}-->
                    <!--{assign var=papc_key value=$smarty.const.PAPC_PREFIX|cat:$arrColumn.column_id}-->
                    <!--{assign var=editing value=false}-->
                    <!--{if $arrForm.column_id.value == $arrColumn.column_id}-->
                        <!--{assign var=editing value=true}-->
                    <!--{/if}-->
                    <tr <!--{if $editing}-->style="background:#ffffdf;"<!--{/if}-->>
                        <td class="column-id">
                            <!--{$arrColumn.column_id|h}-->
                        </td>
                        <td class="name">
                            <!--{$arrColumn.name|h}-->
                            <span class="attention">
                                <!--{if $arrColumn.required}-->
                                    *
                                <!--{/if}-->
                            </span>
                        </td>
                        <td class="name-tag">
                            <input type="text" value="&lt;!--{$arrProduct.<!--{$papc_key|h}-->.name|h}--&gt;" class="box40" onmouseover="this.select(0, this.value.length)" readonly="readonly" />
                        </td>
                        <td class="value-tag">
                            <!--{if $arrColumn.type == $smarty.const.COLUMN_TYPE_TEXT}-->
                                <input type="text" value="&lt;!--{$arrProduct.<!--{$papc_key|h}-->.value|h}--&gt;" class="box40" onmouseover="this.select(0, this.value.length)" readonly="readonly" />
                            <!--{elseif $arrColumn.type == $smarty.const.COLUMN_TYPE_TEXTAREA}-->
                                <input type="text" value="&lt;!--{$arrProduct.<!--{$papc_key|h}-->.value|nl2br}--&gt;" class="box40" onmouseover="this.select(0, this.value.length)" readonly="readonly" />
                            <!--{/if}-->
                        </td>
                        <td class="type">
                            <!--{$arrColumn.type|h}-->
                            <!--{if strlen($arrColumn.max_length) > 0}-->
                                (<!--{$arrColumn.max_length|h}-->)
                            <!--{/if}-->
                        </td>
                        <td class="edit center">
                            <!--{if $editing}-->
                            編集中
                            <!--{else}-->
                            <a href="javascript:void(0);" class="btn-tool" onclick="fnModeSubmit('edit', 'column_id', '<!--{$arrColumn.column_id}-->');">
                                編集
                            </a>
                            <!--{/if}-->
                            <a href="javascript:void(0);" class="btn-tool" onclick="fnModeSubmit('delete', 'column_id', '<!--{$arrColumn.column_id}-->');">
                                削除
                            </a>
                        </td>
                    </tr>
                <!--{/foreach}-->
            </tbody>
        </table>
    </form>
</div>