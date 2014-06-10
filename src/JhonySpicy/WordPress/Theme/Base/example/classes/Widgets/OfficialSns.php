<?php
namespace Widgets;

class OfficialSns extends \WP_Widget {
	private $setting = array('widgetOps'  => array('classname'   => 'official-sns',
												   'description' => '公式 SNS',),
							 'baseId'     => 'official-sns',
							 'widgetName' => '公式 SNS');

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
		$title  = empty($instance['title']) ? '' : esc_attr($instance['title']);
		$facebook_url  = empty($instance['facebook_url']) ? '' : esc_attr($instance['facebook_url']);
		$facebook_id  = empty($instance['facebook_id']) ? '' : esc_attr($instance['facebook_id']);
		$twitter_url  = empty($instance['twitter_url']) ? '' : esc_attr($instance['twitter_url']);

		echo $args['before_widget'];

		if ($title) {
			echo $args['before_title'];
			echo '<h2>'. $title .'</h2>';
			echo $args['after_title'];
		}

		?>
		<iframe src="//www.facebook.com/plugins/likebox.php?href=<?php echo urlencode($facebook_url); ?>&amp;width=237&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false&amp;appId=<?php echo $facebook_id;?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:237px; height:258px;" allowTransparency="true"></iframe>
		<ul>
			<li><a href="<?php echo $facebook_url; ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/bnr01.gif" width="237" height="48" alt="公式Facebook"></a></li>
			<li><a href="<?php echo $twitter_url; ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/bnr02.gif" width="237" height="48" alt="公式Twitter"></a></li>
		</ul>
		<?php

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
		$instance['title']        = strip_tags($new_instance['title']);
		$instance['facebook_url'] = is_url(esc_url($new_instance['facebook_url'])) ? esc_url($new_instance['facebook_url']) : '';
		$instance['facebook_id']  = absint($new_instance['facebook_id']);
		$instance['twitter_url']  = is_url(esc_url($new_instance['twitter_url'])) ? esc_url($new_instance['twitter_url']) : '';

		return $instance;
	}

	/**
	 * 管理画面で設定できる項目を作る
	 *
	 * @param array $instance
	 * @return string|void
	 */
	function form( $instance ) {
		$title        = empty($instance['title']) ? '' : esc_attr($instance['title']);
		$facebook_url = empty($instance['facebook_url']) ? '' : $instance['facebook_url'];
		$facebook_id  = empty($instance['facebook_id']) ? '' : $instance['facebook_id'];
		$twitter_url  = empty($instance['twitter_url']) ? '' : $instance['twitter_url'];

		?>

		<div class="official-sns-wrapper">
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">タイトル</label>
				<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" placeholder="タイトル" value="<?php echo esc_attr($title); ?>">
			</p>

			<div class="detail">
				<label for="<?php echo esc_attr($this->get_field_id('facebook_url')); ?>">公式Facebook</label>
				<input id="<?php echo esc_attr($this->get_field_id('facebook_url')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('facebook_url')); ?>" type="text" placeholder="Facebook Page URL" value="<?php echo esc_attr($facebook_url); ?>">

				<label for="<?php echo esc_attr($this->get_field_id('facebook_id')); ?>">App ID</label>
				<input id="<?php echo esc_attr($this->get_field_id('facebook_id')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('facebook_id')); ?>" type="text" placeholder="Facebook App ID" value="<?php echo esc_attr($facebook_id); ?>">

				<label for="<?php echo esc_attr($this->get_field_id('twitter_url')); ?>">公式Twitter</label>
				<input id="<?php echo esc_attr($this->get_field_id('twitter_url')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('twitter_url')); ?>" type="text" placeholder="Twitter Account URL" value="<?php echo esc_attr($twitter_url); ?>">
			</div>
		</div>
	<?php
	}
}