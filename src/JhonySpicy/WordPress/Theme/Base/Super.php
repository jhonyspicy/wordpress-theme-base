<?php
namespace Jhonyspicy\Wordpress\Theme\Base;

abstract class Super {
	/**
	 * カスタムフィールドなどのリストと、
	 * チェックする関数の配列
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * 画面上に表示される日本語名
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * このクラスがどういう種類なのか
	 * 「post_type」「settings_page」「taxonomy」「」
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * 自分自身の管理画面なのかどうか
	 * @return bool
	 */
	public function is_self() {
		$screen = get_current_screen();

		switch($this->type) {
			case 'post_type':
				if ($screen->post_type == $this->name()) {
					return true;
				}
				break;

			case 'taxonomy':
				if ($screen->taxonomy == $this->name()) {
					return true;
				}
				break;

			case 'settings_page':
				if ($screen->id == 'settings_page_' . $this->name()) {
					return true;
				}
				break;

			default:
				break;
		}

		return false;
	}

	/**
	 * クラス名を取得する(名前空間込)
	 *
	 * @return string
	 */
	protected function class_name() {
		return get_class($this);
	}

	/**
	 * 投稿タイプの名前(ID?)を取得する
	 *
	 * @return string
	 */
	public function name() {
		$v = explode('\\', $this->class_name());
		return strtolower(end($v));
	}

	/**
	 * フックを登録する。
	 */
	public function add_hooks() {
	}

	protected function check_value($input_list) {
		$result_list = array();

		//入力値をチェックしながら保存する
		foreach($this->options as $key => $val) {
			if (is_int($key)) {
				$field = $val;
				$value = $input_list[$val];
			} else {
				if (!empty($val) && is_callable($val)) {
					$field = $key;
					$value = call_user_func_array($val, array($input_list[$key]));
				} else {
					$field = $key;
					$value = $input_list[$key];
				}
			}

			$result_list[$field] = $value;
		}

		return $result_list;
	}
}