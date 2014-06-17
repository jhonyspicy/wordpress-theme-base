<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;
use \Jhonyspicy\Wordpress\Theme\Base\Super as Super;

abstract class PostType extends Super {
	/**
	 * このテーマがサポートするタクソノミー
	 * 特に「category」や「post_tag」などは
	 * ここに書かなければならない。
	 *
	 * @var array
	 */
	protected $taxonomies = array();

	/**
	 * このテーマがサポートするWordPressの機能
	 *
	 * @var array
	 */
	protected $supports = array('title',
								'editor',
								'thumbnail',
								'excerpt');

	/**
	 * カスタムフィールの名前を登録しておくと
	 * それの配列を元に自動で更新する。
	 * この配列に無いキーは無視さるので注意。
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * ラベルを返す。
	 *
	 * @return array
	 */
	protected function get_label() {
		return array('name'               => $this->title,
					 'singular_name'      => $this->title,
					 'add_new'            => $this->title . 'を追加',
					 'add_new_item'       => $this->title . 'を新規追加',
					 'edit_item'          => $this->title . 'の編集',
					 'new_item'           => 'new_item',
					 'view_item'          => $this->title . 'を表示',
					 'all_items'          => $this->title . '一覧',
					 'search_items'       => 'search_items',
					 'not_found'          => $this->title . 'はまだ一つも登録されていません。',
					 'not_found_in_trash' => 'ゴミ箱の中に' . $this->title . 'はありません。',
					 'parent_item_colon'  => 'parent_item_colon',);
	}

	/**
	 * カスタム投稿の設定を取得する。
	 *
	 * @return array
	 */
	protected function get_setting() {
		return array('labels'               => $this->get_label(),
					 'description'          => '',
					 'public'               => true,
					 'hierarchical'         => false,
					 'exclude_from_search'  => null,
					 'publicly_queryable'   => true,
					 'show_ui'              => true,
					 'show_in_menu'         => null,
					 'show_in_nav_menus'    => true,
					 'show_in_admin_bar'    => null,
					 'menu_position'        => 5,
					 'menu_icon'            => null,
					 'capability_type'      => 'post',
					 'capabilities'         => array(),
					 'map_meta_cap'         => null,
					 'supports'             => $this->supports,
					 'register_meta_box_cb' => array($this, 'add_meta_boxes'),
					 'taxonomies'           => $this->taxonomies,
					 'has_archive'          => true,
					 'rewrite'              => true,
					 'query_var'            => true,
					 'can_export'           => true,
					 'delete_with_user'     => null,
					 '_builtin'             => false,
					 '_edit_link'           => 'post.php?post=%d',);
	}

	/**
	 * カスタム投稿を登録
	 */
	public function register_post_type() {
		if (in_array($this->name(), array('post', 'page'))) {
			$supportList = array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'trackbacks',
				'custom-fields',
				'comments',
				'revisions',
				'page-attributes',
				'post-formats',
			);

			foreach($supportList as $support) {
				if (in_array($support, $this->supports)) {
					$this->add_post_type_support($support);
				} else {
					$this->remove_post_type_support($support);
				}
			}
		} else {
			register_post_type($this->name(), $this->get_setting());
		}
	}

	/**
	 * フックを登録する。
	 */
	public function add_hooks() {
		add_action('edit_form_after_title', array($this, 'edit_form_after_title'));
		add_action('edit_form_after_editor', array($this, 'edit_form_after_editor'));
		add_action('delete_post', array($this, 'delete_post'));
		add_action('save_post', array($this, 'save_post'), 10, 3);
		add_action('admin_print_scripts', array($this, 'admin_print_scripts'));
		add_action('admin_print_styles', array($this, 'admin_print_styles'));
		add_action('admin_head', array($this, 'admin_head'));

		if (in_array($this->name(), array('post', 'page'))) {
			add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 10, 2);
		}
	}

	/**
	 * メタボックス
	 */
	public function add_meta_boxes() {
		$args = func_get_args();
		add_meta_box('detail_meta_box', '詳細情報', array($this, 'meta_box_inner'));
	}

	/**
	 * 追加したメタボックスの中身
	 */
	public function meta_box_inner() {
		echo 'this is meta box inner';
	}

	/**
	 * タイトルの下にテキストボックスを出す。
	 */
	public function edit_form_after_title() {
		$this->the_nonce();
	}

	/**
	 * ビジュアルエディタ直下
	 */
	public function edit_form_after_editor () {
	}

	/**
	 * ゴミ箱からも削除されたら呼ばれる
	 *
	 * @param $post_id
	 */
	public function delete_post($post_id) {
	}

	/**
	 * カスタムフィールドを保存する
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 * @return mixed
	 */
	public function save_post($post_id, $post, $update) {
		$custom_values = array();

		$nonce_name = $this->nonce_name();

		//カスタムフィールドの無いカスタム投稿なら何もしない
		if (count($this->options) == 0) {
			return $post_id;
		}

		//POSTにキーがなかったら何もしない。
		if (!array_key_exists($nonce_name, $_POST)) {
			return $post_id;
		}

		// データが先ほど作った編集フォームのから適切な認証とともに送られてきたかどうかを確認。
		// save_post は他の時にも起動する場合がある。
		if (!wp_verify_nonce($_POST[$nonce_name], $this->title . 'に追加したカスタムフィールド')) {
			return $post_id;
		}

		// 自動保存ルーチンかどうかチェック。そうだった場合はフォームを送信しない（何もしない）
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// パーミッションチェック
		if ($this->name() == $_POST['post_type']) {
			if (!current_user_can('edit_post', $post_id)) {
				return $post_id;
			}
		} else {
			return $post_id;
		}

		$checked_list = $this->check_value($_POST);

		foreach($checked_list as $key => $val) {
			if ($val) {
				update_post_meta($post_id, $key, $val);
			} else {
				delete_post_meta($post_id, $key);
			}
		}

		return $post_id;
	}

	/**
	 * 「hidden」に使うnonceのname属性を取得
	 * 規則性があったのでメソッドにしただけ。
	 *
	 * @return string
	 */
	protected function nonce_name() {
		return $this->name() .'_noncename';
	}

	/**
	 * nonce のコードを出力する
	 *
	 * @return string
	 */
	protected function the_nonce() {
		echo $this->get_nonce();
	}

	/**
	 * nonce のコードを取得する
	 *
	 * @return string
	 */
	protected function get_nonce() {
		return '<input type="hidden" name="'. $this->nonce_name() .'" id="'. $this->nonce_name() .'" value="' . wp_create_nonce($this->title . 'に追加したカスタムフィールド') . '" />';
	}

	/**
	 * このポストタイプにWordPressの機能を追加する
	 * 'title'
	 * 'editor' (content)
	 * 'author'
	 * 'thumbnail' (featured image) (current theme must also support Post Thumbnails)
	 * 'excerpt'
	 * 'trackbacks'
	 * 'custom-fields'
	 * 'comments' (also will see comment count balloon on edit screen)
	 * 'revisions' (will store revisions)
	 * 'page-attributes' (template and menu order) (hierarchical must be true) (the page template selector is only available for the page post type)
	 * 'post-formats' add post formats, see Post Formats
	 *
	 * @param $support
	 */
	protected function add_post_type_support($support) {
		add_post_type_support($this->name(), $support);
	}

	/**
	 * このポストタイプのWordPressの機能を除外する
	 * 'title'
	 * 'editor' (content)
	 * 'author'
	 * 'thumbnail' (featured image) (current theme must also support Post Thumbnails)
	 * 'excerpt'
	 * 'trackbacks'
	 * 'custom-fields'
	 * 'comments' (also will see comment count balloon on edit screen)
	 * 'revisions' (will store revisions)
	 * 'page-attributes' (template and menu order) (hierarchical must be true) (the page template selector is only available for the page post type)
	 * 'post-formats' add post formats, see Post Formats
	 *
	 * @param $support
	 */
	protected function remove_post_type_support($support) {
		remove_post_type_support($this->name(), $support);
	}

	/**
	 * ビジュアルエディタに必要なスタイルを読み込む
	 */
	public function admin_head() {
		$file_path = '/css/admin/' . $this->type() . '/'. $this->name() .'/editor.css';
		if (is_file(get_template_directory() . $file_path)) {
			add_editor_style(get_template_directory_uri() . $file_path);
		}
	}
}