<?php
global $gd_api;
?>

<div id="gd" class="gd-details">
	<?php GeodigsTemplates::loadTemplate(
		'listings/details.php',
		array(
			'listing'    => $listing,
			'gd_api_url' => $gd_api->url,
		)
	); ?>
</div>