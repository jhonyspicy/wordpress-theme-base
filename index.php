<?php

$with = 'with';

if (have_posts()){
	while(have_posts()){
		the_post();
		echo <<<EOTHTML
		<article id="post-{$with(get_the_ID()}">
			{$with(getho('the_title', '<header class="entry-header"><h1 class="entry-title">', '</h1></header>'))}
			<div class="entry-content">
				{$with(getho('the_content'))}
				{$with(getho('wp_link_pages', array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __('Pages:') . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				)))}
			</div>
		</article>
EOTHTML;
	}
}
else{
	echo <<<EOTHTML
	<article>
		<header class="page-header">
			<h1 class="page-title">{$with(__('Nothing Found'))}</h1>
		</header>
	</article>
EOTHTML;
}
