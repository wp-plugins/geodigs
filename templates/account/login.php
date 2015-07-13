<div id="gd" class="gd-login">
	<? if (isset($error)): ?>
		<div class="alert alert-danger"><?=$error?></div>
	<? endif; ?>
	
	<form action="<?=$_REQUEST['URI']?>" id="geodigs-login-form" method="POST">
<!-- 		Login type is always geodigs (for now until we have login w/facebook etc) -->
		<input type="hidden" name="type" value="geodigs">
		
		<fieldset class="form-group">
			<input type="text" id="geodigs-login-form-email" class="form-control" name="email" value="<?=esc_attr($_POST['email'])?>" required placeholder="Email">
			<input type="password" id="geodigs-login-form-password" class="form-control" name="password" value="" required placeholder="Password">
		</fieldset>
		<input type="submit" class="btn btn-primary form-control">
<!-- 		<label for="gd-remember-me">Remember Me</label>
		<input type="checkbox" id="gd-remember-me" name="remember_me" value="true"> -->
	</form>
	
	<div>
		<a href="/<?=GD_URL_FORGOT_PASSWORD?>">Forgot your password?</a>
		<div id="gd-signup">
			<span>Don't have an account? <a href="/<?=GD_URL_SIGNUP?>" id="gd-signup-button" class="widefat">Sign up</a></span>
		</div>
	</div>
</div>