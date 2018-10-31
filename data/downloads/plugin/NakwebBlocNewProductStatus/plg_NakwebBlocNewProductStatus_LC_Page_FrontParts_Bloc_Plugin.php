<?php
/*
 * NakwebBlocNewProductStatus
 * Copyright (C) 2012 NAKWEB CO.,LTD. All Rights Reserved.
 * http://www.nakweb.com/
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

// {{{ requires
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';
require_once PLUGIN_UPLOAD_REALDIR . 'NakwebBlocNewProductStatus/NakwebBlocNewProductStatus.php';


/**
 * プラグインの表示データ取得クラス
 *
 * @package NakwebPluginBase
 * @author NAKWEB CO.,LTD.
 * @version $Id: $
 */
class plg_NakwebBlocNewProductStatus_LC_Page_FrontParts_Bloc_Plugin extends LC_Page_FrontParts_Bloc {

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        // プラグインコードの取得（フォルダ名からプラグインコードを取得する）
        $this->plugin_code  = basename(dirname(__FILE__));
    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // 基本情報を渡す
        $objSiteInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->arrInfo = $objSiteInfo->data;

        // 商品ステータスID取得
        $masterData = new SC_DB_MasterData_Ex();
        $arrStatusId = $masterData->getMasterData('mtb_status');

        //// ブロックの情報を取得する
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode($this->plugin_code);
        $arrPluginData = unserialize($plugin['free_field1']);

        //表示するブロックのタイトル
        $title     = $arrPluginData['title'];

        //表示する商品の件数
        $limit     = $arrPluginData['limit'];

        // 集計期間（日数）
        $period    = $arrPluginData['period'];

        // 商品ステータス取得
        //// ブロックファイルネームの末尾の数字から商品ステータスIDを取得する
        $arrFilename = array();
        $arrFilename = explode('_', $this->blocItems['filename']);
        //// ブロックの filename を取得する
        $status_id = $arrFilename[(count($arrFilename) - 1)];

        // 新着商品 ブロック表示用データ
        $this->arrProductStatusNew = $this->lfGetProductStatusNew($status_id, $limit, $period);
        $this->bloc_title_main = $title;
        $this->bloc_title_sub  = $arrStatusId[$status_id];

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        $arrEcVersion = explode('.',ECCUBE_VERSION,3);
        if($arrEcVersion[1]=='12'){
            parent::destroy();
        }
    }


    /**
     * 新着商品取得.
     *
     * @param int 新着商品のステータスID
     * @param int 
     * @param int 新着商品のステータスID
     * @return array 新着商品配列
     */
    function lfGetProductStatusNew($status_id, $limit, $period){
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $col = <<< __EOS__
                p.product_id,
                p.name,
                p.main_list_image,
                p.main_list_comment AS comment,
                MIN(pc.price02) AS price02_min,
                MAX(pc.price02) AS price02_max
__EOS__;
        $from = <<< __EOS__
                dtb_products as p
           LEFT JOIN dtb_products_class as pc
             ON p.product_id = pc.product_id
           LEFT JOIN dtb_product_status as ps
             ON p.product_id = ps.product_id
__EOS__;
        $where = "p.del_flg = 0 AND pc.del_flg = 0 AND p.status = 1 AND ps.product_status_id = " . $status_id . " AND p.create_date >= ?";
        $arrval[] = date("Y-m-d 00:00:00", time() - 60 * 60 * 24 * $period);

        $groupby = "p.product_id, p.name, p.main_list_image, p.main_list_comment, p.create_date";
        $objQuery->setGroupBy($groupby);
        $objQuery->setOrder('p.create_date DESC');
        if ($limit > 0) {
            $objQuery->setLimit($limit);
        }

        //return $objQuery->select($col, $from, $where, $arrval);

        //税率金額を設定する
        $arrProducts = $objQuery->select($col, $from, $where, $arrval);
        SC_Product_Ex::setIncTaxToProducts($arrProducts);
        return $arrProducts;
    }

}
?>
