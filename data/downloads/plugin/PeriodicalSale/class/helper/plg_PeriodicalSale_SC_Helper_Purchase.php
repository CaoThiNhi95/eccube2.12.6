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

require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/helper/plg_PeriodicalSale_SC_Helper_Datetime.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/class/plg_PeriodicalSale_SC_PeriodicalOrder.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/PeriodicalSale.php';

/**
 * 定期受注操作のヘルパークラス
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_SC_Helper_Purchase {
    
    /**
     * 定期配送情報を保存する。
     * 
     * @param integer $periodical_order_id 定期受注ID
     * @param array $arrPeriodicalShippings 定期配送情報 SC_FormParam_Ex::getDbArray()の配列形式
     * @return array 挿入・更新したIDの配列
     */
    static function savePeriodicalShippings($periodical_order_id, $arrPeriodicalShippings){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_shippings';
        //テーブルに存在するフィールドのみ抽出
        $arrPeriodicalShippings = $objQuery->extractOnlyColsOf($table, $arrPeriodicalShippings);
        $arrReturn = array();
        $arrKeys = array_keys($arrPeriodicalShippings);
        
        if(@is_array($arrPeriodicalShippings['periodical_shipping_id'])){
            foreach($arrPeriodicalShippings['periodical_shipping_id'] as $index => $temp){
                
                //保存用のデータを整形
                $arrValues = array();
                foreach($arrKeys as $key){
                    if(isset($arrPeriodicalShippings[$key][$index])){
                        $arrValues[$key] = $arrPeriodicalShippings[$key][$index];
                    }
                }
                $arrValues['periodical_order_id'] = $periodical_order_id;
                $periodical_shipping_id = $arrValues['periodical_shipping_id'];
                
                $table = 'plg_ps_dtb_p_shippings';
                $where = 'periodical_shipping_id = ?';
                $arrWhereValues = array($periodical_shipping_id);
                $exists = $objQuery->exists($table,$where,$arrWhereValues);
                
                //更新の場合
                if($exists){
                    $objQuery->update($table,$arrValues,$where,$arrWhereValues);
                }
                //新規追加の場合
                else{
                    $periodical_shipping_id = $objQuery->nextVal('plg_ps_dtb_p_shippings_periodical_shipping_id');
                    $arrValues['periodical_shipping_id'] = $periodical_shipping_id;
                    $objQuery->insert($table,$arrValues);
                }
                $arrReturn[] = $periodical_shipping_id;
            }
        }
        
        return $arrReturn;
    }
    
    /**
     * 定期配送商品情報を保存する。
     * 
     * @param integer $periodical_order_id 定期受注ID
     * @param array $arrPeriodicalShipmentItems 定期配送商品情報 SC_FormParam_Ex::getDbArray()の配列形式
     * @return array 挿入・更新したIDの配列
     */
    static function savePeriodicalShipmentItems($periodical_order_id, $arrPeriodicalShipmentItems){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_shipment_items';
        //テーブルに存在するフィールドのみ抽出
        $arrPeriodicalShipmentItems = $objQuery->extractOnlyColsOf($table, $arrPeriodicalShipmentItems);
        $arrReturn = array();
        $arrKeys = array_keys($arrPeriodicalShipmentItems);
        
        if(@is_array($arrPeriodicalShipmentItems['periodical_shipment_item_id'])){
            foreach($arrPeriodicalShipmentItems['periodical_shipment_item_id'] as $index => $temp){
                
                //保存用のデータを整形
                $arrValues = array();
                foreach($arrKeys as $key){
                    if($key == 'shipping_id'){
                        //XXX 複数配送先になったら要修正
                        $arrValues[$key] = $arrPeriodicalShipmentItems[$key][0];
                    }
                    elseif(isset($arrPeriodicalShipmentItems[$key][$index])){
                        $arrValues[$key] = $arrPeriodicalShipmentItems[$key][$index];
                    }
                }
                $arrValues['periodical_order_id'] = $periodical_order_id;
                $periodical_shipment_item_id = $arrValues['periodical_shipment_item_id'];
                
                $table = 'plg_ps_dtb_p_shipment_items';
                //複合主キー
                $where = 'periodical_shipment_item_id = ?';
                $arrWhereValues = array($periodical_shipment_item_id);
                $exists = $objQuery->exists($table,$where,$arrWhereValues);
                
                //更新の場合
                if($exists){
                    $objQuery->update($table,$arrValues,$where,$arrWhereValues);
                }
                //新規追加の場合
                else{
                    $periodical_shipment_item_id = $objQuery->nextVal('plg_ps_dtb_p_shipment_items_periodical_shipment_item_id');
                    $arrValues['periodical_shipment_item_id'] = $periodical_shipment_item_id;
                    $objQuery->insert($table,$arrValues);
                }
                $arrReturn[] = $periodical_shipment_item_id;
            }
            
            if(!empty($arrReturn)){
                //今回INSERTまたはUPDATEされなかった行を削除
                $table = 'plg_ps_dtb_p_shipment_items';
                $where = sprintf('periodical_shipment_item_id NOT IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrReturn)));
                $arrWhereValues = $arrReturn;
                $where .= ' AND periodical_order_id = ?';
                $arrWhereValues[] = $periodical_order_id;
                $objQuery->delete($table, $where, $arrWhereValues);
            }
        }
        
        return $arrReturn;
    }
    
    /**
     * 定期受注詳細情報を保存する。
     * 
     * @param integer $periodical_order_id 定期受注ID
     * @param array $arrPeriodicalOrderDetails 定期受注詳細情報 SC_FormParam_Ex::getDbArray()の配列形式
     * @return array 挿入・更新したIDの配列
     */
    static function savePeriodicalOrderDetails($periodical_order_id, $arrPeriodicalOrderDetails){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_order_details';
        //テーブルに存在するフィールドのみ抽出
        $arrPeriodicalOrderDetails = $objQuery->extractOnlyColsOf($table, $arrPeriodicalOrderDetails);
        $arrReturn = array();
        $arrKeys = array_keys($arrPeriodicalOrderDetails);

        if(@is_array($arrPeriodicalOrderDetails['periodical_order_detail_id'])){
            foreach($arrPeriodicalOrderDetails['periodical_order_detail_id'] as $index => $temp){
                
                //保存用のデータを整形
                $arrValues = array();
                foreach($arrKeys as $key){
                    if(isset($arrPeriodicalOrderDetails[$key][$index])){
                        $arrValues[$key] = $arrPeriodicalOrderDetails[$key][$index];
                    }
                }
                $arrValues['periodical_order_id'] = $periodical_order_id;
                $periodical_order_detail_id = $arrValues['periodical_order_detail_id'];
                
                $table = 'plg_ps_dtb_p_order_details';
                $where = 'periodical_order_detail_id = ?';
                $arrWhereValues = array($periodical_order_detail_id);
                $exists = $objQuery->exists($table, $where, $arrWhereValues);
                //更新の場合
                if($exists){
                    $objQuery->update($table,$arrValues,$where,$arrWhereValues);
                }
                //新規追加の場合
                else{
                    $periodical_order_detail_id = $objQuery->nextVal('plg_ps_dtb_p_order_details_periodical_order_detail_id');
                    $arrValues['periodical_order_detail_id'] = $periodical_order_detail_id;
                    $objQuery->insert($table,$arrValues);
                }
                $arrReturn[] = $periodical_order_detail_id;
            }
            
            if(!empty($arrReturn)){
                //今回INSERTまたはUPDATEされなかった行を削除
                $table = 'plg_ps_dtb_p_order_details';
                $where = sprintf('periodical_order_detail_id NOT IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrReturn)));
                $arrWhereValues = $arrReturn;
                $where .= ' AND periodical_order_id = ?';
                $arrWhereValues[] = $periodical_order_id;
                $objQuery->delete($table, $where, $arrWhereValues);
            }
        }
        
        return $arrReturn;
    }
    
    /**
     * 定期受注情報を保存する。
     * 
     * @param integer $periodical_order_id 定期受注ID
     * @param array $arrPeriodicalOrder 定期受注情報 SC_FormParam_Ex::getDbArray()の配列形式
     * @return integer 定期受注ID
     */
    static function savePeriodicalOrder($periodical_order_id, $arrPeriodicalOrder){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_orders';
        $where = 'periodical_order_id = ?';
        $arrWhereValues = array($periodical_order_id);
        //テーブルに存在するフィールドのみ抽出
        $arrPeriodicalOrder = $objQuery->extractOnlyColsOf($table,$arrPeriodicalOrder);
        
        $exists = $objQuery->exists($table,$where,$arrWhereValues);

        //更新の場合
        if($exists){
            $arrPeriodicalOrder['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update($table,$arrPeriodicalOrder,$where,$arrWhereValues);
        }
        //新規追加の場合
        else{
            if(empty($periodical_order_id)){
                $periodical_order_id = $objQuery->nextVal('plg_ps_dtb_p_orders_periodical_order_id');
            }
            
            $arrPeriodicalOrder['periodical_order_id'] = $periodical_order_id;
            $arrPeriodicalOrder['create_date'] = 'CURRENT_TIMESTAMP';
            $arrPeriodicalOrder['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table,$arrPeriodicalOrder);
        }
        
        return $periodical_order_id;
    }
    
    /**
     * 一時定期受注情報を保存する。
     * 
     * @param string $unique_id ユニークID
     * @param array $arrTempPeriodicalOrder 一時定期受注情報の配列
     */
    static function saveTempPeriodicalOrder($unique_id, $arrTempPeriodicalOrder){
        
        if (SC_Utils_Ex::isBlank($unique_id)) {
            return;
        }
        
        $table = 'plg_ps_dtb_temp_p_orders';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        //テーブルに存在するフィールドのみ抽出
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrTempPeriodicalOrder);
        $arrValues['session'] = serialize($_SESSION);
        
        $exists = self::getTempPeriodicalOrder($unique_id);
        //新規追加の場合
        if(empty($exists)){
            
            $arrValues['temp_periodical_order_id'] = $unique_id;
            $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->insert($table,$arrValues);
        }
        //更新の場合
        else{
            $where = 'temp_periodical_order_id = ?';
            $arrWhereValues = array($unique_id);
            $objQuery->update($table,$arrValues,$where,$arrWhereValues);
        }
    }

    /**
     * 一時定期受注情報にIDを適用する。
     * 
     * @param string $unique_id ユニークID
     */
    static function applyOrderIdToTempPeriodicalOrder($unique_id){
        
        $objPurchase = new SC_Helper_Purchase_Ex();
        
        //一時受注情報を取得
        $arrOrderTemp = $objPurchase->getOrderTemp($unique_id);
        
        $arrTempPeriodicalOrder = self::getTempPeriodicalOrder($unique_id);
        $arrTempPeriodicalOrder['order_id'] = $arrOrderTemp['order_id'];
        
        self::saveTempPeriodicalOrder($unique_id, $arrTempPeriodicalOrder);
    }
    
    /**
     * 一時定期受注情報を取得する。
     * 
     * @param integer $unique_id ユニークID
     * @return array 一時定期情報
     */
    static function getTempPeriodicalOrder($unique_id){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrTempPeriodicalOrder =  $objQuery->getRow('*','plg_ps_dtb_temp_p_orders','temp_periodical_order_id = ?',array($unique_id));
        return !empty($arrTempPeriodicalOrder) ? $arrTempPeriodicalOrder : array();
    }
    
    /**
     * 受注IDで一時定期受注情報を取得する。
     * 
     * @param integer $order_id 受注ID
     * @return array 一次定期情報 
     */
    static function getTempPeriodicalOrderByOrderId($order_id){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow('*','plg_ps_dtb_temp_p_orders','order_id = ?',array($order_id));
    }
    
    /**
     * 一時定期受注情報を確定させる。
     * 
     * @param integer $order_id 受注ID
     * @return array 登録した定期受注IDの配列
     */
    static function completePeriodicalOrder($order_id){
        
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        //配送情報を取得
        $arrShippings = $objPurchase->getShippings($order_id);
        //受注情報を取得
        $arrOrder = $objPurchase->getOrder($order_id);
        //一次定期情報を取得
        $arrTempPeriodicalOrder = self::getTempPeriodicalOrderByOrderId($order_id);
        //受注詳細を取得
        $arrOrderDetails = self::getOrderDetailsOfPeriodicalProductByOrderId($order_id);
        //配送時間帯を取得
        $arrDeliveryTimes = $objPurchase->getDelivTime($arrTempPeriodicalOrder['deliv_id']);
        
         //手数料や総額の計算 
        $add_point = 0;
        $use_point = 0;
        $tax = 0;
        $discount = 0;
        $subtotal = 0;
        
        if(!SC_Utils_Ex::isBlank($arrTempPeriodicalOrder['period_payment_id'])){
            $payment_id = $arrTempPeriodicalOrder['period_payment_id'];
        }
        else{
            $payment_id = $arrOrder['payment_id'];
        }
        
        //配送方法・手数料の取得
        $table = 'dtb_payment';
        $where = 'payment_id = ?';
        $arrWhereValues = array($payment_id);
        $objQuery->setOrder('');
        $arrPayment = $objQuery->getRow('charge, payment_method', $table, $where, $arrWhereValues);
        $charge = $arrPayment['charge'];
        $payment_method = $arrPayment['payment_method'];

        foreach($arrOrderDetails as $arrOrderDetail){

            $price = $arrOrderDetail['price'] + $arrOrderDetail['period_price_difference'];
            $quantity = $arrOrderDetail['quantity'];
            $point_rate = $arrOrderDetail['point_rate'];
            $subtotal += SC_Helper_DB_Ex::sfCalcIncTax($price) * $quantity;
            $tax += SC_Helper_DB_Ex::sfTax($price) * $quantity;
            $add_point += SC_Utils_Ex::sfPrePoint($price, $point_rate) * $quantity;
        }

        $total = $subtotal - $discount + $arrOrder['deliv_fee'] + $charge;
        $payment_total = $total;
        $arrUpdateValues = compact('payment_id', 'payment_method', 'add_point', 'use_point', 'tax', 'discount', 'subtotal', 'total', 'payment_total', 'charge');
        
        //ポイント使わないなら
        if(!USE_POINT){
            //add_pointはunset
            unset($arrUpdateValues['add_point']);
        }
        
        $arrPeriodicalOrderIds = array();
        
        foreach($arrShippings as $arrShipping){
            
            /**
             * 定期情報テーブル 
             */
            $table = 'plg_ps_dtb_p_orders';
            $periodical_order_id = $objQuery->nextVal('plg_ps_dtb_p_orders_periodical_order_id');
            $arrValues = array_merge($arrTempPeriodicalOrder, $arrOrder, $arrUpdateValues, compact('periodical_order_id'));
            //テーブルに存在するフィールドのみ抽出
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrValues);
            $objQuery->insert($table, $arrValues);
            
            /**
             * 定期リレーションテーブル 
             */
            $table = 'plg_ps_dtb_relations';
            $arrValues = compact('periodical_order_id','order_id');
            $objQuery->insert($table,$arrValues);
            
            /**
             * 定期配送テーブル 
             */
            $table = 'plg_ps_dtb_p_shippings';
            $arrValues = array_merge($arrShipping, $arrTempPeriodicalOrder);
                
            $arrValues = $arrShipping;
            $arrValues['periodical_order_id'] = $periodical_order_id;
            $arrValues['period_delivery_time'] = $arrDeliveryTimes[$arrTempPeriodicalOrder['period_delivery_time']];

            //テーブルに存在するフィールドのみ抽出
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrValues);
            $objQuery->insert($table,$arrValues);
            
            /**
             * 定期詳細テーブル 
             */
            $table = 'plg_ps_dtb_p_order_details';
            foreach($arrOrderDetails as $arrOrderDetail){
                $arrValues = $objQuery->extractOnlyColsOf($table, $arrOrderDetail);
                $arrValues['price'] += $arrOrderDetail['period_price_difference'];
                $arrValues['periodical_order_id'] = $periodical_order_id;
                $arrValues['periodical_order_detail_id'] = $objQuery->nextVal('plg_ps_dtb_p_order_details_periodical_order_detail_id');
                $objQuery->insert($table, $arrValues);
            }
            
            /**
             * 定期配送商品テーブル 
             */
            $table = 'plg_ps_dtb_p_shipment_items';
            foreach($arrOrderDetails as $arrOrderDetail){
                $periodical_shipment_item_id = $objQuery->nextVal('plg_ps_dtb_p_shipment_items_periodical_shipment_item_id');
                $arrValues = $objQuery->extractOnlyColsOf($table, array_merge($arrShipping, $arrOrderDetail));
                $arrValues['price'] += $arrOrderDetail['period_price_difference'];
                $arrValues['periodical_order_id'] = $periodical_order_id;
                $arrValues['periodical_shipment_item_id'] = $periodical_shipment_item_id;
                $objQuery->insert($table, $arrValues);
            }
            
            self::updateNextPeriod($periodical_order_id);
            $arrPeriodicalOrderIds[] = $periodical_order_id;
        }
        
        return $arrPeriodicalOrderIds;
    }
    
    /**
     * 受注詳細の中から、定期商品のものだけを取得する。
     * 
     * @param integer $order_id 受注ID
     * @return array 受注詳細の配列 
     */
    static function getOrderDetailsOfPeriodicalProductByOrderId($order_id){

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = <<<EOSQL
            dtb_order_detail
                INNER JOIN plg_ps_dtb_p_products
                    ON dtb_order_detail.product_id = plg_ps_dtb_p_products.product_id
                INNER JOIN dtb_products
                    ON dtb_products.product_id = dtb_order_detail.product_id
EOSQL;
        $where = 'is_periodical = ? AND order_id = ?';
        $arrWhereValues = array(1, $order_id);
        $arrOrderDetails = $objQuery->select('*',$table,$where,$arrWhereValues);
        return $arrOrderDetails;
    }
    
    /**
     * セッション値からカート内商品を取得する。
     * (注文確定後は不可)
     * 
     * @return array カート内商品
     */
    static function getCartItemsFromSession(){
        $objCartSess = new SC_CartSession_Ex();
        $cartKey = $objCartSess->getKey();
        $arrCartItems = $objCartSess->getCartList($cartKey);
        return $arrCartItems;
    }
    
    /**
     * 複数配送先の可否判定。
     * 定期商品が入っている場合は複数配送先不可。
     * 
     * @param array $arrCartItems カート内商品配列。指定しないとセッション値から取得する。
     * @return boolean 複数配送先が可能な場合trueを返す。
     */
    static function isMultiOrderable($arrCartItems = null){
        return !self::hasPeriodicalCartItem($arrCartItems);
    }
    
    /**
     * カート内商品に定期商品が含まれているかどうか。
     * 
     * @param array $arrCartItems カート内商品配列。指定しないとセッション値から取得する。
     * @return boolean 定期商品が含まれている場合trueを返す。
     */
    static function hasPeriodicalCartItem($arrCartItems = null){
        
        if(is_null($arrCartItems)){
            $arrCartItems = self::getCartItemsFromSession();
        }
        
        if(empty($arrCartItems)){
            return false;
        }
        
        //商品ID一覧を取得
        $arrProductIds = array();
        foreach($arrCartItems as $arrCartItem){
            $arrProductIds[] = $arrCartItem['productsClass']['product_id'];
        }
        
        return self::hasPeriodicalProductByProductIds($arrProductIds);
    }
    
    /**
     * 受注詳細に定期商品が含まれているかどうか。
     * 
     * @param integer $order_id 受注ID
     * @return boolean 
     */
    static function hasPeriodicalProductByOrderId($order_id){
        
        $arrOrderDetails = SC_Helper_Purchase_Ex::getOrderDetail($order_id);
        
        if(empty($arrOrderDetails)){
            return false;
        }
        
        //商品ID一覧を取得
        $arrProductIds = array();
        foreach($arrOrderDetails as $arrOrderDetail){
            $arrProductIds[] = $arrOrderDetail['product_id'];
        }
        
        return self::hasPeriodicalProductByProductIds($arrProductIds);
    }
    
    /**
     * 指定した商品に定期商品が含まれているかどうか。
     * 
     * @param array $arrProductIds
     * @return boolean 
     */
    static function hasPeriodicalProductByProductIds($arrProductIds){
        
        $product_id_count = count($arrProductIds);
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_p_products';
        $where = sprintf('is_periodical = ? AND product_id IN (%s)',  SC_Utils_Ex::repeatStrWithSeparator('?',$product_id_count));
        //is_periodicalなカート内商品を数える
        $periodical_product_count = $objQuery->count($table, $where, array_merge(array(1),$arrProductIds));
        
        return $periodical_product_count > 0;
    }
    
    /**
     * 定期受注を削除(Logical Delete)
     * 
     * @param array|integer $arrPeriodicalOrderIds 削除する定期受注ID
     */
    static function deletePeriodicalOrders($arrPeriodicalOrderIds){
        
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $arrPeriodicalOrders = $objPeriodicalOrder
            ->fetchByPeriodicalOrderIds($arrPeriodicalOrderIds)
            ->get();

        foreach($arrPeriodicalOrders as $arrPeriodicalOrder){
            
            $periodical_order_id = $arrPeriodicalOrder['periodical_order_id'];
            $arrValues = array('del_flg' => 1);
            self::savePeriodicalOrder($periodical_order_id, $arrValues);
        }
    }
    
    /**
     * 受注を発行する。
     * 
     * @param integer|array $arrPeriodicalOrderIds 発行する定期受注ID、又はその配列。
     * @param boolean $apply_offset trueの場合、次回予定日にオフセットを適用する
     */
    static function commitOrders($arrPeriodicalOrderIds, $apply_offset = true){
        
        //定期受注を取得
        $arrModes = array(
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPPINGS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDER_DETAILS,
            plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPMENT_ITEMS
        );
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $arrPeriodicalOrders = $objPeriodicalOrder
            ->fetchByPeriodicalOrderIds($arrPeriodicalOrderIds)
            ->attach($arrModes)
            ->get();
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        
        foreach($arrPeriodicalOrders as $arrPeriodicalOrder){

            $order_id = $objQuery->nextVal('dtb_order_order_id');
            $periodical_order_id = $arrPeriodicalOrder['periodical_order_id'];
            
            $table = 'dtb_order';
            $arrOrder = $arrPeriodicalOrder;
            if(is_numeric($arrOrder['customer_id'])){
                
                $customer_id = $arrOrder['customer_id'];
                $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerData($customer_id, true);
                foreach($arrCustomer as $key => $value){
                    
                    $order_key = sprintf('order_%s', $key);
                    
                    if(isset($arrOrder[$order_key])){
                        
                        $arrOrder[$order_key] = $value;
                    }
                }
            }

            //固定データを整形してマージ
            $arrRegularValues = array(
                'create_date' => 'CURRENT_TIMESTAMP',
                'update_date' => 'CURRENT_TIMESTAMP',
                'order_id' => $order_id,
                'status' => ORDER_NEW
            );
            $arrValues = array_merge($arrOrder, $arrRegularValues);
            //テーブルに存在するフィールドのみ抽出
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrValues);
            $objQuery->insert($table, $arrValues);
            
            $objPurchase = new SC_Helper_Purchase_Ex();
            $arrDeliveryTimes = $objPurchase->getDelivTime($arrPeriodicalOrder['deliv_id']);
            $time_id = $arrPeriodicalOrder['period_delivery_time'];

            //配送情報
            $table = 'dtb_shipping';
            foreach($arrPeriodicalOrder[plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPPINGS] as $arrPeriodicalShipping){

                //テーブルに存在するフィールドのみ抽出
                $arrValues = $objQuery->extractOnlyColsOf($table, $arrPeriodicalShipping);
                //データを整形
                $arrRegularValues = array(
                    'create_date' => 'CURRENT_TIMESTAMP',
                    'update_date' => 'CURRENT_TIMESTAMP',
                    'order_id' => $order_id,
                    'time_id' => $time_id,
                    'shipping_time' => $arrDeliveryTimes[$time_id],
                    'shipping_date' => $arrPeriodicalOrder['next_period']
                );
                $arrValues = array_merge($arrValues, $arrRegularValues);
                $objQuery->insert($table,$arrValues);
            }

            //受注詳細
            $table = 'dtb_order_detail';
            foreach($arrPeriodicalOrder[plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_ORDER_DETAILS] as $arrPeriodicalOrderDetail){

                $order_detail_id = $objQuery->nextVal('dtb_order_detail_order_detail_id');
                //テーブルに存在するフィールドのみ抽出
                $arrValues = $objQuery->extractOnlyColsOf($table, $arrPeriodicalOrderDetail);
                //データを整形
                $arrRegularValues = array(
                    'order_detail_id' => $order_detail_id,
                    'order_id' => $order_id
                );
                $arrValues = array_merge($arrValues, $arrRegularValues);
                $objQuery->insert($table,$arrValues);
            }

            //配送先別商品情報
            $table = 'dtb_shipment_item';
            foreach($arrPeriodicalOrder[plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_PERIODICAL_SHIPMENT_ITEMS] as $arrPeriodicalShipmentItem){
                
                //テーブルに存在するフィールドのみ抽出
                $arrValues = $objQuery->extractOnlyColsOf($table, $arrPeriodicalShipmentItem);
                //データを整形
                $arrRegularValues = array(
                    'order_id' => $order_id
                );
                $arrValues = array_merge($arrValues, $arrRegularValues);
                $objQuery->insert($table, $arrValues);
            }

            //リレーション
            $table = 'plg_ps_dtb_relations';
            $where = 'periodical_order_id = ?';
            $arrWhereValues = array($periodical_order_id);
            //データを整形
            $total_periodical_times = $objQuery->count($table, $where, $arrWhereValues) + 1;
            $arrValues = array(
                'periodical_order_id' => $periodical_order_id,
                'order_id' => $order_id,
                'periodical_times' => $total_periodical_times
            );
            $objQuery->insert($table,$arrValues);
            
            //定期受注の回数をUPDATE
            $table = 'plg_ps_dtb_p_orders';
            $where = 'periodical_order_id = ?';
            $arrWhereValues = array($periodical_order_id);
            $arrValues = array(
                'total_periodical_times' => $total_periodical_times
            );
            $objQuery->update($table, $arrValues, $where, $arrWhereValues);

            self::updateNextPeriod($periodical_order_id, $apply_offset);
        }
    }
    
    /**
     * 定期受注の次回予定日を算定・登録する。
     * 
     * @param integer|array $arrPeriodicalOrderIds 発行する定期受注ID、又はその配列。
     * @param boolean $apply_offset trueの場合、次回予定日にオフセットを適用する
     */
    static function updateNextPeriod($arrPeriodicalOrderIds, $apply_offset = true){
        
        //定期受注を取得
        $objPeriodicalOrder = new plg_PeriodicalSale_SC_PeriodicalOrder();
        $arrPeriodicalOrders = $objPeriodicalOrder
            ->fetchByPeriodicalOrderIds($arrPeriodicalOrderIds)
            ->attach(plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER)
            ->get();
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        
        foreach($arrPeriodicalOrders as $arrPeriodicalOrder){
            
            $periodical_order_id = $arrPeriodicalOrder['periodical_order_id'];
            //最終発行受注を取得
            $arrLastOrder = $arrPeriodicalOrder[plg_PeriodicalSale_SC_PeriodicalOrder::ALIAS_LAST_ORDER];
            
            $from_time = !empty($arrLastOrder['shipping_date']) ? 
                strtotime($arrLastOrder['shipping_date']) : //最終受注の配送日
                strtotime($arrLastOrder['create_date']);    //最終受注の作成日
            
            $arrPeriodInfo = array_merge($arrPeriodicalOrder, compact('from_time'));
            
            //オフセットを適用しない場合
            if(!$apply_offset){
                $period_offset = 0;
            }
            //適用する場合
            else{
                //nullならプラグインの設定からオフセットを取得となる
                $period_offset = null;
            }
            $next_period = date('Y-m-d', plg_PeriodicalSale_SC_Helper_Datetime::getNextPeriodTime($arrPeriodInfo, $period_offset));
            
            self::savePeriodicalOrder($periodical_order_id, compact('next_period'));
        }
    }
}
