<!-- START featured-row.php -->
<?php
if($featured->address->readable) {
	$address = $featured->address->readable;

	// Shorten listing title
	$address_string = $address;(strlen($address) > 25) ? substr($address, 0, 25).'...' : $address;

	// Format the link so it's <details link>source/mls/<address>
	$link_address = gd_format_listing_url($address, $featured->id);
}

// MLS Photo
$photo = $gd_api->url . "listings/{$featured->id}/photo/0?size=small";
?>

<div class="gd-listing">
	<header>
		<a href="<?=$link_address?>">
			<div class="gd-address">
				<h3><?=$address_string?></h3>
			</div>
			<div class="gd-photo">
				<img src="<?=$photo?>" alt="MLS <?=$featured->mls?>" class="img-responsive" />  
			</div>
		</a>
	</header>

	<div class="gd-details">
		<ul>
			<?php if($featured->beds->count): ?>
			<li>
				<div class="beds-image">
					<img src="<?=GD_URL_IMAGES?>ui/icon-bed.png" alt="Beds"/>
				</div>

				<div class='beds'>
					<?=$featured->beds->count;?>
				</div>
			</li>
			<?php endif; ?>

			<?php if($featured->baths->count): ?>
			<li>
				<div class="baths-image">
					<img src="<?=GD_URL_IMAGES?>ui/icon-bath.png" alt="Baths" />
				</div>

				<div class="baths">
					<?=$featured->baths->count;?>
				</div>
			</li>
			<?php endif; ?>

			<?php if($featured->sizes->total->area->readable): ?>
			<li>
				<div class="sqFt-image">
					<img src="<?=GD_URL_IMAGES?>ui/icon-sq.png" alt="Sq Ft." />
				</div>
				<div class="sqFt">
					<?=$featured->sizes->total->area->readable?>
				</div>
			</li>
			<?php endif; ?>

			<li>
				<div class="source-img">
					<img src="<?=GD_URL_IMAGES . 'sources/' . $featured->source->id . '-md.jpg'?>" class="img-responsive" alt="<?=$featured->source->name?>" />
				</div>
			</li>
		</ul>
	</div><!-- end details -->
	<footer>
		<?php if (Geodigs_User::is_logged_in()): ?>
		<div class="gd-favorite-toggle <?=$favorite_status?>" data-listing-id="<?=$featured->id?>">
			<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/favorite-star.svg'); ?>
			<span class="gd-favorite-add-text">Add Favorite</span>
			<span class="gd-favorite-remove-text">Remove Favorite</span>
		</div>
		<?php endif; ?>
		<div class="gd-price">
			<?php if($featured->price->readable): ?>
			<span><?=$featured->price->readable?></span>
			<?php endif; ?>
		</div>
	</footer>
</div>
<!-- END featured-row.php -->