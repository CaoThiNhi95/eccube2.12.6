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

class plugin_info{
    
    static $PLUGIN_CODE       = 'PeriodicalSale';
    static $PLUGIN_NAME       = '定期販売プラグイン';
    static $CLASS_NAME        = 'PeriodicalSale';
    static $PLUGIN_VERSION    = '1.1.fix2';
    static $COMPLIANT_VERSION = '2.12.0';
    static $AUTHOR            = 'DAISY inc.';
    static $DESCRIPTION       = '商品の定期販売機能を追加するプラグインです。';
    static $PLUGIN_SITE_URL    = 'http://www.ec-cube.net/owners/index.php';
    static $AUTHOR_SITE_URL    = 'http://www.daisy.link/ec-cube/products/about.php';
    static $HOOK_POINTS       = array(
        array('prefilterTransform', 'prefilterTransform'),
        array('LC_Page_Admin_Products_Product_action_after', 'LC_Page_Admin_Products_Product_action_after'),
        array('LC_Page_Admin_Order_Mail_action_after', 'LC_Page_Admin_Order_Mail_action_after'),
        array('LC_Page_Shopping_Deliv_action_after', 'LC_Page_Shopping_Deliv_action_after'),
        array('LC_Page_Shopping_action_after', 'LC_Page_Shopping_action_after'),
        array('LC_Page_Shopping_Payment_action_after', 'LC_Page_Shopping_Payment_action_after'),
        array('LC_Page_Shopping_Payment_action_confirm', 'LC_Page_Shopping_Payment_action_confirm'),
        array('LC_Page_Shopping_Confirm_action_after', 'LC_Page_Shopping_Confirm_action_after'),
        array('LC_Page_Shopping_Confirm_action_confirm', 'LC_Page_Shopping_Confirm_action_confirm'),
        array('LC_Page_Shopping_Deliv_action_before', 'LC_Page_Shopping_Deliv_action_before'),
        array('LC_Page_Shopping_Complete_action_before', 'LC_Page_Shopping_Complete_action_before'),
    );
}
?>