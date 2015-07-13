<?php

class Geodigs_User {
	
	/**
	 * Login using the API data
	 * param $api_data array	data retrieved from the API
	 */
	public static function login($api_data) {
		global $gd_api;
		
		// Build user obj
		$_SESSION['gd_user'] = $api_data;
		Geodigs_User::update_user_info(Geodigs_User::get_user_info($_SESSION['gd_user']->userId));
		
		// Get our favorite listing ids
		Geodigs_User::get_favorites();

		$redirect_url = home_url();
		// If a redirect URL was supplied use it and unset it after
		if (isset($_SESSION['gd_redirect_url'])) {
			// Makes sure we have a slash at the beginning
			if ($_SESSION['gd_redirect_url'][0] !== '/') {
				$redirect_url .= '/';
			}
			$redirect_url .= $_SESSION['gd_redirect_url'];
			unset($_SESSION['gd_redirect_url']);
		}
		
		// Reset view count history
		gd_reset_detail_view_count();
		
		// Redirect
		wp_redirect($redirect_url, 200);
		exit;
	}
	
	/**
	 * Login the agent as an user
	 */
	public static function login_as($userId) {
		global $gd_api, $login_info;
		
		// Store the public key for later
		$public_key = $gd_api->get_key();
		// Switch to the agent's private key
		$gd_api->set_key($login_info['APIKey']);
		
		// Remove any previous user data
		unset($_SESSION['gd_user']);
		
		// Get info for user
		Geodigs_User::update_user_info(Geodigs_User::get_user_info($userId));
		Geodigs_User::get_favorites();
		
		// Reset view count history
		gd_reset_detail_view_count();
		
		// Reset API key
		$gd_api->set_key($public_key);
		
		// Send agent to user home page
		header('Location: /' . GD_URL_ACCOUNT);
		exit;
	}
	
	public static function get_user_info($userId) {
		global $gd_api;
		
		return $gd_api->call('GET', 'users/' . $userId);
	}
	
	public static function update_user_info($data) {
		if ($_SESSION['gd_user'] = (object) array_merge((array) $_SESSION['gd_user'], (array) $data)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * If we are logged in log out and return to home page
	 */
	public static function log_out($redirect = true) {
		if (Geodigs_User::is_logged_in()) {
			// Delete user session
			unset($_SESSION['gd_user']);
		}

		if ($redirect == true) {
			// Redirect to home
			wp_redirect(home_url(), 200);
			exit;
		}
	}
	
	/**
	 * Checks to see if there is a user object stored in the session data
	 * @return bool				user object is set
	 */
	public static function is_logged_in() {
		return isset($_SESSION['gd_user']);
	}
	
	/**
	 * Requires the user to be logged in before proceeding
	 * @param	string	$redirect_url	url to redirect to after login is successful
	 */
	public static function require_login($redirect_url) {
		if (Geodigs_User::is_logged_in() === false) {
			// Redirect to the home page if a url is not specified
			if (!isset($redirect_url)) {
				$redirect_url = home_url();
			}
			$_SESSION['gd_redirect_url'] = $redirect_url;
			wp_redirect(home_url() . '/' . GD_URL_LOGIN, 200);
			exit;
		}
	}
	
	/**
	 * Checks to see if there is a user object stored in the session data
	 * @param  bool $redirect	redirect to login page if not logged in
	 * @return bool				user object is set
	 */
	public static function has_favorite($listing_id) {
		$is_favorite = false;
		
		foreach ($_SESSION['gd_user']->favorites->listings as $listing) {
			if ($listing->id == $listing_id) {
				$is_favorite = true;
				break;
			}
		}
		
		return $is_favorite;
	}
	
	/**
	 * Gets the user's favorite listings id's
	 */
	public static function get_favorites() {
		global $gd_api;
		
		$response = $gd_api->call('GET', 'favorites', array('fields' => 'id'));
		if (!$response->error) {
			$_SESSION['gd_user']->favorites = $response;
		}
	}
	
	/**
	 * Add a favorite
	 * param $listing_id string	ID of listing to add
	 */
	public static function add_favorite($listing_id) {
		global $gd_api;
		
		$response = $gd_api->call('POST', 'favorites/' . $listing_id);
		// Get our new favorite listing ids
		Geodigs_User::get_favorites();
		
		wp_send_json($response);
	}
	
	/**
	 * Delete a favorite
	 * param $listing_id string	ID of listing to delete
	 */
	public static function delete_favorite($listing_id) {
		global $gd_api;
		
		$response = $gd_api->call('DELETE', 'favorites/' . $listing_id);
		// Get our new favorite listing ids
		Geodigs_User::get_favorites();
		
		wp_send_json($response);
	}
	
	/**
	 * Gets the user's saved searches
	 * param $format string	Format to store in
	 */
	public static function get_searches($format = 'object') {
		global $gd_api;
		
		$response = $gd_api->call('GET', 'searches');
		if (!$response->error) {
			if ($format == 'array') {
				$_SESSION['gd_user']->searches = get_object_vars($response);
			}
			else {
				$_SESSION['gd_user']->searches = $response;
			}
		}
	}
	
	/**
	 * Get a saved search
	 * param	$search_id string	ID of search to get
	 * return	$array				Search details
	 */
	public static function get_search($search_id) {
		global $gd_api;
		
		$response   = $gd_api->call('GET', 'searches/' . $search_id); // Returns as an object with each result being an object of that object
		$search     = get_object_vars($response); // Convert the request to an array
		
		
		return $search[$search_id]; // Get the first item in the array which is our chosen search
	}
	
	/**
	 * Save a search for the user to use for listing alerts
	 */
	public static function add_search() {
		global $gd_api;
		
		// Convert our params to a string
		$param_str = '';
		foreach ($_POST as $key => $value) {
			if ($key != 'push' && $key != 'text' && $key != 'email' && $key != 'freq' && $key != 'search_name') {
				if ($value != '') {
					if ($key == 'polygon') {
						// Replace the commas because the API recognizes commas as metacharacters
						$value = str_replace(',', '|', $value);
					}
					$param_str .= $key . ':' . $value . ',';
				}
				unset($_POST[$key]);
			}
		}
		$_POST['params'] = rtrim($param_str, ',');
		
		// Make sure we fill all of our API required vars
		if (!isset($_POST['push'])) {
			$_POST['push'] = '0';
		}
		if (!isset($_POST['text'])) {
			$_POST['text'] = '0';
		}
		if (!isset($_POST['email'])) {
			$_POST['email'] = '0';
		}
		
		// WP uses 'name' so we can only use it temorarily
		if (isset($_POST['name'])) $name = $_POST['name'];
		$_POST['name'] = $_POST['search_name'];
		unset($_POST['search_name']);
		
		// Make API request
		$response = $gd_api->call('POST', 'searches');
		
		// Reset 'name'
		if (isset($name)) $_POST['name'] = $name;
		
		return $response;
	}
	
	/**
	 * Delete a saved search
	 * param $search_id string	ID of search to delete
	 */
	public static function delete_search($search_id) {
		global $gd_api;
		
		$response = $gd_api->call('DELETE', 'searches/' . $search_id);
		
		if ($response->error) {
			return false;
		}
		else {
			return true;
		}
	}
}