<?php
abstract class Widgets {
	static $widgetList = array();

	/**
	 * 自分自身の管理画面なのかどうか
	 * @return bool
	 */
	static public function is_self() {
		$screen = get_current_screen();
		$name = strtolower(self::class_name());

		if ($screen->id != $name) {
			return false;
		}

		return true;
	}

	/**
	 * クラス名を取得する(名前空間込)
	 *
	 * @return string
	 */
	static public function class_name() {
		return get_called_class();
	}

	/**
	 * 投稿タイプの名前(ID?)を取得する
	 *
	 * @param null $name
	 *
	 * @return string
	 */
	static public function name($name = null) {
		if (!$name) {
			$name = self::class_name();
		}
		$v = explode('\\', $name);
		return strtolower(end($v));
	}

	/**
	 * 実際にウィジェットを登録する
	 */
	static public function widgets_init() {
		global $wp_widget_factory;
		//不要なウィジェットをカット
		foreach ($wp_widget_factory->widgets as $widget_class => $widget) {
			if (!in_array($widget_class, array('WP_Widget_Text', 'WP_Nav_Menu_Widget'))) {
				unregister_widget($widget_class);
			}
		}

		self::register_widget('Widgets\Banner');
		self::register_widget('Widgets\SpecialContents');
		self::register_widget('Widgets\OfficialSns');
	}

	static public function register_widget($widget) {
		self::$widgetList[] = $widget;

		register_widget($widget);
	}

	static public function admin_print_scripts() {
		wp_enqueue_media(); //これがないとjavascriptで「wp.media()」実行時にエラーとなる。詳細は不明

		foreach(self::$widgetList as $widget) {
			$widgetName = self::name($widget);
			wp_enqueue_script($widgetName, get_template_directory_uri() . '/js/admin/widgets/'. $widgetName .'.js', array('jquery', 'media-upload', 'media-views'), '1.0.0', true);
		}
	}

	static public function admin_print_styles() {
		foreach(self::$widgetList as $widget) {
			$widgetName = self::name($widget);
			wp_enqueue_style($widgetName, get_template_directory_uri() . '/css/admin/widgets/'. $widgetName .'.css', array(), '1.0.0');
		}
	}
}