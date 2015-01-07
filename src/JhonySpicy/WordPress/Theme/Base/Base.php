<?php
namespace Jhonyspicy\Wordpress\Theme\Base;

use Lib\Widgets;
use Lib\MenuPage;
use Lib\PostType;
use Lib\ShortCode;
use Lib\Taxonomy;

use \Composer\Autoload\ClassMapGenerator;

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
	static private $based_classes = array(
		'PostType',
		'ShortCode',
		'Taxonomy',
		'MenuPage',
		'Widgets',
	);

	static private $name_space = 'Jhonyspicy\\Wordpress\\Theme\\Base\\Lib\\';

	/**
	 * 初期化
	 */
	static public function initialize($args = array()) {
		$args = wp_parse_args( $args, array(
			'PostType'  => 'classes/PostType',
			'ShortCode' => 'classes/ShortCode',
			'Taxonomy'  => 'classes/Taxonomy',
			'MenuPage'  => 'classes/MenuPage',
			'Widgets'   => 'classes/Widgets',
		) );

		$class_maps = array();
		$base_dir   = get_template_directory();
		foreach ($args as $type => $dir) {
			if (is_dir($dir_path = "{$base_dir}/{$dir}")) {
				$class_maps[$type] = ClassMapGenerator::createMap($dir_path);
			}
		}
		if (is_child_theme()) {
			$base_dir = get_stylesheet_directory();
			foreach ($args as $type => $dir) {
				if (is_dir($dir_path = "{$base_dir}/{$dir}")) {
					$class_maps[$type] = ClassMapGenerator::createMap($dir_path);
				}
			}
		}

		self::set_object($class_maps);
		self::add_hooks();
	}

	/**
	 * ポストタイプを登録する
	 *
	 * @param $type
	 * @param $dir
	 */
	private static function set_object($class_maps) {
		foreach($class_maps as $type => $class_map) {
			if ($type == 'Widgets') {
				foreach($class_map as $class => $path) {
					self::$classes[$type][] = $class;
				}
			} else {
				foreach($class_map as $class => $path) {
					$obj = new $class();
					self::$classes[$type][$class] = $obj;
				}
			}
		}
	}

	/**
	 * フックの登録
	 */
	private static function add_hooks() {
		$self = __CLASS__;
		$widgetsName = self::$name_space . 'Widgets';

		//投稿タイプ
		if (array_key_exists('PostType', self::$classes)) {
			foreach(self::$classes['PostType'] as $postType) {
				add_action('init', array($postType, 'register_post_type'));
			}
		}

		//ウィジェットの登録
		if (array_key_exists('Widgets', self::$classes)) {
			add_action('widgets_init', function () use ($self, $widgetsName) {
				$widgetsName::widgets_init();

				foreach($self::$classes['Widgets'] as $widget) {
					$widgetsName::register_widget($widget);
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
		add_action('current_screen', function () use ($self, $widgetsName) {
			if (array_key_exists('PostType', $self::$classes)) {
				foreach($self::$classes['PostType'] as $postType) {
					if ($postType->is_self()) {
						$postType->add_hooks();
					}
				}
			}
			if (array_key_exists('MenuPage', $self::$classes)) {
				foreach($self::$classes['MenuPage'] as $menuPage) {
					if ($menuPage->is_self()) {
						$menuPage->add_hooks();
					}
				}
			}
			if (array_key_exists('Taxonomy', $self::$classes)) {
				foreach($self::$classes['Taxonomy'] as $taxonomy) {
					if ($taxonomy->is_self()) {
						$taxonomy->add_hooks();
					}
				}
			}
			if (array_key_exists('Widgets', $self::$classes)) {
				if ($widgetsName::is_self()) {
					$widgetsName::add_hooks();
				}
			}
		});

		//Ajaxなどの通信でどの画面を出力する予定なのかわからないフック
		if (array_key_exists('PostType', self::$classes)) {
			foreach(self::$classes['PostType'] as $postType) {
				$postType->add_special_hooks();
			}
		}
		if (array_key_exists('MenuPage', self::$classes)) {
			foreach(self::$classes['MenuPage'] as $menuPage) {
				$menuPage->add_special_hooks();
			}
		}
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
		$type = 'PostType';

		return self::get_object($type, $type . '\\' . $post_type);
	}

	/**
	 * メニューページのオブジェクトを取得
	 *
	 * @param $menu_page
	 * @return null
	 */
	static public function get_menu_page_object($menu_page) {
		$type = 'MenuPage';

		return self::get_object($type, $type . '\\' . $menu_page);
	}

	/**
	 * ショートコードのオブジェクトを取得
	 *
	 * @param $short_code
	 * @return null
	 */
	static public function get_short_code_object($short_code) {
		$type = 'ShortCode';

		return self::get_object($type, $type . '\\' . $short_code);
	}

	/**
	 * タクソノミーのオブジェクトを取得
	 *
	 * @param $taxonomy
	 * @return null
	 */
	static public function get_Taxonomy_object($taxonomy) {
		$type = 'Taxonomy';

		return self::get_object($type, $type . '\\' . $taxonomy);
	}

	/**
	 * クラス名からオブジェクトを取得
	 *
	 * @param $type
	 * @param $file
	 *
	 * @return null
	 */
	static public function get_object($type, $file) {
		if (array_key_exists($type, self::$classes) && array_key_exists($file, self::$classes[$type])) {
			return self::$classes[$type][$file];
		} else {
			return null;
		}
	}

	/**
	 * ポストタイプのオブジェクトを取得
	 *
	 * @param $name
	 *
	 * @return null
	 */
	static public function get_post_type_by_name($name) {
		$type = 'PostType';

		return self::get_object_by_name($type, $name);
	}

	/**
	 * スラッグ名(？)からオブジェクトを取得
	 *
	 * @param $type
	 * @param $name
	 *
	 * @return null
	 */
	static public function get_object_by_name($type, $name) {
		if (array_key_exists($type, self::$classes)) {
			foreach(self::$classes[$type] as $post_type) {
				if ($post_type->name() == $name) {
					return $post_type;
				}
			}
		}

		return null;
	}
}
