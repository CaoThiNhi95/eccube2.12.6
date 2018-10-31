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
 * AddProductColumns プラグインのクラス.
 *
 * @package AddProductColumns
 * @author DAISY CO.,LTD.
 * @version $
 */
class plg_AddProductColumns_ClassAutoloader {
    
    /**
     * プラグインクラスファイルのautoload関係。
     * 
     * @param string $class 
     */
    public static function autoload($class){
        
        $arrClassNamePart = explode('_', $class);
        $is_plg = $arrClassNamePart[0] === 'plg' && $arrClassNamePart[1] === 'AddProductColumns';
        $arrClassNamePart = array_values(array_slice($arrClassNamePart, 2));
        $is_ex = end($arrClassNamePart) === 'Ex';
        $is_page = $arrClassNamePart[0] === 'LC' && $arrClassNamePart[1] === 'Page';
        $count = count($arrClassNamePart);
        $classpath = PLUGIN_UPLOAD_REALDIR . 'AddProductColumns/' . ($is_ex ? 'class_ex/' : 'class/');
        
        if($is_plg){
            
            // ページクラスだったら
            if($is_page){
                
                $classpath .= $is_ex ? 'pages_ex/' : 'pages/';
                $length1 = $is_ex ? -1 : 0;
                $length2 = $is_ex ? -2 : 0;
                $temp_classpath1 = str_replace('//', '/', $classpath . strtolower(implode('/', array_slice($arrClassNamePart, 2, $length1))) . '/');
                $temp_classpath2 = str_replace('//', '/', $classpath . strtolower(implode('/', array_slice($arrClassNamePart, 2, $length2))) . '/');
                
                // index.php系のパス
                if(file_exists($temp_classpath1)){
                    
                    $classpath = $temp_classpath1;
                }
                // 通常のパス
                elseif(file_exists($temp_classpath2)){
                    
                    $classpath = $temp_classpath2;
                }
                else{
                    
                    $classpath = '';
                }
            }
            // その他のクラスだったら
            else{
            
                if(($arrClassNamePart[0] === 'SC' || $arrClassNamePart[0] === 'GC') && $arrClassNamePart[1] === 'Utils'){

                    $classpath .= $is_ex ? 'util_ex/' : 'util/';
                }
                elseif($arrClassNamePart[0] === 'SC' && $is_ex === true && $count >= 4){

                    $arrClassNamePartTemp = $arrClassNamePart;
                    // FIXME クラスファイルのディレクトリ命名が変。変な現状に合わせて強引な処理をしてる。
                    // らしいけど、プラグイン側ではよくわからないのでベタ移植。
                    $arrClassNamePartTemp[1] .= '_ex';
                    $classpath .= strtolower(implode('/', array_slice($arrClassNamePartTemp, 1, -2))) . '/';
                }
                elseif ($arrClassNamePart[0] === 'SC' && $is_ex === false && $count >= 3) {

                    $classpath .= strtolower(implode('/', array_slice($arrClassNamePart, 1, -1))) . '/';
                }
                elseif ($arrClassNamePart[0] === 'SC') {
                    // 処理なし
                }
                elseif ($arrClassNamePart[0] === 'Sql'){
                    
                    $classpath .= strtolower($arrClassNamePart[0] . ($is_ex ? '_ex' : '') . '/');
                }
                // PEAR用
                // FIXME トリッキー
                // ベタ移植。
                else {
                    $classpath = '';
                    $class = str_replace('_', '/', $class);
                }
            }
            /* ここまでに$classと$classpathを取得する。 */
            
            preg_match('/^(.+?)((_Ex){0,1})$/i', $class, $arrMatches);
            $version = self::getEccubeVersionIdentifier();
            $version_class = preg_replace('/^(.+?)((_Ex){0,1})$/i', sprintf('$1_%s$2', $version), $class);
            $version_classpath = $classpath . $version_class . '.php';
            $classpath .= $class . '.php';
            
            if(file_exists($version_classpath)){
                
                $base_class_str = str_replace(array('<?php', '?>'), '', file_get_contents($version_classpath));
                $version_class_str = preg_replace(sprintf('/(class +)%s( +extends +[a-zA-Z_\-]+ *{?)/i', $version_class), sprintf('$1%s$2', $class), $base_class_str, 1);
                eval($version_class_str);
            }
            elseif(file_exists($classpath)){
                
                require_once $classpath;
            }
        }
    }
    
    /**
     * EC-CUBEのバージョン識別子を取得する。
     * 
     * @return string 
     */
    public static function getEccubeVersionIdentifier(){
        
        $id = '';
        preg_match_all('/([0-9]+)/', ECCUBE_VERSION, $arrMatches);
        switch($arrMatches[0][0]){
            
            case 2:
                $id .= '2';
                switch($arrMatches[0][1]){
                
                    case 12:
                        $id .= '_12';
                        break;
                    
                    case 13:
                        $id .= '_13';
                        switch($arrMatches[0][2]){
                        
                            case 3:
                                $id .= '_3';
                                break;
                        }
                        break;
                }
                break;
        }
        return $id;
    }
}