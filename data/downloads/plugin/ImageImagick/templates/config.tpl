<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<h2><!--{$tpl_subtitle|h}--></h2>

<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">

<p>
  <!--{$arrPluginInfo.plugin_name|h}-->の設定が行えます。<br/>
</p>

<table border="0" cellspacing="1" cellpadding="8">
  <tr>
    <td bgcolor="#f3f3f3">圧縮品質(1〜100)<span class="red">※</span><br />
      (数値が低いほど高圧縮)
    </td>
    <td>
      <!--{assign var=key value="compression_quality"}-->
      <span class="red"><!--{$arrErr[$key]}--></span><br />
      <input type="text" name="compression_quality" value="<!--{$arrForm.compression_quality|h}-->" maxlength="<!--{$smarty.const.PERCENTAGE_LEN}-->" style="<!--{if $arrErr.compression_quality != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="6" class="box6" />
    </td>
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
