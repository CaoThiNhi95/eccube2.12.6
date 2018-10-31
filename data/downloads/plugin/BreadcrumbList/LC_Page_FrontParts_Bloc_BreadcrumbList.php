<?php
/*
 * 2.13系対応パンくずプラグイン
 * パンくずリストを生成する
 * Copyright (C) 2013 Nobuhiko Kimoto
 * info@nob-log.info
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
require_once CLASS_REALDIR.'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';class LC_Page_FrontParts_Bloc_BreadcrumbList extends LC_Page_FrontParts_Bloc{function init(){parent::init();}function process(){$this->action();$this->sendResponse();}function action(){$P=new SC_Helper_PageLayout_Ex();$P->sfGetPageLayout($this,false,$_SERVER['SCRIPT_NAME'],$this->objDisplay->detectDevice());$this->arrBreadcrumb[0][0]=array();switch($this->arrPageLayout['url']){case'products/list.php':$A=$_GET['category_id'];if($A){$this->arrBreadcrumb[0]=self::getBreadcrumbByCategoryId(intval($A));}else{if($_GET['mode']=='search'){$this->current_name='検索結果';}else{$this->current_name='全商品';}}break;case'products/detail.php':$E=$_GET['product_id'];$this->arrBreadcrumb=SC_Helper_DB_Ex::sfGetMultiCatTree($E);$M=new SC_Product_Ex();$N=$M->getDetail($E);$this->current_name=$N['name'];break;case'index.php':$this->current_name='';break;default:$this->current_name=$this->arrPageLayout['page_name'];break;}$this->arrData=self::loadData();}function getBreadcrumbByCategoryId($A){$D=array();if(!SC_Utils_Ex::sfIsInt($A)||SC_Utils_Ex::sfIsZeroFilling($A)||!SC_Helper_DB_Ex::sfIsRecord('dtb_category','category_id',(array)$A,'del_flg = 0')){$this->current_name='全商品';return array();}$H=SC_Helper_DB_Ex::sfGetCategoryId('',$A);if(empty($H)){$this->current_name='全商品';return array();}$O=new SC_Helper_DB_Ex();$L=$O->sfGetParents("dtb_category","parent_category_id","category_id",$H[0]);$Q=new SC_Query();$B=0;foreach($L as$C){$K="SELECT category_name FROM dtb_category WHERE category_id = ?";$J=array($C);$G=$Q->getOne($K,$J);if($C!=$A){$D[$B]['category_name']=$G;$D[$B]['category_id']=$C;}else{$this->current_name=$G;}$B++;}return$D;}function loadData(){$F=array();$I=SC_Plugin_Util_Ex::getPluginByPluginCode("BreadcrumbList");if(!SC_Utils_Ex::isBlank($I['free_field1'])){$F['css_data']=$I['free_field1'];}return$F;}}
