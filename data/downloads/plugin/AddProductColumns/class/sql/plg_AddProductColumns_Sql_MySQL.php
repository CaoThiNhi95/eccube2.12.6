<?php
/*
 * AddProductColumns
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

require_once PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/class_ex/sql_ex/plg_AddProductColumns_Sql_Ex.php';

/**
 * AddProductColumnsプラグイン のSQLクラス.
 *
 * @package AddProductColumns
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_AddProductColumns_Sql_MySQL extends plg_AddProductColumns_Sql_Ex{
    
    /**
     * テーブル情報。
     * テーブル名 => array(フィールド情報1, フィールド情報2 …)
     */
    protected $arrSchema = array(
        'plg_apc_dtb_columns' => array(
            'column_id' => 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT', 
            'name' => 'TEXT', 
            'type' => 'TEXT', 
            'required' => 'SMALLINT DEFAULT 0 NOT NULL', 
            'max_length' => 'INTEGER'
        ), 
        'plg_apc_dtb_values' => array(
            'value_id' => 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT', 
            'product_id' => 'INTEGER NOT NULL', 
            'column_id' => 'INTEGER NOT NULL', 
            'value' => 'TEXT'
        )
    );
}