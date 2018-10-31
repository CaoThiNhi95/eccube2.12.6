<!--{if $arrForm.has_product_class == false}-->
        <tr>
            <th><!--{$smarty.const.MEMBER_PRICE_TITLE}--></th>
            <td>
                <span class="attention"><!--{$arrErr.plg_memberprice_price03}--></span>
                <input type="text" name="plg_memberprice_price03" value="<!--{$arrForm.plg_memberprice_price03|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.plg_memberprice_price03 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>円
                <span class="attention"> (半角数字で入力)</span>
            </td>
        </tr>
<!--{/if}-->