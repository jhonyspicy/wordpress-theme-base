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
		register_nav_menus(array('menu-1' => 'トップ ヘッダーメニュー',
								 'menu-2'  => '下層ページ ヘッダーメニュー',
								 'menu-3'  => 'メニュー',
								 'menu-4'  => 'メニュー',
								 'menu-5'  => 'メニュー',
								 'menu-6'  => 'メニュー',
		));

		/*
		 * ウィジェットを登録するエリア
		 */
		self::register_sidebar(array('name' => 'フッター サイトマップ (上段)',)); //sidebar-1
		self::register_sidebar(array('name' => 'フッター サイトマップ (下段)',)); //sidebar-2
		self::register_sidebar(array('name' => 'トップ 右側',)); //sidebar-3
		self::register_sidebar(array('name' => 'トップ スペシャルコンテンツ',)); //sidebar-4
		self::register_sidebar(array('name' => 'トップ バーナー',)); //sidebar-5
		self::register_sidebar(array('name' => 'ニュース 右側',)); //sidebar-6
		self::register_sidebar(array('name' => '製品アーカイブ 右側',)); //sidebar-7
//		self::register_sidebar(array('name' => '',)); //sidebar-8
//		self::register_sidebar(array('name' => '',)); //sidebar-9
//		self::register_sidebar(array('name' => '',)); //sidebar-10
//		self::register_sidebar(array('name' => '',)); //sidebar-11
//		self::register_sidebar(array('name' => '',)); //sidebar-12
//		self::register_sidebar(array('name' => '',)); //sidebar-13
//		self::register_sidebar(array('name' => '',)); //sidebar-14
//		self::register_sidebar(array('name' => '',)); //sidebar-15
//		self::register_sidebar(array('name' => '',)); //sidebar-16
//		self::register_sidebar(array('name' => '',)); //sidebar-17
//		self::register_sidebar(array('name' => '',)); //sidebar-18
//		self::register_sidebar(array('name' => '',)); //sidebar-19

		Thumbnail::register_image_size();
	}

	static public function register_sidebar($args) {
		$defaults = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => "</div>\n",
		);

		$args = wp_parse_args( $args, $defaults );

		register_sidebar($args);
	}
}