<?php
class Pager {
	/**
	 * ページャーの関数。数字を配列で返す。
	 * @param $page
	 * @param $max
	 * @param int $num
	 * @return array
	 */
	static function pagerList($page, $max, $num = 10) {
		$arr = array();
		foreach (range(0, $num) as $v) {
			$arr[]	 = $page + $v;
			$arr[]	 = $page - $v;
		}

		$filter = create_function('$v', 'return 1 <= $v && $v <= ' . $max . ';');

		$pager = array_slice(array_filter($arr, $filter), 1, $num);
		sort($pager);
		return $pager;
	}

	/**
	 * ページャーの出力
	 */
	static function the_pager($args = '') {
		global $wp_query;

		$defaults = array('max_page'         => $wp_query->max_num_pages,
		                  'current_page'     => get_query_var('paged') ? get_query_var('paged') : 1,
		                  //表示するページャーの数
		                  'page_num'         => 5,
		                  'next_text'        => 'NEXT',
		                  'prev_text'        => 'PREV',
		                  //1ページしかないときにページャーを出すか
		                  'one_page_visible' => true);

		$r = wp_parse_args($args, $defaults);
		extract($r, EXTR_SKIP);

		$pager_list = self::pagerList($current_page, $max_page, $page_num);

		//
		if (count($pager_list) == 1 && !$one_page_visible) {
			return;
		}

		echo '<div class="paging">';

		if (1 < $current_page) {
			echo '<p class="prev"><a href="' . get_pagenum_link($current_page - 1) . '">'. $prev_text .'</a></p>';
		} else {
			echo '<p></p>';
		}

		echo '<div>';
		echo '<ol>';

		/*if (1 < $pager_list[0]) {
			echo '<li><a href="' . get_pagenum_link(1) . '">1</a></li>';
			echo '<li class="dot">...</li>';
		}*/

		foreach ($pager_list as $pagenum) {
			if ($pagenum == $current_page) {
				echo '<li class="current"><a>' . $pagenum . '</a></li> ';
			} else {
				echo '<li><a href="' . get_pagenum_link($pagenum) . '">' . $pagenum . '</a></li> ';
			}
		}

		/*if ($pager_list[count($pager_list) - 1] < $max_page) {
			echo '<li class="dot">...</li>';
			echo '<li><a href="' . get_pagenum_link($max_page) . '">' . $max_page . '</a></li>';
		}*/

		echo '</ol>';
		echo '</div>';

		if ($current_page < $max_page) {
			echo '<p class="next"><a href="' . get_pagenum_link($current_page + 1) . '">'. $next_text .'</a></p>';
		} else {
			echo '<p></p>';
		}

		echo '</div>';
	}
}