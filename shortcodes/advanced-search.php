<?php
function gd_advanced_search_shortcode_handler() {
	global $gd_api;
	
	// Get data to populate fields with	
	// For now this is hardcoded but if we decide to add this function to the API then replace the array with the API call
	$listing_levels   = array(
		(object) array(
			'id' => 'Main',
			'readable' => 'Main',
		),
		(object) array(
			'id' => 'Upper',
			'readable' => 'Upper',
		),
		(object) array(
			'id' => 'Basement',
			'readable' => 'Basement',
		),
		(object) array(
			'id' => 'Lower',
			'readable' => 'Lower',
		),
		(object) array(
			'id' => 'Other Finished SqFt',
			'readable' => 'Other Finished SqFt',
		),
	);
	$cities = gd_get_cities();
	
	// Get default listing type
	$adv_search_settings = get_option(GD_OPTIONS_ADVANCED_SEARCH);
	$default_type        = $adv_search_settings['DefaultType'];
	
	// If our current city isn't in the list of available cities, add it to the front
	if (isset($_GET['cities']) && !in_array($_GET['cities'], (array) $cities)) {
		array_unshift($cities, (object) array('name' => $_GET['cities']));
	}
	
	// Form is used to search through listings
	$form_action = '/' . GD_URL_SEARCH;
	$form_method = 'get';
	$form_submit = 'Search';
	
	// Map search
	gd_include_google_maps('?libraries=drawing');
	wp_enqueue_script('gd_google_map_search', GD_URL_JS . 'google-map-search.js', array('gd_google_map_api'), false, true);
	
	// Set our params to the get string params
	gd_set_search_params($_GET);
	
	ob_start();
	?>
	<div class="gd-advanced-search">
		<?php
		GeodigsTemplates::loadTemplate(
			'search-forms/advanced.php',
			array(
				'form_action'        => $form_action,
				'form_method'        => $form_method,
				'form_submit'        => $form_submit,
				'listing_alert_form' => $listing_alert_form,
				'search'             => $search,
				'cities'             => $cities,
				'listing_levels'     => $listing_levels,
			)
		);
		?>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}