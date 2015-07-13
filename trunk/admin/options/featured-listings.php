<?php
class Geodigs_Options_Featured_Listings extends Geodigs_Options {
	public $option_id;
	public $fields;
	public $option_name;

	function __construct() {
		$this->option_id = 'gd-options-featured-listings';
		$this->option_name = GD_OPTIONS_FEATURED_LISTINGS;
		$this->fields = get_option($this->option_name);
	}

	function create_form() {}

	public function validate($input) {
		$output = $input;

		// Make sure a number was actually entered
		$output['NumberOf'] = intval($input['NumberOf']);
		if (!$output['NumberOf']) {
			add_settings_error('geodigs_featured_listings', 'update_featured_failed', 'The number of random featured listings entered was not a number.  Please enter a valid number.', 'error');
			$output['NumberOf'] = '';
		}
		else {
			add_settings_error('geodigs_featured_listings', 'update_featured_success', 'Featured Listings settings updated', 'updated');
		}
		
		return $output;
	}

	function toggle_random() {
		$id			= 'select-random';
		$field		= 'SelectRandom';
		$value_1	= 'true';
		$value_2	= 'false';
		// This checks to see if there is an option saved for this field.
		// If there is then use it to see if this element is checked otherwise default it it to true.
		$value_2_checked = $this->fields[$field] ? checked($this->fields[$field], $value_2, false) : true;

		echo '<fieldset>';
			$this->create_radio_button($this->option_id, $id, $this->option_name, $field, $value_1, 'Enabled', checked($this->fields[$field], $value_1, false));
			$this->create_radio_button($this->option_id, $id, $this->option_name, $field, $value_2, 'Disabled', $value_2_checked);
		echo '</fieldset>';
	}

	function number_of() {
		// If this has been defined previously use its value else default to 5
		$value = $this->fields['NumberOf'] ? $this->fields['NumberOf'] : 5;

		$this->create_number_box($this->option_id, 'number-of', $this->option_name, 'NumberOf', $value);
	}

	function sort() {
		$values = array(
			'random'        => 'Random',
			'price::asc'	=> 'Price: Low to High',
			'price::desc'	=> 'Price: High to Low',
			'area::asc'		=> 'Area: Low to High',
			'area::desc'	=> 'Area: High to Low',
			'beds::asc'		=> 'Bedrooms: Low to High',
			'beds::desc'	=> 'Bedrooms: High to Low',
			'baths::asc'	=> 'Bathrooms: Low to High',
			'baths::desc'	=> 'Bathrooms: High to Low',
		);

		// ($option_id, $field_id, $option_name, $fieled_name, $values)
		$this->create_dropdown($this->option_id, 'sort', $this->option_name, 'Sort', $values, $this->fields['Sort']);
	}
}