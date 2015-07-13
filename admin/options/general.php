<?php
class Geodigs_Options_General extends Geodigs_Options {
	public $option_id;
	public $fields;
	public $option_name;

	function __construct() {
		$this->option_id	= 'geodigs-general';
		$this->option_name	= GD_OPTIONS_GENERAL;
		$this->fields		= get_option($this->option_name);
	}

	function create_form() {
	}

	function validate($input) {
		$output	= $input;
		$error	= false;
		
		// Make sure this is a number
		$output['MaxDetailViews'] = intval($input['MaxDetailViews']);
		if (!$output['MaxDetailViews']) {
			// This isn't an integer so set it to empty
			$output['MaxDetailViews'] = '';
			add_settings_error('geodigs_general', 'invalid_view_count', 'You must enter a number for the max listing details page view count', 'error');
			$error = true;
		}
		else {
			// Update agent info if all is good
			$_SESSION['gd_agent']->max_detail_views = $output['MaxDetailViews'];
		}
		
		if (!$error) {
			add_settings_error('geodigs_general', 'general_successful', 'General Settings updated', 'updated');
			return $output;
		}
	}
	
	function listings_layout_field() {
		$options = array();
		$current = $this->fields['ListingsLayout'] ? $this->fields['ListingsLayout'] : 'rows';
		
		$options = array(
			'rows' => 'Rows',
			'columns-2' => '2 Columns',
		);
		
		$this->create_dropdown($this->option_id, 'listings-;ayout', $this->option_name, 'ListingsLayout', $options, $current);
	}

	function max_listing_details_view_count_field() {
		$value = isset($this->fields['MaxDetailViews']) ? $this->fields['MaxDetailViews'] : '5';
		
		$this->create_number_box($this->option_id, 'max-detail-views', $this->option_name, 'MaxDetailViews', $value);
	}

	function advanced_search_cities_field() {
		global $gd_api;
		
		$toggle_name         = 'UseCustomCities';
		$custom_cities_array = $this->option_name . '[CustomCities]';
		$cities              = $gd_api->call('GET', 'cities');
		
		?>
		<fieldset>
			<input type="checkbox" id="<?=$this->option_id?>-adv-search-custom-cities" name="<?=$this->option_name?>[<?=$toggle_name?>]" <?php checked($this->fields[$toggle_name], 'on'); ?>>
			<label for="<?=$this->option_id?>-adv-search-custom-cities">Use custom cities for advanced search</label>
			<ul id="gd-city-selection">
				<?php foreach ($cities as $city): ?>
				<li>
					<input type="checkbox" name="<?=$custom_cities_array?>[<?=$city->name?>]" id="cities[<?=$city->name?>]" <?php checked($this->fields['CustomCities'][$city->name], 'on'); ?>>
					<label for="cities[<?=$city->name?>]"><?=$city->name?></label>
				</li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
		<?php
	}
}

