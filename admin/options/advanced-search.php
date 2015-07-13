<?php
class Geodigs_Options_Advanced_Search extends Geodigs_Options {
	public $option_id;
	public $fields;
	public $option_name;

	function __construct() {
		$this->option_id	= 'geodigs-advanced-search';
		$this->option_name	= 'geodigs_advanced_search';
		$this->fields		= get_option($this->option_name);
	}

	function create_form() {
	}

	function validate($input) {
		$output	 = $input;
		$error	 = false;
		
		if (!$error) {
			add_settings_error('geodigs_advanced_search', 'advanced_search_successful', 'Advanced Search updated', 'updated');
			return $output;
		}
	}
	
	function type() {
		global $gd_api;
		$types  = $gd_api->call("GET", "listings/types");
		$values = array(
			'any' => 'Any'
		);
		
		foreach($types as $type) {
			$values["$type->id"] = $type->readable;
		}

		// ($option_id, $field_id, $option_name, $fieled_name, $values)
		$this->create_dropdown($this->option_id, 'type', $this->option_name, 'DefaultType', $values, $this->fields['DefaultType']);
	}
}