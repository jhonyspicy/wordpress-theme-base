<?php
namespace Jhonyspicy\Wordpress\Theme\Base;
class ShortCode {
	static public $footerScript = '';
	static private $wp_footer = false;

	/**
	 * クラス名を取得する(名前空間込)
	 *
	 * @return string
	 */
	private function class_name() {
		return get_class($this);
	}

	/**
	 * 投稿タイプの名前(ID?)を取得する
	 *
	 * @return string
	 */
	public function name() {
		$v = explode('\\', $this->class_name());
		return strtolower(end($v));
	}

	/**
	 * ショートコードを登録する。
	 */
	static public function after_setup_theme() {
		$googleMaps = new ShortCode\GoogleMaps();
		$menu = new ShortCode\Menu();

		add_shortcode($googleMaps->name(), array($googleMaps, 'do_shortcode'));
		add_shortcode($menu->name(), array($menu, 'do_shortcode'));
	}

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

		add_action('wp_footer', array('ShortCode', 'wp_footer'));
	}

	/**
	 * フッターに蓄えてあるスクリプトを出力する
	 */
	static public function wp_footer() {
		echo self::$footerScript;
	}
}