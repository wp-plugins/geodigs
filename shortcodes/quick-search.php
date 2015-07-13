<?php
function gd_quick_search_shortcode_handler() {
	wp_enqueue_script('gd_widget_quick_search', GD_URL_JS . 'quick-search.js', array('jquery', 'jquery-ui-autocomplete'), false, true);

	$content     = GeodigsTemplates::loadTemplate(
		'search-forms/quick.php',
		array(
			'form_action' => home_url() . '/' . GD_URL_SEARCH,
		),
		false
	);
	
	return $content;
}