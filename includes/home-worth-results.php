<?php
global $gd_api;

Geodigs_User::require_login($_SERVER['REQUEST_URI']);
$response = $gd_api->call('GET', 'worth');
?>

<div id="gd-home-worth-results">
	<?php if ($response): ?>
		<h2><?=$response->street?>, <?=$response->city?> <?=$response->state?> <?=$response->zip?></h2>
		<a href="<?=$response->detailslink?>" target="_blank">Click here for details</a>
		<table class="pure-form">
			<tbody>
				<tr>
					<th>Zestimate</th>
					<td><?=gd_format_price($response->zestimate)?></td>
				</tr>
				<tr>
					<th>Low Range</th>
					<td><?=gd_format_price($response->lowrange)?></td>
				</tr>
				<tr>
					<th>High Range</th>
					<td><?=gd_format_price($response->highrange)?></td>
				</tr>
			</tbody>
		</table>
	<?php else: ?>
		<h2>Zestimate currently not avaialbe for this property</h2>
	<?php endif; ?>
</div>