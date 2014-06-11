<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;
use \Jhonyspicy\Wordpress\Theme\Base\Super as Super;

abstract class MenuPage extends Super {
	/**
	 * 画面上に表示される日本語名
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * データベースに追加するべき項目のリスト
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * 権限レベル
	 * 参考: http://wpdocs.sourceforge.jp/%E3%83%A6%E3%83%BC%E3%82%B6%E3%83%BC%E3%83%AC%E3%83%99%E3%83%AB
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * 管理画面の「設定」の下にメニューを追加
	 */
	public function admin_menu() {
//		add_menu_page($this->title, $this->title, 'manage_options', $this->name(), array($this, 'page_inner'), $icon_url = ''); //トップレベルのメニューを追加する
		add_options_page($this->title, $this->title, $this->capability, $this->name(), array($this, 'page_inner')); //サブメニューを追加する。
	}

	/**
	 * 追加したページの内容(HTML)
	 *
	 * 参考: http://wpdocs.sourceforge.jp/Creating_Options_Pages
	 */
	abstract public function page_inner();

	/**
	 * 設定する項目を登録する
	 * その値をチェックする関数があればそれも同時に設定する
	 */
	public function admin_init() {
		foreach($this->options as $key => $val) {
			if (is_int($key)) {
				register_setting($this->name(), $val);
			} else {
				if (empty($val)) {
					register_setting($this->name(), $key);
				} else {
					register_setting($this->name(), $key, $val);
				}
			}
		}
	}

	/**
	 * フックを登録する。
	 */
	public function add_hooks() {
		add_action('admin_print_scripts', array($this, 'admin_print_scripts'));
		add_action('admin_print_styles', array($this, 'admin_print_styles'));
	}
}