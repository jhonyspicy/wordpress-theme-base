<?php
namespace PostType;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\PostType as PostType;

class Page extends PostType {
	protected $options = array();

	protected $supports = array('title',
								'editor',
								'author',
								'thumbnail',
								'page-attributes',
								'custom-fields',
								'comments',
								'revisions');

	public function meta_box_inner() {
		$_og = get_post_meta(get_the_ID(), '_og', true);
		if (is_array($_og)) {
			if (!array_key_exists('title', $_og)) {
				$_og['title'] = '';
			}
			if (!array_key_exists('description', $_og)) {
				$_og['description'] = '';
			}
			if (!array_key_exists('image', $_og)) {
				$_og['image'] = '';
			}

		} else {
			$_og = array('title'       => '',
						 'description' => '',
						 'image'       => '');
		}

		?>
		<div class="item">
			<h2 class="item_title left_side">OGP</h2>

			<div class="right_side">
				<input type="text" name="_og[title]" class="_og" placeholder="タイトル" value="<?php echo $_og['title']; ?>"/>
				<textarea name="_og[description]" class="_og" cols="30" rows="10" placeholder="説明" ><?php echo $_og['description']; ?></textarea>
				<div class="image">
					<input type="hidden" name="_og[image]" class="_og" value="<?php echo $_og['image']; ?>"/>
					<button class="select_media">画像を選択</button>
					<button class="delete_media">削除</button>
					<div class="viewer">
						<?php
						if ($_og['image']) {
							echo '<img src="'. $_og['image'] .'" />';
						}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

}