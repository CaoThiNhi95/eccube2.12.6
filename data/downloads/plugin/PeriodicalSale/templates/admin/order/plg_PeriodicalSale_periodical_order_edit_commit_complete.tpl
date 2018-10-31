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

<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="periodical_order_id" value="<!--{$arrForm.periodical_order_id.value}-->" />
    <input type="hidden" name="order_id" value="<!--{$arrLastOrder.order_id}-->" />
    <div id="complete">
        <div class="complete-top">
        </div>
        <div class="contents">
            <div class="message">
                受注データを発行しました。
            </div>
        </div>
        <div class="btn-area-top">
        </div>
        <div class="btn-area">
            <ul>
                <li>
                    <a class="btn-action" href="javascript:;" onclick="fnChangeAction('?'); fnModeSubmit('pre_edit','',''); return false;">
                        <span class="btn-prev">定期情報画面へ戻る</span>
                    </a>
                </li>
                <li>
                    <a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_EDIT_URLPATH}-->'); fnModeSubmit('pre_edit','',''); return false;">
                        <span class="btn-prev">発行した受注へ進む</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="btn-area-bottom">
        </div>
    </div>
</form>
