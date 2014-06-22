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
	static public function initialize($args = array()) {
		$args = wp_parse_args($args, array(
			'classes_dir' => 'classes',
		));

		foreach (self::$supportDirList as $dir) {
			//まずは親テーマディレクトリを漁る。
			if (is_dir($dirPath = get_template_directory() . "/{$args['classes_dir']}/" . $dir)) {
				self::walkDirectory($dirPath);
			}

			//子テーマがあればで上書きする。
			if (get_template_directory() != get_stylesheet_directory()) {
				if (is_dir($dirPath = get_stylesheet_directory() . '/classes/' . $dir)) {
					self::walkDirectory($dirPath);
				}
			}
		}

		self::add_hooks();
	}

	static private function walkDirectory($dirPath) {
		$dir = str_replace(dirname($dirPath), '', $dirPath);
		$dir = trim($dir, '/');

		if ($handle = opendir($dirPath)) {
			while (false !== ($file = readdir($handle))) {
				$file_path = $dirPath . '/' . $file;
				if (is_file($file_path)) {
					$className = str_replace('.php', '', $file);
					$classPath = '\\' . $dir . '\\' . $className;

					require_once($dirPath . '/' . $file);

					if (class_exists($classPath)) {
						self::set_object($dir, $classPath, $className);
					}
				}
			}
		}

		closedir($handle);
	}

	static private function set_object($dir, $classPath, $className) {
		if ($dir == 'Widgets') {
			self::$classes[$dir][] = $classPath;
		} else {
			$obj = new $classPath();

			self::$classes[$dir][$className] = $obj;
		}
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
		return self::get_object('PostType', $post_type);
	}

	/**
	 * メニューページのオブジェクトを取得
	 *
	 * @param $menu_page
	 * @return null
	 */
	static public function get_menu_page_object($menu_page) {
		return self::get_object('MenuPage', $menu_page);
	}

	/**
	 * ショートコードのオブジェクトを取得
	 *
	 * @param $short_code
	 * @return null
	 */
	static public function get_short_code_object($short_code) {
		return self::get_object('ShortCode', $short_code);
	}

	/**
	 * タクソノミーのオブジェクトを取得
	 *
	 * @param $taxonomy
	 * @return null
	 */
	static public function get_Taxonomy_object($taxonomy) {
		return self::get_object('Taxonomy', $taxonomy);
	}

	static public function get_object($dir, $file) {
		if (array_key_exists($dir, self::$classes) && array_key_exists($file, self::$classes[$dir])) {
			return self::$classes[$dir][$file];
		} else {
			return null;
		}
	}
}