<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;
use \Jhonyspicy\Wordpress\Theme\Base\Super as Super;

abstract class Taxonomy extends Super {
	protected $type = 'taxonomy';

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
		add_action($this->name() . '_edit_form', array($this, 'edit_form'), 10, 2);

		add_filter('wp_terms_checklist_args', array($this, 'wp_terms_checklist_args'), 10, 2);
	}

	/**
	 * フック「current_screen」に通らないので、
	 * 別途特別にフックを登録する必要があるもの。
	 * フック名にスラッグがつくものが多い。
	 */
	public function add_special_hooks() {
		add_action('created_' . $this->name(), array($this, 'save'), 10, 2);
		add_action('edited_' . $this->name(), array($this, 'save'), 10, 2);
		add_action('delete_' . $this->name(), array($this, 'delete'), 10, 2);
	}

	public function add_form_fields($taxonomy) {
		echo '追加画面に追加する';
	}

	public function edit_form($tag, $taxonomy) {
		echo '編集画面に追加する';
	}

	public function save($term_id, $tt_id) {
		$a = 'a';
	}

	public function delete($term_id, $tt_id) {
		$a = 'a';
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

	/**
	 * 投稿画面に必要なスクリプトを読み込む
	 */
	public function admin_print_scripts() {
		wp_enqueue_script($this->name() . '_script', get_template_directory_uri() . '/js/admin/taxonomy/'. $this->name() .'.js', array('jquery'), '1.0.0', true);
	}

	/**
	 * 投稿画面に必要なスタイルを読み込む
	 */
	public function admin_print_styles() {
		wp_enqueue_style($this->name() . '_style', get_template_directory_uri() . '/css/admin/taxonomy/'. $this->name() .'/style.css', array(), '1.0.0');
	}
}