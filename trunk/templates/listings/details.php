<?php
/**
 * Notes
 *
 * 	- gd_create_table_str($array) takes an array of key=>val pairs and outputs them to a formatted table
 *	- To view all available data points call var_dump($listing)
 */

// Enque resources
function gd_enqueue_gallery_resources() {
	wp_enqueue_style('elastislide', GD_URL_CSS . 'elastislide.css');
	wp_enqueue_style('responsivegallery', GD_URL_CSS . 'responsivegallery.css');
	wp_enqueue_script('easing', GD_URL_JS . 'jquery.easing.1.3.js', array('jquery'), false, true);
	wp_enqueue_script('elastislide', GD_URL_JS . 'jquery.elastislide.js', array('jquery'), false, true);
	wp_enqueue_script('tmpl', GD_URL_JS . 'jquery.tmpl.min.js', array('jquery'), false, true);
	wp_enqueue_script('gd_listing_image_gallery', GD_URL_JS . 'gallery.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'gd_enqueue_gallery_resources');

// General Features
$general = array(
	'MLS'               => $listing->mlsNumber,
	'Status'            => $listing->status->readable,
	'Listing Office'    => $listing->office->name->value,
/* 	'Type'              => $listing->type->value, */
	'Style'             => $listing->style->value,
	'Construction'      => $listing->construction->value,
	'Bedrooms'          => $listing->beds->count,
	'Baths'             => $listing->baths->count,
	'Cooling'           => $listing->cooling->value,
	'Heating'           => $listing->heating->value,
	'Total SqFt'        => $listing->sizes->total->area->readable,
	'Finished SqFt'     => $listing->sizes->finished->area->readable,
	'Above Ground SqFt' => $listing->sizes->aboveGround->area->readable,
	'Acreage'           => $listing->sizes->acreage->area->readable,
	'Lot Size'          => $listing->sizes->lot->area->readable,
	'Year Built'        => $listing->yearBuilt->value,
);

// Room Sizes
$rooms = array(
/* 	'Main Floor' => $listing->sizes->mainFloor->area->readable, */
	'Office/Study' => $listing->rooms->study->readable,
	'Dining Room' => $listing->rooms->dining->readable,
	'Laundry Room' => $listing->rooms->laundry->readable,
	'Kitchen' => $listing->rooms->kitchen->readable,
	'Living Room' => $listing->rooms->living->readable,
	'Rec Room' => $listing->rooms->rec->readable,
	'Family Room' => $listing->rooms->family->readable,
	'Great Room' => $listing->rooms->great->readable,
	'Master Bedroom' => $listing->rooms->masterBed->readable,
	'Bedroom 2' => $listing->rooms->bed2->readable,
	'Bedroom 3' => $listing->rooms->bed3->readable,
	'Bedroom 4' => $listing->rooms->bed4->readable,
	'Bedroom 5' => $listing->rooms->bed5->readable,
);

// School Information
$schools = array(
	'District' => $listing->schools->district->value,
	'Elementary' => $listing->schools->elementary->value,
	'Middle' => $listing->schools->middle->value,
	'High' => $listing->schools->high->value,
);

// Taxes & Fees
$taxes = array(
	'Tax Amount' => $listing->taxes->taxAmount->readable,
	'Tax Year'   => $listing->taxes->taxYear,
);
$fees = array();
foreach ($listing->fees as $fee) {
	$fees[$fee->name] = $fee->readable;
}

// Additional Information
$additional = array();
foreach ($listing->additional as $addition) {
	$additional[$addition->readable] = $addition->value;
}
foreach ($listing->features as $feature) {
	$additional[$feature->readable] = $feature->value;
}
?>

<header class="row">
	<div class="col-md-4">
		<?php if ($_SERVER['HTTP_REFERER']): ?>
			<a href="<?=esc_url($_SERVER['HTTP_REFERER'])?>">Back to search results</a>
			<br>
		<?php endif; ?>
		<h1><?=$listing->price->readable?>
			<?php if ($listing->beds): ?>
				<br>
				<?=$listing->beds->readableLong?>
			<?php endif; ?>
			<?php if ($listing->beds && $listing->baths): ?>
				,
			<?php endif; ?>
			<?php if ($listing->baths): ?>
				<?=$listing->baths->readableLong?>
			<?php endif; ?>
		</h1>
<!-- 		Actions -->
		<div class="gd-actions">
			<a href="/<?=GD_URL_MORE_INFO?>?listing_id=<?=$listing->id;?>&address=<?=urlencode($listing->address->readable)?>" class="gd-links-button">
				<div>
					<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/email.svg'); ?>
					<span>Contact <?=$_SESSION['gd_agent']->name->first?></span>
				</div>
			</a>
			<a href="" class="gd-links-button modal-open" data-modal-id="gd-mortgage-calc-modal">
				<div>
					<input type="hidden" id="gd-mortgage-calc-price" value="<?=$listing->price->readable?>">
					<input type="hidden" id="gd-mortgage-calc-taxes" value="<?=$listing->taxes->taxAmount->readable?>">
					<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/calculator.svg'); ?>
					<span>Mortgage Calculator</span>
				</div>
			</a>
			<a href="/<?=GD_URL_SEARCH?>?lat=<?=$listing->coords->lat?>&lon=<?=$listing->coords->lon?>&radius=1" class="gd-links-button">
				<div>
					<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/search.svg'); ?>
					<span>Nearby Properties</span>
				</div>
			</a>
			<a href="" class="gd-links-button modal-open" data-modal-id="gd-map-it-modal">
				<div>
					<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/map-pin.svg'); ?>
					<span>Map It</span>
				</div>
			</a>
			<?php if (Geodigs_User::is_logged_in()): ?>
				<a>
					<div class="gd-favorite-toggle <?=$favorite_status?>" data-listing-id="<?=$listing->id?>">
						<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/favorite-star.svg'); ?>
						<span class="gd-favorite-add-text">Add Favorite</span>
						<span class="gd-favorite-remove-text">Remove Favorite</span>
					</div>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-md-8">
		<script id="img-wrapper-tmpl" type="text/x-jquery-tmpl">
			<div class="rg-image-wrapper">
				{{if itemsCount > 1}}
					<div class="rg-image-nav">
					<a href="#" class="rg-image-nav-prev">Previous Image</a>
					<a href="#" class="rg-image-nav-next">Next Image</a>
					</div>
				{{/if}}
				<div class="rg-image"></div>
				<div class="rg-loading"></div>
				<div class="rg-caption-wrapper">
					<div class="rg-caption" style="display:none;">
						<p></p>
					</div>
				</div>
			</div>
		</script>
		<div id="rg-gallery" class="rg-gallery">
			<div class="rg-thumbs">
				<!-- Elastislide Carousel Thumbnail Viewer -->
				<div class="es-carousel-wrapper">
					<div class="es-nav"> <span class="es-nav-prev">Previous</span> <span class="es-nav-next">Next</span> </div>
					<div class="es-carousel">
						<ul>
							<? if ($listing->photoCount): ?>
								<? for ($i = 0; $i < $listing->photoCount; $i++): ?>
									<?php
									// For Metrolist listings there is only one small image available so we have to use the large ones
									$small_size = $listing->source->id == 2 ? 'large' : 'small';
									$photo_small = $gd_api_url . "listings/{$listing->id}/photo/{$i}?size={$small_size}";
									$photo_large = $gd_api_url . "listings/{$listing->id}/photo/{$i}?size=large";
									?>
									<li><img src="<?=$photo_small?>" data-large="<?=$photo_large?>" alt="Photo"/></li>
								<? endfor; ?>
							<? endif; ?>
						</ul>
					</div>
				</div>
				<!-- End Elastislide Carousel Thumbnail Viewer --> 
			</div>
			<!-- rg-thumbs --> 
		</div>
	</div>
	<div class="col-md-12">
		<!-- 		Description -->
		<? if($listing->description): ?>
			<div id="gd-description">
				<h3>Property Description</h3>
				<span>
					<?=$listing->description?>
				</span>
			</div>
		<?php endif; ?>
	</div>
</header>
<div class="row">
<!-- 		General Features -->
	<div id="gd-general-feat" class="col-md-6">
		<h1>General Features</h1>
		<?=gd_create_table_str($general)?>
	</div>

<!-- 		Room Sizes -->
	<?php if ($listing->rooms || $listing->sizes->mainFloor): ?>
		<div id="gd-room-sizes" class="col-md-6">
			<h1>Room Sizes</h1>
			<?=gd_create_table_str($rooms)?>
		</div>
	<?php endif; ?>

<!-- 		Schools -->
	<?php if($listing->schools): ?>
		<div id="gd-school-info" class="col-md-6">
			<h1>School Information</h1>
			<?=gd_create_table_str($schools, 1)?>
		</div>
	<?php endif; ?>

<!-- 		Taxes -->
	<?php if($listing->taxes || $listing->fees): ?>
		<div id="gd-taxes" class="col-md-6">
			<h1>Taxes & Fees</h1>
			<?php if ($listing->taxes) echo gd_create_table_str($taxes, 1); ?>
			<?php if ($listing->fees) echo gd_create_table_str($fees, 1); ?>
		</div>
	<?php endif; ?>

<!-- 		Additional Information -->
	<?php if (count($additional) > 0): ?>
		<div id="gd-additional-info" class="col-xs-12">
			<h1>Additional Information</h1>
			<?=gd_create_table_str($additional, 1)?>
		</div>
	<?php endif; ?>
</div>

<!-- Load the modals used here -->
<?php include_once GD_DIR_MODALS . 'mortgage-calculator.php'; ?>
<?php include_once GD_DIR_MODALS . 'map-single.php'; ?>