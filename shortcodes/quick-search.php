<?php
function gd_quick_search_shortcode_handler() {
	wp_enqueue_script('gd_widget_quick_search', GD_URL_JS . 'quick-search.js', array('jquery', 'jquery-ui-autocomplete'), false, true);

	// Get agent sources
	$sources = array();
	foreach ($_SESSION['gd_agent']->sources as $source) {
		array_push($sources, $source->id);
	}
	$sources = implode($sources, ',');
	
	$content = GeodigsTemplates::loadTemplate(
		'search-forms/quick.php',
		array(
			'form_action' => home_url() . '/' . GD_URL_SEARCH,
			'sources'     => $sources,
		),
		false
	);
	
	return $content;
}