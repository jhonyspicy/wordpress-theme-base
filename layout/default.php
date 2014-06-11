<?php
/**
 * layout/default
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<title><?php
			/*
			 * Print the <title> tag based on what is being viewed.
			 */
			global $page, $paged;

			wp_title('|', true, 'right');

// Add the blog name.
			bloginfo('name');

// Add the blog description for the home/front page.
			$site_description = get_bloginfo('description', 'display');
			if ($site_description && ( is_home() || is_front_page() )) {
				echo " | $site_description";
			}

// Add a page number if necessary:
			if ($paged >= 2 || $page >= 2) {
				echo ' | ' . sprintf(__('Page %s', 'twentyten'), max($paged, $page));
			}
			?></title>
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
		<?php
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head();
		?>
	</head>

	<body <?php body_class(); ?>>
		<div id="wrapper">
			<header>
				<?php
				get_header();
				?>
			</header>

			<section id="container">
				<?php
				echo $contents;
				?>
			</section>

			<section id="sidebar">
				<?php
				get_sidebar();
				?>
			</section>

			<footer>
				<?php
				get_footer();
				?>
			</footer>
		</div>
		<?php
		/* Always have wp_footer() just before the closing </body>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to reference JavaScript files.
		 */
		wp_footer();
		?>
	</body>
</html>
