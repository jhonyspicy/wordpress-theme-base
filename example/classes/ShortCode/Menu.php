<?php

namespace ShortCode;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\ShortCode as ShortCode;

class Menu extends ShortCode {

	public function do_shortcode($atts, $content) {
		$atts = shortcode_atts(array('name'            => '',
									 'container'       => '',
									 'container_class' => '',
									 'before'          => '',
									 'after'           => '',
									 'link_before'     => '',
									 'link_after'      => '',), $atts);

		$nav_menu = ! empty( $atts['name'] ) ? wp_get_nav_menu_object( $atts['name'] ) : false;

		if ( !$nav_menu )
			return '';

		return wp_nav_menu(array('menu'            => $nav_menu,
								 'container'       => $atts['container'],
								 'container_class' => $atts['container_class'],
								 'echo'            => false,
								 'before'          => $atts['before'],
								 'after'           => $atts['after'],
								 'link_before'     => $atts['link_before'],
								 'link_after'      => $atts['link_after'],
								 'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',));
	}
}