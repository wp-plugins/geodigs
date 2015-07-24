<?php
$address = isset($_GET['address']) ? $_GET['address'] : $_SESSION['gd_user']->address->street;
$unit = isset($_GET['unit']) ? $_GET['unit'] : $_SESSION['gd_user']->address->unit;
$city = isset($_GET['city']) ? $_GET['city'] : $_SESSION['gd_user']->address->city;
$zip = isset($_GET['zip']) ? $_GET['zip'] : $_SESSION['gd_user']->address->zip;
$state = isset($_GET['state']) ? $_GET['state'] : $_SESSION['gd_user']->address->state;
?>

<div id="gd" class="gd-home-worth">
	<div class="row">
		<div id="gd-zestimate-info" class="col-md-4">
			<img id="gd-zestimate-logo" src="<?=GD_URL_IMAGES?>/ui/zillow.png" alt="Zillow Zestimate">
			<p>The Zestimate home valuation is Zillow's estimated market value for a home, computed using a proprietary formula. It is a starting point in determining a home's value and is not an official appraisal. The Zestimate is calculated from public and user-submitted data. Updating your home facts can help make your Zestimate more accurate. <a href="http://www.zillow.com/zestimate" target="_blank">Learn more</a></p>
		</div>
		<div class="col-md-8">
	<!-- 		Show results if we have them -->
			<?php if ($_GET['address'] && $_GET['city'] && $_GET['zip'] && $_GET['state']): ?>
				<?php include GD_DIR_INCLUDES . 'home-worth-results.php'; ?>
			<?php endif; ?>

	<!-- 		Zestimate Form -->
			<form method="GET">
				<fieldset class="form-group">
					<input type="text" class="form-control" name="address" value="<?=$address?>" placeholder="Street Address" required>
					<input type="text" class="form-control" name="unit" value="<?=$unit?>" placeholder="Unit">
					<input type="text" class="form-control" name="city" value="<?=$city?>" placeholder="City" required>
					<input type="text" class="form-control" name="zip" value="<?=$zip?>" placeholder="Zip Code" required>
					<select name="state" class="form-control" id="gd-home-worth-state">
						<?=gd_state_list($state)?>
					</select>
				</fieldset>
		<!-- 		<fieldset>
					<div class="pure-control-group">
						<select name="valuation" id="gd-home-worth-valuation">
							<option value="" selected disabled>Click here to select your preferred type of valuation</option>
							<option value="CMA">A complete Comparative Market Analysis from the Hansens</option>
							<option value="Zillo">A real time value of the property from zillow.com and a Real List map.</option>
							<option value="Both">Both a real time and a Comparative Market Analysis</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="gd-home-worth-buying">
							<input type="radio" id="gd-home-worth-buying" name="gd-home-worth-interest">
							I'm interested in buying
						</label>
					</div>
					<div class="pure-control-group">
						<label for="gd-home-worth-selling">
							<input type="radio" id="gd-home-worth-selling" name="gd-home-worth-interest">
							I'm interested in selling
						</label>
					</div>
				</fieldset> -->
				<input type="submit" class="btn btn-primary" style="width: 100%" value="Click here to get your real-time estimate from Zillow!">
			</form>
		</div>
	</div>
</div>