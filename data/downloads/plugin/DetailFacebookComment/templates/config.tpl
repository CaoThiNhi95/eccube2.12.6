
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<script type="text/javascript">
</script>

<h2><!--{$tpl_subtitle}--></h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">
<p>FaceBookコメントの設定を行います。<br/>
    <br/>
</p>

<table border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr>
        <td colspan="2" width="90" bgcolor="#f3f3f3">設定</td>
    </tr>
    <tr >
        <td bgcolor="#f3f3f3">app_id<span class="red">※</span></td>
        <td>
        <!--{assign var=key value="free_field1"}-->
        <span class="red"><!--{$arrErr[$key]}--></span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->"</input>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">data-width<span class="red">※</span></td>
        <td>
        <!--{assign var=key value="free_field2"}-->
        <span class="red"><!--{$arrErr[$key]}--></span>
        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->"</input>
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">data-num-posts<span class="red"></span></td>
        <td>
        <!--{assign var=key value="free_field3"}-->
        <span class="red"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->">
        <!--{html_options options=$arrNumList selected=$arrForm[$key]}-->
        </select>
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
