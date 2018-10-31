<!--{*
 * CancelStockBack
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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<script type="text/javascript">
</script>

<h2><!--{$tpl_subtitle}--></h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">

<table border="0" cellspacing="1" cellpadding="8" summary=" ">
        <col width="50%" />
        <col width="50%" />
    <tr >
        <th>変更前対応状況<br><span class="attention" style="font-size:11px;">この項目が未設定の場合は「キャンセル扱いする対応状況」以外から「キャンセル扱いする対応状況」に変更した場合に在庫を自動で戻します。設定した場合はそれらの対応状況から「キャンセル扱いする対応状況」へ変更された場合に在庫を戻します。</span></th>
        <td><span class="attention"><!--{$arrErr.cancel_prev_status}--></span><!--{html_checkboxes options=$arrSTATUS name="cancel_prev_status" selected=$arrForm.cancel_prev_status separator="<br />"}--></td>
    </tr>
    <tr >
        <th>キャンセル扱いとする対応状況<br><span class="attention" style="font-size:11px;">ここで設定した対応状況に変更された場合に在庫を自動で戻します</span></th>
        <td><span class="attention"><!--{$arrErr.cancel_post_status}--></span><!--{html_checkboxes options=$arrSTATUS name="cancel_post_status" selected=$arrForm.cancel_post_status separator="<br />"}--></td>
    </tr>
</table>

<div class="btn-area">
    <ul>
        <li>
            <a class="btn-action" href="javascript:;" onclick="document.form1.submit();return false;"><span class="btn-next">この内容で登録する</span></a>
        </li>
    </ul>
</div>

</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
