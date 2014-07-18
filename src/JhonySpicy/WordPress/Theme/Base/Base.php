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
	static private $basedClasses = array(
		'PostType',
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
			'directories' => array(
				'classes/PostType',
				'classes/ShortCode',
				'classes/Taxonomy',
				'classes/Widgets',
				'classes/MenuPage',
			),
		));

		$class_maps = array();
		$base_dir = get_template_directory();
		foreach ($args['directories'] as $dir) {
			if (is_dir($dir_path = "{$base_dir}/{$dir}")) {
				$class_maps[] = ClassMapGenerator::createMap($dir_path);
			}
		}
		if (is_child_theme()){
			$base_dir = get_stylesheet_directory();
			foreach ($args['directories'] as $dir) {
				if (is_dir($dir_path = "{$base_dir}/{$dir}")) {
					$class_maps[] = ClassMapGenerator::createMap($dir_path);
			}
		}
	}

		$class_map = call_user_func_array('array_merge', $class_maps);
		foreach ($class_map as $clazz => $path){
			require_once($path);

			if (class_exists($clazz)) {
				$base = null;
				foreach(self::$basedClasses as $based_class){
					if (self::parentOf($clazz, $based_class)){
						$base = $based_class;
						break;
					}
				}
				if ($base){
					self::set_object($dir, $clazz, $clazz);
			}
		}
		}

		self::add_hooks();
	}

	static private function parentOf($target, $parent)
	{
		$parent_class = $target;
		while ($parent_class = get_parent_class($parent_class)){
			if ($parent_class == $parent){
				return true;
			}
		}
		return false;
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
		$self = __CLASS__;

		//投稿タイプ
		if (array_key_exists('PostType', self::$classes)) {
			foreach(self::$classes['PostType'] as $postType) {
				add_action('init', array($postType, 'register_post_type'));
			}
		}

		//ウィジェットの登録
		if (array_key_exists('Widgets', self::$classes)) {
			add_action('widgets_init', function () use ($self) {
				Widgets::widgets_init();

				foreach($self::$classes['Widgets'] as $widget) {
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
		add_action('current_screen', function () use ($self) {
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
				if (Widgets::is_self()) {
					Widgets::add_hooks();
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
