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

require_once CLASS_EX_REALDIR . 'page_extends/shopping/LC_Page_Shopping_Deliv_Ex.php';

/**
 * 定期プラグイン のページクラス.
 *
 * @package PeriodicalSale
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_PeriodicalSale_LC_Page_Shopping_Deliv extends LC_Page_Shopping_Deliv_Ex {
    
    /**
     * actionから抜ける 
     */
    function actionExit(){
        SC_Response_Ex::actionExit();
    }
    
    /**
     * リダイレクトする
     * @param type $location
     * @param type $arrQueryString
     * @param type $inheritQueryString
     * @param type $useSsl 
     */
    function sendRedirect($location, $arrQueryString = array(), $inheritQueryString = false, $useSsl = null){
        SC_Response_Ex::sendRedirect($location, $arrQueryString, $inheritQueryString, $useSsl);
    }
}