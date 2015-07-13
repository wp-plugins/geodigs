<?php
function gd_mortgage_calculator_shortcode_handler() {
	wp_enqueue_script('gd-mortgage-calculator', GD_URL_JS . 'mortgage-calculator.js', array('jquery'), false, true);
	
	ob_start(); ?>
	<form action="" id="gd-mortgage-calculator">
		<fieldset>
			<div class="form-group">
				<label for="gd-mortgage-calculator-price">Price</label>
				<input type="text" id="gd-mortgage-calculator-price" onkeyup="dosum()" value="" class="form-control">
				<label for="gd-mortgage-calculator-down-payment">Down Payment</label>
				<input type="text" id="gd-mortgage-calculator-down-payment" onkeyup="dosum()" value="" class="form-control">
				<label for="gd-mortgage-calculator-interest">Interest</label>
				<input type="text" id="gd-mortgage-calculator-interest" onkeyup="dosum()" value="5.25" class="form-control">
				<label for="gd-mortgage-calculator-years">Years</label>
				<input type="text" id="gd-mortgage-calculator-years" onkeyup="dosum()" value="30" class="form-control">
				<label for="gd-mortgage-calculator-annual-taxes">Annual Taxes</label>
				<input type="text" id="gd-mortgage-calculator-annual-taxes" onkeyup="dosum()" value="" class="form-control">
				<label for="gd-mortgage-calculator-insurance">Insurance</label>
				<input type="text" id="gd-mortgage-calculator-insurance" onkeyup="dosum()" value="0" class="form-control">
				<label for="gd-mortgage-calculator-monthly-estimate">Monthly Estimate</label>
				<input type="text" id="gd-mortgage-calculator-monthly-estimate" value="$" class="form-control">
			</div>
			<div class="form-group">
				<input type="button" class="btn btn-primary" value="Reset" onclick="this.form.reset();dosum()">
			</div>
		</fieldset>
	</form>
	<?php
	
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}