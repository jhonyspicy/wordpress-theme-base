<?php
namespace Taxonomy;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\Taxonomy as Taxonomy;

class CustomTaxonomy extends Taxonomy {
	protected $title = 'カスタムタクソノミー';

	protected $options = array(
		'keywords'
	);

	public function name() {
		return 'custom-taxonomy';
	}

	protected function get_post_types() {
		return array('custom-post');
	}

	public function wp_terms_checklist_args($args, $post_id = null) {
		if ($args['taxonomy'] == $this->name()) {
			$args['checked_ontop'] = false; //チェックしたのが上に来るかのフラグ

			if ($args['taxonomy'] == $this->name()) {
				$args['walker'] = new \Walker\Radio();
			}
		}

		return $args;
	}

	public function add_form_fields($taxonomy) {
		?>
		<div class="form-field">
			<label for="keywords">キーワード</label>
			<input type="text" name="keywords" id="keywords" size="40" value="" />
		</div>
	<?php

	}

	public function edit_form_fields($tag, $taxonomy) {
		$term_id = $tag->term_id;
		$key = 'keywords';
		$keywords = get_option("taxonomy_{$term_id}_{$key}");

		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="keywords">キーワード</label></th>
			<td><input type="text" name="keywords" id="keywords" size="40" value="<?php echo $keywords ? esc_html( $keywords ) : ''; ?>" />
		</tr>
		<?php
	}
}
