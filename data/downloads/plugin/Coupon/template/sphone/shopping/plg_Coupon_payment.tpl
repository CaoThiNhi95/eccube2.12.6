        <!-- ▼クーポン使用 ここから -->
        <!--{if $tpl_login == 1}-->
            <script type="text/javascript">
            <!--
                // onload登録
                if (window.addEventListener) { //for W3C DOM
                    window.addEventListener("load", fnCheckInputCoupon, false);
                } else if (window.attachEvent) { //for IE
                    window.attachEvent("onload", fnCheckInputCoupon);
                }
            // -->
            </script>

            <!--★クーポン利用の指定★-->
            <section class="coupon_area">
                <h3 class="subtitle">クーポンを利用する</h3>
             
                <div class="form_area">
                    <div class="point_announce">
                        <p><span class="point">クーポン</span>を使用する事ができます。<br />
                           使用する場合は、「クーポンを使用する」にチェックを入れた後、使用するクーポンIDをご記入ください。</p>
                    </div>

                    <!--▼クーポンフォームボックスここから -->
                    <div class="formBox">
                        <div class="innerBox fb">
                            <p><input type="radio" id="coupon_on" name="coupon_check" value="1" <!--{$arrForm.coupon_check.value|sfGetChecked:1}--> onchange="fnCheckInputCoupon();" class="data-role-none" />
                            <label for="coupon_on">クーポンを使用する</label></p>
                            <!--{assign var=key value="coupon_id_name"}-->
                            <p class="check_point">クーポンコード：<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:""}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box_point data-role-none" /><span class="attention"><!--{$arrErr[$key]}--></span></p>
                        </div>
                        <div class="innerBox fb">
                            <input type="radio" id="coupon_off" name="coupon_check" value="2" <!--{$arrForm.coupon_check.value|sfGetChecked:2}--> onchange="fnCheckInputCoupon();" class="data-role-none" />
                            <label for="coupon_off">クーポンを使用しない</label>
                        </div>
                    </div><!--▲formBox -->
                </div><!--▲form_area -->
            </section>
        <!--{/if}-->

