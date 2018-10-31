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
<style type="text/css">
<!--
#sale_area{
margin-bottom: 20px;
}
#sale_area ul{
}
#sale_area li{
display:block;
clear:both;
padding:10px;
line-height:1.3;
background-color:#FEFEFE;
background: -moz-linear-gradient(center top, #FEFEFE 0%,#EEEEEE 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #FEFEFE),color-stop(1, #EEEEEE));
border-top:#FFF solid 1px;
border-bottom:#CCC solid 1px;
}
#sale_area .sale_comment{
clear:both;
font-size:12px;
letter-spacing:0.1em;
}
-->
</style>
<!-- ▼セール情報 -->
<section id="sale_area">
    <h2 class="title_block">セール情報</h2>
    <ul class="salelist">
        <!--{section name=data loop=$arrSaleInfo max=3}-->
            <li>
                <a id="windowcolumn<!--{$smarty.section.data.index}-->" href="javascript:getSaleDetail(<!--{$arrSaleInfo[data].sale_id}-->);">
                <span class="sale_title"><!--{$arrSaleInfo[data].name|h}--> <!--{$arrSaleInfo[data].end_date|date_format:"%Y&#24180;%m&#26376;%d&#26085;"}-->まで開催中!!</span></a><br />
            </li>
        <!--{/section}-->
    </ul>

    <!--{if $saleCount > 3}-->
        <div class="btn_area">
            <p><a href="javascript:;" class="btn_more" id="btn_more_sale" onclick="getSale(3); return false;">もっとみる(＋3件)</a></p>
        </div>
    <!--{/if}-->
</section>
<!-- ▲セール情報 -->
