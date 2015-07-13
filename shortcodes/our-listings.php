<?php

function gd_our_listings_shortcode_handler() {
	global $gd_api;
	
	$hide_edit_search     = true;
	$our_listings = get_option(GD_OPTIONS_OUR_LISTINGS);
	
	if($our_listings) {
		$route  = 'listings';
		$params = array();

		if ($our_listings['ListingsToDisplay'] == 'agent') {
			$params['agentCode'] = $our_listings['Code'];
		}
		elseif ($our_listings['ListingsToDisplay'] == 'office') {
			$params['officeCode'] = $our_listings['Code'];
		}
		
		// Get type
		if (isset($our_listings['Type']) && $our_listings['Type'] != 'all') {
			$params['type'] = $our_listings['Type'];
		}
		
		// Only active listings
		$params['status'] = 2;

		$results = $gd_api->call('GET', $route, $params);
	}

	// Start the output
	ob_start();
	
	include GD_DIR_INCLUDES . 'listings-results.php';
	
	$content = ob_get_contents();
	ob_end_clean();
	
	return $content;
}