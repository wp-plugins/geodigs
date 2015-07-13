<?php
class Geodigs_Options_Our_Listings extends Geodigs_Options {
	public $option_id;
	public $fields;
	public $option_name;

	function __construct() {
		$this->option_id	= 'geodigs-our-listings';
		$this->option_name	= 'geodigs_our_listings';
		$this->fields		= get_option($this->option_name);
	}

	function create_form() {
	}

	function validate($input) {
		$output	 = $input;
		$error	 = false;
		$sources = (array) $_SESSION['gd_agent']->sources;
		
		if ($input['ListingsToDisplay'] == 'agent') {
			$mlsCode = $sources[$input['Source']]->mlsCode;
			if ($mlsCode) {
				$output['Code'] = $mlsCode;
			}
			else {
				add_settings_error('geodigs_our_listings', 'missing_agent_code', 'No Agent MLS Number was found for this source', 'error');
				$error = true;
			}
		}
		else if ($input['ListingsToDisplay'] == 'office') {
			$officeCode = $sources[$input['Source']]->officeCode;
			if ($officeCode) {
				$output['Code'] = $officeCode;
			}
			else {
				add_settings_error('geodigs_our_listings', 'missing_office_code', 'No Office MLS Code was found for this source', 'error');
				$error = true;
			}
		}
		
		if (!$error) {
			add_settings_error('geodigs_our_listings', 'our_listings_successful', 'Our Listings updated', 'updated');
			return $output;
		}
	}

	function listings_to_display_field() {
		$id			= 'listings-to-display';
		$field		= 'ListingsToDisplay';
		$value_1	= 'agent';
		$value_2	= 'office';
		// This checks to see if there is an option saved for this field.
		// If there is then use it to see if this element is checked otherwise default it it to true.
		$value_1_checked = $this->fields[$field] ? checked($this->fields[$field], $value_1, false) : true;

		echo '<fieldset>';
			$this->create_radio_button($this->option_id, $id, $this->option_name, $field, $value_1, 'Agent Listings', $value_1_checked);
			$this->create_radio_button($this->option_id, $id, $this->option_name, $field, $value_2, 'Office Listings', checked($this->fields[$field], $value_2, false));
		echo '</fieldset>';
	}

	function source_field() {
		$options = array();
		$current = $this->fields['Source'];
		
		foreach ($_SESSION['gd_agent']->sources as $source) {
			$options[$source->id] = $source->name;
		}
		
		$this->create_dropdown($this->option_id, 'source', $this->option_name, 'Source', $options, $current);
	}

	function code_field() {
		$this->create_hidden($this->option_id, 'code', $this->option_name, 'Code', $this->fields['Code']);
	}
	
	function type() {
		global $gd_api;
		$types  = $gd_api->call("GET", "listings/types");
		$values = array(
			'all' => 'All'
		);
		
		foreach($types as $type) {
			$values["$type->id"] = $type->readable;
		}

		// ($option_id, $field_id, $option_name, $fieled_name, $values)
		$this->create_dropdown($this->option_id, 'type', $this->option_name, 'Type', $values, $this->fields['Type']);
	}
}