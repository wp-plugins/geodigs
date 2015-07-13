<?php
if ($_POST) {
	
	// Send confirmation email
	$to	      = get_bloginfo('admin_email');
	$subject  = 'More Information Request: ' . sanitize_text_field($_POST['subject']);
	$headers  = 'From: ' . $_SESSION['gd_user']->email . "\r\n";
	$headers .= 'Reply-To: ' . $_SESSION['gd_user']->email . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	
	// User info
	$name    = $_SESSION['gd_user']->name->full;
	$email   = $_SESSION['gd_user']->email;
	$phone   = $_SESSION['gd_user']->phone->readable;
	$address = $_SESSION['gd_user']->address->readable;
	
	// Request info
	$listing_id      = sanitize_text_field($_POST['listingId']);
	$listing_address = sanitize_text_field($_POST['address']);
	$message         = sanitize_text_field($_POST['message']);
	
	ob_start();
	include GD_DIR_EMAILS . 'more-info-request.php';
	$contents = ob_get_contents();
	ob_end_clean();
	
	// Send it
	mail($to, $subject, $contents, $headers);
}
?>
<div id="geodigs-more-info-requested" class="wrap">
	<h3>You will be contacted about your request soon!</h3>
</div>