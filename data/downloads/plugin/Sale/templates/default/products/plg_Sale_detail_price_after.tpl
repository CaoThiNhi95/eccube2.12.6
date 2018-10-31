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
                            <!--{assign var=price03_min value=`$arrItem.price03_min_inctax`}-->
                            <!--{assign var=price03_max value=`$arrItem.price03_max_inctax`}-->
                            <!--{if $price03_min != $price02_min}-->
                            <br />
                            セール価格(税込)：<span class="price">
                            <!--{if $price03_min == $price03_max}-->
                                <!--{$price03_min|number_format}-->
                            <!--{else}-->
                                <!--{$price03_min|number_format}-->～<!--{$price03_max|number_format}-->
                                <!--{$price03_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price03_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                            <!--{/if}-->円</span>
                            <!--{/if}-->
