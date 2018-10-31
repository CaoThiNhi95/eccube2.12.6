<!--{*
 * NakwebSearchProductStatus
 * Copyright (C) 2012 NAKWEB CO.,LTD. All Rights Reserved.
 * http://www.nakweb.com/
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

<!-- start NakwebSearchProductStatus -->
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<script type="text/javascript">
</script>

<h2><!--{$tpl_subtitle}--></h2>
<form name="plg_form" id="plg_form" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">
<p><!--{$tpl_note}--><br/>
    <br/>
</p>

<table border="0" cellspacing="1" cellpadding="8" summary=" ">
    <!-- 詳細タイトル -->
    <tr>
        <td colspan="2" bgcolor="#f3f3f3">▼<!--{$tpl_subtitle}--> 詳細</td>
    </tr>
    <!-- RadioBox -->
    <tr>
        <td bgcolor="#f3f3f3">商品ステータス</td>
        <td>
            <!--{assign var=key value="product_status_id"}-->
            <!--{if $arrErr[$key]}--><span class="red"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
            <!--{html_radios name=$key options=$arrEnableCheck selected=$arrForm.product_status_id separator='&nbsp;'}-->
        </td>
    </tr>
    <!-- RadioBox -->
    <tr>
        <td bgcolor="#f3f3f3">商品コード（入力欄は商品名入力欄と共通）</td>
        <td> <!--{$arrForm[product_code]}-->
            <!--{assign var=key value="product_code"}-->
            <!--{if $arrErr[$key]}--><span class="red"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
            <!--{html_radios name=$key options=$arrEnableCheck selected=$arrForm.product_code separator='&nbsp;'}-->
        </td>
    </tr>
</table>

<div class="btn-area">
    <ul>
        <li>
            <a class="btn-action" href="javascript:;" onclick="document.plg_form.submit();return false;"><span class="btn-next">この内容で登録する</span></a>
        </li>
    </ul>
</div>

</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
<!-- end   NakwebSearchProductStatus -->
