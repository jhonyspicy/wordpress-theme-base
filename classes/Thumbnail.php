<?php
class Thumbnail {
	static $size_list = array(
		'_thumb_size' => array(
			'w'    => 200,
			'h'    => 300,
			'flag' => true),
	);

	/**
	 * アイキャッチが登録されていないものなら、
	 * ダミー画像を出力する
	 *
	 * @param $html
	 * @param $post_id
	 * @param $post_thumbnail_id
	 * @param $size
	 * @param $attr
	 * @return string
	 */
	static function post_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
		if (!$post_thumbnail_id && get_option('dummy_image')) {

			return self::get_attachment_image(get_option('dummy_image'), $size, $attr);
		}

		return $html;
	}

	/**
	 * アイキャッチ画像のsrcを取得する
	 *
	 * @param null $post_id
	 * @param string $size
	 *
	 * @return string
	 */
	static function get_attachment_image_src($post_id = null, $size = 'post-thumbnail') {
		$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );

		$src = '';
		if ( $post_thumbnail_id ) {
			$image = wp_get_attachment_image_src($post_thumbnail_id, $size, false);
			$src = $image[0];
		}

		/** @var $src string */

		return $src;
	}

	/**
	 * アイキャッチ画像のsrcをそのまま出力
	 *
	 * @param null $post_id
	 * @param string $size
	 */
	static function the_attachment_image_src($post_id = null, $size = 'post-thumbnail') {
		echo self::get_attachment_image_src($post_id, $size);
	}

	/**
	 * 画像のURLから各サイズの画像を取得する
	 *
	 * @param $url
	 * @param string $size
	 * @param string $attr
	 *
	 * @return mixed
	 */
	static function get_attachment_image($url, $size = 'post-thumbnail', $attr = '') {
		$html = wp_get_attachment_image(self::get_attachment_id($url), $size, false, $attr);

		return $html;
	}

	/**
	 * 画像のURLからIDを取得する
	 * @param $url
	 * @return int
	 */
	static function get_attachment_id($url) {
		global $wpdb;
		$sql = "SELECT ID FROM {$wpdb->posts} WHERE guid = %s";

		return (int)$wpdb->get_var($wpdb->prepare($sql, $url));
	}

	/**
	 * 各画像サイズの登録
	 */
	static function register_image_size() {
		set_post_thumbnail_size(86, 86, true); //何も指定しなかった時のデフォルトの大きさ

		foreach(self::$size_list as $key => $val) {
			add_image_size($key, $val['w'], $val['h'], $val['flag']);
		}
	}

	/**
	 * 指定サイズが数字の配列で、
	 * 登録されているものならば登録されているサイズ名に
	 *
	 * @param $size
	 * @return int|string
	 */
	static function post_thumbnail_size($size) {
		//$sizeが配列で登録されているサイズなら
		if (is_array($size)) {
			foreach(self::$size_list as $key => $val) {
				if ($size[0] == $val['w'] && $size[1] == $val['h']) {
					$size = $key;
					break;
				}
			}
		}

		return $size;
	}

	/**
	 * 必要なフックを登録する
	 */
	static public function add_hooks() {
		add_action('after_setup_theme', array('Thumbnail', 'register_image_size'));
		add_action('post_thumbnail_size', array('Thumbnail', 'post_thumbnail_size'));
		add_action('post_thumbnail_html', array('Thumbnail', 'post_thumbnail_html'), 10, 5);
	}
}
