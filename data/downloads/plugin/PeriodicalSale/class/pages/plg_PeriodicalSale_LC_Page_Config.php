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
 
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'PeriodicalSale/PeriodicalSale.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Config extends LC_Page_Admin_Ex {
    
    var $arrForm = array();
    
    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR .'PeriodicalSale/templates/plg_PeriodicalSale_config.tpl';
        $this->tpl_subtitle = '定期販売コンフィグ';
        
        $objMasterData = new SC_DB_MasterData_Ex();
        $this->arrDAYS = $objMasterData->getMasterData('mtb_wday');
        $this->arrWEEKS = $objMasterData->getMasterData('plg_ps_mtb_period_weeks');
        $this->arrDATES = $objMasterData->getMasterData('plg_ps_mtb_period_dates');
        $this->arrPERIODTYPES = PeriodicalSale::getPeriodTypes();
        $this->arrPAYMENTS = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
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
        
        $post = $_POST;
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitFormParam($objFormParam);
        $mode = $this->getMode();
        
        switch($mode){
            
            case 'edit':
                
                $this->lfSetFormParam($objFormParam, $post);
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $this->lfCheckError($objFormParam);
                if(empty($this->arrErr)){
                    
                    $this->lfUpdatePlugin($objFormParam);
                    $this->tpl_onload = <<<EOSCRIPT
                            alert("登録が完了しました。");
                            window.close();
EOSCRIPT;
                }
                break;
            
            default:
                
                $this->lfSetFormParam($objFormParam, PeriodicalSale::getNamedPluginInfo());
                $this->arrForm = $objFormParam->getHashArray();
                break;
        }
        $this->setTemplate($this->tpl_mainpage);
    }
    
    /**
     * エラーチェック
     * 
     * @param SC_FormParam_Ex $objFormParam インスタンス
     * @return array エラー情報配列
     */
    function lfCheckError(SC_FormParam_Ex $objFormParam){
        
        $objError = new SC_CheckError();
        $objError->arrErr = $objFormParam->checkError();
        
        $arrFormParamList = $objFormParam->getFormParamList();
        $message = '%s を最低でも一つ選択して下さい。<br />';
        
        //周期が選択されていなかったら
        if(count(array_filter($arrFormParamList['available_period_types']['value'])) == 0){
            $objError->arrErr['available_period_types'] = sprintf($message, $arrFormParamList['available_period_types']['disp_name']);
        }
        else{
            //周期と、それに対応するキーの配列
            $arrTypesKeys = array(
                'weekly' => array(
                    'available_period_days'
                ),
                'biweeks' => array(
                    'available_period_days'
                ),
                'monthly_day' => array(
                    'available_period_days',
                    'available_period_weeks'
                ),
                'monthly_date' => array(
                    'available_period_dates'
                )
            );
            foreach($arrTypesKeys as $type => $arrKeys){
                foreach($arrKeys as $key){
                    //周期に設定されたキーが選択されていなかったら
                    if(!empty($arrFormParamList['available_period_types']['value'][$type]) && count(array_filter($arrFormParamList[$key]['value'])) == 0){
                        $objError->arrErr[$key] = sprintf($message, $arrFormParamList[$key]['disp_name']);
                    }
                }
            }
        }
        
        return $objError->arrErr;
    }
    
    /**
     * パラメーター情報の初期化。
     *
     * @param SC_FormParam_Ex $objFormParam SC_FormParamインスタンス
     */
    function lfInitFormParam(SC_FormParam_Ex &$objFormParam) {
        
        $objFormParam->addParam('次回定期オフセット', 'period_weekly_offset_days', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_biweekly_offset_days', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_monthly_day_offset_days', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_monthly_date_offset_days', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_weekly_offset', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_biweekly_offset', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_monthly_day_offset', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('次回定期オフセット', 'period_monthly_date_offset', STEXT_LEN, 'n', array('EXIST_CHECK','NUM_CHECK'));
        $objFormParam->addParam('有効週', 'available_period_weeks', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('有効曜日', 'available_period_days', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('有効日付', 'available_period_dates', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('有効周期', 'available_period_types', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('有効支払い方法', 'available_period_payments', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
    }
    
    /**
     * パラメーター情報をセットする。
     *
     * @param SC_FormParam_Ex $objFormParam SC_FormParamインスタンス
     * @param array $arrParams パラメータ
     */
    function lfSetFormParam(SC_FormParam_Ex &$objFormParam, $arrParams) {
        
        $objFormParam->setParam($arrParams);
        
        if(isset($arrParams['period_weekly_offset_days'])){
            $objFormParam->setValue('period_weekly_offset', $objFormParam->getValue('period_weekly_offset_days') * 24 * 60 * 60);
            $objFormParam->setValue('period_biweekly_offset', $objFormParam->getValue('period_biweekly_offset_days') * 24 * 60 * 60);
            $objFormParam->setValue('period_monthly_day_offset', $objFormParam->getValue('period_monthly_day_offset_days') * 24 * 60 * 60);
            $objFormParam->setValue('period_monthly_date_offset', $objFormParam->getValue('period_monthly_date_offset_days') * 24 * 60 * 60);
        }
        elseif(isset($arrParams['period_weekly_offset'])){
            $objFormParam->setValue('period_weekly_offset_days', $objFormParam->getValue('period_weekly_offset') / 24 / 60 / 60);
            $objFormParam->setValue('period_biweekly_offset_days', $objFormParam->getValue('period_biweekly_offset') / 24 / 60 / 60);
            $objFormParam->setValue('period_monthly_day_offset_days', $objFormParam->getValue('period_monthly_day_offset') / 24 / 60 / 60);
            $objFormParam->setValue('period_monthly_date_offset_days', $objFormParam->getValue('period_monthly_date_offset') / 24 / 60 / 60);
        }
        
        $objFormParam->convParam();
    }
    

    /**
     * ページデータを取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param integer $page_id ページID
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return array ページデータの配列
     */
    function getTplMainpage($file_path) {

        if (file_exists($file_path)) {
            $arrfileData = file_get_contents($file_path);
        }
        return $arrfileData;
    }
    
    /**
     * プラグイン情報を更新する
     * 
     * @param SC_FormParam_Ex $objFormParam SC_FormParamインスタンス
     */
    function lfUpdatePlugin(SC_FormParam_Ex &$objFormParam) {
        $arrData = $objFormParam->getHashArray();
        PeriodicalSale::saveNamedPluginInfo($arrData);
    }
}
?>
