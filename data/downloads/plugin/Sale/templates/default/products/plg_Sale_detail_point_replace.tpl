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
                <div class="point">ポイント：
                    <span id="point_default"><!--{strip}-->
                        <!--{if $arrProduct.price03_min == $arrProduct.price03_max}-->
                            <!--{$arrProduct.price03_min|sfPrePoint:$arrProduct.point_rate|number_format}-->
                        <!--{else}-->
                            <!--{if $arrProduct.price03_min|sfPrePoint:$arrProduct.point_rate == $arrProduct.price03_max|sfPrePoint:$arrProduct.point_rate}-->
                                <!--{$arrProduct.price03_min|sfPrePoint:$arrProduct.point_rate|number_format}-->
                            <!--{else}-->
                                <!--{$arrProduct.price03_min|sfPrePoint:$arrProduct.point_rate|number_format}-->～<!--{$arrProduct.price03_max|sfPrePoint:$arrProduct.point_rate|number_format}-->
                            <!--{/if}-->
                        <!--{/if}-->
                    </span><span id="point_dynamic"></span><!--{/strip}-->
                    Pt
                </div>
