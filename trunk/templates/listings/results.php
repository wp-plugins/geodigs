<?php
/**
 * Notes
 *
 * 	- $layout must be attached to gd-listings in the ID because of conflicts with the dsIDX themes.  By doing this it avoids the dsIDX styling
 */
?>
<ol id="gd-listings<?=$layout?>" class="container-fluid">
	<div class="row">
		<?=$listing_results?>
	</div>
</ol>