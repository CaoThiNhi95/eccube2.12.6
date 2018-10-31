<!--{*2013.03.06 SEED クーポン機能*}-->
<!--{if $tpl_login == 1}-->
■ｸｰﾎﾟﾝ利用<br>
ｸｰﾎﾟﾝｺｰﾄﾞをお持ちの場合はご入力下さい｡
<br>
<input type="radio" name="coupon_check" value="1" <!--{$arrForm.coupon_check.value|sfGetChecked:1}-->>ｸｰﾎﾟﾝを使用する<br>
<!--{assign var=key value="coupon_id_name"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
ｸｰﾎﾟﾝｺｰﾄﾞ&nbsp;<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value}-->" maxlength="<!--{$arrForm[$key].length}-->" size="6"><br>
<input type="radio" name="coupon_check" value="2" <!--{$arrForm.coupon_check.value|sfGetChecked:2}-->>ｸｰﾎﾟﾝを使用しない<br>
<br>
<font color="red">※</font>ｸｰﾎﾟﾝｺｰﾄﾞは送付させていただいているﾒﾙﾏｶﾞに記載されております｡<br>
<br>
<!--{/if}-->
<!--{*2013.03.06 SEED クーポン機能*}-->
