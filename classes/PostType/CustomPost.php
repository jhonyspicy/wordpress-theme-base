<?php
namespace PostType;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\PostType as PostType;

class CustomPost extends PostType {
	protected $title = 'カスタムポスト';

	public function __construct() {
		$this->options = array(
			'_a' => array($this, 'some_check'),
			'_b',
			'_c',
		);
	}

	public function meta_box_inner() {
		$_a = get_post_meta(get_the_ID(), '_a', true);
		$_b = get_post_meta(get_the_ID(), '_b', true);
		$_c = get_post_meta(get_the_ID(), '_c', true);

		?>
		<div class="item">
			<h2 class="item_title left_side">テキストボックス</h2>

			<div class="right_side">
				<input type="text" name="_a" class="_a" placeholder="タイトル" value="<?php echo $_a; ?>"/>
				<input type="text" name="_b" class="_b" placeholder="タイトル" value="<?php echo $_b; ?>"/>
			</div>
		</div>
		<div class="item">
			<h2>ビジュアルエディタ</h2>
			<div>
				<?php wp_editor( $_c, '_c', array( 'textarea_rows' => 5, 'media_buttons' => false ) ); ?>
			</div>
		</div>
	<?php
	}

	protected function some_check($v) {
		//...do some thing check

		return $v;
	}
}