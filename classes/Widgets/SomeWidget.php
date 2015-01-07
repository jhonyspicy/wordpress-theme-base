<?php
namespace Widgets;

class SomeWidget extends \WP_Widget {
	private $setting = array('widgetOps'  => array('classname'   => 'someWidget',
												   'description' => '何かのウィジェット',),
							 'baseId'     => 'someWidget',
							 'widgetName' => '何かのウィジェット');

	public function __construct() {
		parent::__construct($this->setting['baseId'], $this->setting['widgetName'], $this->setting['widgetOps']);
	}

	/**
	 * ウィジェット自体の出力(実査いに画面に出力)
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title     = empty($instance['title']) ? '' : esc_attr($instance['title']);
		$link_url  = esc_attr($instance['link_url']);
		$image_url = empty($instance['image_url']) ? '' : esc_attr($instance['image_url']);
		$image_tag = '';
		if ($image_url) {
			$image_tag = wp_get_attachment_image(\Thumbnail::get_attachment_id($image_url), 'full', false, array('alt' => $title));
		}

		echo $args['before_widget'];

		if ($link_url) {
			echo '<a href="'. $link_url .'">';
		}

		echo $image_tag;

		if ($link_url) {
			echo '</a>';
		}

		echo $args['after_widget'];
	}

	/**
	 * 値の更新
	 *
	 * @param array $new_instance
	 * @param array $instance
	 * @return array
	 */
	function update( $new_instance, $instance ) {
		$instance['title']     = strip_tags($new_instance['title']);
		$instance['image_url'] = strip_tags($new_instance['image_url']);
		$instance['link_url']  = strip_tags($new_instance['link_url']);

		return $instance;
	}

	/**
	 * 管理画面で設定できる項目を作る
	 *
	 * @param array $instance
	 * @return string|void
	 */
	function form( $instance ) {
		$title     = empty($instance['title']) ? '' : esc_attr($instance['title']);
		$image_url = empty($instance['image_url']) ? '' : esc_attr($instance['image_url']);
		$link_url  = empty($instance['link_url']) ? '' : esc_attr($instance['link_url']);

		$image_tag = '';
		if ($image_url) {
			$image_tag = '<img src="' . $image_url . '" />';
		}

		?>

		<div class="some-wrapper">
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">タイトル</label>
				<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" placeholder="タイトル" value="<?php echo esc_attr($title); ?>">
			</p>

			<input type="hidden" class="image_url" name="<?php echo esc_attr($this->get_field_name('image_url')); ?>" value="<?php echo esc_attr($image_url); ?>">
			<div class="image_area">
				<?php echo $image_tag; ?>
			</div>
			<button class="select_media">画像を選択</button>
			<button class="delete">削除</button>

			<p>
				<label for="<?php echo esc_attr($this->get_field_id('link_url')); ?>">URL</label>
				<input id="<?php echo esc_attr($this->get_field_id('link_url')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('link_url')); ?>" type="text" value="<?php echo esc_attr($link_url); ?>">
			</p>
		</div>

	<?php
	}
}