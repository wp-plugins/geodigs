<?php

function gd_listings_shortcode_handler($atts) {
	global $gd_api;
	
	$a = shortcode_atts(
		array(
			'fields' => array()
		),
		$atts
	);
	
	$params = array();
	
	// Convert shortcode att to array
	$fields = explode('?', $a['fields']);
	$fields = explode('&', $fields[1]);
	foreach ($fields as $field) {
		$field             = str_replace('amp;', '', $field);
		$param             = explode('=', $field);
		$params[$param[0]] = $param[1];
	}

	$results = $gd_api->call('GET', 'listings', $params);

	// Start the output
	$hide_edit_search = true;
	ob_start();
	
	include_once GD_DIR_INCLUDES . 'listings-results.php';
	
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}