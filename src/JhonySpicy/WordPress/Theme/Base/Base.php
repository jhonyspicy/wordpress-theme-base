<?php
namespace Jhonyspicy\Wordpress\Theme\Base;
use Jhonyspicy\Wordpress\Theme\Base\Lib\Widgets as Widgets;

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
								   'Widgets',
								   'MenuPage',
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

						if (class_exists($classPath)) {
							if ($dir == 'Widgets') {
								self::$classes[$dir][] = $classPath;
							} else {
								$obj = new $classPath();

								self::$classes[$dir][$className] = $obj;
							}
						}
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
	static private function add_hooks() {
		//投稿タイプ
		if (array_key_exists('PostType', self::$classes)) {
			foreach(self::$classes['PostType'] as $postType) {
				add_action('init', array($postType, 'register_post_type'));
			}
		}

		//ウィジェットの登録
		if (array_key_exists('Widgets', self::$classes)) {
			add_action('widgets_init', function () {
				Widgets::widgets_init();

				foreach(self::$classes['Widgets'] as $widget) {
					Widgets::register_widget($widget);
				}
			});
		}

		//メニューページ
		if (array_key_exists('MenuPage', self::$classes)) {
			foreach(self::$classes['MenuPage'] as $menuPage) {
				add_action('admin_menu', array($menuPage, 'admin_menu'));
				add_action('admin_init', array($menuPage, 'admin_init'));

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
			if (array_key_exists('MenuPage', self::$classes)) {
				foreach(self::$classes['MenuPage'] as $menuPage) {
					if ($menuPage->is_self()) {
						$menuPage->add_hooks();
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
			if (array_key_exists('Widgets', self::$classes)) {
				if (Widgets::is_self()) {
					Widgets::add_hooks();
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

	/**
	 * ポストタイプのオブジェクトを取得
	 *
	 * @param $post_type
	 * @return null
	 */
	static public function get_post_type_object($post_type) {
		if (array_key_exists('PostType', self::$classes) && array_key_exists($post_type, self::$classes['PostType'])) {
			return self::$classes['PostType'][$post_type];
		} else {
			return null;
		}
	}

	/**
	 * メニューページのオブジェクトを取得
	 *
	 * @param $menu_page
	 * @return null
	 */
	static public function get_menu_page_object($menu_page) {
		if (array_key_exists('MenuPage', self::$classes) && array_key_exists($menu_page, self::$classes['MenuPage'])) {
			return self::$classes['MenuPage'][$menu_page];
		} else {
			return null;
		}
	}

	/**
	 * ショートコードのオブジェクトを取得
	 *
	 * @param $short_code
	 * @return null
	 */
	static public function get_short_code_object($short_code) {
		if (array_key_exists('ShortCode', self::$classes) && array_key_exists($short_code, self::$classes['ShortCode'])) {
			return self::$classes['ShortCode'][$short_code];
		} else {
			return null;
		}
	}

	/**
	 * タクソノミーのオブジェクトを取得
	 *
	 * @param $taxonomy
	 * @return null
	 */
	static public function get_Taxonomy_object($taxonomy) {
		if (array_key_exists('Taxonomy', self::$classes) && array_key_exists($taxonomy, self::$classes['Taxonomy'])) {
			return self::$classes['Taxonomy'][$taxonomy];
		} else {
			return null;
		}
	}
}