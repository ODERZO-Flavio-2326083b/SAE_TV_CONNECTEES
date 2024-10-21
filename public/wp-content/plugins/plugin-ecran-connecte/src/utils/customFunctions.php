<?php

namespace Utils;

function get_page_by_title_custom($page_title) {
	$args = array(
		'post_type'   => 'page',
		'title'       => $page_title,
		'post_status' => 'publish',
		'posts_per_page' => 1
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$query->the_post();
		return get_post();
	}

	return null;
}