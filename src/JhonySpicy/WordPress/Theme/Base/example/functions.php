<?php
require __DIR__ . '/vendor/autoload.php';
use Atomita\Wordpress\LayoutStyleThemeFacade as LayoutStyleTheme;
use Jhonyspicy\Wordpress\Theme\Base\Base as ThemeBase;
LayoutStyleTheme::initialize();
ThemeBase::initialize();

add_action('after_setup_theme', array('Theme', 'after_setup_theme'));

