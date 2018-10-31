<?php
/**
 * ブラウザの判別
 *
 * @version 1.1.1
 */
class BrowserType {
	/**
	 * ブラウザのタイプを取得する
	 *
	 * @return int 0ならモダンブラウザ、1ならバージョンが10未満のIE、2はそのほかのレガシーブラウザ
	 */
	public static function getBrowserType() {
		if( stristr($_SERVER['HTTP_USER_AGENT'], 'opera') ||
		    stristr($_SERVER['HTTP_USER_AGENT'], 'safari') ||
		    stristr($_SERVER['HTTP_USER_AGENT'], 'chrome') ||
		    stristr($_SERVER['HTTP_USER_AGENT'], 'firefox') ) {
			$type = 0;
		} else if ( stristr($_SERVER['HTTP_USER_AGENT'], "msie") ) {
			if( preg_match('/msie ?[5-9]{1}[ \.]?/i', $_SERVER['HTTP_USER_AGENT']) ) {
				$type = 1;
			} else {
				$type = 0;
			}
		} else if( ! empty($_SERVER['HTTP_ACCEPT']) ) {
			if( strpos('application/xml', $_SERVER['HTTP_ACCEPT']) ||
			    strpos('application/xhtml+xml', $_SERVER['HTTP_ACCEPT']) ) {
				$type = 0;
			} else {
				$type = 2;
			}
		} else {
			$type = 2;
		}

		return (int) $type;
	}

	/**
	 * モダンブラウザかどうかの判定
	 *
	 * @return bool モダンブラウザならtrue、そうでなければfalse
	 */
	public static function isModernBrowser() {
		return (bool) (self::getBrowserType() === 0);
	}

	/**
	 * レガシーブラウザかどうかの判定
	 *
	 * @return bool レガシーブラウザならtrue、そうでなければfalse
	 */
	public static function isLegacyBrowser() {
		return (bool) (self::getBrowserType() > 0);
	}

	/**
	 * versionが10未満のIEかどうかの判定
	 *
	 * @return bool versionが10未満のIEならtrue、そうでなければfalse
	 */
	public static function isOldIe() {
		return (bool) (self::getBrowserType() === 1);
	}

	/**
	 * IEのバージョンを整数値で取得する
	 *
	 * @return int IEならば1以上の整数値、そうでなければ0
	 */
	public static function getIeVer() {
		if ( self::getBrowserType() !== 1 ) {
			$ie_ver = 0;
		} else {
			preg_match('/msie ?([0-9]+)/i', $_SERVER['HTTP_USER_AGENT'], $ie_ver);
			$ie_ver = $ie_ver[1];
		}

		return (int) $ie_ver;
	}

	/**
	 * スマートフォンやタブレットなどのモバイル端末かどうかを判定する
	 *
	 * @return bool モバイル端末ならばtrue、そうでなければfalse
	 * @link http://core.trac.wordpress.org/browser/tags/3.5.1/wp-includes/vars.php
	 */
	public static function isMobile() {
		if ( self::isLegacyBrowser() ) {
			$is_mobile = false;
		} else if ( stristr($_SERVER['HTTP_USER_AGENT'], 'mobile') ||
		            stristr($_SERVER['HTTP_USER_AGENT'], 'android') ||
		            stristr($_SERVER['HTTP_USER_AGENT'], 'silk/') ||
		            stristr($_SERVER['HTTP_USER_AGENT'], 'kindle') ||
		            stristr($_SERVER['HTTP_USER_AGENT'], 'blackberry') ||
		            stristr($_SERVER['HTTP_USER_AGENT'], 'opera mini') ||
		            stristr($_SERVER['HTTP_USER_AGENT'], 'opera mobi') ) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return (bool) $is_mobile;
	}
}
?>