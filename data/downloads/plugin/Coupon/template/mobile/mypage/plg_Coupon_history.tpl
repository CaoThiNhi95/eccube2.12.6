<!--{*2013.03.04 SEED クーポン機能*}-->
    <!--{assign var=coupon_discount_price value="`$tpl_arrOrderData.coupon_discount_price`"}-->
    <!--{if $coupon_discount_price > 0}-->
        <br>クーポン：<!--{$coupon_discount_price|number_format}-->円
    <!--{/if}-->
<!--{*2013.03.04 SEED クーポン機能*}-->
