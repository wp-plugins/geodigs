<?php
global $gd_api;

$errors  = array();
$success = false;

if ($_POST) {
	if ($_SESSION['gd_user']->email != $_POST['email'] && $_POST['emailConfirm'] != $_POST['email']) {
		$errors['emails'] = 'Emails do not match';
	}
	if ($_POST['password'] != '') {
		if ($_POST['password'] != $_POST['passwordConfirm']) {
			$errors['passwords'] = 'Passwords do not match';
		}
		elseif ($_POST['password'] == $_POST['passwordConfirm'] && !gd_is_valid_password($_POST['password'])) {
			$errors['password_invalid'] = 'Password must contain at least 6 characters with at least 1 letter and 1 number';
		}
	}
	else {
		unset($_POST['password']);
	}
	
	$_POST['phone'] = preg_replace('/\D+/', '', $_POST['phone']);
	if (strlen($_POST['phone']) != 10) {
		$errors['phone_length'] = 'Invalid phone number';
	}
	
	if (count($errors) == 0) {
		// Unset uneeded vars
		if (isset($_POST['emailConfirm'])) {
			unset($_POST['emailConfirm']);
		}
		if (isset($_POST['passwordConfirm'])) {
			unset($_POST['passwordConfirm']);
		}
		
		$_POST['id'] = $_SESSION['gd_user']->id;
		
		// Uses the $_POST data
		$response = $gd_api->call('PATCH', 'users');
		if (!isset($response->error)) {
			$user_info = Geodigs_User::get_user_info($_SESSION['gd_user']->id);
			if (Geodigs_User::update_user_info($user_info)) {
				$success = true;
			}
		}
	}
}

GeodigsTemplates::loadTemplate(
	'account/settings.php',
	array(
		'errors' => $errors,
		'success' => $success,
	));