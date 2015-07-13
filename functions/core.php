<?php

function gd_require_http() {
	if (is_ssl()) {
		header('Location: http://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
	}
}

function gd_require_ssl() {
	if (!is_ssl()) {
		header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
	}
}

function gd_include_google_maps($query) {
	if (!wp_script_is('gd_google_map_api', 'enqueued')) {
		wp_enqueue_script('gd_google_map_api', PROTOCOL . 'maps.google.com/maps/api/js' . $query, false, true);
	}
}

function gd_set_search_params($params) {
	global $gd_search_params;
	
	$gd_search_params = (array) $params;
}
function gd_get_search_param($param) {
	global $gd_search_params;
	
	if (isset($gd_search_params[$param])) {
		return esc_attr($gd_search_params[$param]);
	}
	else {
		return '';
	}
}

/**
 * Checks if the agent has a source
 * @param  int	ID of source
 * @return bool	Agent has source
 */
function gd_agent_has_source($source_id) {
	return array_key_exists($source_id, (array) $_SESSION['gd_agent']->sources);
}

/**
 * Checks if the user has exceeded the max listing details view count
 * @param  string	$listing_id	listing id of requested listing
 * @return bool				if the user has exceeded the view count
 */
function gd_under_detail_view_limit($listing_id) {
	// Checks if the cookie exists
	if (isset($_COOKIE['gd_listings_viewed'])) {
		$cookie             = stripslashes($_COOKIE['gd_listings_viewed']);
		$gd_listings_viewed = json_decode($cookie, true);
		
		// If our cookie fails to decode return false
		if (!is_array($gd_listings_viewed)) {
			return false;
		}
	}
	else {
		$gd_listings_viewed = array();
	}

	// if this is a new listing add it to the record
	if(in_array($listing_id, $gd_listings_viewed) == false) {
		array_push($gd_listings_viewed, $listing_id);
	}

	// if we have reacehd our view limit let us know\
	if(count($gd_listings_viewed) <= $_SESSION['gd_agent']->max_detail_views) {
		setcookie('gd_listings_viewed', json_encode($gd_listings_viewed), time() + (10 * 365 * 24 * 60 * 60), '/'); // expires in 10 years
		return true;
	}
	else {
		return false;
	}
}

/**
 * Resets the listing detail views count
 */
function gd_reset_detail_view_count() {
	if(isset($_COOKIE['gd_listings_viewed']))
	{
		unset($_COOKIE['gd_listings_viewed']);
		setcookie('gd_listings_viewed', '', time() - 3600, '/'); // empty value and old timestamp
	}
}

/**
 * Replaces existing query vars or adds new ones
 * @param  array	$vars	array containing query vars to edit/add
 * @return string			new query string
 */
function gd_edit_query_vars($vars) {
	parse_str($_SERVER['QUERY_STRING'], $query_vars);
	
	foreach ($vars as $key => $val) {
		$query_vars[$key] = $val;
	}
	
	return http_build_query($query_vars);
}
/**
 * Checks if a password is at least 6 characters and contains both a number and letter
 * @param  string $password	password string
 * @return bool				password is valid or not
 */
function gd_is_valid_password($password) {
	if (strlen($password) >= 6 && preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password)) {
		return true;
	}
	else {
		return false;
	}
}

function gd_get_cities() {
	global $gd_api;
	
	$general_options = get_option(GD_OPTIONS_GENERAL);
	if ($general_options['UseCustomCities'] == 'on') {
		$custom_cities = array_keys($general_options['CustomCities']);
		$cities        = array();
		
		foreach ($custom_cities as $city) {
			$cities[] = (object) array('name' => $city);
		}
	}
	else {
		$cities = $gd_api->call("GET", "cities");
	}
	
	return $cities;
}
