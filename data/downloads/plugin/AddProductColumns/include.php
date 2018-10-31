<?php
require_once(PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/plg_AddProductColumns_ClassAutoloader.php');
spl_autoload_register(array('plg_AddProductColumns_ClassAutoloader', 'autoload'));

define('COLUMN_TYPE_TEXT', 'text');
define('COLUMN_TYPE_TEXTAREA', 'textarea');
/** 接頭詞 (ProductsAddProductColumnの略) */
define('PAPC_PREFIX', 'papc');

if(isset($_SESSION['Message.AddProductColumns.Updated']) && isset($objPage)){
    
    $objPage->tpl_onload .= sprintf('alert("%s");', $_SESSION['Message.AddProductColumns.Updated']);
    unset($_SESSION['Message.AddProductColumns.Updated']);
}

$objSession = new plg_AddProductColumns_SC_Helper_Session_Ex();
$objSession->pluginAdminAuthorization();