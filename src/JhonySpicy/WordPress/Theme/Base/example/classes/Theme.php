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
								 'menu-2' => '下層ページ ヘッダーメニュー',
								 'menu-3' => 'ニュース 下部のメニュー',
								 'menu-4' => 'Q&A 下部の解決しない時は',
//								 'menu-5' => 'メニュー',
//								 'menu-6' => 'メニュー',
//								 'menu-7' => 'メニュー',
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
		self::register_sidebar(array('name' => 'Q&A 左側', 'before_widget' => '<div id="%1$s" class="widget %2$s container clearfix">')); //sidebar-8
		self::register_sidebar(array('name' => 'Q&A メイン(上段)', 'before_widget' => '<div id="%1$s" class="widget %2$s box-qa">')); //sidebar-9
		self::register_sidebar(array('name' => 'Q&A メイン(下段)', 'before_widget' => '<div id="%1$s" class="widget %2$s box-qa">')); //sidebar-10
//		self::register_sidebar(array('name' => '',)); //sidebar-11
//		self::register_sidebar(array('name' => '',)); //sidebar-12
//		self::register_sidebar(array('name' => '',)); //sidebar-13
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

	static public function add_hooks() {
		add_action('after_setup_theme', array(__CLASS__, 'after_setup_theme'));
	}
}