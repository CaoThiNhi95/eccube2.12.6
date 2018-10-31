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

/**
 * 定期プラグイン のSQLクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_MySQL{
    
    /**
     * テーブル情報。
     * テーブル名 => array(フィールド情報1, フィールド情報2 …)
     */
    public static $arrSchema = array(
        'plg_ps_dtb_relations' => array(
            'periodical_order_id INTEGER NOT NULL',
            'order_id INTEGER NOT NULL',
            'periodical_times INTEGER NOT NULL DEFAULT 1'
        ),
        'plg_ps_dtb_p_orders' => array(
            'periodical_order_id SERIAL NOT NULL PRIMARY KEY',
            'total_periodical_times INTEGER NOT NULL DEFAULT 1',
            'customer_id INTEGER',
            'message TEXT',
            'order_name01 TEXT',
            'order_name02 TEXT',
            'order_kana01 TEXT',
            'order_kana02 TEXT',
            'order_email TEXT',
            'order_tel01 TEXT',
            'order_tel02 TEXT',
            'order_tel03 TEXT',
            'order_fax01 TEXT',
            'order_fax02 TEXT',
            'order_fax03 TEXT',
            'order_zip01 TEXT',
            'order_zip02 TEXT',
            'order_pref SMALLINT',
            'order_addr01 TEXT',
            'order_addr02 TEXT',
            'order_sex SMALLINT',
            'order_birth DATETIME',
            'order_job INTEGER',
            'subtotal NUMERIC',
            'discount NUMERIC NOT NULL DEFAULT 0',
            'deliv_id INTEGER',
            'deliv_fee NUMERIC',
            'charge NUMERIC',
            'use_point NUMERIC NOT NULL DEFAULT 0',
            'add_point NUMERIC NOT NULL DEFAULT 0',
            'birth_point NUMERIC NOT NULL DEFAULT 0',
            'tax NUMERIC',
            'total NUMERIC',
            'payment_total NUMERIC',
            'payment_id INTEGER',
            'payment_method TEXT',
            'note TEXT',
            'status SMALLINT',
            'period_type TEXT',
            'period_delivery_time TEXT',
            'period_week TEXT',
            'period_day TEXT',
            'period_date TEXT',
            'next_period DATETIME',
            'create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'update_date TIMESTAMP NOT NULL',
            'del_flg SMALLINT NOT NULL DEFAULT 0',
            'periodical_status SMALLINT NOT NULL DEFAULT 0'
        ),
        'plg_ps_dtb_p_shipment_items' => array(
            'periodical_shipment_item_id SERIAL NOT NULL PRIMARY KEY',
            'shipping_id INTEGER NOT NULL',
            'periodical_order_id INTEGER NOT NULL',
            'product_class_id INTEGER NOT NULL',
            'product_name TEXT NOT NULL',
            'product_code TEXT',
            'classcategory_name1 TEXT',
            'classcategory_name2 TEXT',
            'price NUMERIC',
            'quantity NUMERIC'
        ),
        'plg_ps_dtb_p_order_details' => array(
            'periodical_order_detail_id SERIAL NOT NULL PRIMARY KEY',
            'periodical_order_id INTEGER NOT NULL',
            'product_id INTEGER NOT NULL',
            'product_class_id INTEGER NOT NULL',
            'product_name TEXT NOT NULL',
            'product_code TEXT',
            'classcategory_name1 TEXT',
            'classcategory_name2 TEXT',
            'price NUMERIC',
            'quantity NUMERIC',
            'point_rate NUMERIC NOT NULL DEFAULT 0'
        ),
        'plg_ps_dtb_p_shippings' => array(
            'periodical_shipping_id SERIAL NOT NULL PRIMARY KEY',
            'periodical_order_id INTEGER NOT NULL',
            'shipping_id INTEGER NOT NULL',
            'shipping_name01 TEXT',
            'shipping_name02 TEXT',
            'shipping_kana01 TEXT',
            'shipping_kana02 TEXT',
            'shipping_tel01 TEXT',
            'shipping_tel02 TEXT',
            'shipping_tel03 TEXT',
            'shipping_fax01 TEXT',
            'shipping_fax02 TEXT',
            'shipping_fax03 TEXT',
            'shipping_pref SMALLINT',
            'shipping_zip01 TEXT',
            'shipping_zip02 TEXT',
            'shipping_addr01 TEXT',
            'shipping_addr02 TEXT',
            'time_id INTEGER',
            'shipping_date TEXT',
            'del_flg SMALLINT NOT NULL DEFAULT 0'
        ),
        'plg_ps_dtb_p_products' => array(
            'product_id INTEGER NOT NULL UNIQUE',
            'is_periodical SMALLINT NOT NULL DEFAULT 1',
            'period_price_difference INTEGER NOT NULL DEFAULT 0'
        ),
        'plg_ps_dtb_temp_p_orders' => array(
            'temp_periodical_order_id VARCHAR(255) NOT NULL PRIMARY KEY',
            'deliv_id INTEGER',
            'period_payment_id INTEGER',
            'period_type TEXT',
            'period_delivery_time TEXT',
            'period_week TEXT',
            'period_day TEXT',
            'period_date TEXT',
            'create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'session TEXT',
            'order_id INTEGER'
        ),
        'plg_ps_mtb_period_weeks' => array(
            'id SMALLINT NOT NULL PRIMARY KEY',
            'name TEXT',
            'rank SMALLINT NOT NULL DEFAULT 0'
        ),
        'plg_ps_mtb_period_dates' => array(
            'id SMALLINT NOT NULL PRIMARY KEY',
            'name TEXT',
            'rank SMALLINT NOT NULL DEFAULT 0'
        ),
        'plg_ps_mtb_p_order_statuses' => array(
            'id SMALLINT NOT NULL PRIMARY KEY',
            'name TEXT',
            'rank SMALLINT NOT NULL DEFAULT 0'
        )
    );
}