<?php
/**
 * Notes
 *
 *	- To see all available data points call var_dump($listing)
 * 	- gd_format_listing_detail($varToCheck, $text, $class) when called takes a listing attr, checks if it exists, and then outputs it with text and an optional class
 *	- Use $count to figure out what listing is currently being outputted.  This can be used to alternate styles for odd/even rows.
 */
?>
<li class="gd-listing col-xs-12">
	<div class="gd-media">
		<div class="gd-photo">
			<a href="<?=$listingUrl?>">
				<img src="<?=$photo?>" alt="MLS <?=$listing->mls?>" class="img-responsive"/>
			</a>
		</div>
	</div>
	<div class="gd-primary-data">
		<div class="gd-address">
			<a href="<?=$listingUrl?>">
				<?=$listing->address->readable?>
			</a>
			<?php if (Geodigs_User::is_logged_in()): ?>
			<div class="gd-favorite-toggle <?=$favorite_status?>" data-listing-id="<?=$listing->id?>">
				<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/favorite-star.svg'); ?>
				<span class="gd-favorite-add-text">Add Favorite</span>
				<span class="gd-favorite-remove-text">Remove Favorite</span>
			</div>
			<?php endif; ?>
		</div>
		<div class="gd-price">
			<?php gd_format_listing_detail($listing->price->readable, $listing->price->readable); ?>
		</div>
		<div class="gd-mls-number">MLS #<?=$listing->mlsNumber?></div>
	</div>
	<div class="gd-secondary-data">
		<?php
			gd_format_listing_detail($listing->beds->count, $listing->beds->count . ' beds');
			gd_format_listing_detail($listing->baths->count, $listing->baths->count . ' baths');
			gd_format_listing_detail($listing->sizes->total->area->readable, $listing->sizes->total->area->readable);
		?>
<!-- 		DO NOT REMOVE THIS ENTIRELY AS IT IS REQUIRED TO BE DISPLAYED -->
		<div class="source-img">
			<img src="<?=GD_URL_IMAGES . 'sources/' . $listing->source->id . '-md.jpg'?>" class="img-responsive" alt="<?=$listing->source->name?>" />
		</div>
<!-- 		DO NOT REMOVE THIS ENTIRELY AS IT IS REQUIRED TO BE DISPLAYED -->
	</div>
</li>