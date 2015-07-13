<?php
/**
 * Notes
 *
 *	- To see all available data points call var_dump($listing)
 *	- Use $count to figure out what listing is currently being outputted.  This can be used to alternate styles for odd/even rows.
 */
?>
<div class="gd-listing">
	<header>
		<a href="<?=$listing_url?>">
			<div class="gd-address">
				<h3><?=$listing->address->readable?></h3>
			</div>
			<div class="gd-photo">
				<img src="<?=$photo?>" alt="MLS <?=$listing->mls?>" class="img-responsive" />  
			</div>
		</a>
	</header>

	<div class="gd-details">
		<ul>
			<?php if($listing->beds->count): ?>
			<li>
				<div class="beds-image">
					<img src="<?=GD_URL_IMAGES?>ui/icon-bed.png" alt="Beds"/>
				</div>

				<div class='beds'>
					<?=$listing->beds->count;?>
				</div>
			</li>
			<?php endif; ?>

			<?php if($listing->baths->count): ?>
			<li>
				<div class="baths-image">
					<img src="<?=GD_URL_IMAGES?>ui/icon-bath.png" alt="Baths" />
				</div>

				<div class="baths">
					<?=$listing->baths->count;?>
				</div>
			</li>
			<?php endif; ?>

			<?php if($listing->sizes->total->area->readable): ?>
			<li>
				<div class="sqFt-image">
					<img src="<?=GD_URL_IMAGES?>ui/icon-sq.png" alt="Sq Ft." />
				</div>
				<div class="sqFt">
					<?=$listing->sizes->total->area->readable?>
				</div>
			</li>
			<?php endif; ?>

			<li>
				<div class="source-img">
					<img src="<?=GD_URL_IMAGES . 'sources/' . $listing->source->id . '-md.jpg'?>" class="img-responsive" alt="<?=$listing->source->name?>" />
				</div>
			</li>
		</ul>
	</div>
	<footer>
		<?php if (Geodigs_User::is_logged_in()): ?>
			<div class="gd-favorite-toggle <?=$favorite_status?>" data-listing-id="<?=$listing->id?>">
				<?php echo file_get_contents(GD_DIR_IMAGES . 'ui/favorite-star.svg'); ?>
				<span class="gd-favorite-add-text">Add Favorite</span>
				<span class="gd-favorite-remove-text">Remove Favorite</span>
			</div>
		<?php endif; ?>
		
		<div class="gd-price">
			<?php if($listing->price->readable): ?>
				<span><?=$listing->price->readable?></span>
			<?php endif; ?>
		</div>
	</footer>
</div>