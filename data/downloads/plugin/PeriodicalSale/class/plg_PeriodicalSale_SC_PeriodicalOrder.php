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
 * 定期プラグイン の定期情報取得クラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_SC_PeriodicalOrder {
    
    /**
     * エイリアス 
     */
    const ALIAS_PERIODICAL_ORDERS =                 'periodical_orders';
    const ALIAS_PERIODICAL_ORDER_DETAILS =          'periodical_order_details';
    const ALIAS_PERIODICAL_SHIPPINGS =              'periodical_shippings';
    const ALIAS_PERIODICAL_SHIPMENT_ITEMS =         'periodical_shipment_items';
    const ALIAS_ORDERS =                            'orders';
    const ALIAS_FIRST_ORDER =                       'first_order';
    const ALIAS_LAST_ORDER =                        'last_order';
    
    /**
     * アソシエーションテーブル 
     */
    const ASSOCIATION_TABLE = '
                plg_ps_dtb_p_orders AS periodical_orders
                    LEFT JOIN
                        (
                            SELECT
                                periodical_order_id,
                                MIN(periodical_times) AS min_times,
                                MAX(periodical_times) AS max_times
                            FROM
                                plg_ps_dtb_relations
                            GROUP BY
                                periodical_order_id
                        ) relations
                        ON relations.periodical_order_id = periodical_orders.periodical_order_id
                    LEFT JOIN
                        plg_ps_dtb_relations first_relation
                        ON
                            relations.periodical_order_id = first_relation.periodical_order_id
                            AND relations.min_times = first_relation.periodical_times
                    LEFT JOIN
                        plg_ps_dtb_relations last_relation
                        ON
                            relations.periodical_order_id = last_relation.periodical_order_id
                            AND relations.max_times = last_relation.periodical_times
                    LEFT JOIN
                        dtb_order first_order
                        ON first_relation.order_id = first_order.order_id
                    LEFT JOIN
                        dtb_order last_order
                        ON last_relation.order_id = last_order.order_id';
    
    //定期受注の配列
    private $arrPeriodicalOrders;
    //定期受注IDの配列
    private $arrPeriodicalOrderIds;
    //定期受注ポインタの配列
    private $arrPeriodicalOrderPoints;
    
    /**
     * 定期受注ポインタを更新する
     * 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function updatePeriodicalOrderPoints(){
        
        $this->arrPeriodicalOrderPoints = array();
        if(is_array($this->arrPeriodicalOrders)){
            foreach($this->arrPeriodicalOrders as &$arrPeriodicalOrder){
                $periodical_order_id = $arrPeriodicalOrder['periodical_order_id'];
                $this->arrPeriodicalOrderPoints[$periodical_order_id] =& $arrPeriodicalOrder;
            }
        }
        return $this;
    }
    
    /**
     * 定期受注IDを更新する
     * 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function updatePeriodicalOrderIds(){
            
        $this->arrPeriodicalOrderIds = array();
        if(is_array($this->arrPeriodicalOrders)){
            foreach($this->arrPeriodicalOrders as $arrPeriodicalOrder){
                $this->arrPeriodicalOrderIds[] = $arrPeriodicalOrder['periodical_order_id'];
            }
        }
        return $this;
    }
    
    /**
     * 定期受注IDの配列を取得する
     * 
     * @param boolean $update trueの場合、取得前にIDを更新する
     * @return array 
     */
    function getPeriodicalOrderIds($update = false){
        
        if(!is_array($this->arrPeriodicalOrderIds) || $update){
            $this->updatePeriodicalOrderIds();
        }
        
        return $this->arrPeriodicalOrderIds;
    }
    
    /**
     * セットされた定期受注を取得する。
     * (getの前にfetchやattachをする必要がある)
     * 
     * @return array 定期受注の配列
     */
    function get(){
        
        return $this->arrPeriodicalOrders;
    }
    
    /**
     * セットされた定期受注の最初の1つを取得する。
     * 存在しない場合は空の配列を返す。
     *
     * @return array 
     */
    function getOne(){
        
        return reset($this->arrPeriodicalOrders) ? reset($this->arrPeriodicalOrders) : array();
    }
    
    /**
     * 定期受注を取得・セットする。
     * (その他の情報も取得する場合attachも使用する)
     *
     * @param boolean $customer trueの場合、セッション上の顧客IDを条件に追加する
     * @param SC_Query_Ex $objQuery インスタンス
     * @param boolean $update trueの場合、定期受注IDや定期受注ポインタの配列も更新する
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function fetch($customer = false, SC_Query_Ex &$objQuery = null, $update = true){
        
        if(!($objQuery instanceof SC_Query_Ex)){
            $objQuery = $this->getDefaultQuery(self::ALIAS_PERIODICAL_ORDERS);
        }
        
        if($customer){
            
            //セッションから顧客を取得し、条件にセットする
            $objCustomer = new SC_Customer_Ex();
            $customer_id = $objCustomer->getValue('customer_id');
            if(!SC_Utils_Ex::isBlank($customer_id)){
                $objQuery->andWhere('periodical_orders.customer_id = ?');
                $objQuery->arrWhereVal[] = $customer_id;
            }
        }
        
        $from = self::ASSOCIATION_TABLE;
        //他の列も必要な場合はfetch後にattachを使う
        $cols = sprintf('%s.*', self::ALIAS_PERIODICAL_ORDERS);
        $this->arrPeriodicalOrders = $objQuery->select($cols, $from);
        
        //更新がtrueの場合
        if($update){
            //定期受注IDを更新
            $this->updatePeriodicalOrderIds();
            //定期受注ポインタを更新
            $this->updatePeriodicalOrderPoints();
        }
        
        return $this;
    }
    
    /**
     * 定期受注IDからfetchする。
     *
     * @param integer|array $arrPeriodicalOrderIds 定期受注ID、又はその配列
     * @param boolean $customer trueの場合、セッション上の顧客IDを条件に追加する
     * @param SC_Query_Ex $objQuery インスタンス
     * @param boolean $update trueの場合、定期受注IDや定期受注ポインタの配列も更新する
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function fetchByPeriodicalOrderIds($arrPeriodicalOrderIds, $customer = false, SC_Query_Ex &$objQuery = null, $update = true){
        
        if(!($objQuery instanceof SC_Query_Ex)){
            $objQuery = $this->getDefaultQuery(self::ALIAS_PERIODICAL_ORDERS);
        }
        
        $this->__applyWherePeriodicalOrderIds($objQuery, self::ALIAS_PERIODICAL_ORDERS, $arrPeriodicalOrderIds);
        return $this->fetch($customer, $objQuery, $update);
    }
    
    /**
     * 受注IDからfetchする。
     * 他のfetchと違い、受注発行回数も付与される。
     * 
     * @param integer|array $arrOrderIds 受注ID、又はその配列
     * @param boolean $customer trueの場合、セッション上の顧客IDを条件に追加する
     * @param SC_Query_Ex $objQuery インスタンス
     * @param boolean $update trueの場合、定期受注IDや定期受注ポインタの配列も更新する
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function fetchByOrderIds($arrOrderIds, $customer = false, SC_Query_Ex &$objQuery = null, $update = true){
        
        if(is_numeric($arrOrderIds)){
            $arrOrderIds = array($arrOrderIds);
        }
        
        $objRelationQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'plg_ps_dtb_relations';
        $arrWhereValues = $arrOrderIds;
        $where = sprintf('order_id IN (%s)', SC_Utils_Ex::repeatStrWithSeparator('?', count($arrWhereValues)));
        $arrRelations = $objRelationQuery->select('*', $table, $where, $arrWhereValues);
        $arrRelationPoints = array();
        $arrPeriodicalOrderIds = array();
        
        foreach($arrRelations as &$arrRelation){
            
            $periodical_order_id = $arrRelation['periodical_order_id'];
            $arrRelationPoints[$periodical_order_id] =& $arrRelation;
            $arrPeriodicalOrderIds[] = $periodical_order_id;
        }

        $return = $this->fetchByPeriodicalOrderIds($arrPeriodicalOrderIds, $customer, $objQuery, $update);
        
        foreach($this->arrPeriodicalOrders as &$arrPeriodicalOrder){
            
            $periodical_order_id = $arrPeriodicalOrder['periodical_order_id'];
            
            if(isset($arrRelationPoints[$periodical_order_id])){
                
                $arrPeriodicalOrder = array_merge($arrPeriodicalOrder, $arrRelationPoints[$periodical_order_id]);
            }
        }
        
        return $return;
    }
    
    /**
     * 検索条件から件数を取得する
     *
     * @param boolean $customer trueの場合、セッション上の顧客IDを条件に追加する
     * @param SC_Query_Ex $objQuery インスタンス
     * @return integer 件数
     */
    static function count($customer = false, SC_Query_Ex &$objQuery = null){
        
        
        if(!($objQuery instanceof SC_Query_Ex)){
            $objQuery = self::getDefaultQuery(self::ALIAS_PERIODICAL_ORDERS);
        }
        
        if($customer){
            
            $objCustomer = new SC_Customer_Ex();
            $customer_id = $objCustomer->getValue('customer_id');
            if(!SC_Utils_Ex::isBlank($customer_id)){
                $objQuery->andWhere('periodical_orders.customer_id = ?');
                $objQuery->arrWhereVal[] = $customer_id;
            }
        }
        $from = self::ASSOCIATION_TABLE;
        $objQuery->setOrder('');
        $objQuery->setGroupBy('');
        return $objQuery->count($from);
    }
    
    /**
     * fetchした定期受注情報に、追加情報を付与する。
     * 配列の場合、全てself::getDefaultQuery()のSC_Query_Exが使用される。
     * 
     * @param string|array $arrModes 付与する情報のエイリアスの文字列、又はその配列。(nullの場合、全てのattachが実行される。)
     * @param SC_Query_Ex $objQuery インスタンス (但し$arrModesが配列の場合は無視される)
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function attach($arrModes = null, SC_Query_Ex &$objQuery = null){
        
        //モードが配列なら
        if(is_array($arrModes)){
            //配列分繰り返す
            foreach($arrModes as $mode){
                $this->attach($mode);
            }
            return $this;
        }
        //nullなら
        elseif(is_null($arrModes)){
            //全てのモードでattach
            $arrModes = array(
                self::ALIAS_PERIODICAL_ORDER_DETAILS,
                self::ALIAS_PERIODICAL_SHIPPINGS,
                self::ALIAS_PERIODICAL_SHIPMENT_ITEMS,
                self::ALIAS_FIRST_ORDER,
                self::ALIAS_LAST_ORDER,
                self::ALIAS_ORDERS,
            );
            return $this->attach($arrModes);
        }
        
        $mode = $arrModes;
        $return = null;
        
        if(!($objQuery instanceof SC_Query_Ex)){
            $objQuery = self::getDefaultQuery($mode);
        }
        
        switch($mode){
            
            case self::ALIAS_PERIODICAL_ORDER_DETAILS:
                $return = $this->__attachPeriodicalOrderDetails($objQuery);
                break;
            
            case self::ALIAS_PERIODICAL_SHIPPINGS:
                $return = $this->__attachPeriodicalShippings($objQuery);
                break;
            
            case self::ALIAS_PERIODICAL_SHIPMENT_ITEMS:
                $return = $this->__attachPeriodicalShipmentItems($objQuery);
                break;
            
            case self::ALIAS_FIRST_ORDER:
                $return = $this->__attachFirstOrder($objQuery);
                break;
            
            case self::ALIAS_LAST_ORDER:
                $return = $this->__attachLastOrder($objQuery);
                break;
            
            case self::ALIAS_ORDERS:
                $return = $this->__attachOrders($objQuery);
        }
        
        return $return;
    }
    
    /**
     * セットされている定期受注情報から、指定したエイリアスの情報を空にする
     *
     * @param string $alias 
     */
    function __initAssociation($alias){
        
        foreach($this->arrPeriodicalOrders as &$arrPeriodicalOrder){
            
            $arrPeriodicalOrder[$alias] = array();
        }
    }
    
    /**
     * SC_Query_Exインスタンスに、定期受注IDのWHEREをセットする
     *
     * @param SC_Query_Ex $objQuery インスタンス
     * @param string $alias テーブルのエイリアス (省略可能)
     * @param integer|array $arrPeriodicalOrderIds 定期受注ID、又はその配列 (nullの場合、セットされている定期受注ID全て)
     */
    function __applyWherePeriodicalOrderIds(SC_Query_Ex &$objQuery, $alias = '', $arrPeriodicalOrderIds = null){
        
        if(is_numeric($arrPeriodicalOrderIds)){
            $arrPeriodicalOrderIds = array($arrPeriodicalOrderIds);
        }
        elseif(is_null($arrPeriodicalOrderIds)){
            $arrPeriodicalOrderIds = $this->getPeriodicalOrderIds();
        }
        
        if(!empty($alias)){
            $alias = sprintf('%s.', $alias);
        }
        
        if(!empty($arrPeriodicalOrderIds)){
            $where = sprintf('%speriodical_order_id IN (%s)', $alias, SC_Utils_Ex::repeatStrWithSeparator('?', count($arrPeriodicalOrderIds)));
            $objQuery->arrWhereVal = array_merge($objQuery->arrWhereVal, $arrPeriodicalOrderIds);
        }
        else{
            $where = '0 = 1';
        }
        $objQuery->andWhere($where);
    }
    
    /**
     * 最初の受注を紐付ける
     *
     * @param SC_Query_Ex $objQuery 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function __attachFirstOrder(SC_Query_Ex &$objQuery){
        
        $alias = self::ALIAS_FIRST_ORDER;
        $aggregate = 'MIN';
        $cols = '*';
        //XXX del_flg = 0の指定、ホントは$objQuery内でやりたい
        $from = <<<EOSQL
            (
                SELECT
                    periodical_order_id,
                    $aggregate(periodical_times) AS periodical_times
                FROM
                    plg_ps_dtb_relations AS sub
                INNER JOIN dtb_order
                    ON
                        sub.order_id = dtb_order.order_id
                        AND dtb_order.del_flg = 0
                GROUP BY
                    periodical_order_id
            ) AS grouped_relation
                LEFT JOIN plg_ps_dtb_relations relation
                    ON
                        grouped_relation.periodical_order_id = relation.periodical_order_id
                        AND grouped_relation.periodical_times = relation.periodical_times
                LEFT JOIN dtb_order $alias
                    ON relation.order_id = $alias.order_id
                LEFT JOIN dtb_shipping
                    ON dtb_shipping.order_id = $alias.order_id
EOSQL;
        
        $this->__applyWherePeriodicalOrderIds($objQuery, 'relation');
        $arrResults = $objQuery->select($cols, $from);
        foreach($arrResults as $arrResult){
            $periodical_order_id = $arrResult['periodical_order_id'];
            $point =& $this->arrPeriodicalOrderPoints[$periodical_order_id];
            $point[$alias] = $arrResult;
        }
        return $this;
    }
    
    /**
     * 最後の受注を紐付ける
     *
     * @param SC_Query_Ex $objQuery 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function __attachLastOrder(SC_Query_Ex &$objQuery){
        
        $alias = self::ALIAS_LAST_ORDER;
        $aggregate = 'MAX';
        $cols = '*';
        //XXX del_flg = 0の指定、ホントは$objQuery内でやりたい
        $from = <<<EOSQL
            (
                SELECT
                    periodical_order_id,
                    $aggregate(periodical_times) AS periodical_times
                FROM
                    plg_ps_dtb_relations AS sub
                INNER JOIN dtb_order
                    ON
                        sub.order_id = dtb_order.order_id
                        AND dtb_order.del_flg = 0
                GROUP BY
                    periodical_order_id
            ) AS grouped_relation
                LEFT JOIN plg_ps_dtb_relations relation
                    ON
                        grouped_relation.periodical_order_id = relation.periodical_order_id
                        AND grouped_relation.periodical_times = relation.periodical_times
                LEFT JOIN dtb_order $alias
                    ON relation.order_id = $alias.order_id
                LEFT JOIN dtb_shipping
                    ON dtb_shipping.order_id = $alias.order_id
EOSQL;
        
        $this->__applyWherePeriodicalOrderIds($objQuery, 'relation');
        $arrResults = $objQuery->select($cols, $from);
        foreach($arrResults as $arrResult){
            $periodical_order_id = $arrResult['periodical_order_id'];
            $point =& $this->arrPeriodicalOrderPoints[$periodical_order_id];
            $point[$alias] = $arrResult;
        }
        return $this;
    }
    
    /**
     * 全ての受注を紐付ける
     *
     * @param SC_Query_Ex $objQuery 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function __attachOrders(SC_Query_Ex &$objQuery){
        
        $alias = self::ALIAS_ORDERS;
        $this->__initAssociation($alias);
        $cols = '*';
        //XXX 複数配送先にするなら、dtb_shippingとJOINしてはいけない (1:多になるから)
        $from = <<<EOSQL
            dtb_order AS $alias
                INNER JOIN plg_ps_dtb_relations
                    ON $alias.order_id = plg_ps_dtb_relations.order_id
                INNER JOIN dtb_shipping
                    ON $alias.order_id = dtb_shipping.order_id
EOSQL;
        
        $this->__applyWherePeriodicalOrderIds($objQuery);
        $arrResults = $objQuery->select($cols, $from);
        foreach($arrResults as $arrResult){
            $periodical_order_id = $arrResult['periodical_order_id'];
            $point =& $this->arrPeriodicalOrderPoints[$periodical_order_id];
            $point[$alias][] = $arrResult;
        }
        return $this;
    }
    
    /**
     * 配送先別商品を紐付ける
     *
     * @param SC_Query_Ex $objQuery 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function __attachPeriodicalShipmentItems(SC_Query_Ex &$objQuery){
        
        $alias = self::ALIAS_PERIODICAL_SHIPMENT_ITEMS;
        $this->__initAssociation($alias);
        $cols = '*';
        $from = sprintf('plg_ps_dtb_p_shipment_items AS %s', $alias);
        
        $this->__applyWherePeriodicalOrderIds($objQuery);
        $arrResults = $objQuery->select($cols, $from);
        foreach($arrResults as $arrResult){
            $periodical_order_id = $arrResult['periodical_order_id'];
            $point =& $this->arrPeriodicalOrderPoints[$periodical_order_id];
            $point[$alias][] = $arrResult;
        }
        return $this;
    }
    
    /**
     * 配送先情報を紐付ける
     *
     * @param SC_Query_Ex $objQuery 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function __attachPeriodicalShippings(SC_Query_Ex &$objQuery){
        
        $alias = self::ALIAS_PERIODICAL_SHIPPINGS;
        $this->__initAssociation($alias);
        $cols = '*';
        $from = sprintf('plg_ps_dtb_p_shippings AS %s', $alias);
        
        $this->__applyWherePeriodicalOrderIds($objQuery);
        $arrResults = $objQuery->select($cols, $from);
        foreach($arrResults as $arrResult){
            $periodical_order_id = $arrResult['periodical_order_id'];
            $point =& $this->arrPeriodicalOrderPoints[$periodical_order_id];
            $point[$alias][] = $arrResult;
        }
        return $this;
    }
    
    /**
     * 定期受注詳細を紐付ける
     *
     * @param SC_Query_Ex $objQuery 
     * @return plg_PeriodicalSale_SC_PeriodicalOrder 
     */
    function __attachPeriodicalOrderDetails(SC_Query_Ex &$objQuery){
      
        $alias = self::ALIAS_PERIODICAL_ORDER_DETAILS;
        $this->__initAssociation($alias);
        $cols = <<<EOSQL
        $alias.*,
            dtb_products.product_id,
            dtb_products.main_large_image,
            dtb_products.main_list_image,
            dtb_products.main_image,
            CASE WHEN
                EXISTS(
                    SELECT * FROM dtb_products AS enable_products
                        WHERE product_id = dtb_products.product_id
                            AND del_flg = 0
                            AND status = 1
                )
                THEN '1'
                ELSE '0'
            END AS enable
EOSQL;
        $from = <<<EOSQL
            plg_ps_dtb_p_order_details AS $alias
                LEFT JOIN dtb_products
                    ON $alias.product_id = dtb_products.product_id
EOSQL;
        
        $this->__applyWherePeriodicalOrderIds($objQuery);
        $arrResults = $objQuery->select($cols, $from);
        foreach($arrResults as $arrResult){
            $periodical_order_id = $arrResult['periodical_order_id'];
            $point =& $this->arrPeriodicalOrderPoints[$periodical_order_id];
            $point[$alias][] = $arrResult;
        }
        return $this;
    }
    
    /**
     * 各アソシエーションのSC_Query_Exインスタンスを取得する。
     * 
     * @param string $type SC_Query_Exのタイプ
     * @return array SC_Query_Exのインスタンスの配列
     */
    static function getDefaultQuery($type){
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        switch($type){
            
            case self::ALIAS_PERIODICAL_ORDERS:
                $objQuery->andWhere(sprintf('%s.del_flg = 0', $type));
                $objQuery->setOrder(sprintf('%s.periodical_order_id DESC', $type));
                break;
            
            case self::ALIAS_PERIODICAL_ORDER_DETAILS:
                $objQuery->setOrder(sprintf('%s.product_id ASC', $type));
                break;
            
            case self::ALIAS_PERIODICAL_SHIPPINGS:
                $objQuery->setOrder(sprintf('%s.shipping_id ASC', $type));
                break;
            
            case self::ALIAS_PERIODICAL_SHIPMENT_ITEMS:
                $objQuery->setOrder(sprintf('%s.product_class_id ASC', $type));
                break;
            
            case self::ALIAS_ORDERS:
                $objQuery->andWhere(sprintf('%s.del_flg = 0', $type));
                $objQuery->setOrder(sprintf('%s.order_id DESC', $type));
                break;
            
            case self::ALIAS_LAST_ORDER:
                break;
            
            case self::ALIAS_FIRST_ORDER:
                break;
        }
        return $objQuery;
    }
}
