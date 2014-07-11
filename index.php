<?php

use \atomita\wordpress\utils\collection\Query;

Twig::render('index.twig', array(
	'posts' => new Query,
	'sidebar' => true,
));
