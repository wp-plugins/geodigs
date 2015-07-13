<!-- START: listing-results.php -->
<div id="gd" class="gd-results">
	<!-- If we have listings show them -->
	<?php 
	// Prepare the JS data to be passed
	$latitudes     = array();
	$longitudes    = array();
	$map_previews  = array();
	$listings      = $results->listings;
	$listing_count = count($listings);

	if (isset($listings) && $listing_count > 0) {
		// Get layout settings
		$general_options = get_option(GD_OPTIONS_GENERAL);
		$layout          = '';
		// If our layout is 'rows' then we are using the dsIDX layout
		if (isset($general_options['ListingsLayout']) && $general_options['ListingsLayout'] != 'rows') {
			$layout = '-' . $general_options['ListingsLayout'];
		}
		
		/** Header **/
		
		// Get sort options
		$sorts = array(
			array('orderBy' => 'price', 'orderByDirection' => 'asc', 'label' => 'Price: Low to High'),
			array('orderBy' => 'price', 'orderByDirection' => 'desc', 'label' => 'Price: High to Low'),
			array('orderBy' => 'area', 'orderByDirection' => 'asc', 'label' => 'Area: Low to High'),
			array('orderBy' => 'area', 'orderByDirection' => 'desc', 'label' => 'Area: High to Low'),
			array('orderBy' => 'beds', 'orderByDirection' => 'asc', 'label' => 'Bedrooms: Low to High'),
			array('orderBy' => 'beds', 'orderByDirection' => 'desc', 'label' => 'Bedrooms: High to Low'),
			array('orderBy' => 'baths', 'orderByDirection' => 'asc', 'label' => 'Bathrooms: Low to High'),
			array('orderBy' => 'baths', 'orderByDirection' => 'desc', 'label' => 'Bathrooms: High to Low'),
		);
		ob_start();
		?>
		<option value="">None</option>
		<?php foreach ($sorts as $sort): ?>
			<?php
			$value    = $sort['orderBy'] . '::' . $sort['orderByDirection'];
			$selected = $_GET['orderBy'] == $sort['orderBy'] && $_GET['orderByDirection'] == $sort['orderByDirection'] ? 'selected' : '';
			?>
			<option value="<?=$value?>" <?=$selected?>><?=$sort['label']?></option>
		<?php endforeach; ?>
		<?php
		$sort_options = ob_get_contents();
		ob_end_clean();
		
		// Output listings header template
		GeodigsTemplates::loadTemplate(
			'listings/header.php',
			array(
				'show_map'         => true,
				'show_edit_search' => true,
				'show_sort'        => true,
				'show_count'       => true,
				'sort_options'     => $sort_options,
				'edit_search_link' => '/' . GD_URL_ADV_SEARCH . '?' . $_SERVER['QUERY_STRING'],
				'listings_start'   => $results->pagination->currentListings->start->readable,
				'listings_end'     => $results->pagination->currentListings->end->readable,
				'listings_total'   => $results->pagination->totalListings->readable,
			)
		);
	
		// Get the listings
		$listing_results = '';
		$count           = 0;
		foreach($listings as $listing) {
			// Get photo
			$photo       = $gd_api->url . "listings/{$listing->id}/photo/0?size=small";
			$photo_large = $gd_api->url . "listings/{$listing->id}/photo/0?size=large";

			// Get the address and URL
			$address     = isset($listing->address->readable) ? $listing->address->readable : 'No Address';
			$listing_url = gd_format_listing_url($address, $listing->id);
			
			// Get favorite status
			$favorite_status = Geodigs_User::has_favorite($listing->id) ? 'gd-favorite-toggle-on' : '';

			// Add map coords. for JS data
			array_push($latitudes, $listing->coords->lat);
			array_push($longitudes, $listing->coords->lon);

			// Add map preview for JS data
			array_push(
				$map_previews,
				GeodigsTemplates::loadTemplate(
					'listings/map-preview.php',
					array(
						'listing'     => $listing,
						'listing_url' => $listing_url,
						'photo'       => $photo,
					),
					false
				)
			);

			// Load the listing result template
			switch ($general_options['ListingsLayout']) {
				case 'columns-2':
					$template = 'listings/columns-2.php';
					break;
				
				case 'rows':
				default;
					$template = 'listings/row.php';
					break;
			}
			$listing_results .= GeodigsTemplates::loadTemplate(
				$template,
				array(
					'listing'         => $listing,
					'address'         => $address,
					'listing_url'     => $listing_url,
					'photo'           => $photo,
					'photo_large'     => $photo_large,
					'favorite_status' => $favorite_status,
					'loop_count'      => $count,
				),
				false
			);
			
			$count++;
		}
		
		// Load listing results template
		GeodigsTemplates::loadTemplate(
			'listings/results.php',
			array(
				'layout'          => $layout,
				'listing_results' => $listing_results,
			)
		);

		// Get pagination
		ob_start();
		include_once GD_DIR_INCLUDES . 'listings-pagination.php';
		$pagination = ob_get_contents();
		ob_end_clean();
		
		// Load listing footer template
		GeodigsTemplates::loadTemplate(
			'listings/footer.php',
			array(
				'pagination' => $pagination,
			)
		);
		

		// Get the info to setup our map view radiuses
		$center_listing    = $listing_count / 2;
		$center_lon_radius = $longitudes[$center_listing];
		$center_lat_radius = $latitudes[$center_listing];

		// Create the JS data to be passed
		$js_data = array(
			'latitudes'         => $latitudes,
			'longitudes'        => $longitudes,
			'map_previews'      => $map_previews,
			'listing_count'     => $listing_count,
			'center_lon_radius' => $center_lon_radius,
			'center_lat_radius' => $center_lat_radius,
		);
		gd_include_google_maps();
		wp_enqueue_script('gd_google_map_results', GD_URL_JS . 'google-map-results.js', array('gd_google_map_api'), false, true);
		wp_localize_script('gd_google_map_results', 'php_vars', $js_data);
	}
	else {
		echo '<h2>No listings found</h2>';
	} ?>
</div>
<!-- END: listing-results.php -->