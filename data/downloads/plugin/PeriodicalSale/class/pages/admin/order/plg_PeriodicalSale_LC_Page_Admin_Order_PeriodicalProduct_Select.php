<?php
/*
 * PeriodicalSale
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
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

require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_ProductSelect_Ex.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Admin_Order_PeriodicalProduct_Select extends LC_Page_Admin_Order_ProductSelect_Ex {


    /**
     * POSTされた値からSQLのWHEREとBINDを配列で返す。
     * 
     * @param SC_FormParam $objFormParam インスタンス
     * @return array ('where' => where string, 'bind' => databind array)
     */
    function createWhere(&$objFormParam, &$objDb) {
        $arrForm = $objFormParam->getHashArray();
        //↓元ソースからの変更行ここだけ
        $where = 'alldtl.del_flg = 0 AND alldtl.product_id IN (SELECT product_id FROM plg_ps_dtb_p_products WHERE is_periodical = 1)';
        $bind = array();
        foreach ($arrForm as $key => $val) {
            if ($val == '') {
                continue;
            }

            switch ($key) {
                case 'search_name':
                    $where .= ' AND name ILIKE ?';
                    $bind[] = '%'.$val.'%';
                    break;
                case 'search_category_id':
                    list($tmp_where, $tmp_bind) = $objDb->sfGetCatWhere($val);
                    if ($tmp_where != '') {
                        $where.= ' AND alldtl.product_id IN (SELECT product_id FROM dtb_product_categories WHERE ' . $tmp_where . ')';
                        $bind = array_merge((array)$bind, (array)$tmp_bind);
                    }
                    break;
                case 'search_product_code':
                    $where .=    ' AND alldtl.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code LIKE ? AND del_flg = 0 GROUP BY product_id)';
                    $bind[] = '%'.$val.'%';
                    break;

                default:
                    break;
            }
        }
        return array(
            'where' => $where,
            'bind'  => $bind,
        );
    }
}
