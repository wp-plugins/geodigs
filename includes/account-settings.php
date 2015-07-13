<?php
global $gd_api;

$errors = array();

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
			Geodigs_User::update_user_info($user_info);
		}
	}
}
?>

<div class="wrap">
	<form action="" id="geodigs-account-settings-form" class="gd-form-full" method="POST">
		<?php if (count($errors) > 0): ?>
			<div id="geodigs-account-settings-form-errors">
			<?php foreach ($errors as $error): ?>
				<span class="gd-error"><?=$error?></span><br>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<fieldset>
			<h2>Details</h2>
			<div class="pure-g">
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-first-name">First Name</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-first-name" name="firstName" value="<?=$_SESSION['gd_user']->name->first?>" required>
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-last-name">Last Name</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-last-name" name="lastName" value="<?=$_SESSION['gd_user']->name->last?>" required>
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-phone">Phone</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-phone" name="phone" value="<?=$_SESSION['gd_user']->phone->readable?>" required>
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-email">Email</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-email" name="email" value="<?=$_SESSION['gd_user']->email?>" required>
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-email-confirm"></label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-email-confirm" name="emailConfirm" placeholder="Confirm email">
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-password">Password</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="password" id="geodigs-account-settings-form-password" name="password">
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-password-confirm"></label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="password" id="geodigs-account-settings-form-password-confirm" name="passwordConfirm" placeholder="Confirm password">
				</div>
			</div>
		</fieldset>
		<fieldset>
			<h2>Address</h2>
			<div class="pure-g">
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-street">Street</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-street" name="addressStreet" value="<?=$_SESSION['gd_user']->address->street?>">
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-unit">Unit</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-unit" name="addressUnit" value="<?=$_SESSION['gd_user']->address->unit?>">
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-city">City</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-city" name="addressCity" value="<?=$_SESSION['gd_user']->address->city?>">
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-zip">Zip Code</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<input type="text" id="geodigs-account-settings-form-zip" name="addressZip" value="<?=$_SESSION['gd_user']->address->zip?>">
				</div>
				<div class="pure-u-md-1-4 pure-u-24-24">
					<label for="geodigs-account-settings-form-state">State</label>
				</div>
				<div class="pure-u-md-3-4 pure-u-24-24">
					<select id="geodigs-account-settings-form-state" name="addressState" value="<?=$_SESSION['gd_user']->address->state?>">
						<?=gd_state_list($_SESSION['gd_user']->address->state)?>
					</select>
				</div>
			</div>
		</fieldset>
		<div class="pure-controls">
			<input type="submit" value="Update">
        </div>
	</form>
</div>