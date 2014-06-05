<?php
namespace Jhonyspicy\Wordpress\Theme\Base;

class Base {
	/**
	 * それぞれサポートする対象のクラスを格納する。
	 * Base::initialize()後にセットされる。
	 * @var
	 */
	static $classes = array();

	/**
	 * サポート対象のディレクトリ一覧
	 * @var array
	 */
	static $supportDirList = array('PostType',
								   'ShortCode',
								   'Taxonomy',
//								   'Widgets',
	);

	/**
	 * 初期化
	 */
	static public function initialize() {
		foreach (self::$supportDirList as $dir) {
			$dirPath = get_stylesheet_directory() . '/classes/' . $dir;
			if (is_dir($dirPath) && $handle = opendir($dirPath)) {

				while (false !== ($file = readdir($handle))) {
					if (is_file(get_stylesheet_directory() . '/classes/' . $dir . '/' . $file)) {
						$className = str_replace('.php', '', $file);
						$classPath = '\\' . $dir . '\\' . $className;
						$obj       = new $classPath();

						self::$classes[$dir][$className] = $obj;
					}
				}

				closedir($handle);
			}
		}

		self::add_hooks();
	}

	/**
	 * フックの登録
	 */
	static public function add_hooks() {
		//投稿タイプ
		if (array_key_exists('PostType', self::$classes)) {
			foreach(self::$classes['PostType'] as $postType) {
				add_action('init', array($postType, 'register_post_type'));
			}
		}

		//タクソノミー
		if (array_key_exists('Taxonomy', self::$classes)) {
			foreach(self::$classes['Taxonomy'] as $taxonomy) {
				add_action('init', array($taxonomy, 'register_taxonomy'));
			}
		}

		//ショートコード
		if (array_key_exists('ShortCode', self::$classes)) {
			foreach(self::$classes['ShortCode'] as $shortCode) {
				add_shortcode($shortCode->name(), array($shortCode, 'do_shortcode'));
			}
		}

		//管理画面で必要になるフックを登録する
		add_action('current_screen', function () {
			if (array_key_exists('PostType', self::$classes)) {
				foreach(self::$classes['PostType'] as $postType) {
					if ($postType->is_self()) {
						$postType->add_hooks();
					}
				}
			}
			if (array_key_exists('Taxonomy', self::$classes)) {
				foreach(self::$classes['Taxonomy'] as $taxonomy) {
					if ($taxonomy->is_self()) {
						$taxonomy->add_hooks();
					}
				}
			}
		});

		//Ajaxなどの通信でどの画面を出力する予定なのかわからないフック
		if (array_key_exists('Taxonomy', self::$classes)) {
			foreach(self::$classes['Taxonomy'] as $taxonomy) {
				$taxonomy->add_special_hooks();
			}
		}
	}
}