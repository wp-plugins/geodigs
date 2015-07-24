<?php
class gd_options_seo extends Geodigs_Options {
	public $id_prefix;
	public $options;
	public $options_name;

	function __construct() {
		$this->id_prefix = 'geodigs-seo';
		$this->options_name = 'geodigs_seo';
		$this->options = get_option($this->options_name);
	}

	function create_form() {
		echo '<span class="description">Manage your Search Engine Optimization</span>';
	}

	function validate($input) {
		$output = $input;

		return $output;
	}

	function keywords() {
		$this->create_text_box($this->id_prefix, 'keywords', $this->options_name, 'Keywords', $this->options['Keywords']);
	}
}