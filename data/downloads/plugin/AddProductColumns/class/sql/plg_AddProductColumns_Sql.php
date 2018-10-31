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

class plg_AddProductColumns_Sql{
    
    /**
     * テーブル構造
     * @var array 
     */
    protected $arrSchema = array(
    );
    
    /**
     * 初期登録データ
     * @var array
     */
    protected $arrInitialData = array(
//        'plg_hoge_dtb_fuga' => array(
//            array(
//                'name' => 'hogefuga',
//            ),
//        )
    );
    
    function getSchema(){
        
        return (array)$this->arrSchema;
    }
    
    function getInitialData(){
        
        return (array)$this->arrInitialData;
    }
}