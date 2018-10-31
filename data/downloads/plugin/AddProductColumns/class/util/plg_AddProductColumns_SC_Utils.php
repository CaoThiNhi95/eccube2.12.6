<?php
/*
 * AddProductColumns
 * Copyright(c) 2015 DAISY Inc. All Rights Reserved.
 *
 * http://www.daisy.link/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * AddProductColumns のユーティリティクラス
 *
 * @package Util
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_AddProductColumns_SC_Utils {

    /**
     * 存在するファイルのみrequire_onceする。
     * パスの配列も指定可能だが、その場合存在しないパスはスキップして、残りのファイルを読み込む。
     * 
     * @param array|string $arrPaths require_onceするファイルのパス
     * @return boolean 全てのパスがrequire_onceされたら、trueを返す
     */
    static function requireOnceIfExists($arrPaths){
        
        $result = true;
        $error = false;
        
        if(is_array($arrPaths)){
            
            foreach($arrPaths as $path){
                
                if(!self::requireOnceIfExists($path)){
                    
                    $error = true;
                }
            }
            
            if($error){
                
                $result = false;
            }
        }
        else{
            
            $path = $arrPaths;
            
            if(file_exists($path)){
                
                require_once $path;
            }
            else{
                
                $result = false;
            }
        }
        
        return $result;
    }
    
    /**
     * ディレクトリとその内容を、再帰的にコピーする。
     * 
     * @param string $src コピー元パス
     * @param string $dst コピー先パス
     * @param boolean $delete_first trueの場合、$dstを削除してからコピーする。
     */
    static function copy_recursive($src, $dst, $delete_first = false) {

        if ($delete_first && file_exists($dst)) {
            
            rmdir_recursive($dst);
        }

        if (is_dir($src)) {
            
            if(!file_exists($dst) || !is_dir($dst)){
                
                mkdir($dst);
            }

            $files = scandir($src);
            foreach ($files as $file) {
                if (($file != ".") && ($file != "..")) {
                    self::copy_recursive("$src/$file", "$dst/$file");
                }
            }
        }
        else if (file_exists($src)) {
            
            copy($src, $dst);
        }
    }
}
