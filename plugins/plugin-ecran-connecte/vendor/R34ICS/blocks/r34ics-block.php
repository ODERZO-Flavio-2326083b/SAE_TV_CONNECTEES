<?php

add_action('init', function() {

	if (!function_exists('register_block_type')) { return; }
	
	wp_register_script(
		'r34ics-block-editor',
		plugins_url('r34ics-block/index.js', __FILE__),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime(plugin_dir_path(__FILE__) . '/r34ics-block/index.js')
	);
		
	register_block_type('r34ics/r34ics-block', array(
		'editor_script' => 'r34ics-block-editor',
		'render_callback' => 'r34ics_block_render_callback',
		'attributes' => array(
			'r34ics_url' => array(
				'selector' => 'div',
				'source' => 'text',
				'type' => 'string',
			),
			'r34ics_title' => array(
				'type' => 'string',
			),
			'r34ics_description' => array(
				'type' => 'string',
			),
			'r34ics_view' => array(
				'type' => 'select',
			),
		),
	));
	
	function r34ics_block_render_callback($attributes, $content) {
		ob_start();
		echo do_shortcode('[ics_calendar url="' . esc_url(strip_tags($content)) . '" title="' . esc_attr($attributes['r34ics_title']) . '" description="' . esc_attr($attributes['r34ics_description']) . '" view="' . esc_attr($attributes['r34ics_view']) . '"]');
		return ob_get_clean();
	}

});