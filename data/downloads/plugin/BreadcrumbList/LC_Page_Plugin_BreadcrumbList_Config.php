<?php
/*
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
require_once CLASS_EX_REALDIR.'page_extends/admin/LC_Page_Admin_Ex.php';class LC_Page_Plugin_BreadcrumbList_Config extends LC_Page_Admin_Ex{var$arrForm=array();function init(){parent::init();$this->tpl_mainpage=PLUGIN_UPLOAD_REALDIR."BreadcrumbList/config.tpl";$this->tpl_subtitle="パンくず";}function process(){$this->action();$this->sendResponse();echo'<div id="popup-container">';echo'<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fnoblog&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=209355525823502" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>';echo'<h2>公開中のプラグイン一覧</h2>';echo@file_get_contents('https://dl.dropboxusercontent.com/u/2048067/plugin.html');echo'</div>';}function action(){$A=new SC_FormParam_Ex();$this->lfInitParam($A);$A->setParam($_POST);$A->convParam();$B=array();switch($this->getMode()){case'edit':$B=$A->getHashArray();$this->arrErr=$A->checkError();if(count($this->arrErr)==0){$this->arrErr=$this->save($B);if(count($this->arrErr)==0){$this->tpl_onload="alert('登録が完了しました。');";$this->tpl_onload.='window.close();';}}break;default:$G=SC_Plugin_Util_Ex::getPluginByPluginCode("BreadcrumbList");$B['css_data']=$G['free_field1'];break;}$this->arrForm=$B;$this->setTemplate($this->tpl_mainpage);}function lfInitParam(&$A){$A->addParam('CSS','css_data',LLTEXT_LEN,'',array('EXIST_CHECK','MAX_LENGTH_CHECK'));}function save($F){$H=array();$D=&SC_Query_Ex::getSingletonInstance();$D->begin();$C=array();$C['free_field1']=$F['css_data'];$C['update_date']='CURRENT_TIMESTAMP';$E="plugin_code = 'BreadcrumbList'";$D->update('dtb_plugin',$C,$E);$D->commit();return$H;}}
