<?php
namespace PostType;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\PostType as PostType;

class SpecialContents extends PostType {
	protected $title = 'スペシャルコンテンツ';

	public function __construct() {
		$this->options = array(
			'_a' => array($this, 'test'),
			'_b'
		);
	}

	public function name() {
		return 'special-contents';
	}

	public function meta_box_inner() {
		$_a = get_post_meta(get_the_ID(), '_a', true);
		$_b = get_post_meta(get_the_ID(), '_b', true);

		?>
		<div class="item">
			<h2 class="item_title left_side">OGP</h2>

			<div class="right_side">
				<input type="text" name="_a" class="_a" placeholder="タイトル" value="<?php echo $_a; ?>"/>
				<input type="text" name="_b" class="_b" placeholder="タイトル" value="<?php echo $_b; ?>"/>
			</div>
		</div>
	<?php
	}

	protected function test($v) {
		return $v;
	}
}