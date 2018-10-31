<?php
/*
 * Help
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

/**
 * ヘルプ機能プラグイン のアップデートクラス.
 *
 * @package Help
 * @author CUORE CO.,LTD.
 */
class plugin_update {
    /**
     * アップデート
     * updateはアップデート時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function update($arrPlugin) {
        if($arrPlugin['plugin_code'] == "Sale") {

            switch(DB_TYPE){
                case "pgsql": //Postgres
                    $drop_sql = <<< __EOS__
DROP VIEW plg_sale_vw_products_sale_allclass_detail;
DROP VIEW plg_sale_vw_products_sale_detail;
DROP VIEW plg_sale_vw_sale_off;
DROP VIEW plg_sale_vw_sale_class;
DROP VIEW plg_sale_vw_sale;
__EOS__;


                    $create_sql = <<< __EOS__

CREATE VIEW plg_sale_vw_sale AS
SELECT
    ds.*,
    dsg.category_id,
    dsg.product_id
FROM
    plg_sale_dtb_sale ds,
    plg_sale_dtb_sale_goods dsg
WHERE
    ds.start_date <= DATE(NOW()) AND
    DATE(NOW()) <= ds.end_date AND
    ds.sale_id = dsg.sale_id
;

CREATE VIEW plg_sale_vw_sale_class AS
SELECT
    dpc.product_id,
    dpc.price01,
    dpc.price02,
    vs.value AS p_value,
    vs.division AS p_division,
    vs.category AS p_category
FROM
    dtb_products_class dpc,
    plg_sale_vw_sale vs
WHERE
    dpc.product_id = vs.product_id AND
    dpc.del_flg = 0
UNION ALL
SELECT
    dpc.product_id,
    dpc.price01,
    dpc.price02,
    vs.value AS p_value,
    vs.division AS p_division,
    vs.category AS p_category
FROM
    dtb_products_class dpc,
    plg_sale_vw_sale vs,
    dtb_product_categories dpt
WHERE
    dpc.product_id = dpt.product_id AND
    vs.category_id = dpt.category_id AND
    dpc.del_flg = 0
;

CREATE VIEW plg_sale_vw_sale_off AS
SELECT
    product_id as vw_sale_product_id,
    p_division,
    CASE WHEN p_division = 1 THEN trunc(MIN(price02) - (MIN(price02) / 100) * MAX(p_value))
         WHEN p_division = 2 THEN MIN(price02) - MAX(p_value)
         ELSE NULL
    END AS price03_min,
    CASE WHEN p_division = 1 THEN trunc(MAX(price02) - (MAX(price02) / 100) * MAX(p_value))
         WHEN p_division = 2 THEN MAX(price02) - MAX(p_value)
         ELSE NULL
    END AS price03_max
FROM
    plg_sale_vw_sale_class
GROUP BY
    vw_sale_product_id,
    p_division
;

CREATE VIEW plg_sale_vw_products_sale_detail AS
SELECT
    dtb_products.product_id,
    dtb_products.name,
    dtb_products.maker_id,
    dtb_products.status,
    dtb_products.comment1,
    dtb_products.comment2,
    dtb_products.comment3,
    dtb_products.comment4,
    dtb_products.comment5,
    dtb_products.comment6,
    dtb_products.note,
    dtb_products.main_list_comment,
    dtb_products.main_list_image,
    dtb_products.main_comment,
    dtb_products.main_image,
    dtb_products.main_large_image,
    dtb_products.sub_title1,
    dtb_products.sub_comment1,
    dtb_products.sub_image1,
    dtb_products.sub_large_image1,
    dtb_products.sub_title2,
    dtb_products.sub_comment2,
    dtb_products.sub_image2,
    dtb_products.sub_large_image2,
    dtb_products.sub_title3,
    dtb_products.sub_comment3,
    dtb_products.sub_image3,
    dtb_products.sub_large_image3,
    dtb_products.sub_title4,
    dtb_products.sub_comment4,
    dtb_products.sub_image4,
    dtb_products.sub_large_image4,
    dtb_products.sub_title5,
    dtb_products.sub_comment5,
    dtb_products.sub_image5,
    dtb_products.sub_large_image5,
    dtb_products.sub_title6,
    dtb_products.sub_comment6,
    dtb_products.sub_image6,
    dtb_products.sub_large_image6,
    dtb_products.del_flg,
    dtb_products.creator_id,
    dtb_products.create_date,
    dtb_products.update_date,
    dtb_products.deliv_date_id,
    T4.product_code_min,
    T4.product_code_max,
    T4.price01_min,
    T4.price01_max,
    T4.price02_min,
    T4.price02_max,
    T4.price03_min,
    T4.price03_max,
    T4.stock_min,
    T4.stock_max,
    T4.stock_unlimited_min,
    T4.stock_unlimited_max,
    T4.class_count,
    dtb_maker.name AS maker_name
FROM
    dtb_products
    LEFT JOIN
    (
        SELECT
            product_id,
            MIN(product_code) AS product_code_min,
            MAX(product_code) AS product_code_max,
            MIN(price01) AS price01_min,
            MAX(price01) AS price01_max,
            MIN(price02) AS price02_min,
            MAX(price02) AS price02_max,
            MIN(stock) AS stock_min,
            MAX(stock) AS stock_max,
            MIN(stock_unlimited) AS stock_unlimited_min,
            MAX(stock_unlimited) AS stock_unlimited_max,
            COALESCE(MIN(vsmax.price03_min), MIN(dtb_products_class.price02)) AS price03_min,
            COALESCE(MIN(vsmax.price03_max), MAX(dtb_products_class.price02)) AS price03_max,
            COUNT(*) as class_count
        FROM
            dtb_products_class
            LEFT JOIN
            plg_sale_vw_sale_off AS vsmax
            ON
            vsmax.vw_sale_product_id = dtb_products_class.product_id
        WHERE
            dtb_products_class.del_flg = 0
        GROUP BY
            dtb_products_class.product_id
    ) AS T4
    ON
    dtb_products.product_id = T4.product_id
    LEFT JOIN
    dtb_maker
    ON
    dtb_products.maker_id = dtb_maker.maker_id
;

CREATE VIEW plg_sale_vw_products_sale_allclass_detail AS
SELECT
    T2.product_class_id,
    T2.product_id,
    T2.classcategory_id1,
    T2.classcategory_id2,
    T2.product_type_id,
    T2.product_code,
    T2.stock,
    T2.stock_unlimited,
    T2.sale_limit,
    T2.price01,
    T2.price02,
    T2.deliv_fee,
    T2.point_rate,
    T2.creator_id,
    T2.create_date,
    T2.update_date,
    T2.down_filename,
    T2.down_realfilename,
    T2.del_flg,
    CASE WHEN T3.price03 IS NULL THEN T2.price02
         ELSE T3.price03
    END AS price03
FROM
    dtb_products_class T2
    LEFT JOIN
    (
        SELECT
            T1.product_class_id,
            MIN(price03) AS price03
        FROM
            (
                SELECT
                    dpc.product_class_id,
                    vs.value AS p_value,
                    vs.division AS p_division,
                    vs.category AS p_category,
                    CASE WHEN division = 1 THEN trunc(MAX(price02) - (MAX(price02) / 100) * MAX(value))
                         WHEN division = 2 THEN MAX(price02) - MAX(value)
                         ELSE NULL
                    END AS price03
                FROM
                    dtb_products_class dpc,
                    plg_sale_vw_sale vs
                WHERE
                    dpc.product_id = vs.product_id
                GROUP BY
                    dpc.product_class_id,
                    vs.value,
                    vs.division,
                    vs.category
                UNION ALL
                SELECT
                    dpc.product_class_id,
                    vs.value AS p_value,
                    vs.division AS p_division,
                    vs.category AS p_category,
                    CASE WHEN division = 1 THEN trunc(MAX(price02) - (MAX(price02) / 100) * MAX(value))
                         WHEN division = 2 THEN MAX(price02) - MAX(value)
                         ELSE NULL
                    END AS price03
                FROM
                    dtb_products_class dpc,
                    plg_sale_vw_sale vs,
                    dtb_product_categories dpt
                WHERE
                    dpc.product_id = dpt.product_id AND
                    vs.category_id = dpt.category_id
                GROUP BY
                    dpc.product_class_id,
                    vs.value,
                    vs.division,
                    vs.category
            ) T1
        GROUP BY
            T1.product_class_id
    ) T3
    ON
    T2.product_class_id = T3.product_class_id
WHERE
    T2.del_flg = 0
;
__EOS__;
                    break;
                case "mysql": //MySQL
                    $drop_sql = <<< __EOS__
DROP VIEW plg_sale_vw_products_sale_allclass_detail;
DROP VIEW plg_sale_vw_products_sale_allclass_detail_2;
DROP VIEW plg_sale_vw_products_sale_allclass_detail_1;
DROP VIEW plg_sale_vw_products_sale_detail;
DROP VIEW plg_sale_vw_products_sale_detail_1;
DROP VIEW plg_sale_vw_sale_off;
DROP VIEW plg_sale_vw_sale_class;
DROP VIEW plg_sale_vw_sale;
__EOS__;

                    $create_sql = <<< __EOS__

CREATE VIEW plg_sale_vw_sale AS
SELECT
    ds.*,
    dsg.category_id,
    dsg.product_id
FROM
    plg_sale_dtb_sale ds,
    plg_sale_dtb_sale_goods dsg
WHERE
    ds.start_date <= DATE(NOW()) AND
    DATE(NOW()) <= ds.end_date AND
    ds.sale_id = dsg.sale_id
;

CREATE VIEW plg_sale_vw_sale_class AS
SELECT
    dpc.product_id,
    dpc.price01,
    dpc.price02,
    vs.value AS p_value,
    vs.division AS p_division,
    vs.category AS p_category
FROM
    dtb_products_class dpc,
    plg_sale_vw_sale vs
WHERE
    dpc.product_id = vs.product_id AND
    dpc.del_flg = 0
UNION ALL
SELECT
    dpc.product_id,
    dpc.price01,
    dpc.price02,
    vs.value AS p_value,
    vs.division AS p_division,
    vs.category AS p_category
FROM
    dtb_products_class dpc,
    plg_sale_vw_sale vs,
    dtb_product_categories dpt
WHERE
    dpc.product_id = dpt.product_id AND
    vs.category_id = dpt.category_id AND
    dpc.del_flg = 0
;

CREATE VIEW plg_sale_vw_sale_off AS
SELECT
    product_id as vw_sale_product_id,
    p_division,
    CASE WHEN p_division = 1 THEN TRUNCATE(MIN(price02) - (MIN(price02) / 100) * MAX(p_value), 0)
         WHEN p_division = 2 THEN MIN(price02) - MAX(p_value)
         ELSE NULL
    END AS price03_min,
    CASE WHEN p_division = 1 THEN TRUNCATE(MAX(price02) - (MAX(price02) / 100) * MAX(p_value), 0)
         WHEN p_division = 2 THEN MAX(price02) - MAX(p_value)
         ELSE NULL
    END AS price03_max
FROM
    plg_sale_vw_sale_class
GROUP BY
    vw_sale_product_id,
    p_division
;

CREATE VIEW plg_sale_vw_products_sale_detail_1 AS
SELECT
    product_id,
    MIN(product_code) AS product_code_min,
    MAX(product_code) AS product_code_max,
    MIN(price01) AS price01_min,
    MAX(price01) AS price01_max,
    MIN(price02) AS price02_min,
    MAX(price02) AS price02_max,
    MIN(stock) AS stock_min,
    MAX(stock) AS stock_max,
    MIN(stock_unlimited) AS stock_unlimited_min,
    MAX(stock_unlimited) AS stock_unlimited_max,
    COALESCE(MIN(vsmax.price03_min), MIN(dtb_products_class.price02)) AS price03_min,
    COALESCE(MIN(vsmax.price03_max), MAX(dtb_products_class.price02)) AS price03_max,
    COUNT(*) as class_count
FROM
    dtb_products_class
    LEFT JOIN
    plg_sale_vw_sale_off AS vsmax
    ON
    vsmax.vw_sale_product_id = dtb_products_class.product_id
WHERE
    dtb_products_class.del_flg = 0
GROUP BY
    dtb_products_class.product_id
;

CREATE VIEW plg_sale_vw_products_sale_detail AS
SELECT
    dtb_products.product_id,
    dtb_products.name,
    dtb_products.maker_id,
    dtb_products.status,
    dtb_products.comment1,
    dtb_products.comment2,
    dtb_products.comment3,
    dtb_products.comment4,
    dtb_products.comment5,
    dtb_products.comment6,
    dtb_products.note,
    dtb_products.main_list_comment,
    dtb_products.main_list_image,
    dtb_products.main_comment,
    dtb_products.main_image,
    dtb_products.main_large_image,
    dtb_products.sub_title1,
    dtb_products.sub_comment1,
    dtb_products.sub_image1,
    dtb_products.sub_large_image1,
    dtb_products.sub_title2,
    dtb_products.sub_comment2,
    dtb_products.sub_image2,
    dtb_products.sub_large_image2,
    dtb_products.sub_title3,
    dtb_products.sub_comment3,
    dtb_products.sub_image3,
    dtb_products.sub_large_image3,
    dtb_products.sub_title4,
    dtb_products.sub_comment4,
    dtb_products.sub_image4,
    dtb_products.sub_large_image4,
    dtb_products.sub_title5,
    dtb_products.sub_comment5,
    dtb_products.sub_image5,
    dtb_products.sub_large_image5,
    dtb_products.sub_title6,
    dtb_products.sub_comment6,
    dtb_products.sub_image6,
    dtb_products.sub_large_image6,
    dtb_products.del_flg,
    dtb_products.creator_id,
    dtb_products.create_date,
    dtb_products.update_date,
    dtb_products.deliv_date_id,
    T4.product_code_min,
    T4.product_code_max,
    T4.price01_min,
    T4.price01_max,
    T4.price02_min,
    T4.price02_max,
    T4.price03_min,
    T4.price03_max,
    T4.stock_min,
    T4.stock_max,
    T4.stock_unlimited_min,
    T4.stock_unlimited_max,
    T4.class_count,
    dtb_maker.name AS maker_name
FROM
    dtb_products
    LEFT JOIN
    plg_sale_vw_products_sale_detail_1 AS T4
    ON
    dtb_products.product_id = T4.product_id
    LEFT JOIN
    dtb_maker
    ON
    dtb_products.maker_id = dtb_maker.maker_id
;

CREATE VIEW plg_sale_vw_products_sale_allclass_detail_1 AS
SELECT
    dpc.product_class_id,
    vs.value AS p_value,
    vs.division AS p_division,
    vs.category AS p_category,
    CASE WHEN division = 1 THEN TRUNCATE(MAX(price02) - (MAX(price02) / 100) * MAX(value), 0)
         WHEN division = 2 THEN MAX(price02) - MAX(value)
         ELSE NULL
    END AS price03
FROM
    dtb_products_class dpc,
    plg_sale_vw_sale vs
WHERE
    dpc.product_id = vs.product_id
GROUP BY
    dpc.product_class_id,
    vs.value,
    vs.division,
    vs.category
UNION ALL
SELECT
    dpc.product_class_id,
    vs.value AS p_value,
    vs.division AS p_division,
    vs.category AS p_category,
    CASE WHEN division = 1 THEN TRUNCATE(MAX(price02) - (MAX(price02) / 100) * MAX(value), 0)
         WHEN division = 2 THEN MAX(price02) - MAX(value)
         ELSE NULL
    END AS price03
FROM
    dtb_products_class dpc,
    plg_sale_vw_sale vs,
    dtb_product_categories dpt
WHERE
    dpc.product_id = dpt.product_id AND
    vs.category_id = dpt.category_id
GROUP BY
    dpc.product_class_id,
    vs.value,
    vs.division,
    vs.category
;

CREATE VIEW plg_sale_vw_products_sale_allclass_detail_2 AS
SELECT
    T1.product_class_id,
    MIN(price03) AS price03
FROM
    plg_sale_vw_products_sale_allclass_detail_1 T1
GROUP BY
    T1.product_class_id
;

CREATE VIEW plg_sale_vw_products_sale_allclass_detail AS
SELECT
    T2.product_class_id,
    T2.product_id,
    T2.classcategory_id1,
    T2.classcategory_id2,
    T2.product_type_id,
    T2.product_code,
    T2.stock,
    T2.stock_unlimited,
    T2.sale_limit,
    T2.price01,
    T2.price02,
    T2.deliv_fee,
    T2.point_rate,
    T2.creator_id,
    T2.create_date,
    T2.update_date,
    T2.down_filename,
    T2.down_realfilename,
    T2.del_flg,
    CASE WHEN T3.price03 IS NULL THEN T2.price02
         ELSE T3.price03
    END AS price03
FROM
    dtb_products_class T2
    LEFT JOIN
    plg_sale_vw_products_sale_allclass_detail_2 T3
    ON
    T2.product_class_id = T3.product_class_id
WHERE
    T2.del_flg = 0
;
__EOS__;
                    break;
            }

            // SQL を実行
            $arrSql = array_merge(preg_split('/;/', $drop_sql, 0, PREG_SPLIT_NO_EMPTY), preg_split('/;/', $create_sql, 0, PREG_SPLIT_NO_EMPTY));
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            foreach ($arrSql as $val) {
                $objQuery->query($val);
            }
        }
    }
}
?>
