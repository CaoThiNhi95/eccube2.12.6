<?php
/*
 * ListSubComment
 * Copyright (C) 2013 INA Corporation. All Rights Reserved.
 * http://www.e-ina.co.jp/
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

/* 
 * カテゴリ毎にコンテンツを設定する事ができます。
 */
class ListSubComment extends SC_Plugin_Base
{
	/**
	 * コンストラクタ
	 * プラグイン情報(dtb_plugin)をメンバ変数をセットします.
	 * @param array $arrSelfInfo dtb_pluginの情報配列
	 * @return void
	 */
	public function __construct(array $arrSelfInfo)
	{
		parent::__construct($arrSelfInfo);
	}

	/**
	 * インストール時に実行される処理を記述します.
	 * @param array $arrPlugin dtb_pluginの情報配列
	 * @return void
	 */
	function install($arrPlugin)
	{
		mkdir(PLUGIN_HTML_REALDIR . "ListSubComment/media");
		SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . "ListSubComment/media/", PLUGIN_HTML_REALDIR . "ListSubComment/media/");
		copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] ."/logo.png", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] ."/logo.png");
	}

	/**
	 * 削除時に実行される処理を記述します.
	 * @param array $arrPlugin dtb_pluginの情報配列
	 * @return void
	 */
	function uninstall($arrPlugin)
	{
		SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "ListSubComment/media");
		SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "ListSubComment");
	}

	/**
	 * 有効にした際に実行される処理を記述します.
	 * @param array $arrPlugin dtb_pluginの情報配列
	 * @return void
	 */
	function enable($arrPlugin)
	{
		//nop
	}

	/**
	 * 無効にした際に実行される処理を記述します.
	 * @param array $arrPlugin dtb_pluginの情報配列
	 * @return void
	 */
	function disable($arrPlugin)
	{
		//nop
	}

	/**
	 * 処理の介入箇所とコールバック関数を設定
	 * registerはプラグインインスタンス生成時に実行されます
	 * 
	 * @param SC_Helper_Plugin $objHelperPlugin 
	 */
	function register(SC_Helper_Plugin $objHelperPlugin)
	{
		parent::register($objHelperPlugin,$priority);

		//ヘッダへの追加
		$template_dir = PLUGIN_UPLOAD_REALDIR . 'ListSubComment/templates/';
		$objHelperPlugin->setHeadNavi($template_dir . 'plg_listSubComment_header.tpl');
	}

	/**
	 * prefilterコールバック関数
	 * テンプレートの変更処理を行います.
	 *
	 * @param string &$source テンプレートのHTMLソース
	 * @param LC_Page_Ex $objPage ページオブジェクト
	 * @param string $filename テンプレートのファイル名
	 * @return void
	 */
	function prefilterTransform(&$source,LC_Page_Ex $objPage,$filename)
	{
		//SC_Helper_Transformのインスタンスを生成.
		$objTransform = new SC_Helper_Transform($source);

		//呼び出し元テンプレートを判定します.
		switch($objPage->arrPageLayout['device_type_id'])
		{
			case DEVICE_TYPE_MOBILE: //モバイル
			case DEVICE_TYPE_SMARTPHONE: //スマホ
				break;

			case DEVICE_TYPE_PC: //PC
				//商品一覧画面
				if (strpos($filename,"products/list.tpl") !== false)
				{
					//divタグのclass=form要素をプラグイン側で用意したテンプレートと置き換えます.
					$template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/templates/';
					$objTransform->select('div.listphoto')->replaceElement(file_get_contents($template_dir . 'listsubcomment_admin_products_list_subcomment_add.tpl'));
				}
				break;

			case DEVICE_TYPE_ADMIN: //管理画面
			default:
				break;
		}

		//変更を実行します
		$source = $objTransform->getHTML();
	}

	function loadClassFileChange(&$classname,&$classpath)
	{
		//変えたいクラス名でフィルタ,*_Exにフィルタ推奨
		//代替読み込みされるクラスファイルを用意
		if ($classname == 'SC_Product_Ex')
		{
			$classpath = PLUGIN_UPLOAD_REALDIR . "ListSubComment/SC_Product_Dev.php";
			//上で指定した代替読み込みされるファイル内のクラス名が、本来の読み込み先と違うクラス名の場合、$classname を変更するクラス名にする。
			$classname = 'SC_Product_Dev';
		}
	}
}
?>
