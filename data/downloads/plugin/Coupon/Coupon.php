<?php /* * Coupon
 */

/**
 * プラグインのメインクラス
 *
 * @package Coupon
 * @author SEED
 */

require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Payment.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Confirm.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/pages/shopping/plg_Coupon_LC_Page_Shopping_Complete.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/pages/admin/order/plg_Coupon_LC_Page_Admin_Order_Edit.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/pages/admin/order/plg_Coupon_LC_Page_Admin_Order_Mail.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/plg_Coupon_SQL.php';
require_once PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/plg_Coupon_Postgres.php';

class Coupon extends SC_Plugin_Base {


    // コンストラクタ
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

     /* {{{ インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     }}} */
    function install($arrPlugin) {

        //html,tplファイルの削除
        if(ADMIN_DIR){
            $admin_dir = ADMIN_DIR;
        }else{
            $admin_dir = "admin";
        }

        // 必要なファイルをコピーします.

        //ロゴ
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/logo.png', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/logo.png');

        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/config.php', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/config.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/logo.png', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");
        mkdir(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/media');
        SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] .'/media/', PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . '/media/');

        //html
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/admin/contents/plg_Coupon_coupon.php', HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_coupon.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/admin/contents/plg_Coupon_coupon_input.php', HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_coupon_input.php');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/html/admin/contents/plg_Coupon_product_select.php', HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_product_select.php');

        //tpl
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/template/admin/contents/plg_Coupon_coupon.tpl', SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/template/admin/contents/plg_Coupon_coupon_input.tpl', SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon_input.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/template/admin/contents/plg_Coupon_product_select.tpl', SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_product_select.tpl');
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . '/template/admin/contents/plg_Coupon_coupon_complete.tpl', SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon_complete.tpl');

        //テーブル作成
        self::createPlgTable();

    }

     /* {{{ アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     }}} */
    function uninstall($arrPlugin) {
        // メディアディレクトリ削除.
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] .  '/media');
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR .  $arrPlugin['plugin_code']);
        
        //html,tplファイルの削除
        if(ADMIN_DIR){
            $admin_dir = ADMIN_DIR;
        }else{
            $admin_dir = "admin";
        }
        
        //html,tplファイルの削除
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_coupon.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_coupon_input.php');
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . $admin_dir . '/contents/plg_Coupon_product_select.php');
        SC_Helper_FileManager_Ex::deleteFile(SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon.tpl');
        SC_Helper_FileManager_Ex::deleteFile(SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon_input.tpl');
        SC_Helper_FileManager_Ex::deleteFile(SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_product_select.tpl');
        SC_Helper_FileManager_Ex::deleteFile(SMARTY_TEMPLATES_REALDIR . 'admin/contents/plg_Coupon_coupon_complete.tpl');        
        
        //プラグイン本体
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code']);
        
        //テーブル削除
        self::deletePlgTable();
        
    }

    /* {{{ 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
    }}} */
    function enable($arrPlugin) {
        // nop
    }

    /* {{{ 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
    }}} */
    function disable($arrPlugin) {
        // nop
    }


    /* {{{ 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
    }}} */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        //テンプレートの変更
        $objHelperPlugin->addAction('prefilterTransform', array(&$this, 'prefilterTransform'));

        //SC_FormParamをフックする
        $objHelperPlugin->addAction('SC_FormParam_construct', array(&$this, 'addParam'));

        //SC_系クラスをフックする
        $objHelperPlugin->addAction('loadClassFileChange',array(&$this,'loadClassFileChange'));

        //クーポン入力画面
        $objHelperPlugin->addAction('LC_Page_Shopping_Payment_action_after', array(&$this, 'LC_PageShopping_Payment'));
        $objHelperPlugin->addAction('LC_Page_Shopping_Payment_action_confirm', array(&$this, 'LC_PageShopping_Payment'));

        //クーポン利用確認画面（購入確認画面、購入実行時）
        $objHelperPlugin->addAction('LC_Page_Shopping_Confirm_action_after', array(&$this, 'LC_PageShopping_Confirm'));
        $objHelperPlugin->addAction('LC_Page_Shopping_Confirm_action_confirm', array(&$this, 'LC_PageShopping_Confirm'));
        
        //注文完了画面(メール送信をする）
        $objHelperPlugin->addAction('LC_Page_Shopping_Complete_action_before', array(&$this, 'LC_PageShopping_Complete'));

        //注文編集画面
        $objHelperPlugin->addAction('LC_Page_Admin_Order_Edit_action_after', array(&$this, 'LC_Page_Admin_Order_Edit'));

        //注文編集画面からのメール送信
        $objHelperPlugin->addAction('LC_Page_Admin_Order_Mail_action_send', array(&$this, 'LC_Page_Admin_Order_Mail'));

    }

	//プラグイン用の新規テーブルの作成
	function createPlgTable(){
     
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        
        if(DB_TYPE=="mysql"){
            $sqlObj = new plg_Coupon_SQL();        
        }else{
            $sqlObj = new plg_Coupon_Postgres();        
        }
         
        //dtb_coupon        
        $sql = $sqlObj->create_dtb_coupon();
        $objQuery->query($sql);

        //dtb_coupon_products
        $sql = $sqlObj->create_dtb_coupon_products();
        $objQuery->query($sql);
        
        //dtb_coupon_used
        $sql = $sqlObj->create_dtb_coupon_used();
        $objQuery->query($sql);

        //mtb_coupon_enable
        $sql = $sqlObj->create_mtb_coupon_enable();
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_enable(0);
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_enable(1);
        $objQuery->query($sql);

        //mtb_coupon_discount_type
        $sql = $sqlObj->create_mtb_coupon_discount_type();
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_discount_type(0);
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_discount_type(1);
        $objQuery->query($sql);

        //mtb_coupon_target
        $sql = $sqlObj->create_mtb_coupon_target();
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_target(0);
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_target(1);
        $objQuery->query($sql);

        //mtb_coupon_count_limit
        $sql = $sqlObj->create_mtb_coupon_count_limit();
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_count_limit(0);
        $objQuery->query($sql);
        $sql = $sqlObj->insert_mtb_coupon_count_limit(1);
        $objQuery->query($sql);
        
        //dtb_order_temp
        $sql = $sqlObj->alter_dtb_order_temp();
        $objQuery->query($sql);
        
        //dtb_order
        $sql = $sqlObj->alter_dtb_order();
        $objQuery->query($sql);
        
        //indexを追加する
        if(DB_TYPE=="pgsql"){
            $sqlObj->addIndex($objQuery);
        }
	}
	
	//プラグイン用テーブルの削除
	function deletePlgTable(){
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        if(DB_TYPE=="mysql"){
            $sqlObj = new plg_Coupon_SQL();
        }else{
            $sqlObj = new plg_Coupon_Postgres();
            
            $sql = $sqlObj->deleteSeq();
            $objQuery->query($sql);
        }
        
        //テーブルの削除
        $sql = $sqlObj->deleteTable();	
        $objQuery->query($sql);
        
        //カラムの削除
        $sql = $sqlObj->deleteColumn_dtb_order_temp();
        $objQuery->query($sql);
        $sql = $sqlObj->deleteColumn_dtb_order();
        $objQuery->query($sql);
	}

	/**
	*  SC_FormParamをフックする(パラメータの追加)
	**/
	function addParam($class_name,$param){
	    if(strpos($class_name,'LC_Page_Shopping_Payment')!==false){
	        $param->addParam("クーポン使用", "coupon_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');
            $param->addParam("クーポンコード", "coupon_id_name", 20, "n", array("MAX_LENGTH_CHECK"));
	    }

	    if(strpos($class_name,'LC_Page_Admin_Order_Edit')!==false){
	    	$param->addParam("クーポンによる割引額", "coupon_discount_price");
	    }

	}

	/**
	 * SC_系クラスをフックする
	 */
	function loadClassFileChange(&$classname,&$classpath){
		if($classname == 'SC_Fpdf_Ex'){
			$classpath = PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/SC_MyFpdf.php';
			$classname = "SC_MyFpdf";
		}

		if($classname == 'SC_Helper_Mail_Ex'){
			$classpath = PLUGIN_UPLOAD_REALDIR . 'Coupon/data/class/SC_Helper_MyMail.php';
			$classname = "SC_Helper_MyMail";
		}

	}

	/**
	*  クーポン入力画面
	**/
	function LC_PageShopping_Payment(LC_Page_Ex $objPage){
		$obj = new plg_Coupon_LC_Page_Shopping_Payment();
		$obj->exec($objPage);
	}

	/**
	*  クーポン利用確認画面（購入確認画面、購入実行時）
	**/
	function LC_PageShopping_Confirm(LC_Page_Ex $objPage){
		$obj = new plg_Coupon_LC_Page_Shopping_Confirm();
		$obj->exec($objPage);
	}

	/**
	*  購入完了画面(メール送信する)
	**/
	function LC_PageShopping_Complete(LC_Page_Ex $objPage){
		$obj = new plg_Coupon_LC_Page_Shopping_Complete();
		$obj->exec($objPage);
	}

	/**
	*  注文編集画面
	**/
	function LC_Page_Admin_Order_Edit(LC_Page_Ex $objPage){
		$obj = new plg_Coupon_LC_Page_Admin_Order_Edit();
		$obj->exec($objPage);
	}

	/**
	 *  注文編集画面からのメール送信
	 **/
	function LC_Page_Admin_Order_Mail(LC_Page_Ex $objPage){
		$obj = new plg_Coupon_LC_Page_Admin_Order_Mail();
		$obj->exec($objPage);
	}


    /* {{{ プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
    }}} */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'Coupon/template/';


        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_PC:

                //クーポン利用画面
                if (strpos($filename, 'shopping/payment.tpl') !== false) {
                    $objTransform->select('.btn_area')->insertBefore(file_get_contents($template_dir . 'default/shopping/plg_Coupon_payment.tpl'));
                }
                //クーポン利用確認画面(購入確認画面)
                if (strpos($filename, 'shopping/confirm.tpl') !== false) {
                	//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
                    $objTransform->select('table tr',4)->insertBefore(file_get_contents($template_dir . 'default/shopping/plg_Coupon_confirm.tpl'));
                }
                //購入履歴画面
                if (strpos($filename, 'mypage/history.tpl') !== false) {
                	//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
                	$objTransform->select('table tr',5)->insertBefore(file_get_contents($template_dir . 'default/mypage/plg_Coupon_history.tpl'));
                }

                break;

            case DEVICE_TYPE_MOBILE:
               	//クーポン利用画面
            	if (strpos($filename, 'shopping/payment.tpl') !== false) {
            		$objTransform->select('textarea')->insertAfter(file_get_contents($template_dir . 'mobile/shopping/plg_Coupon_payment.tpl'));
            	}

            	//クーポン利用確認画面(購入確認画面)
            	if (strpos($filename, 'shopping/confirm.tpl') !== false) {
            		//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
            		$objTransform->select("form br",12)->insertBefore(file_get_contents($template_dir . 'mobile/shopping/plg_Coupon_confirm.tpl'));
            	}
            	
            	//購入履歴画面
            	if (strpos($filename, 'mypage/history.tpl') !== false) {
            		//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
            		$objTransform->select("br",18)->insertBefore(file_get_contents($template_dir . 'mobile/mypage/plg_Coupon_history.tpl'));
            	}
            	
                break;

            case DEVICE_TYPE_SMARTPHONE:

                //クーポン利用画面
                if (strpos($filename, 'shopping/payment.tpl') !== false) {
                    $objTransform->select('.btn_area')->insertBefore(file_get_contents($template_dir . 'sphone/shopping/plg_Coupon_payment.tpl'));
                    //なぜかテンプレートが崩れるので応急処置
                    $objTransform->select('#form1 .btn_area')->insertAfter('</form></section>');
                }
                
                //クーポン利用確認画面(購入確認画面)
                if (strpos($filename, 'shopping/confirm.tpl') !== false) {
                	//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
                	$objTransform->select('.result_area li',2)->insertBefore(file_get_contents($template_dir . 'sphone/shopping/plg_Coupon_confirm.tpl'));
                }
                
                //購入履歴画面
                if (strpos($filename, 'mypage/history.tpl') !== false) {
                	//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
                	$objTransform->select('.total_area div',3)->insertBefore(file_get_contents($template_dir . 'sphone/mypage/plg_Coupon_history.tpl'));
                }
                break;
            
            
            case DEVICE_TYPE_ADMIN:
            default:

            	//グローバルナビに追加
            	if(strpos($filename, 'contents/subnavi.tpl') !== false){
            		$objTransform->select('li',2)->insertAfter(file_get_contents($template_dir . 'admin/plg_Coupon_subnavi.tpl'));
            	}

            	//注文編集画面(受注管理)
                if (strpos($filename, 'order/edit.tpl') !== false) {
                	//indexの指定はカスタマイズしてある場合は要注意（デフォルトのec-cubeに対応)
                	if (strstr(ECCUBE_VERSION, '2.12')){
                	    $objTransform->select('#order-edit-products tr',4)->insertBefore(file_get_contents($template_dir . 'admin/order/plg_Coupon_edit.tpl'));
                	}else{
                        $objTransform->select('.order-edit-products tr',4)->insertBefore(file_get_contents($template_dir . 'admin/order/plg_Coupon_edit.tpl'));
                	}
                }

            	break;
        }

        $source = $objTransform->getHTML();
    }
}
