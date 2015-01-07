<?php
class Theme {
	static public function after_setup_theme() {
		/*
		 * テーマのサポート
		 */
		add_theme_support('post-thumbnails'); //アイキャッチ画像(記事投稿時)
		add_theme_support('menus'); //外観 => メニュー
		add_theme_support('automatic-feed-links'); //RSS
		add_theme_support('widgets');

		/*
		 * テーマで必要なメニュー
		 */
		register_nav_menus(array('menu-1' => 'メニュー1',
								 'menu-2' => 'メニュー2',
								 'menu-3' => 'メニュー3',
		));

		/*
		 * ウィジェットを登録するエリア
		 */
		self::register_sidebar(array('name' => 'ウィジェットエリア1',)); //sidebar-1
		self::register_sidebar(array('name' => 'ウィジェットエリア2',)); //sidebar-2
		self::register_sidebar(array('name' => 'ウィジェットエリア3',)); //sidebar-3
	}

	/**
	 * ウィジェットを登録するエリアを
	 * 登録する関数をラップ
	 *
	 * @param $args
	 */
	static public function register_sidebar($args) {
		$defaults = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => "</div>\n",
		);

		$args = wp_parse_args( $args, $defaults );

		register_sidebar($args);
	}

	/**
	 * htmlに付加する記事に関連するクラスに
	 * スラッグを追加する
	 *
	 * @param $classes
	 * @param $class
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public static function post_class( $classes, $class, $post_id ) {
		$post = get_post($post_id);
		if ($post && $post->post_name) {
			array_push($classes, $post->post_name);
		}

		return $classes;
	}

	static public function add_hooks() {
		add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
		add_action('post_class', array(__CLASS__, 'post_class'), 10, 3);
	}
}