<?php
/**
 * This class handles the GeoDigs template system
 */

class GeodigsTemplates {
	// Variables
	private static $local;
	private static $path;
	private static $templates;
	
	/**
	 * Initialize the GeoDigs templating system
	 */
	public static function init()
	{
		// Set the path and create the directory if it doesn't exist
		self::$path = $_SERVER['DOCUMENT_ROOT'] . '/geodigs/templates/';
		if (!file_exists(self::$path)) {
			mkdir(self::$path, 0777, true);
		}
		
		// Set the local path
		self::$local = dirname(__FILE__) . '/templates/';

		// Get our templates
		self::$templates = array();
		self::getTemplates();
	}

	/**
	 * Get all files in the GeoDigs template directory
	 */
	public static function getTemplates()
	{
		// Setup recursive iterators
		$recursiveDirectoryIterator = new RecursiveDirectoryIterator(self::$path, RecursiveDirectoryIterator::SKIP_DOTS);
		$recursiveIteratorIterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);

		// Get all the files
		foreach($recursiveIteratorIterator as $file) {
			// If they are php or html files add them to our template list
			$extention = pathinfo($file, PATHINFO_EXTENSION);
			if ($extention == 'php' || $extention == 'html') {
				array_push(self::$templates, $file);
			}
		}
	}

	/**
	 * Gets and outputs the template
	 * @param  string $template Template file to use
	 * @param  array $vars      php variables to pass to the template
	 * @param  bool $output     Output the results or return them
	 */
	public static function loadTemplate($template, $vars, $output = true)
	{
		// Get template file
		$file = self::$path . $template;
		// If the template does not exist in the external folder use the local/default version
		if (!file_exists($file)) {
			$file = self::$local . $template;
			
			if (!file_exists($file)) {
				echo 'Could not find template file ' . $file;
				return false;
			}
		}

		// Extract variables
		extract($vars);

		// Begin output
		ob_start();

		echo "<!-- BEGIN {$file} -->";
		include $file;
		echo "<!-- END {$file} -->";

		// If we aren't outputting the buffer contents return the contents
		if ($output) {
			ob_end_flush();
			return true;
		} else {
			$contents = ob_get_contents();
			ob_end_clean();
			
			return $contents;
		}
	}
}