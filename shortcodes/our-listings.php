<?php

function gd_our_listings_shortcode_handler() {
	global $gd_api;
	
	$hide_edit_search     = true;
	$our_listings = get_option(GD_OPTIONS_OUR_LISTINGS);
	
	if($our_listings) {
		$route  = 'listings';
		$params = array();
		$source = $_SESSION['gd_agent']->sources->{$our_listings['Source']};

		if ($our_listings['ListingsToDisplay'] == 'agent') {
			$params['agentCode'] = $source->mlsCode;
		}
		elseif ($our_listings['ListingsToDisplay'] == 'office') {
			$params['officeCode'] = $source->officeCode;
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