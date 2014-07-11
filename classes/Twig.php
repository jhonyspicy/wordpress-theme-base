<?php

use \atomita\Facade;
use \atomita\utils\MethodIsFunction;

class Twig extends \atomita\Facade
{
	static protected function facadeInstance()
	{
		static $twig;
		if (!isset($twig)){
			$loader = new Twig_Loader_Filesystem(__DIR__.'/../views');
			$escaper = new Twig_Extension_Escaper(true);
			$twig = new Twig_Environment($loader, array(
				'cache' => __DIR__.'/../cache',
				'auto_reload' => true
			));
			$twig->addExtension($escaper);
		}
		return $twig;
	}

	static function render($template, array $context = array()){
		$twig = static::facadeInstance();
		echo $twig->render($template, array_merge(array('func' => new MethodIsFunction), $context));
	}
}
