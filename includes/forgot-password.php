<div class="wrap">
	<?php if ($page_type == 'form'): ?>
		<form action="<?=$_REQUEST['URI']?>" id="gd-forgot-password-form" method="POST">
			<p>Enter the email address that you used to signup.<br>You will recieve an email with a temporary password to use to login.</p>
			<input type="text" id="gd-forgot-password-form-email" name="email" placeholder="Email address">
			<input type="submit">
		</form>
<!-- 		Errors -->
		<?php foreach ($errors as $error => $desc): ?>
			<p class="gd-error"><?=$desc?></p>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<?php if ($page_type == 'success'): ?>
		<h4>An email with a temporary password has been sent to you!</h4>
		<a href="/<?=GD_URL_LOGIN?>">Click here to login</a>
	<?php endif; ?>
</div>