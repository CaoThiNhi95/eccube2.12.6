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

<!--{assign var=key value="is_periodical"}-->
<tr>
    <th>
        定期販売
    </th>
    <td>
        <span class="attention">
            <!--{$arrErr[$key]}-->
        </span>
        <input type="hidden" name="<!--{$key}-->" value="0" />
        <label>
            <input type="checkbox" name="<!--{$key}-->" value="1" <!--{if $arrForm[$key] == "1"}-->checked<!--{/if}--> />
            定期販売商品
        </label><br />
        <small class="attention">
            ※ダウンロード商品は定期販売出来ません。
        </small>
    </td>
</tr>
<!--{assign var=key value="period_price_difference"}-->
<tr>
    <th>
        定期価格差
    </th>
    <td>
        <span class="attention">
            <!--{$arrErr[$key]}-->
        </span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
        <small class="attention">
            ※2回目以降の価格に加算されます。<br />
            ※負数指定で割引き。
        </small>
    </td>
</tr>