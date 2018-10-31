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
<script type="text/javascript">//<![CDATA[
/**
 * 規格の選択状態に応じて, フィールドを設定する.
 */
$(function() {
    // 規格1選択時
    $('select[name=classcategory_id1]')
        .change(function() {
            var $form = $(this).parents('form');
            var product_id = $form.find('input[name=product_id]').val();
            var $sele1 = $(this);
            var $sele2 = $form.find('select[name=classcategory_id2]');

            // 規格1のみの場合
            if (!$sele2.length) {
                eccube.changeMemberPrice($form, product_id, $sele1.val(), '0');
            }
        });

    // 規格2選択時
    $('select[name=classcategory_id2]')
        .change(function() {
            var $form = $(this).parents('form');
            var product_id = $form.find('input[name=product_id]').val();
            var $sele1 = $form.find('select[name=classcategory_id1]');
            var $sele2 = $(this);
            eccube.changeMemberPrice($form, product_id, $sele1.val(), $sele2.val());
        });
});

eccube.changeMemberPrice = function($form, product_id, classcat_id1, classcat_id2) {

    classcat_id2 = classcat_id2 ? classcat_id2 : '';

    var classcat2;

    // 商品一覧時
    if (eccube.hasOwnProperty('productsClassCategories')) {
        classcat2 = eccube['productsClassCategories'][product_id][classcat_id1]['#' + classcat_id2];
    }
    // 詳細表示時
    else {
        classcat2 = eccube['classCategories'][classcat_id1]['#' + classcat_id2];
    }

    // 会員価格
    var $price03_default = $form.find('[id^=price03_default]');
    var $price03_dynamic = $form.find('[id^=price03_dynamic]');
    if (classcat2
        && typeof classcat2['plg_memberprice_price03'] != 'undefined'
        && String(classcat2['plg_memberprice_price03']).length >= 1) {

        $price03_dynamic.text(classcat2['plg_memberprice_price03']).show();
        $price03_default.hide();
    } else {
        $price03_dynamic.hide();
        $price03_default.show();
    }
}

var $form = $(document.form1);
var product_id = $form.find('input[name=product_id]').val();
var $sele1 = $form.find('select[name=classcategory_id1]');
var $sele2 = $form.find('select[name=classcategory_id2]');

if (!$sele2.length) {
    eccube.changeMemberPrice($form, product_id, $sele1.val() ? $sele1.val() : '__unselected','0');
}else{
    eccube.changeMemberPrice($form, product_id, $sele1.val() ? $sele1.val() : '__unselected',$sele2.val() ? $sele2.val() : '__unselected');
}
//]]></script>