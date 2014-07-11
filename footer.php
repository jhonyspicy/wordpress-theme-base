<?php
/*
 * Third party plugins that hijack the theme will call wp_footer() to get the footer template.
 * We use this so end our output buffer (started in header.php) and render into the view/page-plugin.twig template.
 */
$context = $GLOBALS['Context'];
if (!isset($context)) {
	throw new \Exception('Context not set in footer.');
}
$context['content'] = ob_get_contents();
ob_end_clean();
$templates = 'page-plugin.twig';
Twig::render($templates, $context);
