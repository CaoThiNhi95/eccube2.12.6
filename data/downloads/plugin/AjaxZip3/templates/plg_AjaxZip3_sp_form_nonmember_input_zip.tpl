<dd>
    <!--{assign var=key1 value="order_zip01"}-->
    <!--{assign var=key2 value="order_zip02"}-->
    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
    <p>
        <input type="tel" name="<!--{$key1}-->"
            value="<!--{$arrForm[$key1].value|h}-->"
            max="<!--{$arrForm[$key1].length}-->"
            onkeyup="fnCallAddress('', '<!--{$key1}-->','<!--{$key2}-->','order_pref','order_addr01', true);"
            style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;－&nbsp;
        <input type="tel" name="<!--{$key2}-->"
            value="<!--{$arrForm[$key2].value|h}-->"
            max="<!--{$arrForm[$key2].length}-->"
            onkeyup="fnCallAddress('', '<!--{$key1}-->','<!--{$key2}-->','order_pref','order_addr01', true);"
            style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" class="boxShort text data-role-none" />&nbsp;
        <a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fn">郵便番号検索</span></a>
    </p>
</dd>
