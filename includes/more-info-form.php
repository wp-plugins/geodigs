<div id="geodigs-more-info" class="wrap">
	<h2><?=esc_attr($_GET['address'])?></h2>
	<p>You can request additional information and/or request a property showing for this property.  Along with your message your details will be shared with the realtor so they may contact you in response to your message.</p>
	<form action="<?='/' . GD_URL_MORE_INFO_REQUESTED?>" method="POST">
<!-- 		Hidden inputs -->
		<input type="hidden" name="listingId" value="<?=esc_attr($_GET['listingId'])?>">
		<input type="hidden" name="address" value="<?=esc_attr($_GET['address'])?>">
		
		<div class="form-group">
			<input type="text" placeholder="Subject" name="subject" class="form-control" required>
			<textarea placeholder="Message" name="message" class="form-control" required></textarea>
		</div>

		<input type="submit" class="btn btn-primary" value="Request More Information">
	</form>
</div>