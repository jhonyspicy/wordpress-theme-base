<?php
namespace MenuPage;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\MenuPage as MenuPage;

class Setting extends MenuPage {
	protected $title = '専用設定';

	public function __construct() {
		$this->options = array('facebook_url' => 'esc_url',
							   'app_id',
							   'twitter_url'  => 'esc_url',
							   'popular_keyword',
							   'ogp',
		);
	}

	public function page_inner() {
		$ogp = wp_parse_args(get_option('ogp'), array('title'       => '',
													  'description' => '',
													  'image'       => ''));

		$image_tag = '';
		if (!empty($ogp['image'])) {
			$image_tag = '<img src="' . $ogp['image'] . '" />';
		}
		?>
		<div class="wrap kentem">
			<h1><?php echo $this->title; ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields($this->name()); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="facebook_url">Facebook Page</label></th>
						<td><input type="text" name="facebook_url" value="<?php echo get_option('facebook_url'); ?>" placeholder="Facebook Page" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="app_id">Facebook App ID</label></th>
						<td><input type="text" name="app_id" value="<?php echo get_option('app_id'); ?>" placeholder="Facebook App ID" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="twitter_url">Twitter Page</label></th>
						<td><input type="text" name="twitter_url" value="<?php echo get_option('twitter_url'); ?>" placeholder="Twitter Page" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">OGP</th>
						<td>
							<input type="text" name="ogp[title]" value="<?php echo $ogp['title']; ?>" placeholder="タイトル" />
							<textarea name="ogp[description]" id="ogp[description]" cols="30" rows="10" placeholder="説明文"><?php echo $ogp['description']; ?></textarea>
							<div class="image">
								<input type="hidden" name="ogp[image]" value="<?php echo $ogp['image']; ?>">
								<div class="media_viewer">
									<?php echo $image_tag; ?>
								</div>
								<button class="select_media">画像を選択</button>
								<button class="delete_media">削除</button>
							</div>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
	<?php
	}

	public function admin_print_scripts() {
		wp_enqueue_media(); //これがないとjavascriptで「wp.media()」実行時にエラーとなる。詳細は不明
		wp_enqueue_script($this->name() . '_script', get_template_directory_uri() . '/js/admin/menu_page/'. $this->name() .'.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-draggable'), '1.0.0', true);
	}
}