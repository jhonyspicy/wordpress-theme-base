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
//								   'ShortCode',
//								   'Taxonomy',
//								   'Widgets',
	);

	/**
	 * 初期化
	 */
	static public function initialize() {
		foreach (self::$supportDirList as $dir) {
			$dirname = get_stylesheet_directory() . '/classes/' . $dir;
			if (is_dir($dirname) && $handle = opendir($dirname)) {
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
		if (array_key_exists('PostType', self::$classes)) {
			foreach(self::$classes['PostType'] as $postType) {
				add_action('init', array($postType, 'register_post_type'));
			}
		}

		add_action('current_screen', function () {
			if (array_key_exists('PostType', self::$classes)) {
				foreach(self::$classes['PostType'] as $postType) {
					if ($postType->is_self()) {
						$postType->add_hooks();
					}
				}
			}
		});
	}
}