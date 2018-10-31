<?php

/**
 * クーポンの のSQLクラス.
 *
 */
class plg_Coupon_SQL{
    
    function plg_Coupon_SQL(){
    
    }
    
    
    /**
     * dtb_couponテーブルの作成
     */
    function create_dtb_coupon(){
     
     $sql = "
    CREATE TABLE IF NOT EXISTS dtb_coupon
(
  `coupon_id` integer NOT NULL,
  `coupon_id_name` text NOT NULL,
  `discount_price` integer,
  `discount_percent` integer,
  `discount_type` smallint NOT NULL DEFAULT 0,
  `memo` text,
  `enable_flg` smallint NOT NULL DEFAULT 0,
  `coupon_target` smallint NOT NULL DEFAULT 0,
  `create_date` timestamp NOT NULL DEFAULT now(),
  `update_date` timestamp,
  `start_date` timestamp,
  `end_date` timestamp,
  `del_flg` smallint NOT NULL DEFAULT 0,
  `use_limit` integer NOT NULL DEFAULT 1,
  `count_limit` smallint NOT NULL DEFAULT 1,
  CONSTRAINT dtb_coupon_pkey PRIMARY KEY (coupon_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";
        return $sql;
    }

    
    /**
     * dtb_couponテーブルの作成
     */
    function create_dtb_coupon_products(){
     
     $sql = "
CREATE TABLE IF NOT EXISTS dtb_coupon_products
(
  `coupon_id` integer NOT NULL,
  `product_id` integer NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8
;
";
        return $sql;
    }
    


    /**
     * dtb_coupon_usedテーブルの作成
     */
    function create_dtb_coupon_used(){
     
     $sql = "
CREATE TABLE IF NOT EXISTS `dtb_coupon_used` (
  `coupon_used_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`coupon_used_id`),
  KEY `customer_id` (`customer_id`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
        return $sql;
    }
    


    /**
     * mtb_coupon_enableテーブルの作成
     */
    function create_mtb_coupon_enable(){
     
     $sql = "
CREATE TABLE IF NOT EXISTS mtb_coupon_enable
(
  `id` smallint NOT NULL,
  `name` text,
  `rank` smallint NOT NULL DEFAULT 0,
  CONSTRAINT mtb_coupon_enable_pkey PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";
        return $sql;
    }

    /**
     * mtb_coupon_enableテーブルにデータの追加
     */
    function insert_mtb_coupon_enable($val){
     if($val==0){
     $sql = "
insert into mtb_coupon_enable values (0,'有効','1')
";
     }else{
     $sql = "
insert into mtb_coupon_enable values (1,'無効','2')
";     
     }
        return $sql;
    }
    
    
    /**
     * mtb_coupon_discount_typeテーブルの作成
     */
    function create_mtb_coupon_discount_type(){
     
     $sql = "
CREATE TABLE IF NOT EXISTS mtb_coupon_discount_type
(
  `id` smallint NOT NULL,
  `name` text,
  `rank` smallint NOT NULL DEFAULT 0,
  CONSTRAINT mtb_coupon_discount_type_pkey PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";
        return $sql;
    }

    /**
     * mtb_coupon_discount_typeテーブルにデータの追加
     */
    function insert_mtb_coupon_discount_type($val){
     if($val==0){
     $sql = "
insert into mtb_coupon_discount_type values (0,'定額','1')
";
     }else{
     $sql = "
insert into mtb_coupon_discount_type values (1,'定率','2')
";     
     }
        return $sql;
    }

    
    /**
     * mtb_coupon_targetテーブルの作成
     */
    function create_mtb_coupon_target(){
     
     $sql = "
CREATE TABLE IF NOT EXISTS mtb_coupon_target
(
  `id` smallint NOT NULL,
  `name` text,
  `rank` smallint NOT NULL DEFAULT 0,
  CONSTRAINT mtb_coupon_target_pkey PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";
        return $sql;
    }

    /**
     * mtb_coupon_targetテーブルにデータの追加
     */
    function insert_mtb_coupon_target($val){
     if($val==0){
     $sql = "
insert into mtb_coupon_target values (0,'全商品対象','1')
";
     }else{
     $sql = "
insert into mtb_coupon_target values (1,'商品を限定','2')
";     
     }
     
        return $sql;
    }
    
    
    /**
     * mtb_coupon_count_limitテーブルの作成
     */
    function create_mtb_coupon_count_limit(){
     
     $sql = "
CREATE TABLE IF NOT EXISTS mtb_coupon_count_limit
(
  `id` smallint NOT NULL,
  `name` text,
  `rank` smallint NOT NULL DEFAULT 0,
  CONSTRAINT mtb_coupon_count_pkey PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";
        return $sql;
    }

    /**
     * mtb_coupon_count_limitテーブルにデータの追加
     */
    function insert_mtb_coupon_count_limit($val){
if($val==0){
     $sql = "
insert into mtb_coupon_count_limit values (0,'無制限','1')
";
     }else{
     $sql = "
insert into mtb_coupon_count_limit values (1,'1回限り','2')
";     
     }
        return $sql;
    }
    
    /**
     *  dtb_order_tempテーブルにカラム追加
     **/
    function alter_dtb_order_temp(){
        $sql = "
ALTER TABLE dtb_order_temp
ADD COLUMN coupon_check smallint,
ADD COLUMN coupon_id_name text, 
ADD COLUMN coupon_id integer, 
ADD COLUMN coupon_discount_price numeric default 0, 
ADD COLUMN coupon_discount_percent numeric default 0
";
        return $sql;
    }
    
    /**
     *  dtb_orderテーブルにカラム追加
     **/
    function alter_dtb_order(){
        $sql = "
ALTER TABLE dtb_order 
ADD COLUMN coupon_discount_price numeric default 0,
ADD COLUMN coupon_discount_percent numeric default 0
";
        return $sql;
    }
    
    
    /**
     *   クーポンプラグインを削除する際に関連テーブルの削除を行う
     **/
     function deleteTable(){
         $sql = "DROP TABLE IF EXISTS 
dtb_coupon,
dtb_coupon_products,
dtb_coupon_used,
mtb_coupon_enable,
mtb_coupon_discount_type,
mtb_coupon_target,
mtb_coupon_count_limit,
dtb_coupon_coupon_id_seq,
dtb_coupon_used_coupon_used_id_seq";
        return $sql;
    }
    
    /**
     *  クーポンプラグインを削除する際に追加したカラムの削除を行う
     **/
     function deleteColumn_dtb_order_temp(){
         $sql = "
ALTER TABLE `dtb_order_temp`
  DROP `coupon_check`,
  DROP `coupon_id_name`,
  DROP `coupon_id`,
  DROP `coupon_discount_price`,
  DROP `coupon_discount_percent`
";
        return $sql;
     
     }

    /**
     *  クーポンプラグインを削除する際に追加したカラムの削除を行う
     **/
     function deleteColumn_dtb_order(){
         $sql = "
ALTER TABLE `dtb_order`
  DROP `coupon_discount_price`,
  DROP `coupon_discount_percent`
";
        return $sql;
     
     }


    
}