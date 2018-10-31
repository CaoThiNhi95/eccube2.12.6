<?php

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

class LC_Page_Plugin_ImageImagick_Config extends LC_Page_Admin_Ex
{
    var $plugin_code = 'ImageImagick';
    var $default_compression_quality = 80;
    var $arrPluginInfo = array();
    var $arrForm = array();

    function init()
    {
        parent::init();

        // プラグイン情報を取得.
        $this->arrPluginInfo = SC_Plugin_Util_Ex::getPluginByPluginCode($this->plugin_code);

        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR
            . $this->plugin_code . '/templates/config.tpl';
        $this->tpl_subtitle = $this->arrPluginInfo['plugin_name'] . '設定画面';
    }

    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    function action()
    {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        $arrForm = array();

        switch ($this->getMode()) {
            case 'edit':
                $arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                // エラーなしの場合にはデータを更新
                if (count($this->arrErr) == 0) {
                    // データ更新
                    $this->arrErr = $this->updateData($arrForm);
                    if (count($this->arrErr) == 0) {
                        $this->tpl_onload = "alert('登録が完了しました。');";
                    }
                }
                break;

            default:
                $arrForm['compression_quality'] = $this->arrPluginInfo['free_field1'];

                // 初期値設定
                if (empty($arrForm['compression_quality'])) {
                    $arrForm['compression_quality'] = $this->default_compression_quality;
                }

                break;
        }

        $this->arrForm = $arrForm;
        $this->setTemplate($this->tpl_mainpage);
    }

    function lfInitParam(SC_FormParam_Ex &$objFormParam)
    {
        $objFormParam->addParam('圧縮品質', 'compression_quality', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    function updateData($arrData)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sqlval = array(
            'free_field1'   => $arrData['compression_quality'],
        );
        $where = 'plugin_code = ?';
        $arrVal = array(
            $this->plugin_code,
        );

        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where, $arrVal);
    }
}
