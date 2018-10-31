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

                        <p class="sale_price"><span class="mini">ポイント：</span><span id="point_default">
                        	<!--{if strlen($arrProduct.plg_memberprice_price03_min) > 0 && (($smarty.session.customer && $smarty.const.PLG_MEMBER_PRICE_LOGIN_DISP == 1) || $smarty.const.PLG_MEMBER_PRICE_LOGIN_DISP == 0)}-->
                                <!--{if $arrProduct.plg_memberprice_price03_min == $arrProduct.plg_memberprice_price03_max}-->
                                    <!--{$arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                <!--{else}-->
                                    <!--{if $arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.plg_memberprice_price03_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
                                        <!--{$arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                    <!--{else}-->                  <!--{$arrProduct.plg_memberprice_price03_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->～<!--{$arrProduct.plg_memberprice_price03_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                    <!--{/if}-->
                                <!--{/if}-->
                            <!--{else}-->
                                <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                                    <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                <!--{else}-->
                                    <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
                                        <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                    <!--{else}-->
                                        <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->～<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                    <!--{/if}-->
                                <!--{/if}-->
                            <!--{/if}-->
                            </span><span id="point_dynamic"></span>Pt
                        </p>
