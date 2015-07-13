<div class="wrap">
	<form id="geodigs-signup-form" method="POST">
		<?php foreach ($errors as $error): ?>
			<div class="alert alert-danger">
				<?=$error?>
			</div>
		<?php endforeach; ?>
<!-- 			Hidden Fields -->
<!-- 		Login type is always geodigs (for now until we have login w/facebook etc) -->
		<input type="hidden" name="type" value="geodigs">
		<input type="hidden" name="agentCode" value="<?=$_SESSION['gd_agent']->code?>">
<!-- 			End Hidden Fields -->
		
<!-- 		Notes -->
		<div>
			<small>* denotes a required field</small>
		</div>
<!-- 		End Notes -->
		
		<div class="form-group">
<!-- 			General info -->
			<input type="text" class="form-control" id="geodigs-signup-form-first-name" name="firstName" required placeholder="First Name*" value="<?=esc_attr($_POST['firstName'])?>">
			<input type="text" class="form-control" id="geodigs-signup-form-last-name" name="lastName" required placeholder="Last Name*" value="<?=esc_attr($_POST['lastName'])?>">
			<input type="text" class="form-control" id="geodigs-signup-form-phone" name="phone" required placeholder="Phone Number*" value="<?=esc_attr($_POST['phone'])?>">
		</div>
		<div class="form-group">
<!-- 		Email -->
			<input type="text" class="form-control" id="geodigs-signup-form-email" name="email" required placeholder="Email*" value="<?=esc_attr($_POST['email'])?>">
			<input type="text" class="form-control" id="geodigs-signup-form-email-confirm" name="emailConfirm" required placeholder="Confirm email*" value="<?=esc_attr($_POST['emailConfirm'])?>">
		</div>
		<div class="form-group">
<!-- 		Password -->
			<input type="password" class="form-control" id="geodigs-signup-form-password"  name="password" required placeholder="Password*" value="<?=esc_attr($_POST['password'])?>">
			<input type="password" class="form-control" id="geodigs-signup-form-password-confirm"  name="passwordConfirm" required placeholder="Confirm password*">
		</div>
		
		<h2>Address</h2>
		<div class="form-group">
<!-- 			Street/Unit -->
			<div class="row form-group">
				<div class="col-sm-8">
					<input type="text" class="form-control" id="geodigs-signup-form-street" name="addressStreet" placeholder="Street" value="<?=esc_attr($_POST['addressStreet'])?>">
				</div>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="geodigs-signup-form-unit" name="addressUnit" placeholder="Unit" value="<?=esc_attr($_POST['addressUnit'])?>">
				</div>
			</div>

<!-- 			City/Zip -->
			<div class="row form-group">
				<div class="col-sm-8">
					<input type="text" class="form-control" id="geodigs-signup-form-city" name="addressCity" placeholder="City" value="<?=esc_attr($_POST['addressCity'])?>">
				</div>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="geodigs-signup-form-zip" name="addressZip" placeholder="Zip" value="<?=esc_attr($_POST['addressZip'])?>">
				</div>
			</div>
			<select id="geodigs-signup-form-state" name="addressState">
				<?=gd_state_list($_POST['addressState'])?>
			</select>
		</div>
		<div>
	<!-- 		Submit -->
			<input type="submit" class="btn btn-primary">
		</div>
	</form>
</div>