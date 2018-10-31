<!--{*
 * NakwebSearchProductStatus
 * Copyright (C) 2012 NAKWEB CO.,LTD. All Rights Reserved.
 * http://www.nakweb.com/
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

<!-- start nakweb_search_product_status -->
            <!--{if $plg_nakweb_00003_arrProductStatusList}-->
            <dl class="formlist">
                <dt>商品ステータスから選ぶ</dt>
                <dd>
                    <!--{html_checkboxes name="plg_nakweb_00003_product_status_id" options=$plg_nakweb_00003_arrProductStatusList selected=$plg_nakweb_00003_product_status_id separator="<br />"}-->
                </dd>
            </dl>
            <!--{/if}-->
<!-- end   nakweb_search_product_status -->

