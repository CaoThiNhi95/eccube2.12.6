<?php
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

/**
 * セールプラグイン のメインクラス.
 *
 * @package Sale
 * @author CUORE CO.,LTD.
 */
class Sale extends SC_Plugin_Base {

    /**
     * コンストラクタ
     *
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        // テーブル、ビューの作成
        switch(DB_TYPE){
            case "pgsql" :
                $create_sql = <<< __EOS__
CREATE TABLE plg_sale_mtb_sale_category (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE TABLE plg_sale_mtb_sale_division (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE TABLE plg_sale_mtb_sale_status (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE TABLE plg_sale_dtb_sale (
    sale_id int NOT NULL UNIQUE,
    name text NOT NULL,
    category smallint NOT NULL,
    division smallint NOT NULL,
    value int NOT NULL,
    comment text,
    start_date date NOT NULL,
    end_date date NOT NULL,
    status smallint NOT NULL,
    PRIMARY KEY (sale_id)
);

CREATE TABLE plg_sale_dtb_sale_goods (
    goods_id int NOT NULL UNIQUE,
    sale_id int NOT NULL,
    category_id int,
    product_id int,
    PRIMARY KEY (goods_id)
);

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
            case "mysql" :
                $create_sql = <<< __EOS__
CREATE TABLE plg_sale_mtb_sale_category (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE plg_sale_mtb_sale_division (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE plg_sale_mtb_sale_status (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE plg_sale_dtb_sale (
    sale_id int NOT NULL,
    name text NOT NULL,
    category smallint NOT NULL,
    division smallint NOT NULL,
    value int NOT NULL,
    comment text,
    start_date date NOT NULL,
    end_date date NOT NULL,
    status smallint NOT NULL,
    PRIMARY KEY (sale_id)
) ENGINE=InnoDB;

CREATE TABLE plg_sale_dtb_sale_goods (
    goods_id int NOT NULL,
    sale_id int NOT NULL,
    category_id int,
    product_id int,
    PRIMARY KEY (goods_id)
) ENGINE=InnoDB;

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
        
        // シーケンス作成
        $objQuery->conn->manager->createSequence('plg_sale_dtb_sale_sale_id');
        $objQuery->conn->manager->createSequence('plg_sale_dtb_sale_goods_goods_id');
        
        $sql_split = preg_split('/;/', $create_sql, 0, PREG_SPLIT_NO_EMPTY);
        foreach ($sql_split as $val) {
            $objQuery->query($val);
        }
        // マスタデータ作成
        $insert_sql = <<< __EOS__
INSERT INTO plg_sale_mtb_sale_category (id, name, rank) VALUES (1, 'セール', 0);
INSERT INTO plg_sale_mtb_sale_category (id, name, rank) VALUES (2, '特価品', 1);

INSERT INTO plg_sale_mtb_sale_division (id, name, rank) VALUES (1, '%', 0);
INSERT INTO plg_sale_mtb_sale_division (id, name, rank) VALUES (2, '円', 1);

INSERT INTO plg_sale_mtb_sale_status (id, name, rank) VALUES (1, '有効', 0);
INSERT INTO plg_sale_mtb_sale_status (id, name, rank) VALUES (0, '無効', 1);
__EOS__;
        $sql_split = preg_split('/;/', $insert_sql, 0, PREG_SPLIT_NO_EMPTY);
        foreach ($sql_split as $val) {
            $objQuery->query($val);
        }
        $objQuery->commit();
        // ファイルコピー
        if(copy(PLUGIN_UPLOAD_REALDIR . "Sale/admin/products/plg_Sale_LC_Page_Admin_Products_Sale.php", DATA_REALDIR . "class/pages/admin/products/plg_Sale_LC_Page_Admin_Products_Sale.php") === false);
        if(copy(PLUGIN_UPLOAD_REALDIR . "Sale/admin/products/plg_Sale_LC_Page_Admin_Products_Sale_Ex.php", DATA_REALDIR . "class_extends/page_extends/admin/products/plg_Sale_LC_Page_Admin_Products_Sale_Ex.php") === false);
        if(copy(PLUGIN_UPLOAD_REALDIR . "Sale/admin/products/plg_Sale_sale.php", HTML_REALDIR . "admin/products/plg_Sale_sale.php") === false);
        if(copy(PLUGIN_UPLOAD_REALDIR . "Sale/templates/admin/products/plg_Sale_sale.tpl", TEMPLATE_ADMIN_REALDIR . "products/plg_Sale_sale.tpl") === false);
        if(copy(PLUGIN_UPLOAD_REALDIR . "Sale/plg_Sale_products.js", PLUGIN_HTML_REALDIR . "Sale/plg_Sale_products.js") === false);
        if(copy(PLUGIN_UPLOAD_REALDIR . "Sale/logo.png", PLUGIN_HTML_REALDIR . "Sale/logo.png") === false);
    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        
        // シーケンスの削除
        $objQuery->conn->manager->dropSequence('plg_sale_dtb_sale_sale_id');
        $objQuery->conn->manager->dropSequence('plg_sale_dtb_sale_goods_goods_id');
        
        // テーブル、ビューの削除
        switch(DB_TYPE){
            case "pgsql" :
                $drop_sql = <<< __EOS__
DROP VIEW plg_sale_vw_products_sale_allclass_detail;
DROP VIEW plg_sale_vw_products_sale_detail;
DROP VIEW plg_sale_vw_sale_off;
DROP VIEW plg_sale_vw_sale_class;
DROP VIEW plg_sale_vw_sale;
DROP TABLE plg_sale_dtb_sale;
DROP TABLE plg_sale_dtb_sale_goods;
DROP TABLE plg_sale_mtb_sale_category;
DROP TABLE plg_sale_mtb_sale_division;
DROP TABLE plg_sale_mtb_sale_status;
__EOS__;
                break;
            case "mysql" :
                $drop_sql = <<< __EOS__
DROP VIEW plg_sale_vw_products_sale_allclass_detail;
DROP VIEW plg_sale_vw_products_sale_allclass_detail_2;
DROP VIEW plg_sale_vw_products_sale_allclass_detail_1;
DROP VIEW plg_sale_vw_products_sale_detail;
DROP VIEW plg_sale_vw_products_sale_detail_1;
DROP VIEW plg_sale_vw_sale_off;
DROP VIEW plg_sale_vw_sale_class;
DROP VIEW plg_sale_vw_sale;
DROP TABLE plg_sale_dtb_sale;
DROP TABLE plg_sale_dtb_sale_goods;
DROP TABLE plg_sale_mtb_sale_category;
DROP TABLE plg_sale_mtb_sale_division;
DROP TABLE plg_sale_mtb_sale_status;
__EOS__;
                break;
        }
        $sql_split = preg_split('/;/', $drop_sql, 0, PREG_SPLIT_NO_EMPTY);
        foreach ($sql_split as $val) {
            $objQuery->query($val);
        }
        $objQuery->commit();
        // ファイル削除
        if(SC_Helper_FileManager_Ex::deleteFile(DATA_REALDIR . "class/pages/admin/products/plg_Sale_LC_Page_Admin_Products_Sale.php") === false);
        if(SC_Helper_FileManager_Ex::deleteFile(DATA_REALDIR . "class_extends/page_extends/admin/products/plg_Sale_LC_Page_Admin_Products_Sale_Ex.php") === false);
        if(SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . "admin/products/plg_Sale_sale.php") === false);
        if(SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_ADMIN_REALDIR . "products/plg_Sale_sale.tpl") === false);
        if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "Sale/plg_Sale_products.js") === false); print_r("失敗");
        if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "Sale/logo.png") === false); print_r("失敗");
    }

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
        // nop
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
        // nop
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     * @return void
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        $objHelperPlugin->addAction("LC_Page_FrontParts_Bloc_News_action_after", array($this, "bloc_news_after"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("loadClassFileChange", array(&$this, "loadClassFileChange"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("prefilterTransform", array(&$this, "prefilterTransform"), $this->arrSelfInfo['priority']);
    }

    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR ."Sale/templates/";
        switch($objPage->arrPageLayout['device_type_id']) {
            // 端末種別：PC
            case DEVICE_TYPE_PC:
                if (strpos($filename, 'products/detail.tpl') !== false) {
                    $objTransform->select('dl.sale_price')->insertAfter(file_get_contents($template_dir . 'default/products/plg_Sale_detail_sale_price_after.tpl'));
                    $objTransform->select('div.point')->replaceElement(file_get_contents($template_dir . 'default/products/plg_Sale_detail_point_replace.tpl'));
                    $objTransform->select('span.price')->insertAfter(file_get_contents($template_dir . 'default/products/plg_Sale_detail_price_after.tpl'));
                }
                if (strpos($filename, 'products/list.tpl') !== false) {
                    $objTransform->select('span.price')->insertAfter(file_get_contents($template_dir . 'default/products/plg_Sale_list.tpl'));
                }
                if (strpos($filename, 'mypage/favorite.tpl') !== false) {
                    $objTransform->select('span.price')->insertAfter(file_get_contents($template_dir . 'default/mypage/plg_Sale_favorite.tpl'));
                }
                break;
            // 端末種別：モバイル
            case DEVICE_TYPE_MOBILE:
                if (strpos($filename, 'products/detail.tpl') !== false) {
                    $objTransform->select('')->replaceElement(file_get_contents($template_dir . 'mobile/products/plg_Sale_detail.tpl'));
                }
                if (strpos($filename, 'products/list.tpl') !== false) {
                    $objTransform->select('br',3)->insertAfter(file_get_contents($template_dir . 'mobile/products/plg_Sale_list.tpl'));
                }
                break;
            // 端末種別：スマートフォン
            case DEVICE_TYPE_SMARTPHONE:
                if (strpos($filename, 'site_main.tpl') !== false) {
                    $objTransform->select('div#windowcolumn')->insertBefore(file_get_contents($template_dir . 'sphone/plg_Sale_site_main.tpl'));
                }
                if (strpos($filename, 'products/detail.tpl') !== false) {
                    $objTransform->select('p.sale_price',0)->insertAfter(file_get_contents($template_dir . 'sphone/products/plg_Sale_detail_sale_price_after.tpl'));
                    $objTransform->select('span#point_default')->replaceElement(file_get_contents($template_dir . 'sphone/products/plg_Sale_detail_point_default_replace.tpl'));
                    $objTransform->select('span.price',1)->insertAfter(file_get_contents($template_dir . 'sphone/products/plg_Sale_detail_price_after.tpl'));
                }
                if (strpos($filename, 'products/list.tpl') !== false) {
                    $objTransform->select('span.price')->insertAfter(file_get_contents($template_dir . 'sphone/products/plg_Sale_list.tpl'));
                }
                if (strpos($filename, 'mypage/favorite.tpl') !== false) {
                    $objTransform->select('span.mini.productPrice')->insertAfter(file_get_contents($template_dir . 'sphone/mypage/plg_Sale_favorite.tpl'));
                }
                break;
            // 端末種別：管理画面
            case DEVICE_TYPE_ADMIN:
            default:
                if (strpos($filename, 'products/subnavi.tpl') !== false) {
                    $objTransform->select('ul.level1 li',8)->insertAfter(file_get_contents($template_dir . 'admin/products/plg_Sale_subnavi.tpl'));
                }
                break;
        }
        if (strpos($filename, 'default/frontparts/bloc/news.tpl') !== false) {
            $objTransform->select('div.news_contents')->appendFirst(file_get_contents($template_dir . 'default/frontparts/bloc/plg_Sale_news.tpl'));
        }
        if (strpos($filename, 'mobile/frontparts/bloc/news.tpl') !== false) {
            $objTransform->select('center')->appendFirst(file_get_contents($template_dir . 'mobile/frontparts/bloc/plg_Sale_news.tpl'));
        }
        if (strpos($filename, 'sphone/frontparts/bloc/news.tpl') !== false) {
            $objTransform->select('section#news_area')->insertBefore(file_get_contents($template_dir . 'sphone/frontparts/bloc/plg_Sale_news_before.tpl'));
            $objTransform->select('section#news_area')->insertAfter(file_get_contents($template_dir . 'sphone/frontparts/bloc/plg_Sale_news_after.tpl'));
        }
        if (strpos($filename, 'default/frontparts/bloc/recommend.tpl') !== false) {
            $objTransform->select('span.price')->insertAfter(file_get_contents($template_dir . 'default/frontparts/bloc/plg_Sale_recommend.tpl'));
        }
        if (strpos($filename, 'sphone/frontparts/bloc/recommend.tpl') !== false) {
            $objTransform->select('span.price')->insertAfter(file_get_contents($template_dir . 'sphone/frontparts/bloc/plg_Sale_recommend.tpl'));
        }
        $source = $objTransform->getHTML();
    }

    function loadClassFileChange(&$classname, &$classpath) {
        if($classname == "SC_CartSession_Ex") {
            $classpath = PLUGIN_UPLOAD_REALDIR . "Sale/plg_Sale_SC_CartSession.php";
            $classname = "plg_Sale_SC_CartSession";
        }
        if($classname == "SC_Product_Ex") {
            $classpath = PLUGIN_UPLOAD_REALDIR . "Sale/plg_Sale_SC_Product.php";
            $classname = "plg_Sale_SC_Product";
        }
        if($classname == "SC_Helper_CSV_Ex") {
            $classpath = PLUGIN_UPLOAD_REALDIR . "Sale/plg_Sale_SC_Helper_CSV.php";
            $classname = "plg_Sale_SC_Helper_CSV";
        }
        if($classname == "SC_Helper_Purchase_Ex") {
            $classpath = PLUGIN_UPLOAD_REALDIR . "Sale/plg_Sale_SC_Helper_Purchase.php";
            $classname = "plg_Sale_SC_Helper_Purchase";
        }
    }

    /**
     * .
     *
     * @param LC_Page_FrontParts_Bloc_News $objPage 新着情報のページクラス
     * @return void
     */
    function bloc_news_after($objPage) {

        $objFormParam = new SC_FormParam_Ex();
        switch($objPage->getMode()){
            case "getSaleList":
                $this->lfInitSaleParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objPage->arrErr = $objFormParam->checkError(false);
                if(empty($objPage->arrErr)){
                    $json = $this->lfGetSaleForJson($objFormParam);
                    echo $json;
                    exit;
                } else {
                    echo $objPage->lfGetErrors($objPage->arrErr);
                    exit;
                }
                break;
            case "getSaleDetail":
                $this->lfInitSaleParam($objFormParam);
                $objFormParam->setParam($_GET);
                $objFormParam->convParam();
                $objPage->arrErr = $objFormParam->checkError(false);
                if(empty($objPage->arrErr)){
                     $json = $this->lfGetSaleDetailForJson($objFormParam);
                     echo $json;
                     exit;
                } else {
                    echo $objPage->lfGetErrors($objPage->arrErr);
                    exit;
                }
                break;
            default:
                // セール情報取得
                $objPage->saleCount = $this->lfGetSaleCount();
                $objPage->arrSaleInfo = $this->lfGetSaleInfo();
                break;
        }
    }

   /**
     * 開催中セール情報パラメーター初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitSaleParam(&$objFormParam) {
        $objFormParam->addParam("現在ページ", "pageno", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("表示件数", "disp_number", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("セールID", "sale_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
    }

    /**
     * 開催中セール情報をJSON形式で取得する
     * (ページと表示件数を指定)
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return String $json 新着情報のJSONを返す
     */
    function lfGetSaleForJson(&$objFormParam) {

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrData = $objFormParam->getHashArray();

        $dispNumber = $arrData['disp_number'];
        $pageNo = $arrData['pageno'];
        if(!empty($dispNumber) && !empty($pageNo)){
             $objQuery->setLimitOffset($dispNumber, (($pageNo - 1) * $dispNumber));
        }

        if(DB_TYPE == "mysql") {
            $where = "DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN start_date AND end_date";
        } else {
            $where = "TO_CHAR(NOW(), 'YYYY-MM-DD') BETWEEN TO_CHAR(start_date, 'YYYY-MM-DD') AND TO_CHAR(end_date, 'YYYY-MM-DD')";
        }
        $objQuery->setOrder("end_date");
        $arrSaleList = $objQuery->select("sale_id, name, comment, end_date", "plg_sale_dtb_sale", $where);

        // 開催中セール情報の最大ページ数をセット
        $saleCount = $this->lfGetSaleCount();
        $arrSaleList["sale_page_count"] = ceil($saleCount / 3);

        $json =  SC_Utils_Ex::jsonEncode($arrSaleList);    //JSON形式

        return $json;
    }

    /**
     * 開催中セール情報1件分をJSON形式で取得する
     * (sale_idを指定)
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return String $json 新着情報1件分のJSONを返す
     */
    function lfGetSaleDetailForJson(&$objFormParam) {

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrData = $objFormParam->getHashArray();
        $saleId = $arrData['sale_id'];
        $arrNewsList = $objQuery->select("name, comment", "plg_sale_dtb_sale", "sale_id = ?", array($saleId));

        $json =  SC_Utils_Ex::jsonEncode($arrNewsList);    //JSON形式

        return $json;
    }

    /**
     * 開催中セール情報の件数を取得する
     *
     * @return Integer $count 開催中セール情報の件数を返す
     */
    function lfGetSaleCount() {

        $count = 0;

        $objQuery = SC_Query_Ex::getSingletonInstance();
        if(DB_TYPE == "mysql") {
            $where = "DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN start_date AND end_date";
        } else {
            $where = "TO_CHAR(NOW(), 'YYYY-MM-DD') BETWEEN TO_CHAR(start_date, 'YYYY-MM-DD') AND TO_CHAR(end_date, 'YYYY-MM-DD')";
        }
        $count = $objQuery->count("plg_sale_dtb_sale", $where);

        return $count;
    }

    /**
     * 開催中セール情報取得処理.
     *
     * @return array $arrRet セール情報配列
     */
    function lfGetSaleInfo() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $column = "sale_id, name, comment, end_date";
        $table = "plg_sale_dtb_sale";
        if(DB_TYPE == "mysql") {
            $where = "DATE_FORMAT(NOW(), '%Y-%m-%d') BETWEEN start_date AND end_date";
        } else {
            $where = "TO_CHAR(NOW(), 'YYYY-MM-DD') BETWEEN TO_CHAR(start_date, 'YYYY-MM-DD') AND TO_CHAR(end_date, 'YYYY-MM-DD')";
        }
        $order = "end_date";

        $objQuery->setorder($order);
        $arrRet = $objQuery->select($column, $table, $where);

        return $arrRet;
    }
}

?>
