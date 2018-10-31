<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( id ){
	var fm = window.opener.document.form<!--{$smarty.get.rank}-->;
	fm.product_id.value = id;
	fm.mode.value = 'set_item';
	fm.rank.value = '<!--{$smarty.get.rank}-->';
	fm.submit();
	window.close();
	return false;
}

var tags = new Array();

function setTag(tag){
	var textArea = window.opener.document.getElementById('plg_shiro8_categorycontents_category_contents');
	textArea.value += tag;
}
//-->
</script>

<form name="form1" id="form1" method="post" action="#">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

	<!--▼タグ結果表示-->
	<!--{if $tpl_linemax}-->
	<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#FFFFFF">
		<tr class="fs12">
			<td align="left"><!--{$tpl_linemax}-->件が該当しました。</td>
		</tr>
		<tr class="fs12">
			<td align="center">
			<!--▼ページナビ-->
			<!--{$tpl_strnavi}-->
			<!--▲ページナビ-->
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>

	<!--▼タグ表示部分-->
	<table width="420" border="0" cellspacing="1" cellpadding="5" bgcolor="#cccccc">
		<tr bgcolor="#f0f0f0" align="center" class="fs12">
			<td width="90">登録画像</td>
			<td width="130">ファイル名</td>
			<td width="150">htmlタグ</td>
			<td width="50">コピー</td>
		</tr>
		<!--{section name=cnt loop=$arrProducts}-->
		<!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
		<tr bgcolor="#FFFFFF" class="fs12n">
			<td width="90" align="center">
			<!--{if $arrProducts[cnt].url != ""}-->
				<!--{assign var=image_path value="`$arrProducts[cnt].url`"}-->
				<img src="<!--{$image_path}-->" width="150" alt=""/>
			<!--{else}-->
				<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_DIR`"}-->
				<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$image_path}-->&width=65&height=65" alt="">
			<!--{/if}-->
			</td>
			<td width="130"><!--{$arrProducts[cnt].file_name|escape|default:"-"}--></td>
			<td width="150"><!--{$arrProducts[cnt].tag|escape}--></td>
			<td width="50" align="center">
				<script>
					tags.push('<!--{$arrProducts[cnt].tag}-->');
				</script>
				<a href="javascript:void(0);" onClick="javascript:setTag(tags[<!--{$smarty.section.cnt.iteration}-->-1]);return false;">決定</a>
			</td>
		</tr>
		<!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
		<!--{sectionelse}-->
		<tr bgcolor="#FFFFFF" class="fs10n">
			<td colspan="4">商品が登録されていません</td>
		</tr>
		<!--{/section}-->
	</table>
	<!--{/if}-->
	<!--▲タグ表示-->

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->