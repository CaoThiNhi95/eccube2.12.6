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

 
<!--{if count($arrColumns) > 0}-->
    <h2>
        追加情報
    </h2>
    <table id="add-product-columns">
        <tbody>
            <!--{foreach from=$arrColumns item=arrColumn}-->
                <!--{assign var=key value=$smarty.const.PAPC_PREFIX|cat:$arrColumn.column_id}-->
                <tr class="<!--{$arrColumn.type|h}-->">
                    <!--{if $arrColumn.type == $smarty.const.COLUMN_TYPE_TEXT}-->
                        <th>
                            <!--{$arrColumn.name|h}-->
                            <span class="attention">
                                <!--{if $arrColumn.required == "1"}-->
                                    *
                                <!--{/if}-->
                            </span>
                        </th>
                        <td>
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <input type="text" name="<!--{$key}-->" class="box60" maxlength="<!--{$arrColumn.max_length}-->" value="<!--{$arrForm[$key]|h}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                            <!--{if strlen($arrColumn.max_length) > 0}-->
                                <span class="attention">
                                    (上限<!--{$arrColumn.max_length}-->文字)
                                </span>
                            <!--{/if}-->
                        </td>
                    <!--{elseif $arrColumn.type == $smarty.const.COLUMN_TYPE_TEXTAREA}-->
                        <th>
                            <!--{$arrColumn.name|h}-->
                            <span class="attention">
                                <!--{if $arrColumn.required == "1"}-->
                                    *
                                <!--{/if}-->
                                (タグ許可)
                            </span>
                        </th>
                        <td>
                            <span class="attention">
                                <!--{$arrErr[$key]}-->
                            </span>
                            <textarea name="<!--{$key}-->" class="area60" maxlength="<!--{$arrColumn.max_length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|h}--></textarea>
                            <!--{if strlen($arrColumn.max_length) > 0}-->
                                <br />
                                <span class="attention">
                                    (上限<!--{$arrColumn.max_length}-->文字)
                                </span>
                            <!--{/if}-->
                        </td>
                    <!--{/if}-->
                </tr>
            <!--{/foreach}-->
    </table>
<!--{/if}-->