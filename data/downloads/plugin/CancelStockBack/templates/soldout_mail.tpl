<!--{*
 * SoldOutMail
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

【商品の売れ切れ通知】
商品ID：<!--{$arrProduct.product_id}-->
商品コード：<!--{$arrProduct.product_code}-->
商品名：<!--{$arrProduct.name}-->
<!--{if $arrProduct.classcategory_name1}-->
<!--{$arrProduct.class_name1}-->：<!--{$arrProduct.classcategory_name1}-->
<!--{/if}-->
<!--{if $arrProduct.classcategory_name2}-->
<!--{$arrProduct.class_name2}-->：<!--{$arrProduct.classcategory_name2}-->
<!--{/if}-->

上記の商品が売れ切れました。