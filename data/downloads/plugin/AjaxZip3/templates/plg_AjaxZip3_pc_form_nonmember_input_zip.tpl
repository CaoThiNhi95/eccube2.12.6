<tr>
    <th>郵便番号<span class="attention">※</span></th>
    <td>
        <!--{assign var=key1 value="order_zip01"}-->
        <!--{assign var=key2 value="order_zip02"}-->
        <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
        <p class="top">〒&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" onkeyup="fnCallAddress('', '<!--{$key1}-->','<!--{$key2}-->','order_pref','order_addr01', true);" />&nbsp;-&nbsp;    <input type="text"    name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" onkeyup="fnCallAddress('', '<!--{$key1}-->','<!--{$key2}-->','order_pref','order_addr01', true);" />　
        <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索</span></a>
        </p>
    </td>
</tr>
