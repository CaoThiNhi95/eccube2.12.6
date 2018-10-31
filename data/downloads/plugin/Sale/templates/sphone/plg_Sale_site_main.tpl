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
#salewindowcolumn{
background:#FFF;
padding-bottom:30px;
}
-->
</style>
    <!-- ▼セール明細情報 -->
    <div id="salewindowcolumn" data-role="dialog">
     <div data-role="header" data-backbtn="true" data-theme="f">
       <h2>セール情報</h2>
     </div>

     <div data-role="content" data-theme="d">
       <dl class="view_detail">
         <dt><a href="javascript:;" rel="external" target="_blank"></a></dt>
           <dd id="saleComment"></dd>
       </dl>
         <p><a href="<!--{$smarty.const.HTTP_URL}-->" class="btn_more" data-rel="back">セール一覧にもどる</a></p>
     </div>
    </div>
    <!-- ▲セール明細情報 -->
