<?php
/* 
 * カテゴリ毎にコンテンツを設定する事ができます。
 */
class DetailFacebookComment extends SC_Plugin_Base {

    /**
     * コンストラクタ
     *
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        if(copy(PLUGIN_UPLOAD_REALDIR . "DetailFacebookComment/logo.png", PLUGIN_HTML_REALDIR . "DetailFacebookComment/logo.png") === false);
    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
    }
    
    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
        // nop
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
        // nop
    }
    
    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'DetailFacebookComment/templates/';
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_PC:
                // 商品詳細画面
                if (strpos($filename, 'products/detail.tpl') !== false) {
                    $objTransform->select('div.review_bloc')->insertBefore(file_get_contents($template_dir . 'detail_fb_comment.tpl'));
                }
                break;
            case DEVICE_TYPE_MOBILE:
            case DEVICE_TYPE_SMARTPHONE:
            case DEVICE_TYPE_ADMIN:
            default:
                break;
        }
        $source = $objTransform->getHTML();
    }
    

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     * 
     * @param SC_Helper_Plugin $objHelperPlugin 
     * @return void
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        // プラグイン設定情報をページに設定する
        $objHelperPlugin->addAction('LC_Page_Products_Detail_action_before', array($this, 'set_detail_fb_config'));
        // テンプレート差し込み
        $objHelperPlugin->addAction('prefilterTransform', array(&$this, 'prefilterTransform'), 1);
    }
    
    /**
     * プラグインの設定情報を取得し、ページにセットします.
     * 
     * @param type $objPage 
     */
    function set_detail_fb_config($objPage) {
        $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("DetailFacebookComment");
        $objPage->detail_fb_comment_app_id = $plugin['free_field1'];
        $objPage->detail_fb_comment_data_width = $plugin['free_field2'];
        $objPage->detail_fb_comment_data_num_post = $plugin['free_field3'];
    }
    

}

?>
