<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;
abstract class PostType {
	/**
	 * 画面上に表示される日本語名
	 *
	 * @var string
	 */
	protected $title;

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
	 * 自分自身の管理画面なのかどうか
	 * @return bool
	 */
	public function is_self() {
		$screen = get_current_screen();

		if ($screen->post_type != $this->name()) {
			return false;
		}

		return true;
	}

	/**
	 * クラス名を取得する(名前空間込)
	 *
	 * @return string
	 */
	private function class_name() {
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
		add_action('save_post', array($this, 'save_post'));
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
	public function add_meta_boxes($post_type, $post) {
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
		echo $this->the_nonce();
	}

	/**
	 * カスタムフィールドを保存する
	 */
	public function save_post($post_id) {
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

		//カスタムフィールドの値を集める
		foreach($this->options as $field) {
			$custom_values[$field] = $_POST[$field];
		}

		//入力項目のチェックとか、
		$custom_values = $this->custom_field_check($custom_values);

		//更新する
		foreach($this->options as $field) {
			if (array_key_exists($field, $custom_values) && $custom_values[$field]) {
				update_post_meta($post_id, $field, $custom_values[$field]);
			} else {
				delete_post_meta($post_id, $field);
			}
		}

		return $post_id;
	}

	/**
	 * 入力項目のチェック
	 * オーバーライドして使ってね
	 *
	 * @param $values
	 *
	 * @return mixed
	 */
	protected function custom_field_check($values) {
		return $values;
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
	 * nonce のコードを取得する
	 *
	 * @return string
	 */
	protected function the_nonce() {
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
	 * 投稿画面に必要なスクリプトを読み込む
	 */
	public function admin_print_scripts() {
		if (is_file(get_template_directory() . '/js/admin/post_type/'. $this->name() .'.js')) {
			wp_enqueue_script($this->name() . '_script', get_template_directory_uri() . '/js/admin/post_type/'. $this->name() .'.js', array('jquery'), '1.0.0', true);
		}
	}

	/**
	 * 投稿画面に必要なスタイルを読み込む
	 */
	public function admin_print_styles() {
		if (is_file(get_template_directory_uri() . '/css/admin/post_type/'. $this->name() .'/style.css')) {
			wp_enqueue_style($this->name() . '_style', get_template_directory_uri() . '/css/admin/post_type/'. $this->name() .'/style.css', array(), '1.0.0');
		}
	}

	/**
	 * ビジュアルエディタに必要なスタイルを読み込む
	 */
	public function admin_head() {
		if (is_file(get_template_directory_uri() . '/css/admin/'. $this->name() .'/editor.css')) {
			add_editor_style(get_template_directory_uri() . '/css/admin/'. $this->name() .'/editor.css');
		}
	}
}