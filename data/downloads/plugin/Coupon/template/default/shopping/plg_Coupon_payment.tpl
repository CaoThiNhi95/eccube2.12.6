        <!-- ▼クーポン使用 ここから -->
        <!--{if $tpl_login == 1}-->
        <script type="text/javascript">
        <!--
            function fnCheckInputCoupon() {
                if(document.form1['coupon_check']) {
                    list = new Array(
                        'coupon_id_name'
                    );
                    if(!document.form1['coupon_check'][0].checked) {
                        color = "#dddddd";
                        flag = true;
                    } else {
                        color = "";
                        flag = false;
                    }

                    len = list.length
                    for(i = 0; i < len; i++) {
                        if(document.form1[list[i]]) {
                            var current_color = document.form1[list[i]].style.backgroundColor;
                            if (color != "#dddddd" && (current_color == "#ffe8e8" || current_color == "rgb(255, 232, 232)"))
                            {
                                continue;
                            }
                            document.form1[list[i]].disabled = flag;
                            document.form1[list[i]].style.backgroundColor = color;
                        }
                    }
                }
            }
        // -->
        </script>

        <div class="point_area">
          <h3>クーポンを利用する</h3>
          <p><span class="attention">クーポン</span>を使用する事ができます。<br />
          使用する場合は、「クーポンを使用する」にチェックを入れた後、使用するクーポンIDをご記入ください。</p>
          <div class="point_announce">
            <p><!--{$name01|h}--> <!--{$name02|h}-->様の今回のご購入合計金額：<span class="price"><!--{$arrPrices.subtotal|number_format}-->円</span>
            <span class="attention">(送料、手数料を含みません。)</span></p>
            <ul>
              <li><input type="radio" id="coupon_on" name="coupon_check" value="1" <!--{$arrForm.coupon_check.value|sfGetChecked:1}--> onclick="fnCheckInputCoupon();" /><label for="point_on">クーポンを使用する</label></li>
              <!--{assign var=key value="coupon_id_name"}-->
              <li><span class="attention"><!--{$arrErr[$key]}--></span></li>
              <li class="underline">&nbsp;&nbsp;&nbsp;&nbsp;クーポンコード&nbsp;<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:""}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box120" /></li>
              <li><input type="radio" id="coupon_off" name="coupon_check" value="2" <!--{$arrForm.coupon_check.value|sfGetChecked:2}--> onclick="fnCheckInputCoupon();" /><label for="point_off">クーポンを使用しない</label></li>
            </ul>
            <br />
            <p><span class="attention">※</span>クーポンIDは送付させていただいているメルマガに記載されております 。</p>
          </div>
        </div>
        <!--{/if}-->
        <!-- ▲クーポン使用 ここまで -->

