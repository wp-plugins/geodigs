<div>
	<p>
		This form allows you to register a domain name and have the domain point directly to that property's detail page on your site.<br />
		The cost of domain name registration is $19.99 per year, which we'll charge to your credit card on file when you submit this form.  
		<br />
	</p>
	<p>
		Once the registration goes through, we'll email you to let you know your new domain is live.
	</p>
</div>
<!--Forms-->
<div>
	<form action="http://geodigs.com/contact/form2email.php" method="post" target="_blank">
		<!-- Hidden inputs -->
		<input type="hidden" name="FORM TYPE" value="Custom domain to property details page.">
		<input type="hidden" name="Submitted_From" value="<?=$_SERVER['HTTP_HOST']?>">
		<input type="hidden" name="Submitted_Time" value="<?=date('m-d-Y g:i A')?>">
		<!-- End hidden inputs -->

		<table class="form-table">
			<tr>
				<th for="required_domain_name">Domain to be registered i.e. YourDomain.com</th>
				<td>
					<input type="text" name="required_domain_name" id="InputName">
				</td>
			</tr>
			<tr>
				<th for="required_email">Email address to receive notificaitons and verify purchase *</th>
				<td>
					<input type="email" name="required_email" id="InputEmail">
				</td>
			</tr>
			<tr>
				<th for="required_MLS_Number">MLS number of property*</th>
				<td>
					<input type="number" name="required_MLS_Number" id="InputNumber">
				</td>
			</tr>
			<tr>
				<th for="message">We will register the domain under YOUR name. If you want the domain name to be owned by someone else, please include that name, address, email and phone number.</th>
				<td>
					<textarea rows="6" name="message"></textarea>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>