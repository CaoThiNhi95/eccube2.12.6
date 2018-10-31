<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<script type="text/javascript">
<!--
        // 定額・定率切替
        function onChangeDiscountType() {
            var e = document.getElementsByName("discount_type");
            var price   = document.getElementById("discount_price");
            var percent = document.getElementById("discount_percent");
            for(var i=0;i<e.length;i++) {
                if (e[i].checked)
                    switch(e[i].value) {
                        case '0': percent.disabled=true;  price.disabled=false; break;
                        case '1': percent.disabled=false; price.disabled=true;  break;
                    }
            }
        }

        // 対象商品表示切替
        function displayCouponTarget() {
            var e = document.getElementsByName("coupon_target");
            var div = document.getElementById("coupon_target_limited");
            for(var i=0;i<e.length;i++) {
                if (e[i].checked)
                    switch(e[i].value) {
                        case '0': div.style.display = "none"; break;
                        case '1': div.style.display = "block";  break;
                    }
            }
        }

        function fnFormRegistConfirm() {
          /*if (fnConfirm()) {
                document.form1.submit();
            }*/
            document.forms['form1']['mode'].value = 'regist';
            document.form1.submit();
        }

	// onload登録
        function fnFormInit() {
	    if (window.addEventListener) { //for W3C DOM
	        window.addEventListener("load", onChangeDiscountType, false);
	        window.addEventListener("load", displayCouponTarget, false);
	    } else if (window.attachEvent) { //for IE
	        window.attachEvent("onload", onChangeDiscountType);
	        window.attachEvent("onload", displayCouponTarget);
	    }
        }
//-->
</script>

<!--★★メインコンテンツ★★-->
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="<!--{$mode}-->">
  <input type="hidden" name="coupon_id" value="<!--{$arrForm.coupon_id}-->">
  <input type="hidden" name="product_id" value="<!--{$product_id|default:''}-->" />
  <!--{foreach item=val from=$prodcuts_list}-->
  <input type="hidden" name="prodcuts_list[]" value="<!--{$val}-->">
  <!--{/foreach}-->
  <table class="form">
    <tr>
      <th colspan="1">クーポンID<span class="red">*</span></th>
      <td colspan="3">
        <!--{if $arrErr.coupon_id_name}--><span class="red"><!--{$arrErr.coupon_id_name}--></span><!--{/if}-->
        <input type="text" name="coupon_id_name" size="65" class="box20" <!--{if $arrErr.coupon_id_name}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.coupon_id_name|escape}-->" />(半角英数字20文字以下)
      </td>
    </tr>
    <tr>
      <th colspan="1">メモ(非公開)</th>
      <td colspan="3">
        <!--{if $arrErr.memo}--><span class="red12"><!--{$arrErr.memo}--></span><br><!--{/if}-->
        <input type="text" name="memo" size="65" class="box65" <!--{if $arrErr.memo}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.memo|escape}-->" />
      </td>
    </tr>
    <tr>
      <th>定率・定額<span class="red">*</span></th>
      <td>
        <!--{if $arrErr.discount_type}--><span class="red"><!--{$arrErr.discount_type}--></span><!--{/if}-->
        <span <!--{if $arrErr.discount_type}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{html_radios name="discount_type" options=$arrDiscountType separator="&nbsp;" selected=$arrForm.discount_type|default:0 onclick="onChangeDiscountType()" }--></span>
      </td>
      <th>値引き<span class="red">*</span></th>
      <td>
        <!--{if $arrErr.discount_price}--><span class="red"><!--{$arrErr.discount_price}--></span><!--{/if}-->
        <!--{if $arrErr.discount_percent}--><span class="red"><!--{$arrErr.discount_percent}--></span><!--{/if}-->
        <input type="text" name="discount_price" id="discount_price" size="65" class="box6" maxlength="5" <!--{if $arrErr.discount_price}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.discount_price|escape}-->" />円&nbsp;
        <input type="text" name="discount_percent" id="discount_percent" size="65" class="box3" maxlength="2" <!--{if $arrErr.discount_percent}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.discount_percent|escape}-->" />％
      </td>
    </tr>
    <tr>
      <th colspan="1">利用可能購入金額下限<span class="red">*</span></th>
      <td colspan="3">
        <!--{if $arrErr.use_limit}--><span class="red"><!--{$arrErr.use_limit}--></span><!--{/if}-->
        <input type="text" name="use_limit" id="use_limit" size="65" class="box6" maxlength="5" <!--{if $arrErr.use_limit}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.use_limit|escape}-->" />円
      </td>
    </tr>
    <tr>
      <th colspan="1">有効/無効<span class="red">*</span></th>
      <td colspan="3">
        <!--{if $arrErr.enable_flg}--><span class="red12"><!--{$arrErr.enable_flg}--></span><br><!--{/if}-->
        <span <!--{if $arrErr.enable_flg}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{html_radios name="enable_flg" options=$arrEnable separator="&nbsp;" selected=$arrForm.enable_flg|default:0}--></span>
      </td>
    </tr>
    <tr>
      <th colspan="1">有効期限<span class="red">*</span></th>
      <td colspan="3">
        <span class="red"><!--{$arrErr.start_year}--><!--{$arrErr.start_month}--><!--{$arrErr.start_day}--></span>
        <span class="red"><!--{$arrErr.end_year}--><!--{$arrErr.end_month}--><!--{$arrErr.end_day}--></span>
        <select name="start_year" <!--{if $arrErr.start_year or $arrErr.start_month or $arrErr.start_day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>----</option>
          <!--{html_options options=$arrYear selected=$start_selected_year}-->
        </select>年
        <select name="start_month" <!--{if $arrErr.start_year or $arrErr.start_month or $arrErr.start_day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrMonth selected=$start_selected_month}-->
        </select>月
        <select name="start_day" <!--{if $arrErr.start_year or $arrErr.start_month or $arrErr.start_day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrDay selected=$start_selected_day}-->
        </select>日
        &nbsp;～&nbsp;
        <select name="end_year" <!--{if $arrErr.end_year or $arrErr.end_month or $arrErr.end_day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>----</option>
          <!--{html_options options=$arrYear selected=$end_selected_year}-->
        </select>年
        <select name="end_month" <!--{if $arrErr.end_year or $arrErr.end_month or $arrErr.end_day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrMonth selected=$end_selected_month}-->
        </select>月
        <select name="end_day" <!--{if $arrErr.end_year or $arrErr.end_month or $arrErr.end_day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
          <option value="" selected>--</option>
          <!--{html_options options=$arrDay selected=$end_selected_day}-->
        </select>日
      </td>
    </tr>
    <tr>
      <th colspan="1">対象商品<span class="red">*</span></th>
      <td colspan="3">
        <span <!--{if $arrErr.coupon_target}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{html_radios name="coupon_target" options=$arrCouponTarget separator="&nbsp;" selected=$arrForm.coupon_target|default:0 onclick="displayCouponTarget()"}--></span>
        <!--{if $arrErr.coupon_target}--><span class="red"><!--{$arrErr.coupon_target}--></span><br><!--{/if}-->
        <div id="coupon_target_limited">
          <input type="button" onclick="win03('./plg_Coupon_product_select.php', 'coupon_target_select', '615', '500'); " value="選択" /><br/>
          <!--{foreach item=val from=$arrProducts}-->
            <!--{$val.name|escape}--> [<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnFormModeSubmit('form1','delete_product','product_id','<!--{$val.product_id}-->'); return false;" >削除</a>] <br>
          <!--{/foreach}-->
        </div>
      </td>
    </tr>
    <tr>
      <th colspan="1">回数制限<span class="red">*</span></th>
      <td colspan="3">
        <!--{if $arrErr.count_limit}--><span class="red"><!--{$arrErr.count_limit}--></span><!--{/if}-->
        <span <!--{if $arrErr.count_limit}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{html_radios name="count_limit" options=$arrCountLimit separator="&nbsp;" selected=$arrForm.count_limit|default:1 }--></span>
      </td>
    </tr>
  </table>
  <center><div class="btn-area">
    <ul><li><a class="btn-action" href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnFormRegistConfirm(); return false;"><span class="btn-next">この内容で登録する</span></a></li></ul>
  </div></center>
</form>
<!--★★メインコンテンツ★★-->

