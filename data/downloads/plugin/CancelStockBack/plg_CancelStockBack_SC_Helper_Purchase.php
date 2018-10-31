<?php
/*
 * CancelStockBack
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
 */
require_once PLUGIN_UPLOAD_REALDIR . "CancelStockBack/plg_CancelStockBack_SC_Helper_Purchase_Ext.php";

class plg_CancelStockBack_SC_Helper_Purchase extends SC_Helper_Purchase{
    /**
     * 受注.対応状況の更新
     *
     * 必ず呼び出し元でトランザクションブロックを開いておくこと。
     *
     * @param integer $orderId 注文番号
     * @param integer|null $newStatus 対応状況 (null=変更無し)
     * @param integer|null $newAddPoint 加算ポイント (null=変更無し)
     * @param integer|null $newUsePoint 使用ポイント (null=変更無し)
     * @param array $sqlval 更新後の値をリファレンスさせるためのパラメーター
     * @return void
     */
    function sfUpdateOrderStatus($orderId, $newStatus = null, $newAddPoint = null, $newUsePoint = null, &$sqlval = array()) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrOrderOld = $objQuery->getRow('status, add_point, use_point, customer_id', 'dtb_order', 'order_id = ?', array($orderId));

        // 対応状況が変更無しの場合、DB値を引き継ぐ
        if (is_null($newStatus)) {
            $newStatus = $arrOrderOld['status'];
        }

        // 使用ポイント、DB値を引き継ぐ
        if (is_null($newUsePoint)) {
            $newUsePoint = $arrOrderOld['use_point'];
        }

        // 加算ポイント、DB値を引き継ぐ
        if (is_null($newAddPoint)) {
            $newAddPoint = $arrOrderOld['add_point'];
        }

        if (USE_POINT !== false) {
            // 会員.ポイントの加減値
            $addCustomerPoint = 0;

            // ▼使用ポイント
            // 変更前の対応状況が利用対象の場合、変更前の使用ポイント分を戻す
            if ($this->isUsePoint($arrOrderOld['status'])) {
                $addCustomerPoint += $arrOrderOld['use_point'];
            }

            // 変更後の対応状況が利用対象の場合、変更後の使用ポイント分を引く
            if ($this->isUsePoint($newStatus)) {
                $addCustomerPoint -= $newUsePoint;
            }

            // ▲使用ポイント

            // ▼加算ポイント
            // 変更前の対応状況が加算対象の場合、変更前の加算ポイント分を戻す
            if ($this->isAddPoint($arrOrderOld['status'])) {
                $addCustomerPoint -= $arrOrderOld['add_point'];
            }

            // 変更後の対応状況が加算対象の場合、変更後の加算ポイント分を足す
            if ($this->isAddPoint($newStatus)) {
                $addCustomerPoint += $newAddPoint;
            }
            // ▲加算ポイント

            if ($addCustomerPoint != 0) {
                // ▼会員テーブルの更新
                $objQuery->update('dtb_customer', array('update_date' => 'CURRENT_TIMESTAMP'),
                                  'customer_id = ?', array($arrOrderOld['customer_id']),
                                  array('point' => 'point + ?'), array($addCustomerPoint));
                // ▲会員テーブルの更新

                // 会員.ポイントをマイナスした場合、
                if ($addCustomerPoint < 0) {
                    $sql = 'SELECT point FROM dtb_customer WHERE customer_id = ?';
                    $point = $objQuery->getOne($sql, array($arrOrderOld['customer_id']));
                    // 変更後の会員.ポイントがマイナスの場合、
                    if ($point < 0) {
                        // ロールバック
                        $objQuery->rollback();
                        // エラー
                        SC_Utils_Ex::sfDispSiteError(LACK_POINT);
                    }
                }
            }
        }

        // ▼受注テーブルの更新
        if (empty($sqlval)) {
            $sqlval = array();
        }
		
		$arrCancelPrevStatus = plg_CancelStockBack_SC_Helper_Purchase_Ext::getCancelStatus('prev');
		$arrCancelPostStatus = plg_CancelStockBack_SC_Helper_Purchase_Ext::getCancelStatus('post');

        if (USE_POINT !== false) {
            $sqlval['add_point'] = $newAddPoint;
            $sqlval['use_point'] = $newUsePoint;
        }
        // 対応状況が発送済みに変更の場合、発送日を更新
        if ($arrOrderOld['status'] != ORDER_DELIV && $newStatus == ORDER_DELIV) {
            $sqlval['commit_date'] = 'CURRENT_TIMESTAMP';
        }
        // 対応状況が入金済みに変更の場合、入金日を更新
        elseif ($arrOrderOld['status'] != ORDER_PRE_END && $newStatus == ORDER_PRE_END) {
            $sqlval['payment_date'] = 'CURRENT_TIMESTAMP';
        }
		// 対応状況がキャンセルに変更の場合、在庫を戻す
		if (((in_array($arrOrderOld['status'],$arrCancelPrevStatus) && count($arrCancelPrevStatus) > 0) || (!in_array($arrOrderOld['status'],$arrCancelPostStatus) && count($arrCancelPrevStatus) == 0)) && in_array($newStatus,$arrCancelPostStatus)) {
			plg_CancelStockBack_SC_Helper_Purchase_Ext::backStock($orderId);
		// 対応状況がキャンセルからの変更の場合、在庫を減らす
		}elseif (in_array($arrOrderOld['status'],$arrCancelPostStatus) && ((in_array($newStatus,$arrCancelPrevStatus) && count($arrCancelPrevStatus) > 0) || (!in_array($newStatus,$arrCancelPostStatus) && count($arrCancelPrevStatus) == 0))) {
			plg_CancelStockBack_SC_Helper_Purchase_Ext::reduceStock($orderId);
		}

        $sqlval['status'] = $newStatus;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';

        $dest = $objQuery->extractOnlyColsOf('dtb_order', $sqlval);
        $objQuery->update('dtb_order', $dest, 'order_id = ?', array($orderId));
        // ▲受注テーブルの更新

        //会員情報の最終購入日、購入合計を更新
        if ($arrOrderOld['customer_id'] > 0 and $arrOrderOld['status'] != $newStatus) {
            SC_Customer_Ex::updateOrderSummary($arrOrderOld['customer_id']);
        }
    }
}