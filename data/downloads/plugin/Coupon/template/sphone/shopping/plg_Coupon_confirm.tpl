<!--{*2013.03.06 SEED_KN クーポン機能*}-->
<!--{if $arrForm.coupon_check != "" && $arrForm.coupon_check != "0" && $arrForm.coupon_discount_price > 0}-->
<li><span class="mini">クーポン利用 ：</span>-<!--{$arrForm.coupon_discount_price|number_format}-->円<!--{*(<!--{<!--{$arrForm.coupon_discount_percent|number_format}-->}-->％)*}--></li>
<!--{/if}-->
<!--{*2013.03.06 SEED_KN クーポン機能*}-->
