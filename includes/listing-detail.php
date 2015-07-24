<?php
global $gd_api;
			
// Get favorite status
$favorite_status = Geodigs_User::has_favorite($listing->id) ? 'gd-favorite-toggle-on' : '';
?>

<div id="gd" class="gd-details">
	<?php GeodigsTemplates::loadTemplate(
		'listings/details.php',
		array(
			'listing'    => $listing,
			'gd_api_url' => $gd_api->url,
			'favorite_status' => $favorite_status,
		)
	); ?>
</div>