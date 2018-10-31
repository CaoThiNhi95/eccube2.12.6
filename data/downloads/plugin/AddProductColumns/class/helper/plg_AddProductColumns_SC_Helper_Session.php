<?php
/*
 * AddProductColumns
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

/**
 * AddProductColumns のヘルパークラス
 *
 * @package AddProductColumns
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_AddProductColumns_SC_Helper_Session extends SC_Helper_Session_Ex {

    /**
     * 管理画面の認証を行う.
     *
     * mtb_auth_excludes へ登録されたページは, 認証を除外する.
     *
     * @return void
     */
    public static function pluginAdminAuthorization() {
        if (($script_path = realpath($_SERVER['SCRIPT_FILENAME'])) !== FALSE) {
            $arrScriptPath = explode('/', str_replace('\\', '/', $script_path));
            $arrAdminPath = explode('/', str_replace('\\', '/', substr(HTML_REALDIR . 'plugin/AddProductColumns/admin/', 0, -1)));
            $arrDiff = array_diff_assoc($arrAdminPath, $arrScriptPath);
            if (in_array('admin', $arrDiff)) {
                return;
            } else {
                $masterData = new SC_DB_MasterData_Ex();
                $arrExcludes = $masterData->getMasterData('mtb_auth_excludes');
                foreach ($arrExcludes as $exclude) {
                    $arrExcludesPath = explode('/', str_replace('\\', '/', HTML_REALDIR . ADMIN_DIR . $exclude));
                    $arrDiff = array_diff_assoc($arrExcludesPath, $arrScriptPath);
                    if (count($arrDiff) === 0) {
                        return;
                    }
                }
            }
        }
        SC_Utils_Ex::sfIsSuccess(new SC_Session_Ex());
    }
}
