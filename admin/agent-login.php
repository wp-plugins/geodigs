<?php
class gd_agent_login {
	function create_login_form() {
		echo '<span class="description">Please enter a valid Geodigs Agent Code and Geodigs API Key to continue</span>';
	}

	/**
	 * Call the API with the key and agent code from the input.
	 * If the call fails to even send then the API key was wrong,
	 * if the call sends but doesn't return a valid agent the code was wrong,
	 * but we don't distinguish between the two.  We treat them the same.
	 * @param  array $input "agent code and api key"
	 * @return array        "status and agent information if successful"
	 */
	function login_validate($input) {
		global $gd_api;

		$output = array();

		if ($input['AgentCode'] && $input['APIKey']) {
			// Test the new API key to validate the agent code
			$gd_api->set_key($input['APIKey']);
			$agent_response = $gd_api->call('GET', 'agents/' . $input['AgentCode']);

			if ($agent_response->error) {
				$output['Status'] = 'invalid';
				add_settings_error('geodigs_login', 'invalid_login', 'Geodigs Agent Code or Geodigs API Key is invalid.');
			}
			else if ($agent_response->name) {
				// Save input values
				$output = $input;
				$output['AgentSource'] = $agent_response->mlsCode->source;
				$output['Status'] = 'success';
				add_settings_error('geodigs_login', 'successful_login', 'Geodigs Login information updated.', 'updated');
			}

		}

		return $output;
	}

	function geodigs_agent_code() {
		$options = get_option('geodigs_login');
		echo '<input type="text" id="geodigs-agent-id" name="geodigs_login[AgentCode]" value="' . $options['AgentCode'] . '">';
		
		// if (GD_LOGIN_STATUS == 'invalid') {
		// 	echo '<br/><span class="description gd-error">Agent code is invalid.</span>';
		// }
	}

	function geodigs_api_key() {
		$options = get_option('geodigs_login');
		echo '<input type="text" id="geodigs-api-key" name="geodigs_login[APIKey]" value="' . $options['APIKey'] . '">';
		
		// if (GD_LOGIN_STATUS == 'invalid') {
		// 	echo '<br/><span class="description gd-error">API Key is invalid.</span>';
		// }
	}
}