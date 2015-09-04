<div class="gd-quick-search">
	<form id="gd-quick-search-form" action="<?=$form_action?>" method="get"> 
<!-- 		Search only active listings by default -->
		<input type="hidden" name="status" value="2">
<!-- 		Order by price high to low first -->
		<input type="hidden" name="orderBy" value="price">
		<input type="hidden" name="orderByDirection" value="desc">
<!-- 		Only for this agent's source/s -->
		<input type="hidden" id="gd-quick-search-sources" name="sources" value="<?=$sources?>">
		
		<div class="gd-quick-search-input form-group">
			<input class="gd-quick-search-selector form-control" type="text" placeholder="Search... Type a city, street name, zip code, or MLS #"></input>
			<div class="gd-quick-search-throbber">
				<span class="gd-quick-search-throbber-helper"></span>
				<img src="<?=GD_URL_IMAGES . 'ui/throbber-autocomplete.gif'?>"/>
			</div>
			<div class="gd-quick-search-fields">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-3">
				<select name="minBed" class="form-control">
					<option value="" selected disabled>Beds</option>
					<?php for ($i = 1; $i < 13; $i++): ?>
						<option value="<?=$i?>" <?php if ($i == $_GET["minBed"]) echo 'selected'; ?>><?=$i?>+</option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="col-md-3">
				<select name="minBaths" class="form-control">
					<option value="" selected disabled>Baths</option>
					<?php for ($i = 1; $i < 13; $i++): ?>
						<option value="<?=$i?>" <?php if ($i == $_GET["minBaths"]) echo 'selected'; ?>><?=$i?>+</option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-sm-6">
						<input class="form-control number-input" type="text" name="minPrice" value="<?=esc_attr($_GET["minPrice"])?>" placeholder="Price Min">
					</div>
					<div class="col-sm-6">
						<input class="form-control number-input" type="text" name="maxPrice" value="<?=esc_attr($_GET["maxPrice"])?>" placeholder="Price Max">
					</div>
				</div>
			</div>
		</div>
		<div>
			<input type="submit" class="form-control" value="search">
		</div>
	</form>
</div>