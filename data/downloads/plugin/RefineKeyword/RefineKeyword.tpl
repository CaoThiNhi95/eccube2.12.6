<script type="text/javascript">//<![CDATA[
    // 表示件数を変更
    function fnChangeName(name) {
        fnSetVal('name', name);
        fnSetVal('pageno', 1);
        fnSubmit();
    }
//]]></script>

キーワードで絞り込む:<!--{foreach from=$arrKeywords item=i}-->
<a href="javascript:fnChangeName('<!--{$i|h}-->');"><!--{$i|h}--></a>&nbsp;
<!--{/foreach}-->
