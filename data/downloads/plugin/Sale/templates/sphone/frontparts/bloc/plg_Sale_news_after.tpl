<!--{*
/*
 * Sale
 *
 * Copyright(c) 2009-2012 CUORE CO.,LTD. All Rights Reserved.
 *
 * http://ec.cuore.jp/
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
 */
 *}-->
<script>
    var salePageNo = 2;

    function getSale(limit) {
        $.mobile.showPageLoadingMsg();
        var i = limit;

        $.ajax({
            url: "<!--{$smarty.const.ROOT_URLPATH}-->frontparts/bloc/news.php",
            type: "POST",
            data: "mode=getSaleList&pageno="+salePageNo+"&disp_number="+i,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                if (result.error) {
                    alert(result.error);
                } else {
                    for (var j = 0; j < i; j++) {
                        if (result[j] != null) {
                            var sale = result[j];
                            var maxCnt = $("#sale_area ul.salelist li").length - 1;
                            var saleEl = $("#sale_area ul.salelist li").get(maxCnt);
                            saleEl = $(saleEl).clone(true).insertAfter(saleEl);
                            maxCnt++;

                            //件名をセット
                            var saleDateDispArray = sale.end_date.split("-"); //ハイフンで年月日を分解
                            var saleDateDisp = saleDateDispArray[0] + "年" + saleDateDispArray[1] + "月" + saleDateDispArray[2] + "日";
                            $($("#sale_area ul.salelist li a span.sale_title").get(maxCnt)).text(sale.name + " " + saleDateDisp + "まで開催中!!");

                            //リンクをセット
                            $($("#sale_area ul.salelist li a").get(maxCnt)).attr("href", "javascript:getSaleDetail(" + sale.sale_id + ");");
                        }
                    }

                    //すべての新着情報を表示したか判定
                    var salePageCount = result.sale_page_count;
                    if (parseInt(salePageCount) <= salePageNo) {
                        $("#btn_more_sale").hide();
                    }

                    salePageNo++;
                }
                $.mobile.hidePageLoadingMsg();
            }
        });
    }

    function getSaleDetail(saleId) {
        if (loadingState == 0) {
            $.mobile.showPageLoadingMsg();
            loadingState = 1;
            $.ajax({
                url: "<!--{$smarty.const.ROOT_URLPATH}-->frontparts/bloc/news.php",
                type: "GET",
                data: "mode=getSaleDetail&sale_id="+saleId,
                cache: false,
                async: false,
                dataType: "json",
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    alert(textStatus);
                    $.mobile.hidePageLoadingMsg();
                    loadingState = 0;
                },
                success: function(result){
                    if (result.error) {
                        alert(result.error);
                        $.mobile.hidePageLoadingMsg();
                        loadingState = 0;
                    }
                    else if (result[0] != null) {
                        var sale = result[0];
                        var maxCnt = 0;

                        //件名をセット
                        $($("#salewindowcolumn dl.view_detail dt a").get(maxCnt)).text(sale.name);

                        //コメントをセット(iphone4の場合、innerHTMLの再描画が行われない為、タイマーで無理やり再描画させる)
                        setTimeout( function() {
                            $("#saleComment").html(sale.comment.replace(/\n/g,"<br />"));
                        }, 10);

                        $.mobile.changePage('#salewindowcolumn', {transition: "slideup"});
                        //ダイアログが開き終わるまで待機
                        setTimeout( function() {
                            loadingState = 0;
                            $.mobile.hidePageLoadingMsg();
                        }, 1000);
                    }
                }
            });
        }
    }
</script>
