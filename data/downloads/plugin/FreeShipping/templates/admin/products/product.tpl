<!--{*
 * FreeShipping
 * Copyright (C) 2012 Bratech CO.,LTD. All Rights Reserved.
 * http://wwww.bratech.co.jp/
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
        <tr>
            <th>送料無料対象設定</th>
            <td>
                <span class="attention"><!--{$arrErr.plg_freeshipping_flg}--></span>
                <input type="checkbox" name="plg_freeshipping_flg" value="1" style="<!--{if $arrErr.plg_freeshipping_flg != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" <!--{if $arrForm.plg_freeshipping_flg == 1}-->checked="checked"<!--{/if}--> />送料無料対象商品に設定する
            </td>
        </tr>