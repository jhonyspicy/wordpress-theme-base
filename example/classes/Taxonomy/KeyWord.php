<?php
namespace Taxonomy;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\Taxonomy as Taxonomy;

class KeyWord extends Taxonomy {
	protected $title = 'キーワード';

	protected function get_post_types() {
		return array('custom-post');
	}

	protected function get_setting() {
		return array('hierarchical'      => false,
					 'labels'            => $this->get_labels(),);
	}
}