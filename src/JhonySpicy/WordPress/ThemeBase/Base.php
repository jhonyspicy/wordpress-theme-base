<?php

namespace Jhonyspicy\Wordpress\ThemeBase;

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
					add_action('init', array($postType, 'register_post_type'));

					if ($postType->is_self()) {
						add_action('edit_form_after_title', array($postType, 'edit_form_after_title')); //タイトルの下に何かを追加
						add_action('admin_print_scripts', array($postType, 'admin_print_scripts')); //必要となるスクリプトを読み込む
						add_action('admin_print_styles', array($postType, 'admin_print_styles')); //必要となるスタイルを読み込む
						add_action('admin_head', array($postType, 'admin_head')); //ビジュアルエディタのスタイルを読み込む
						add_action('add_meta_boxes', array($postType, 'add_meta_boxes')); //カスタムフィールド
						add_action('save_post', array($postType, 'save_post')); //カスタムフィールドの保存
						$postType->post_type_support();
					}
				}
			}
		});
	}
}