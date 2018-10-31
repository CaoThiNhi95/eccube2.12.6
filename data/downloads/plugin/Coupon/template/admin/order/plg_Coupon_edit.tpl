<!--{* 2013.03.04 SEED クーポン機能 *}-->
<tr>
    <th colspan="5" class="column right">クーポンによる割引額</th>
    <td class="right">
        <span class="attention"><!--{$arrErr.coupon_discount_price}--></span>
        <!--{if $coupon_discount_price > 0}-->
            <!--{$coupon_discount_price|number_format}--> 円
        <!--{else}-->
            <!--{$arrForm.coupon_discount_price.value|number_format}--> 円
        <!--{/if}-->
    </td>
</tr>
<!--{* 2013.03.04 SEED クーポン機能 *}-->
