<?php
/* Create our detail columns */
$detail_columns = array();
$column         = 0;
$count          = 0;
foreach ($search->readableParams as $key => $value) {
	$detail_columns[$column][$key] = $value;
	$count++;
		
	if ($count % 4 == 0) {
		$column++;
		$detail_columns[$column] = array();
	}
}
// Remove empty arrays
$detail_columns = array_filter($detail_columns);

// Create HTML
$details = '';
foreach ($detail_columns as $detail_column) {
	$details .= '<ul class="pure-u-1 pure-u-md-1-3">';
	foreach ($detail_column as $key => $value) {
		$details .= "<li><strong>{$key}: </strong>{$value}</li>";
	}
	$details .= '</ul>';
}
?>
<div class="gd-listing-alert">
	<div class="gd-listing-alert-header">
		<h4 class="gd-listing-alert-name">
			<?=$search->name?> <small>Saved <?=$search->ago?></small>
		</h4>
	</div>
	<div class="gd-listing-alert-details pure-g">
		<?=$details?>
	</div>
	<div class="gd-listing-alert-links">
		<a href="<?=$search_link?>">View Search</a>
		<div class="gd-listing-alert-tools">
			<a href="<?=$edit_link?>">Edit Alert</a> | <a href="<?=$remove_link?>">Remove Alert</a>
		</div>
	</div>
</div>