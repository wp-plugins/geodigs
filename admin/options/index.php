<?php
// These are all of the option sections
include 'our-listings.php';
include 'featured-listings.php';
include 'general.php';
include 'seo.php';
include 'advanced-search.php';

class Geodigs_Options {
	function create_dropdown($option_id, $field_id, $option_name, $fieled_name, $values, $current) {
		$id		= "{$option_id}-{$field_id}";
		$name	= "{$option_name}[{$fieled_name}]";

		echo "<select id='{$id}' name='{$name}'>";
		foreach ($values as $value => $label) {
			echo "<option value='{$value}' " . selected($current, $value, false) . ">{$label}</option>";
		}
		echo '</select><br>';
	}

	function create_radio_button($option_id, $field_id, $option_name, $fieled_name, $value, $label, $is_checked) {
		$id			= "{$option_id}-{$field_id}-{$value}";
		$name		= "{$option_name}[{$fieled_name}]";
		$checked	= $is_checked ? 'checked' : '';

		echo "<input type='radio' id='{$id}' name='{$name}' value='{$value}' {$checked}><label for='{$id}'>{$label}</label><br>";
	}

	function create_text_box($option_id, $field_id, $option_name, $fieled_name, $value) {
		$id		= "{$option_id}-{$field_id}";
		$name	= "{$option_name}[{$fieled_name}]";

		echo "<input type='text' id='{$id}' name='{$name}' value='{$value}' /><br>";
	}

	function create_textarea($option_id, $field_id, $option_name, $fieled_name, $value) {
		$id		= "{$option_id}-{$field_id}";
		$name	= "{$option_name}[{$fieled_name}]";

		echo "<textarea id='{$id}' name='{$name}'>{$value}</textarea><br>";
	}

	function create_hidden($option_id, $field_id, $option_name, $fieled_name, $value) {
		$id		= "{$option_id}-{$field_id}";
		$name	= "{$option_name}[{$fieled_name}]";

		echo "<input type='hidden' id='{$id}' name='{$name}' value='{$value}' /><br>";
	}

	function create_number_box($option_id, $field_id, $option_name, $fieled_name, $value) {
		$id		= "{$option_id}-{$field_id}";
		$name	= "{$option_name}[{$fieled_name}]";

		echo "<input type='number' id='{$id}' name='{$name}' value='{$value}' /><br>";
	}
}