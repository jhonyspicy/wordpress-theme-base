<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;
use \Jhonyspicy\Wordpress\Theme\Base\Super as Super;

class ShortCode extends Super {
	static public $footerScript = '';
	static private $wp_footer = false;

	/**
	 * 定義されていないことは無いとは思うけど、
	 * とりあえず、なくてもエラーにならないように定義しておく
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function do_shortcode($atts, $content) {
		return do_shortcode($content);
	}

	/**
	 * フッターにスクリプト出力するスクリプトを登録する
	 *
	 * @param $script
	 */
	static public function register_script($script) {
		self::$footerScript .= $script;

		if (self::$wp_footer) {
			return;
		}

		self::$wp_footer = true;

		add_action('wp_footer', array('Jhonyspicy\Wordpress\Theme\Base\Lib\ShortCode', 'wp_footer'));
	}

	/**
	 * フッターに蓄えてあるスクリプトを出力する
	 */
	static public function wp_footer() {
		echo self::$footerScript;
	}
}