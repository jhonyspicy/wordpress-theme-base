<?php
require __DIR__ . '/vendor/autoload.php';
use Jhonyspicy\Wordpress\Theme\Base\Base as ThemeBase;
ThemeBase::initialize();

Theme::add_hooks();
