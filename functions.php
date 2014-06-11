<?php

require __DIR__ . '/vendor/autoload.php';


use Jhonyspicy\Wordpress\Theme\Base\Base as ThemeBase;
ThemeBase::initialize();


use Atomita\Wordpress\LayoutStyleThemeFacade as LayoutStyle;
LayoutStyle::initialize();
