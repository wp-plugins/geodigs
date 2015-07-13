<?php
/**
 * Notes
 *
 * 	- THIS TEMPLATE IS USED IN MORE THAN ONE LOCATION
 * 		- This template is used for the advanced search and for creating new listing alerts.
 * 		- To make changes specific to listing alerts make sure they are wrapped in a conditional statement checking for $listing_alert_form == true
 * 	- gd_get_search_param($param) checks to make sure the requested param actually exists in the param collection.
 * 		- The param collection differs for advanced search and for listing alerts so this function must be used in order to ensure compatability for all use cases
 * 	- $form_action, $form_method, $form_submit differ depending on where this template is being used.  These must be used in order to ensure compatability for all use cases
 * 	- Select elements with the class "ajax-list" are populated after initial page load in order to improve performance
 * 	- gd_agent_has_source($sourceId) is used to check for different sources (1 = IRES, 2 = Metrolist, etc)
 * 	- To see all data for a search object call var_dump($search)
 */
?>

<form action="<?=$form_action?>" method="<?=$form_method?>">
	<?php if ($listing_alert_form): ?>
<!-- 		Name (only for listing alerts) -->
		<input type="text" name="search_name" id="gd-listing-alerts-name" class="form-control" placeholder="Name" value="<?=$search->name?>" required>
	<?php endif; ?>
	<!-- Map search -->
	<fieldset>
		<input type="hidden" name="polygon" value="<?=gd_get_search_param('polygon')?>">
<!-- 				Circles -->
		<input type="hidden" name="lat" value="<?=gd_get_search_param('lat')?>">
		<input type="hidden" name="lon" value="<?=gd_get_search_param('lon')?>">
		<input type="hidden" name="radius" value="<?=gd_get_search_param('radius')?>">
<!-- 				Rectangles -->
		<input type="hidden" name="minLat" value="<?=gd_get_search_param('minLat')?>">
		<input type="hidden" name="minLon" value="<?=gd_get_search_param('minLon')?>">
		<input type="hidden" name="maxLat" value="<?=gd_get_search_param('maxLat')?>">
		<input type="hidden" name="maxLon" value="<?=gd_get_search_param('maxLon')?>">
	</fieldset>
<!-- 	Map -->
	<div class="form-group">
		<?php if (gd_get_search_param('edit_search')): ?>
			<div>
				<p>
					The green shape below shows your previous selected area.  <a href="#" id="gd-map-reset">Click here</a> to reset the map.
				</p>
			</div>
		<?php endif; ?>
		<div id="gd-map"></div>
	</div>
	<!-- Submit Button -->
	<div class="form-group">
		<input type="submit" class="btn btn-primary" value="<?=$form_submit?>"/>
	</div>

	<div class="row">
		<!-- Search by address -->
		<div class="form-group col-md-6">
			<h3>Address</h3>
			<label>Street Number</label>
			<input class="form-control" type="text" name="streetNumber" value="<?=gd_get_search_param('streetNumber')?>">
			<label>Street Name</label>
			<input class="form-control" type="text" name="streetName" value="<?=gd_get_search_param('streetName')?>">
			<label>Unit</label>
			<input class="form-control" type="text" name="unit" value="<?=gd_get_search_param('unit')?>">
			<label>Zip</label>
			<input class="form-control number-input" type="text" name="zip" value="<?=gd_get_search_param('zip')?>">
			<label>Cities</label>
			<select class="form-control" name="cities">
				<option value="">Any</option>
				<?foreach($cities as $city):?>
					<option value="<?=$city->name?>" <?php selected($city->name, gd_get_search_param('cities')); ?>><?=$city->name?></option>
				<?endforeach;?>
			</select>
			<label>County</label>
			<input class="form-control" type="text" name="county" value="<?=gd_get_search_param('county')?>">
		</div>

		<!-- Neighborhood Details -->
		<div class="form-group col-md-6">
			<h3>Neighborhood Details</h3>
			<!-- Elementary School -->
			<label>Elementary School</label>
			<input class="form-control" type="text" name="schoolElementary" value="<?=gd_get_search_param('schoolElementary')?>">
			<!-- Middle School -->
			<label>Middle School</label>
			<input class="form-control" type="text" name="schoolMiddle" value="<?=gd_get_search_param('schoolMiddle')?>">
			<!-- High School -->
			<label>Hight School</label>
			<input class="form-control" type="text" name="schoolHigh" value="<?=gd_get_search_param('schoolHigh')?>">
		</div>
	</div>
	<!-- Property Details -->
	<div class="form-group">
		<h3>Property Details</h3>
		<div class="row">
			<div class="col-md-4">
				<!-- Price -->
				<label>Price</label>
				<div class="row">
					<div class="col-xs-6">
						<input class="form-control number-input" type="text" name="minPrice" value="<?=gd_get_search_param('minPrice')?>" placeholder="Min">
					</div>
					<div class="col-xs-6">
						<input class="form-control number-input" type="text" name="maxPrice" value="<?=gd_get_search_param('maxPrice')?>" placeholder="Max">
					</div>
				</div>

				<!-- Beds -->
				<label>Bedrooms</label>
				<select class="form-control" name="minBed">
					<option value="">Any</option>
					<?php for ($i = 1; $i < 13; $i++): ?>
						<option value="<?=$i?>" <?php if ($i == gd_get_search_param('minBed')) echo 'selected'; ?>><?=$i?>+</option>
					<?php endfor; ?>
				</select>

				<!-- Baths -->
				<label>Bathrooms</label>
				<select class="form-control" name="minBath">
					<option value="">Any</option>
					<?php for ($i = 1; $i < 13; $i++): ?>
						<option value="<?=$i?>" <?php if ($i == gd_get_search_param('minBath')) echo 'selected'; ?>><?=$i?>+</option>
					<?php endfor; ?>
				</select>

				<!-- Year Built -->
				<label>Year Built</label>
				<div class="row">
					<div class="col-xs-6">
						<input class="form-control number-input" type="text" name="minYearBuilt" value="<?=gd_get_search_param('minYearBuilt')?>" placeholder="Min">
					</div>
					<div class="col-xs-6">
						<input class="form-control number-input" type="text" name="maxYearBuilt" value="<?=gd_get_search_param('maxYearBuilt')?>" placeholder="Max">
					</div>
				</div>
				<!-- Master Bedroom Level -->
				<!-- This is only added if the agent has Metrolist listings -->
				<?php if (gd_agent_has_source(2)): ?>
<!-- 					<label>Master Bedroom Level</label>
					<select name="masterBedLevel">
						<option value="">Any</option>
						<?foreach($listing_levels as $level):?>
							<option value="<?=$level->id?>"  <?php selected($level->id, gd_get_search_param('masterBedLevel')); ?>><?=$level->readable;?></option>
						<?endforeach;?>
					</select> -->
				<?php endif; ?>
			</div>
			<div class="col-md-4">
				<!-- MLS Number -->
				<label>MLS Number</label>
				<input class="form-control" type="text" name="mls" value="<?=gd_get_search_param('mls')?>">
				<!-- Property Status -->
				<label>Property Status</label>
				<!-- Defaults to Active -->
				<select class="form-control ajax-list" name="status" data-url="<?=GD_URL_PROXY_API_STATUSES?>" data-selected="<?=gd_get_search_param('status')?>" data-default="2">
					<option value="">Any</option>
				</select>
				<!-- Property Type -->
				<label>Property Type</label>
				<select class="form-control ajax-list" name="type" data-url="<?=GD_URL_PROXY_API_TYPES?>" data-selected="<?=gd_get_search_param('type')?>">
					<option value="">Any</option>
				</select>
				<!-- Property Style -->
				<label>Property Style</label>
				<select class="form-control ajax-list" name="style" data-url="<?=GD_URL_PROXY_API_STYLES?>" data-selected="<?=gd_get_search_param('style')?>">
					<option value="">Any</option>
				</select>
				<!-- Finished Basement -->
				<!-- This is only added if the agent has Metrolist listings -->
				<?php if (gd_agent_has_source(2)): ?>
<!-- 							<label>Finished Basement</label>
					<select class="form-control" name="finishedBasement">
						<option value="">Any</option>
						<option value="1" <?php selected(gd_get_search_param('finishedBasement'), 1) ?>>Yes</option>
						<option value="0" <?php selected(gd_get_search_param('finishedBasement'), 0) ?>>No</option>
					</select> -->
				<?php endif; ?>
			</div>
			<div class="col-md-4">
				<!-- Above Area -->
				<label>Above Ground SqFt (min to max)</label>
				<div class="row">
					<div class="col-xs-6">
						<select class="form-control" name="minAboveArea">
							<option value="">Any</option>
							<?for($i = 1; $i < 15; $i++):
								$value = $i * 500; ?>
								<option value="<?=$value?>" <?php selected($value, gd_get_search_param('minAboveArea')); ?>>At least <?=number_format($value) . "sqft";?></option>
							<?endfor;?>
						</select>
					</div>
					<div class="col-xs-6">
						<select class="form-control" name="maxAboveArea">
							<option value="">Any</option>
							<?for($i = 1; $i < 15; $i++):
								$value = $i * 500; ?>
								<option value="<?=$value?>" <?php selected($value, gd_get_search_param('maxAboveArea')); ?>>At most <?=number_format($value) . "sqft";?></option>
							<?endfor;?>
						</select>
					</div>
				</div>

				<!-- Finished Area -->
				<label>Finished SqFt (min to max)</label>
				<div class="row">
					<div class="col-xs-6">
						<select class="form-control" name="minFinishedArea">
							<option value="">Any</option>
							<?for($i = 1; $i < 15; $i++):
								$value = $i * 500; ?>
								<option value="<?=$value?>" <?php selected($value, gd_get_search_param('minFinishedArea')); ?>>At least <?=number_format($value) . "sqft";?></option>
							<?endfor;?>
						</select>
					</div>
					<div class="col-xs-6">
						<select class="form-control" name="maxFinishedArea">
							<option value="">Any</option>
							<?for($i = 1; $i < 15; $i++):
								$value = $i * 500; ?>
								<option value="<?=$value?>" <?php selected($value, gd_get_search_param('maxFinishedArea')); ?>>At most <?=number_format($value) . "sqft";?></option>
							<?endfor;?>
						</select>
					</div>
				</div>

				<!-- Total Area -->
				<label>Total SqFt (min to max)</label>
				<div class="row">
					<div class="col-xs-6">
						<select class="form-control" name="minTotalArea">
							<option value="">Any</option>
							<?for($i = 1; $i < 15; $i++):
								$value = $i * 500; ?>
								<option value="<?=$value?>" <?php selected($value, gd_get_search_param('minTotalArea')); ?>>At least <?=number_format($value) . "sqft";?></option>
							<?endfor;?>
						</select>
					</div>
					<div class="col-xs-6">
						<select class="form-control" name="maxTotalArea">
							<option value="">Any</option>
							<?for($i = 1; $i < 15; $i++):
								$value = $i * 500; ?>
								<option value="<?=$value?>" <?php selected($value, gd_get_search_param('maxTotalArea')); ?>>At most <?=number_format($value) . "sqft";?></option>
							<?endfor;?>
						</select>
					</div>
				</div>

				<!-- Parking Spaces -->
				<label>Minimum Parking Spaces</label>
				<select class="form-control" name="minParkingSpaces">
					<option value="">Any</option>
					<?for($i = 1; $i < 6; $i++): ?>
						<option value="<?=$i?>" <?php selected($i, gd_get_search_param('minParkingSpaces')); ?>>At least <?= ($i == 1) ? "1 space" : "$i spaces";?></option>
					<?endfor;?>
				</select>

				<!-- Acreage -->
				<label>Acreage</label>
				<div class="row">
					<div class="col-xs-6">
						<input class="form-control number-input" type="text" name="minAcreage" value="<?=gd_get_search_param('minAcreage')?>" placeholder="Min">
					</div>
					<div class="col-xs-6">
						<input class="form-control number-input" type="text" name="maxAcreage" value="<?=gd_get_search_param('maxAcreage')?>" placeholder="Max">
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ($listing_alert_form): ?>
		<h3>Alert Settings</h3>
		<div id="gd-listing-alerts-settings" class="form-group row">
			<div class="col-sm-4">
				<!-- Email -->
				<div class="checkbox">
					<label>
						<input type="checkbox" name="email" <?php checked($search->email, '1') ?> value="1">
						Alert via email
					</label>
				</div>
				<!-- Text -->
<!-- 				<div class="checkbox">
					<label>
						<input type="checkbox" name="text" <?php checked($search->text, '1') ?> value="1">
						Alert via text
					</label>
				</div> -->
				<!-- Frequency -->
				<label for="freq">Frequency</label>
				<select name="freq" required>
					<option value="daily" <?php selected('daily', $search->freq); ?>>Daily</option>
					<option value="instant" <?php selected('instant', $search->freq); ?>>Instant</option>
					<option value="never" <?php selected('never', $search->freq); ?>>Never</option>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<div class="form-group">
		<input type="submit" class="btn btn-primary" value="<?=$form_submit?>"/>
	</div>
</form>