<?
	global $gd_api;

	$photo_count  = $listing->photoCount;
	$image_array = array();
	$images = array();
	
	for($i = 1; $i <= $photo_count; $i++) {
		array_push($images, $gd_api->url."listings/{$listing->id}/photo/{$i}");
	}

	function gd_enqueue_gallery_resources() {
		wp_enqueue_style('elastislide', GD_URL_CSS . 'elastislide.css');
		wp_enqueue_style('responsivegallery', GD_URL_CSS . 'responsivegallery.css');
		wp_enqueue_script('easing', GD_URL_JS . 'jquery.easing.1.3.js', array('jquery'), false, true);
		wp_enqueue_script('elastislide', GD_URL_JS . 'jquery.elastislide.js', array('jquery'), false, true);
		wp_enqueue_script('tmpl', GD_URL_JS . 'jquery.tmpl.min.js', array('jquery'), false, true);
		wp_enqueue_script('gd_listing_image_gallery', GD_URL_JS . 'gallery.js', array('jquery'), false, true);
	}
	add_action('wp_enqueue_scripts', 'gd_enqueue_gallery_resources');
?>
<div id="gd" class="gd-details">
	<div id="gd-actions">
		<a href="/<?=GD_URL_MORE_INFO?>?listing_id=<?=$listing->id;?>&address=<?=urlencode($listing->address->readable)?>" class="gd-links-button">More Information</a>
	</div>
	<div id="gd-header">
		<table>
			<tr>
				<td>
					<div id="gd-media-td">
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
						<div class="listingMain" >
							<div id="rg-gallery" class="rg-gallery">
								<div class="rg-thumbs">
									<!-- Elastislide Carousel Thumbnail Viewer -->
									<div class="es-carousel-wrapper">
										<div class="es-nav"> <span class="es-nav-prev">Previous</span> <span class="es-nav-next">Next</span> </div>
										<div class="es-carousel">
											<ul>
												<? if ($photo_count): ?>
													<? for ($i = 1; $i <= $photo_count; $i++): ?>
														<li><img src="<?=$gd_api->url?>listings/<?=$listing->id?>/photo/<?=$i?>" data-large="<?=$gd_api->url?>listings/<?=$listing->id?>/photo/<?=$i?>" alt="Photo"/></li>
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
					</div>
					<div>
						<table id="gd-primary-data">
							<?
								// gd_format_listing_detail_row(field to check to see if we should show this, header, value, id);
								gd_format_listing_detail_row($listing->price, 'Price', $listing->price->readable, 'gd-price');
								gd_format_listing_detail_row($listing->status, 'Status', $listing->status->readable, 'gd-status');
								gd_format_listing_detail_row($listing->beds, 'Bedrooms', $listing->beds->count);
								gd_format_listing_detail_row($listing->baths, 'Baths', $listing->baths->count);
								gd_format_listing_detail_row($listing->mlsNumber, 'MLS', $listing->mlsNumber);
								gd_format_listing_detail_row($listing->sizes->total, 'Total Area', $listing->sizes->total->area->readable);
								gd_format_listing_detail_row($listing->sizes->finished, $listing->sizes->finished->name, $listing->sizes->finished->area->readable);
								gd_format_listing_detail_row($listing->sizes->finishedInclBasement, $listing->sizes->finishedInclBasement->name, $listing->sizes->finishedInclBasement->area->readable);
								gd_format_listing_detail_row($listing->yearBuilt, 'Year Built', $listing->yearBuilt);
								gd_format_listing_detail_row($listing->type, 'Type', $listing->type->value);
							?>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<? if($listing->description): ?>
	<div id="gd-description">
		<h3>Property Description</h3>
		<span>
			<?=$listing->description?>
		</span>
	</div>
	
	<? endif; ?>
	<div id="gd-general-feat">
		<h3>General Features</h3>
		<table id="gd-secondary-data">
			<?
			gd_format_listing_detail_row($listing->construction->value, 'Construction', $listing->construction->value);
			gd_format_listing_detail_row($listing->cooling->value, 'Cooling', $listing->cooling->value);
			gd_format_listing_detail_row($listing->heating->value, 'Heating', $listing->heating->value);
			// gd_format_listing_detail_row($listing->cooling->value, 'Basement', $listing->cooling->value);
			// gd_format_listing_detail_row($listing->cooling->value, 'Garage', $listing->cooling->value);
			gd_format_listing_detail_row($listing->style->value, 'Style', $listing->style->value);
			?>
		</table>
	</div>

	<?php if ($listing->rooms || $listing->sizes->mainFloor): ?>
	<div id="gd-room-sizes">
		<h3>Room Sizes</h3>
		<table class="gd-supplemental-data gd-fields">
			<?
			gd_format_listing_detail_row($listing->sizes->mainFloor->area->readable, 'Main Floor', $listing->sizes->mainFloor->area->readable);
			gd_format_listing_detail_row($listing->rooms->study->readable, 'Office/Study', $listing->rooms->study->readable);
			gd_format_listing_detail_row($listing->rooms->dining->readable, 'Dining Room', $listing->rooms->dining->readable);
			gd_format_listing_detail_row($listing->rooms->laundry->readable, 'Laundry Room', $listing->rooms->laundry->readable);
			gd_format_listing_detail_row($listing->rooms->kitchen->readable, 'Kitchen', $listing->rooms->kitchen->readable);
			gd_format_listing_detail_row($listing->rooms->living->readable, 'Living Room', $listing->rooms->living->readable);
			gd_format_listing_detail_row($listing->rooms->rec->readable, 'Rec Room', $listing->rooms->rec->readable);
			gd_format_listing_detail_row($listing->rooms->family->readable, 'Family Room', $listing->rooms->family->readable);
			gd_format_listing_detail_row($listing->rooms->great->readable, 'Great Room', $listing->rooms->great->readable);
			gd_format_listing_detail_row($listing->rooms->masterBed->readable, 'Master Bedroom', $listing->rooms->masterBed->readable);
			gd_format_listing_detail_row($listing->rooms->bed2->readable, 'Bedroom 2', $listing->rooms->bed2->readable);
			gd_format_listing_detail_row($listing->rooms->bed3->readable, 'Bedroom 3', $listing->rooms->bed3->readable);
			gd_format_listing_detail_row($listing->rooms->bed4->readable, 'Bedroom 4', $listing->rooms->bed4->readable);
			gd_format_listing_detail_row($listing->rooms->bed5->readable, 'Bedroom 5', $listing->rooms->bed5->readable);
			?>
		</table>
	</div>
	<? endif; ?>

	<? if($listing->school): ?>
	<div id="gd-school-info">
		<h3>School Information</h3>
		<table class="gd-supplemental-data gd-fields">
			<?php
			gd_format_listing_detail_row($listing->school->district, 'District', $listing->school->district);
			gd_format_listing_detail_row($listing->school->elementary, 'Elementary', $listing->school->elementary);
			gd_format_listing_detail_row($listing->school->middle, 'Middle', $listing->school->middle);
			gd_format_listing_detail_row($listing->school->high, 'High', $listing->school->high);
			?>
		</table>
	</div>
	<? endif; ?>

	<? if($listing->taxes): ?>
	<div id="gd-taxes">
		<h3>Taxes & Fees</h3>
		<table class="gd-supplemental-data gd-fields">
			<?php
			gd_format_listing_detail_row($listing->taxes->taxAmount, 'Tax Amount', $listing->taxes->taxAmount->readable);
			gd_format_listing_detail_row($listing->taxes->taxYear, 'Tax Year', $listing->taxes->taxYear);
			?>
		</table>
	</div>
	<? endif; ?>
	
	<div id="gd-map">
		<iframe frameborder="0" src="https://maps.google.com/maps?oe=utf-8&amp;client=firefox-a&amp;q=<?=urlencode($listing->address->readable)?>&amp;output=embed"></iframe>
	</div>
<!-- 	<div id="gd-additional-information">
		<h1>Additional Information</h1>
	</div> -->
	<!-- [END] accordion -->
</div>