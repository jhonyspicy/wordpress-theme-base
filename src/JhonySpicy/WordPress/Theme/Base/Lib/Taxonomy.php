<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;
use \Jhonyspicy\Wordpress\Theme\Base\Super as Super;

abstract class Taxonomy extends Super {
	/**
	 * タクソノミーのラベルを取得
	 *
	 * @return array
	 */
	protected function get_labels() {
		return array('name'         => $this->title,
					 'menu_name'    => $this->title,
					 'all_items'    => $this->title . '一覧',
					 'add_new_item' => $this->title . 'を追加',
					 'search_items' => $this->title . 'を検索');
	}

	/**
	 * タクソノミーの設定項目
	 *
	 * @return array
	 */
	protected function get_setting() {
		return array('hierarchical'      => true,
					 'labels'            => $this->get_labels(),);
	}

	/**
	 * どのカスタム投稿に追加するのか
	 *
	 * @return array
	 */
	protected function get_post_types() {
		return array();
	}

	/**
	 * タクソノミーを追加する
	 */
	public function register_taxonomy() {
		register_taxonomy($this->name(), $this->get_post_types(), $this->get_setting());
	}

	public function add_hooks() {
		add_action($this->name() . '_add_form_fields', array($this, 'add_form_fields'));
		add_action($this->name() . '_edit_form_fields', array($this, 'edit_form_fields'), 10, 2);
		add_action('admin_print_styles', array($this, 'admin_print_styles'));
		add_action('delete_term_taxonomy', array($this, 'delete_term_taxonomy'));
	}

	public function add_special_hooks() {
		add_action('wp_terms_checklist_args', array($this, 'wp_terms_checklist_args'), 10, 2);
		add_action('created_' . $this->name(), array($this, 'save'), 10, 2);
		add_action('edited_' . $this->name(), array($this, 'save'), 10, 2);
		add_action('delete_' . $this->name(), array($this, 'delete'), 10, 2);
	}

	/**
	 * タクソノミーが削除されたら呼ばれる。
	 *
	 * @param $tt_id
	 */
	public function delete_term_taxonomy($tt_id) {
	}

	/**
	 * 追加画面に追加するhtml
	 *
	 * @param $taxonomy
	 */
	public function add_form_fields($taxonomy) {
	}

	/**
	 * 編集画面に追加する
	 *
	 * @param $tag
	 * @param $taxonomy
	 */
	public function edit_form_fields($tag, $taxonomy) {
	}

	public function save($term_id, $tt_id) {
		$checked_list = $this->check_value($_POST);

		foreach($checked_list as $key => $val) {
			if ($val) {
				update_option("taxonomy_{$term_id}_{$key}", $val);
			} else {
				delete_option("taxonomy_{$term_id}_{$key}");
			}
		}
	}

	/**
	 * 削除した時に呼ばれる
	 *
	 * @param $term_id
	 * @param $tt_id
	 */
	public function delete($term_id, $tt_id) {
		//不要になったオプション値を削除する
		foreach($this->options as $key => $val) {
			if (is_int($key)) {
				delete_option("taxonomy_{$term_id}_{$val}");
			} else {
				delete_option("taxonomy_{$term_id}_{$key}");
			}
		}
	}

	/**
	 * デフォルトの見た目から変更したい場合は
	 * このフックを使う。
	 *
	 * @param $args
	 * @param null $post_id
	 *
	 * @return mixed
	 */
	public function wp_terms_checklist_args($args, $post_id = null) {
		return $args;
	}
}
