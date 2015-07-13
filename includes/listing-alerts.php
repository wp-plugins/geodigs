<?php
global $gd_api;

switch ($_GET['action']) {
	case 'add':
		if (isset($_POST['search_name'])) {
			// Add new search
			if(Geodigs_User::add_search()) {
				$success = 'Your alert has been saved.';
			}
			else {
				$error = 'Your alert could not be saved.';
			}
		}
		goto show_searches;
		break;
	case 'edit':
		$page_title  = 'Edit Listing Alert';
		$search      = Geodigs_User::get_search($_GET['search_id']);
		$form_action = '/' . GD_URL_LISTING_ALERTS . '?action=update&search_id=' . $_GET['search_id'];
		break;
	case 'update':
		if (isset($_POST['search_name'])) {
			// We lose our POST data when we delete the search so make sure we save it
			Geodigs_User::delete_search($_GET['search_id']);
			if(Geodigs_User::add_search()) {
				$success = 'Your alert has been updated.';
			}
			else {
				$error = 'Your alert could not be updated.';
			}
		}
		goto show_searches;
	case 'delete':
			if(Geodigs_User::delete_search($_GET['search_id'])) {
				$success = 'Your alert has been deleted.';
			}
			else {
				$error = 'Your alert could not be deleted.';
			}
	default:
	show_searches:
		$show_instruct = true;
		$page_title    = 'Listing Alerts';
		$form_action   = '/' . GD_URL_LISTING_ALERTS . '?action=add';
		Geodigs_User::get_searches('array');
}

// We use post for listing alerts
$form_method        = 'post';
$form_submit        = 'Save';
$listing_alert_form = true;

// Get data to populate fields with	
$listing_types    = $gd_api->call("GET", "listings/types");
$listing_statuses = $gd_api->call("GET", "listings/statuses");
$listing_styles   = $gd_api->call("GET", "listings/styles");
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

// If our current city isn't in the list of available cities, add it to the front
if (isset($_GET['cities']) && !in_array($_GET['cities'], (array) $cities)) {
	array_unshift($cities, (object) array('name' => $_GET['cities']));
}

// Map search
if (!wp_script_is('gd_google_map_api', 'enqueued')) {
	wp_enqueue_script('gd_google_map_api', PROTOCOL . 'maps.google.com/maps/api/js?libraries=drawing', false, true);
}
wp_enqueue_script('gd_google_map_search', GD_URL_JS . 'google-map-search.js', array('gd_google_map_api'), false, true);
?>

<!-- Notifcations -->
<?php if ($success): ?>
	<div class="alert alert-success" role="alert">
		<p><?=$success?></p>
	</div>
<?php endif; ?>
<?php if ($error): ?>
	<div class="alert alert-danger" role="alert">
		<p><?=$error?></p>
	</div>
<?php endif; ?>

<div id="gd-create-listing-alert">
<!-- 	Instruction text -->
	<?php if ($show_instruct): ?>
		<h2>
			To Create New Listing Alerts
			<small>
				start by naming your search below and then filling out the applicable fields and selecting "Save Alert."
			</small>
		</h2>
	<?php endif; ?>
	
	<?php gd_set_search_params($search->params); ?>
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
	<br>
<!-- 	Existing alerts -->
	<?php if (count($_SESSION['gd_user']->searches) > 0): ?>
		<div class="gd-saved-listing-alerts">
			<h2>Saved Alerts (<?=count($_SESSION['gd_user']->searches)?>)</h2>
			<?php $sr = 0;
			foreach($_SESSION['gd_user']->searches as $search) {
				// If there is no id this isn't a real search
				if (!$search->id) {
					continue;
				}
				
				$search_link = '/' . GD_URL_SEARCH . '?' . http_build_query($search->params);
				$edit_link   = '/' . GD_URL_LISTING_ALERTS . '?action=edit&search_id=' . $search->id;
				$remove_link = '/' . GD_URL_LISTING_ALERTS . '?action=delete&search_id=' . $search->id;
				
				include GD_DIR_INCLUDES . 'listing-alerts-row.php';
			} ?>
		</div>
	<?php endif; ?>
</div>