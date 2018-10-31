<!--{*
 * MemberPrice
 * Copyright (C) 2012 Bratech CO.,LTD. All Rights Reserved.
 * http://wwww.bratech.co.jp/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *}-->

<script>
    var pageNo = 2;
    var url = "<!--{$smarty.const.P_DETAIL_URLPATH}-->";
    var imagePath = "<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/";
    var statusImagePath = "<!--{$TPL_URLPATH}-->";

    function getProducts2(limit) {
        $.mobile.showPageLoadingMsg();
        var i = limit;
        //送信データを準備
        var postData = {};
        $('#form1').find(':input').each(function(){
            postData[$(this).attr('name')] = $(this).val();
        });
        postData["mode"] = "json";
        postData["pageno"] = pageNo;

        $.ajax({
            type: "POST",
            data: postData,
            url: "<!--{$smarty.const.ROOT_URLPATH}-->products/list.php",
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                var productStatus = result.productStatus;
                for (var product_id in result) {
                    if (isNaN(product_id)) continue;
                    var product = result[product_id];
                    var productHtml = "";
                    var maxCnt = $(".list_area").length - 1;
                    var productEl = $(".list_area").get(maxCnt);
                    productEl = $(productEl).clone(true).insertAfter(productEl);
                    maxCnt++;

                    //商品写真をセット
                    $($(".list_area .listphoto img").get(maxCnt)).attr({
                        src: "<!--{$smarty.const.IMAGE_SAVE_URLPATH}-->" + product.main_list_image,
                        alt: product.name
                    });

                    // 商品ステータスをセット
                    var statusAreaEl = $($(".list_area div.statusArea").get(maxCnt));
                    // 商品ステータスの削除
                    statusAreaEl.empty();

                    if (productStatus[product.product_id] != null) {
                        var statusEl = '<ul class="status_icon">';
                        var statusCnt = productStatus[product.product_id].length;
                        for (var k = 0; k < statusCnt; k++) {
                            var status = productStatus[product.product_id][k];
                            var statusImgEl = '<li>' + status.status_name + '</li>' + "\n";
                            statusEl += statusImgEl;
                        }
                        statusEl += "</ul>";
                        statusAreaEl.append(statusEl);
                    }

                    //商品名をセット
                    $($(".list_area a.productName").get(maxCnt)).text(product.name);
                    $($(".list_area a.productName").get(maxCnt)).attr("href", url + product.product_id);

                    //販売価格をセット
                    var price = $($(".list_area span.price").get(maxCnt));
                    //販売価格をクリア
                    price.empty();
                    var priceVale = "";
                    //販売価格が範囲か判定
                    if (product.price02_min == product.price02_max) {
                        priceVale = product.price02_min_tax_format + '円';
                    } else {
                        priceVale = product.price02_min_tax_format + '～' + product.price02_max_tax_format + '円';
                    }
                    price.append(priceVale);

                    //会員価格をセット
                    var price2 = $($(".list_area span.price2").get(maxCnt));
                    //会員価格をクリア
                    price2.empty();
                    var priceVale2 = "";
                    //会員価格が範囲か判定
                    if (product.plg_memberprice_price03_min_inctax == product.plg_memberprice_price03_max_inctax) {
                        priceVale2 = product.plg_memberprice_price03_min_tax_format + '円';
                    } else {
                        priceVale2 = product.plg_memberprice_price03_min_tax_format + '～' + product.plg_memberprice_price03_max_tax_format + '円';
                    }
                    if(product.plg_memberprice_price03_min_inctax != 0 && product.plg_memberprice_price03_min_inctax != "" && product.plg_memberprice_price03_min_inctax != null){
                    	price2.append(priceVale2);
                    }

                    //コメントをセット
                    $($(".list_area .listcomment").get(maxCnt)).text(product.main_list_comment);
                }
                pageNo++;

                //すべての商品を表示したか判定
                if (parseInt($("#productscount").text()) <= $(".list_area").length) {
                    $("#btn_more_product").hide();
                }
                $.mobile.hidePageLoadingMsg();
            }
        });
    }
</script>