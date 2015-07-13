<?php
function gd_featured_shortcode_handler() {
	global $gd_api;
	
	// Figure out how to get the listings: random or selected?
	$featured_options = get_option(GD_OPTIONS_FEATURED_LISTINGS);
	// var_dump($_SESSION['gd_agent']);
	if ($featured_options['SelectRandom'] == 'true') {
		$params = array();
		$route  = 'listings';
		
		// Do the listings belong to the agent or the office?
		$our_listings = get_option(GD_OPTIONS_OUR_LISTINGS);

		// Get sort order
		if ($featured_options['Sort'] == 'random') {
			$params['orderBy']          = 'random';
			$params['orderByDirection'] = 'desc';
		}
		else {
			$order_by	                = explode('::', $featured_options['Sort']);
			$params['orderBy']          = $order_by[0];
			$params['orderByDirection'] = $order_by[1];
		}
		
		// Get type
		if (isset($our_listings['Type']) && $our_listings['Type'] != 'all') {
			$params['type'] = $our_listings['Type'];
		}
		// Get limit
		$params['limit'] = $featured_options['NumberOf'];
		// Only active listings
		$params['status'] = 2;

		if ($our_listings['ListingsToDisplay'] == 'agent') {
			$params['agentCode'] = $our_listings['Code'];
		}
		elseif ($our_listings['ListingsToDisplay'] == 'office') {
			$params['officeCode'] = $our_listings['Code'];
		}
		
		$featureds = $gd_api->call('GET', $route, $params);
	}
	else {
		// Get featured listings normally
		$featureds = $gd_api->call('GET', 'featured');
		$route = 'featured';
		$params = array();
	}
	$listings = $featureds->listings;

	// Start output
	ob_start();
	echo '<div id="gd-featured-listings">';

	if($listings) {
		$count = 0;
		foreach($listings as $listing) {
			// Get listing URL and photo
			$listing_url = gd_format_listing_url($listing->address->readable, $listing->id);
			$photo       = $gd_api->url . "listings/{$listing->id}/photo/0?size=small";
			$photo_large = $gd_api->url . "listings/{$listing->id}/photo/0?size=large";
			
			// Get favorite status
			$favorite_status = Geodigs_User::has_favorite($listing->id) ? 'gd-favorite-toggle-on' : '';
			
			GeodigsTemplates::loadTemplate(
				'listings/featured.php',
				array(
					'listing'         => $listing,
					'listing_url'     => $listing_url,
					'photo'           => $photo,
					'photo_large'     => $photo_large,
					'favorite_status' => $favorite_status,
					'count'           => $count,
				)
			);
			
			$count++;
		}
	}

	echo '</div>';
	
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}