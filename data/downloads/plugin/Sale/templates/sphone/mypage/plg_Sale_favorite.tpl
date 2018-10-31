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
                                    <!--{if $arrFavorite[cnt].price03_min_inctax != $arrFavorite[cnt].price02_min_inctax}-->
                                    <br />
                                    <span class="mini productPrice">セール価格：
                                    <!--{if $arrFavorite[cnt].price03_min == $arrFavorite[cnt].price03_max}-->
                                    <!--{$arrFavorite[cnt].price03_min_inctax|number_format}-->
                                    <!--{else}-->
                                    <!--{$arrFavorite[cnt].price03_min_inctax|number_format}-->～<!--{$arrFavorite[cnt].price03_max_inctax|number_format}-->
                                    <!--{/if}-->円</span>
                                    <!--{/if}-->
