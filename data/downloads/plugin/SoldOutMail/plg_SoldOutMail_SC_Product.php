<?php
/*
 * SoldOutMail
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
 
require_once CLASS_REALDIR . 'SC_Product.php';

class plg_SoldOutMail_SC_Product extends SC_Product{
    /**
     * 在庫を減少させる.
     *
     * 指定の在庫数まで, 在庫を減少させる.
     * 減少させた結果, 在庫数が 0 未満になった場合, 引数 $quantity が 0 の場合は,
     * 在庫の減少を中止し, false を返す.
     * 在庫の減少に成功した場合は true を返す.
     *
     * @param integer $productClassId 商品規格ID
     * @param integer $quantity 減少させる在庫数
     * @return boolean 在庫の減少に成功した場合 true; 失敗した場合 false
     */
    function reduceStock($productClassId, $quantity) {

        if ($quantity == 0) {
            return false;
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_products_class', array(),
                          'product_class_id = ?', array($productClassId),
                          array('stock' => 'stock - ?'), array($quantity));
        // TODO エラーハンドリング

        $productsClass = $this->getDetailAndProductsClass($productClassId);
		if($productsClass['stock_unlimited'] != '1' && $productsClass['stock'] == 0){
			$this->lfSendSoldOutMail($productClassId);
		}
        if ($productsClass['stock_unlimited'] != '1' && $productsClass['stock'] < 0) {
            return false;
        }

        return true;
    }
	
    /**
     * 売り切れ通知メール送信
     *
     * @param integer $product_class_id
     * @access private
     * @return void
     */
    function lfSendSoldOutMail($product_class_id) {
		$productsClass = $this->getDetailAndProductsClass($product_class_id);
        $objHelperMail  = new SC_Helper_Mail_Ex();
        $CONF           = SC_Helper_DB_Ex::sfGetBasisData();

        //--　メール送信
        $objMailText    = new SC_SiteView_Ex();
        $objMailText->assign('arrProduct', $productsClass);
        $toMail = $objMailText->fetch(PLUGIN_UPLOAD_REALDIR . 'SoldOutMail/templates/soldout_mail.tpl');
		$product_name = $productsClass['name'];
		if($productsClass['classcategory_name1']){
			$product_name .= '/'.$productsClass['classcategory_name1'];
		}
		if($productsClass['classcategory_name2']){
			$product_name .= '/'.$productsClass['classcategory_neme2'];
		}
        $subject = $objHelperMail->sfMakesubject('在庫切れ通知【商品名:'.$product_name.'】');
        $objMail = new SC_SendMail_Ex();

        $objMail->setItem(
                              ''                                // 宛先
                            , $subject                  // サブジェクト
                            , $toMail           // 本文
                            , $CONF['email03']          // 配送元アドレス
                            , $CONF['shop_name']        // 配送元 名前
                            , $CONF['email03']          // reply_to
                            , $CONF['email04']          // return_path
                            , $CONF['email04']          // Errors_to
        );
        // 宛先の設定
        $objMail->setTo($CONF['email02'], $CONF['shop_name']);
        $objMail->sendMail();
    }	
}