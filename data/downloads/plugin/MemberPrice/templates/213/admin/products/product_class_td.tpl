<!--{*
 * MemberPrice
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
 
                <td class="center">
                    <!--{assign var=key value="plg_memberprice_price03"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key][$index] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />
                </td>