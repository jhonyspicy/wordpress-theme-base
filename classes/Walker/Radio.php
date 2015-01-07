<?php
namespace Walker;

class Radio extends \Walker_Category_Checklist {
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0) {
		extract($args);
		if (empty($taxonomy)) {
			$taxonomy = 'category';
		}

		if ($taxonomy == 'category') {
			$name = 'post_category';
		} else {
			$name = 'tax_input[' . $taxonomy . ']';
		}

		$class = in_array($category->term_id, $popular_cats) ? ' class="popular-category"' : '';

		//ラジオボタンで出力する
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="radio" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' . checked(in_array($category->term_id, $selected_cats), true, false) . disabled(empty($args['disabled']), false, false) . ' /> ' . esc_html(apply_filters('the_category', $category->name)) . '</label>';
	}
}