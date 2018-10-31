<?php

require_once PLUGIN_UPLOAD_REALDIR . 'ImageImagick/LC_Page_Plugin_ImageImagick_Config.php';

$objPage = new LC_Page_Plugin_ImageImagick_Config();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();

