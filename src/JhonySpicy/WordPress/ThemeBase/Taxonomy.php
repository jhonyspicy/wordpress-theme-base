<?php
namespace Jhonyspicy\Wordpress\ThemeBase;
abstract class Taxonomy {
	protected $title = 'タクソノミー名';

	private function class_name() {
		return get_class($this);
	}

	protected function name() {
		$v = explode('\\', $this->class_name());
		return strtolower(end($v));
	}

	/**
	 * タクソノミーのラベルを取得
	 *
	 * @return array
	 */
	protected function getLabels() {
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
	protected function getSetting() {
		return array('hierarchical'      => true,
					 'labels'            => $this->getLabels(),);
	}

	/**
	 * どのカスタム投稿に追加するのか
	 *
	 * @return array
	 */
	protected function getPostTypes() {
		return array();
	}

	/**
	 * タクソノミーを追加する
	 */
	public function register_taxonomy() {
		global $_seminar;

		register_taxonomy($this->name(), $this->getPostTypes(), $this->getSetting());

		add_filter('wp_terms_checklist_args', array($this, 'wp_terms_checklist_args'), 10, 2);
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