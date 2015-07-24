<div id="gd" class="gd-account-settings">
	<form id="geodigs-account-settings-form" method="POST">
		<?php foreach ($errors as $error): ?>
			<div class="alert alert-danger">
				<?=$error?>
			</div>
		<?php endforeach; ?>
		<?php if ($success): ?>
			<div class="alert alert-success">
				Account settings updated
			</div>
		<?php endif; ?>
		<fieldset>
			<h2>Details</h2>
			<div class="pure-g">
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-first-name">First Name*</label>
				</div>
				<div class="col-md-9">
					<input type="text" class="form-control" id="geodigs-account-settings-form-first-name" name="firstName" value="<?=$_SESSION['gd_user']->name->first?>" required>
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-last-name">Last Name*</label>
				</div>
				<div class="col-md-9">
					<input type="text" class="form-control" id="geodigs-account-settings-form-last-name" name="lastName" value="<?=$_SESSION['gd_user']->name->last?>" required>
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-phone">Phone*</label>
				</div>
				<div class="col-md-9">
					<input type="tel" class="form-control" id="geodigs-account-settings-form-phone" name="phone" value="<?=$_SESSION['gd_user']->phone->readable?>" required>
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-email">Email*</label>
				</div>
				<div class="col-md-9">
					<input type="email" class="form-control" id="geodigs-account-settings-form-email" name="email" value="<?=$_SESSION['gd_user']->email?>" required>
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-email-confirm"></label>
				</div>
				<div class="col-md-9">
					<input type="email" class="form-control" id="geodigs-account-settings-form-email-confirm" name="emailConfirm" placeholder="Confirm email">
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-password">Password</label>
				</div>
				<div class="col-md-9">
					<input type="password" class="form-control" id="geodigs-account-settings-form-password" name="password">
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-password-confirm"></label>
				</div>
				<div class="col-md-9">
					<input type="password" class="form-control" id="geodigs-account-settings-form-password-confirm" name="passwordConfirm" placeholder="Confirm password">
				</div>
			</div>
		</fieldset>
		<fieldset>
			<h2>Address</h2>
			<div class="pure-g">
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-street">Street</label>
				</div>
				<div class="col-md-9">
					<input type="text" class="form-control" id="geodigs-account-settings-form-street" name="addressStreet" value="<?=$_SESSION['gd_user']->address->street?>">
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-unit">Unit</label>
				</div>
				<div class="col-md-9">
					<input type="text" class="form-control" id="geodigs-account-settings-form-unit" name="addressUnit" value="<?=$_SESSION['gd_user']->address->unit?>">
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-city">City</label>
				</div>
				<div class="col-md-9">
					<input type="text" class="form-control" id="geodigs-account-settings-form-city" name="addressCity" value="<?=$_SESSION['gd_user']->address->city?>">
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-zip">Zip Code</label>
				</div>
				<div class="col-md-9">
					<input type="text" class="form-control" id="geodigs-account-settings-form-zip" name="addressZip" value="<?=$_SESSION['gd_user']->address->zip?>">
				</div>
				<div class="col-md-3">
					<label for="geodigs-account-settings-form-state">State</label>
				</div>
				<div class="col-md-9">
					<select class="form-control" id="geodigs-account-settings-form-state" name="addressState" value="<?=$_SESSION['gd_user']->address->state?>">
						<?=gd_state_list($_SESSION['gd_user']->address->state)?>
					</select>
				</div>
			</div>
		</fieldset>
		<div class="pure-controls">
			<input type="submit" class="btn btn-primary" value="Update">
        </div>
	</form>
</div>