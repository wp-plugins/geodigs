<?php

function gd_format_listing_detail($check, $text = '', $class = '') {
	if ($check) {
		echo '<div class="' . esc_attr($class) . '">' . esc_html($text) . '</div>';
	}
}

function gd_format_listing_detail_row($check, $text = '', $value = '', $id = '') {
	if ($check) { ?>
		<tr id="<?=esc_attr($id)?>">
			<th><?=esc_html($text)?></th>
			<td><?=esc_html($value)?></td>
		</tr>
	<? }
}

function gd_format_listing_url($address, $id) {
	// Filter address
	$address = str_replace(' ', '-', $address); // Replace spaces with dashes
	$address = str_replace(str_split(' .,\\/:*?"<>|'), '', $address); // Remove any illegal characters
	
	// Format the link so it's <details link>source/mls/<address>
	$url = get_site_url() . '/' . GD_URL_DETAILS . $address . '/' . str_replace('::', '-', $id) . '/';
	return esc_url($url);
}

function gd_format_price($price, $echo = false) {
	$readable_price = '$' . number_format($price);
	
	if ($echo) {
		echo esc_html($readable_price);
	}
	else {
		return esc_html($readable_price);
	}
}