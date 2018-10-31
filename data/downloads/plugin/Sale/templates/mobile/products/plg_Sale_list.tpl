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
        <!--{if $arrProduct.price03_min_inctax != $arrProduct.price02_min_inctax}-->
            セール価格：
            <!--{if $arrProduct.price03_min_inctax == $arrProduct.price03_max_inctax}-->
                <!--{$arrProduct.price03_min_inctax|number_format}-->円
            <!--{else}-->
                <!--{$arrProduct.price03_min_inctax|number_format}-->円～<!--{$arrProduct.price03_max_inctax|number_format}-->円
            <!--{/if}-->
            <br>
        <!--{/if}-->
