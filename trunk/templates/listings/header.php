<header id="gd-listings-header">
	<?php if ($show_map): ?>
		<div id="gd-map"></div>
	<?php endif; ?>
	<div id="gd-listings-toolbar">
<!-- 		Add Edit Search link/button -->
		<?php if ($show_edit_search): ?>
			<a href="<?=$edit_search_link?>">Edit Search</a>
		<?php endif; ?>
<!-- 		Sort dropdown -->
		<?php if ($show_sort): ?>
			<div class="gd-sorting-control">
				<form>
					<label for="sort">Sort by: </label>
					<select name="sort">
						<?=$sort_options?>
					</select>
				</form>
			</div>
		<?php endif; ?>
	</div>
<!-- 	Results count -->
	<?php if ($show_count): ?>
		<div id="gd-listings-count">
			<span>Search Results <?=$listings_start?>-<?=$listings_end?> of <?=$listings_total?></span>
		</div>
	<?php endif; ?>
</header>