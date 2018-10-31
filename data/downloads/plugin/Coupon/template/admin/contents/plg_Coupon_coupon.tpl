<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="coupon_id" value="">
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
<div id="system" class="contents-main">
    <!--▼クーポン一覧ここから-->
    <table class="list">
        <colgroup width="5%">
        <colgroup width="13%">
        <colgroup width="15%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="17%">
        <colgroup width="7%">
        <colgroup width="4%">
        <colgroup width="4%">
        <colgroup width="5%">
        <div class="btn">
          <a class="btn-action" href="#" onclick="location.href='./plg_Coupon_coupon_input.php'"><span class="btn-next">クーポンを新規作成</span></a>
          <br /><br />
          <!--▼ページ送り-->
          <span class="attention"><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
          <!--{if count($list_data) > 0}-->
            <!--{include file=$tpl_pager}-->
          <!--{/if}-->
          <!--▲ページ送り-->
        </div>
        <tr>
            <th>No.</th>
            <th>クーポンID</th>
            <th>メモ(非公開)</th>
            <th>割引額(率)</th>
            <th>対象</th>
            <th>利用可能<br />購入金額下限</th>
            <th>有効期限</th>
            <th>利用可能<br />回数制限</th>
            <th>編集</th>
            <th>削除</th>
            <th>使用回数</th>
        </tr>
        <!--{foreach name=loop from=$list_data item=data}-->
        <!--▼クーポン<!--{$smarty.section.data.iteration}-->-->
        <!--{assign var=enable_flg value=$data.enable_flg|escape}-->
        <!--{if $enable_flg==1 || $smarty.now>$data.end_date_timestamp }-->
            <!--{assign var=tr_color value='#B0B0B0'}-->
        <!--{else}-->
            <!--{assign var=tr_color value='#ffffff'}-->
        <!--{/if}-->
        <tr bgcolor="<!--{$tr_color}-->">
            <td><!--{$data.coupon_id|escape}--></td>
            <td><!--{$data.coupon_id_name|escape}--></td>
            <td><!--{$data.memo|escape}--></td>
            <td align="center"><!--{if $data.discount_type==0 }-->&yen;<!--{$data.discount_price|escape|number_format}--><!--{else}--><!--{$data.discount_percent|escape}-->%<!--{/if}--></td>
            <td align="center"><!--{if $data.coupon_target==0 }-->全て<!--{else}-->限定<!--{/if}--></td>
            <td align="center">\<!--{$data.use_limit|escape}--></td>
            <td align="center"><!--{$data.start_date|sfDispDBDate:false|escape}-->～<!--{$data.end_date|sfDispDBDate:false|escape}--></td>
            <td align="center"><!--{if $data.count_limit==0 }-->無制限<!--{else}-->1回限り<!--{/if}--></td>
            <td align="center"><a href="" onclick="eccube.changeAction('plg_Coupon_coupon_input.php', 'form1'); eccube.fnFormModeSubmit('form1', 'edit', 'coupon_id', <!--{$data.coupon_id}-->); return false;">編集</a></td>
            <td align="center"><a href="" onclick="eccube.fnFormModeSubmit('form1', 'delete', 'coupon_id', <!--{$data.coupon_id}-->); return false;">削除</a></td>
            <td align="center"><!--{if $data.used_num }--><!--{$data.used_num|escape|number_format}--><!--{else}-->0<!--{/if}-->回</td>
        </tr>
        <!--▲クーポン<!--{$smarty.section.data.iteration}-->-->
        <!--{foreachelse}-->
        <tr bgcolor="#ffffff"><td colspan="11" align="center"> まだクーポンが登録されていません </td></tr>
        <!--{/foreach}-->
    </table>

    <div class="paging">
        <!--▼ページ送り-->
        <!--{$tpl_strnavi}-->
        <!--▲ページ送り-->
    </div>
</div>
</form>
