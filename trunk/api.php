<?php

class GeodigsApi {
	public $key;
	public $url = 'http://api.geodigs.com/v1/';

	public function __construct($key = '') {
		$this->key = $key;
	}

	public function set_key($key) {
		$this->key = $key;
	}

	public function get_key() {
		return $this->key;
	}

	/**
	 * Sets up the call to the Geodigs API
	 * @param  string $method     type of request
	 * @param  string $route      resource for request
	 * @param  array  $parameters optional parameters to add onto the call
	 * @return json_object        API response
	 */
	public function call($method, $route, $parameters = array()) {
		switch ($method) {
			case 'DELETE':
				return $this->delete($route, $parameters);
			
			case 'GET':
				return $this->get($route, $parameters);

			case 'PATCH':
				return $this->patch($route, $parameters);

			case 'POST':
				return $this->post($route, $parameters);

			case 'PUT':
				return $this->put($route, $parameters);
			
			default:
				return 'ERROR: Unknown API method: ' . $method;
		}
	}

	public function delete($method, $parameters) {
		$url = $this->url . $method;

		// Build our parameters
		// NOTE: $parameters values take precident
		$params = array_merge($_GET, $parameters);

		// Add the params to the URL
		$url .= $this->construct_parameters($params);

		$response = $this->curl_url($url, $_POST, 'DELETE');
		return json_decode($response);
	}

	public function get($method, $parameters) {
		$url = $this->url . $method;

		// Build our parameters
		// NOTE: $parameters values take precident
		$params = array_merge($_GET, $parameters);
		
		// 'listingsPage' isn't a real param for the API
		// it's used because WP uses 'page' already so if it exists
		// make sure to instead pass it as 'page'
		if (isset($params['listingsPage'])) {
			$params['page'] = $params['listingsPage'];
			unset($params['listingsPage']);
		}

		// Add the params to the URL
		$url .= $this->construct_parameters($params);

		$response = $this->curl_url($url);
		return json_decode($response);
	}

	public function patch($method, $parameters) {
		$url = $this->url . $method;

		// Add the params to the URL
		$url .= $this->construct_parameters($parameters);

		$response = $this->curl_url($url, $_POST, 'PATCH');
		return json_decode($response);
	}

	public function post($method, $parameters) {
		$url = $this->url . $method;

		// Build our parameters
		// NOTE: $parameters values take precident
		$params = array_merge($_GET, $parameters);

		// Add the params to the URL
		$url .= $this->construct_parameters($params);

		$response = $this->curl_url($url, $_POST, 'POST');
		return json_decode($response);
	}

	public function put($method, $parameters) {
		$url = $this->url . $method;

		// Build our parameters
		// NOTE: $parameters values take precident
		$params = array_merge($_GET, $parameters);

		// Add the params to the URL
		$url .= $this->construct_parameters($params);

		$response = $this->curl_url($url, $_POST, 'PUT');
		return json_decode($response);
	}

	public function curl_url($url, $post_data = null, $request = '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		// If the user is logged in use their API Key
		if (!is_admin() && isset($_SESSION["gd_user"])) {
			$key = $_SESSION["gd_user"]->apiKey;
		}
		else {
			$key = $this->key;
		}
		
		if (empty($key)) {
			echo 'ERROR: No API key provided.';
			return;
		}
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('API-Key: ' . $key, 'API-Debug: disabled'));
		
		if ($post_data) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		}
		
		switch ($request) {
			case 'DELETE':
	    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			
			case 'PATCH':
	    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
				break;
			
			case 'PUT':
	    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				break;
			
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				break;
			
		}

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Build a string to add to our url for an API call
	 * @param  [array $params array of parameters to add
	 * @return string         URL encoded string of parameters
	 */
	private function construct_parameters($params = array()) {
		if (empty($params) == false) {
			$params_str	= '?';
			$first		= true;

			foreach ($params as $key => $value) {
				// If this param is not the first one add the '&' symbol
				if (!$first) {
					$params_str .= '&';
				}
				else {
					$first = false;
				}

				// Add the key + value pair
				$params_str .= urlencode($key) . '=' . urlencode($value);
			}

			return $params_str;
		}
		else {
			return '';
		}
	}
}