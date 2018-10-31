<!--{if $arrForm.has_product_class == false}-->
            <tr>
                <th><!--{$smarty.const.MEMBER_PRICE_TITLE}--></th>
                <td>
                    <!--{if strlen($arrForm.plg_memberprice_price03) >= 1}--><!--{$arrForm.plg_memberprice_price03|h}--> 円<!--{/if}-->
                </td>
            </tr>
<!--{/if}-->