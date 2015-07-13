<div class="gd-map-preview">
	<div><?=$listing->address->street->readable?></div>
	<div><?=$listing->price->readable?></div>
	<img src="<?=$photo?>" alt="MLS <?=$listing->mlsNumber?>" />
	<div>
		<?php if ($listing->beds->count): ?>
			<span>Beds: <?=$listing->beds->count?></span>
		<?php endif; ?>
		<?php if ($listing->baths->count): ?>
			<span>Baths: <?=$listing->baths->count?></span>
		<?php endif; ?>
	</div>
	<div><a href="<?=$listing_url?>">View More...</a></div>
</div>