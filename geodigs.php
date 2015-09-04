<?php
/**
 * Plugin Name: GeoDigs
 * Version: 1.4.2
 * Author: New Media One
 * Author URI: www.newmediaone.net
 * License: GPL2
 */

/*  Copyright 2015  New Media One  (email : support@newmediaone.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*****************************************************************************
TABLE OF CONTENTS
******************************************************************************
*	#SECTION 1.0 - Setup
*		#SECTION 1.1 - API
*		#SECTION 1.2 - Agent Setup
*		#SECTION 1.3 - Directories
*		#SECTION 1.4 - Rewrite Rules
*		#SECTION 1.5 - Pages and Menu Items
*	#SECTION 2.0 - Scripts and Styles
*	#SECTION 3.0 - Widgets
*	#SECTION 4.0 - Shortcodes
*	#SECTION 5.0 - Misc. Functions
*****************************************************************************/

##############################################################################
#SECTION 1.0 Setup
##############################################################################

/* $gd_show_debug = true; */
if ($gd_show_debug) {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
}

// Reset session vars
$gd_reset_session = false;

// Wordpress recommends to use this for security
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Setup protocol
$protocol = is_ssl() ? 'https://' : 'http://';
define('PROTOCOL', $protocol);

// Get API key and login status
// NOTE: there is a difference between not having a login status and an invalid login status,
// so we check if there is a login status before checking what the status is
$gd_api_key = '';
$login_info = get_option('geodigs_login');
if ($login_info['Status']) {
	if ($login_info['Status'] == 'success'){
		// For Status to be 'success' the APIKey must be supplied
		// so we don't have to check for it
		$gd_api_key = $login_info['APIKey'];

		// Start our session
		session_start();
		
		// Used for dev testing
		if ($gd_reset_session) {
			unset($_SESSION['gd_agent']);
			unset($_SESSION['gd_user']);
		}

		define('GD_LOGIN_STATUS', 'success');
	}
	else {
		define('GD_LOGIN_STATUS', 'invalid');
	}
}
else {
	define('GD_LOGIN_STATUS', 'not_logged_in');
}

// Admin Settings
define('GD_ADMIN_PAGE_CALENDARS', 'geodigs-calendars');
define('GD_ADMIN_PAGE_DOCUMENT_STORE', 'geodigs-document-store');
define('GD_ADMIN_PAGE_FEATURED_LISTINGS', 'geodigs-featured-listings');
define('GD_ADMIN_PAGE_GENERAL', 'geodigs-general');
define('GD_ADMIN_PAGE_USERS', 'geodigs-users');
define('GD_ADMIN_SECTION_ADVANCED_SEARCH', 'advanced-search');
define('GD_ADMIN_SECTION_GENERAL', 'general');
define('GD_ADMIN_SECTION_LISTINGS', 'listings');
define('GD_ADMIN_SECTION_OUR_LISTINGS', 'our-listings');
define('GD_ADMIN_TAB_GENERAL', 'general');
define('GD_ADMIN_TAB_LISTINGS', 'listings');

define('GD_OPTIONS_ADVANCED_SEARCH', 'geodigs_advanced_search');
define('GD_OPTIONS_FEATURED_LISTINGS', 'geodigs_featured_listings');
define('GD_OPTIONS_GENERAL', 'geodigs_general');
define('GD_OPTIONS_LOGIN', 'geodigs_login');
define('GD_OPTIONS_OUR_LISTINGS', 'geodigs_our_listings');

// Store options in array for uninstall
$gd_options = array(
	GD_OPTIONS_FEATURED_LISTINGS,
	GD_OPTIONS_GENERAL,
	GD_OPTIONS_OUR_LISTINGS,
);

// Get our function files
require_once 'functions/core.php';
require_once 'functions/format.php';
require_once 'functions/helper.php';

// Get our classes
require_once 'calendars.php';
require_once 'document-store.php';
require_once 'Templates.php';
require_once 'user.php';

GeodigsTemplates::init();

##############################################################################
#SECTION 1.1 API
##############################################################################

require_once 'api.php';
$gd_api = new GeodigsApi($gd_api_key);


##############################################################################
#SECTION 1.2 Agent Setup
##############################################################################

// If our info is valid make sure our agent is stored in the session
if (GD_LOGIN_STATUS == 'success') {
	// If there was an error in the previous session with the agent or the agent hasn't been stored store it
	if($_SESSION['gd_agent']->error || isset($_SESSION['gd_agent']) == false) {
		$_SESSION['gd_agent'] = $gd_api->call('GET', 'agents/' . $login_info['AgentCode']);
		
		if ($_SESSION['gd_agent']) {
			// Max Detail Views
			$general_options = get_option(GD_OPTIONS_GENERAL);
			$_SESSION['gd_agent']->max_detail_views = $general_options['MaxDetailViews'];

			// Switch to public key
			$gd_api->set_key($_SESSION['gd_agent']->publicKey);
		} else {
			echo 'There has been an error.  Please check back later.';
		}
	}
}


##############################################################################
#SECTION 1.3 Directories
##############################################################################

// Files aren't always included from the correct directory so this makes sure we do
$gd_directory = file_exists(dirname( __FILE__ ) . '/geodigs.php') ? dirname( __FILE__ ) . "/" : '';

define('GD_DIR', $gd_directory);
define('GD_DIR_ADMIN', GD_DIR . 'admin/');
define('GD_DIR_ADMIN_INCLUDES', GD_DIR_ADMIN . 'includes/');
define('GD_DIR_CSS', GD_DIR . 'css/');
define('GD_DIR_DOCUMENT_STORE', $_SERVER['DOCUMENT_ROOT'] . '/document-store/');
define('GD_DIR_EMAILS', GD_DIR . 'emails/');
define('GD_DIR_IMAGES', GD_DIR . 'images/');
define('GD_DIR_INCLUDES', GD_DIR . 'includes/');
define('GD_DIR_JS', GD_DIR . 'js/');
define('GD_DIR_MODALS', GD_DIR . 'modals/');
define('GD_DIR_THEMES', GD_DIR . 'themes/');
define('GD_DIR_WIDGETS', GD_DIR . 'widgets/');

// URL paths
define('GD_URL_PLUGIN', plugins_url() . '/geodigs/');
define('GD_URL_CSS', GD_URL_PLUGIN . 'css/');
define('GD_URL_IMAGES', GD_URL_PLUGIN . 'images/');
define('GD_URL_JS', GD_URL_PLUGIN . 'js/');

##############################################################################
#SECTION 1.4 Rewrite Rules
##############################################################################

function gd_add_query_vars($vars) {
	$vars[] = 'gd_action';
	$vars[] = 'gd_listing_id';
	$vars[] = 'doc_id';
	$vars[] = 'api_action';

	return $vars;
}
add_filter('query_vars', 'gd_add_query_vars');

// Redirects
$gd_url_prefix       = 'real-estate/';
$gd_account_prefix   = 'account/';
$gd_favorites_prefix = 'favorites/';
$gd_proxy_api        = 'geodigs/api/';

define('GD_URL_ACCOUNT', $gd_account_prefix);
define('GD_URL_ACCOUNT_SETTINGS', $gd_account_prefix . 'settings/');
define('GD_URL_ADD_FAVORITE', $gd_favorites_prefix . 'add/');
define('GD_URL_ADV_SEARCH', $gd_url_prefix . 'find/');
define('GD_URL_DELETE_FAVORITE', $gd_favorites_prefix . 'delete/');
define('GD_URL_DETAILS', $gd_url_prefix . 'home/');
define('GD_URL_DOCUMENT_STORE', 'document-store/');
define('GD_URL_FAVORITES', $gd_favorites_prefix);
define('GD_URL_FORGOT_PASSWORD', $gd_account_prefix . 'forgot-password/');
define('GD_URL_HOME_WORTH', 'home-worth/');
define('GD_URL_LISTING_ALERTS', 'listing-alerts/');
define('GD_URL_LOGIN', $gd_account_prefix . 'login/');
define('GD_URL_LOG_OUT', $gd_account_prefix . 'log-out/');
define('GD_URL_MORE_INFO', GD_URL_DETAILS . 'more-info/');
define('GD_URL_MORE_INFO_REQUESTED', GD_URL_MORE_INFO . 'requested/');
define('GD_URL_OUR_LISTINGS', $gd_url_prefix . 'our-listings/');
define('GD_URL_PROXY_API_STATUSES', $gd_proxy_api . 'statuses/');
define('GD_URL_PROXY_API_STYLES', $gd_proxy_api . 'styles/');
define('GD_URL_PROXY_API_TYPES', $gd_proxy_api . 'types/');
define('GD_URL_SEARCH', $gd_url_prefix . 'homes/');
define('GD_URL_SIGNUP', $gd_account_prefix . 'signup/');



function gd_add_rewrite_rules($rules) {
	$rewrite_rules = array(
		GD_URL_ACCOUNT . '?$'                             => 'index.php?gd_action=account-home', // Account Home
		GD_URL_ACCOUNT_SETTINGS . '?$'                    => 'index.php?gd_action=account-settings', // Account Settings
		GD_URL_ADD_FAVORITE . '?$'                        => 'index.php?gd_action=add-favorite', // Add a favorite
		GD_URL_ADV_SEARCH . '?$'                          => 'index.php?gd_action=advanced-search', // Takes us to the advanced search page
		GD_URL_DELETE_FAVORITE . '?$'                     => 'index.php?gd_action=delete-favorite', // Delete a favorite
  		GD_URL_DETAILS . '([A-Za-z0-9-]*)/(\d+)-(\d+)/?$' => 'index.php?gd_action=details&gd_listing_id=$matches[2]::$matches[3]', // Gives us the details for the selected listing */
		GD_URL_DOCUMENT_STORE . '(\d+)?$'                 => 'index.php?gd_action=download-document&doc_id=$matches[1]', // Download document from document store
		GD_URL_FAVORITES . '?$'                           => 'index.php?gd_action=favorites', // Manage favorites
		GD_URL_FORGOT_PASSWORD . '?$'                     => 'index.php?gd_action=forgot-password', // Allows the user to reset their password via email
		GD_URL_HOME_WORTH . '?$'                          => 'index.php?gd_action=home-worth', // Allows the user to calculate their homeworth
		GD_URL_LISTING_ALERTS . '?$'                      => 'index.php?gd_action=listing-alerts', // Manage Listing Alerts
		GD_URL_LOGIN . '?$'                               => 'index.php?gd_action=login', // Lets users login to their accounts
		GD_URL_LOG_OUT . '?$'                             => 'index.php?gd_action=log-out', // Lets users log out of their accounts
		GD_URL_MORE_INFO . '?$'                           => 'index.php?gd_action=more-info', // Lets users request more information from the realtor
		GD_URL_MORE_INFO_REQUESTED . '?$'                 => 'index.php?gd_action=more-info-requested', // Send request to client and show success message
		GD_URL_OUR_LISTINGS . '?$'                        => 'index.php?gd_action=our-listings', // Show just our listings
		GD_URL_PROXY_API_STATUSES . '?$'                  => 'index.php?gd_action=proxy-api&api_action=get-statuses', // Used to make JS calls to the API for listing statuses
		GD_URL_PROXY_API_STYLES . '?$'                    => 'index.php?gd_action=proxy-api&api_action=get-styles', // Used to make JS calls to the API for listing styles
		GD_URL_PROXY_API_TYPES . '?$'                     => 'index.php?gd_action=proxy-api&api_action=get-types', // Used to make JS calls to the API for listing types
		GD_URL_SEARCH . '?$'                              => 'index.php?gd_action=search', // Gets us listings that match the search terms
		GD_URL_SIGNUP . '?$'                              => 'index.php?gd_action=signup', // Page for user to signup for favorites, alerts, etc
	);
	
	$rules = $rewrite_rules + $rules;
	return $rules;
}
add_filter('rewrite_rules_array', 'gd_add_rewrite_rules');

function gd_flush_rules(){
	$rules = get_option( 'rewrite_rules' );

	if ( ! isset( $rules['(project)/(\d*)$'] ) ) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}
register_activation_hook(__FILE__, 'gd_flush_rules');

##############################################################################
#SECTION 1.5 Pages and Menu Items
##############################################################################

/* Pages */
if (is_admin()) {
	require_once 'admin/index.php';
}
else {
	require_once 'pages.php';
	$gd_page_handler = new Geodigs_Page_Handler();
}

/* Menu */
function gd_create_nav_menu() {
	// Check if the menu exists
	$menu_exists = wp_get_nav_menu_object('GeoDigs');

	// If it doesn't exist, let's create it.
	if (!$menu_exists) {
		$menu_id = wp_create_nav_menu('GeoDigs');

		// Set up default menu items
		wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title'  =>  __('Our Listings'),
			'menu-item-url'    => home_url('/' . GD_URL_OUR_LISTINGS), 
			'menu-item-status' => 'publish'
		));

		wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title'  =>  __('Advanced Search'),
			'menu-item-url'    => home_url('/' . GD_URL_ADV_SEARCH), 
			'menu-item-status' => 'publish'
		));

		wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title'  =>  __('What\'s My Home Worth'),
			'menu-item-url'    => home_url('/' . GD_URL_HOME_WORTH), 
			'menu-item-status' => 'publish'
		));
	}
}
add_action('init', 'gd_create_nav_menu');

// Add user account links
function gd_add_nav_menu_items($items) {
	$items = '<li id="gd-search-modal-button" class="menu-item menu-item-type-custom menu-item-object-custom"><a href="#" class="modal-open" data-modal-id="gd-quick-search-modal">Quick Search</a></li>' . $items;
	
	// If the user is logged in add a link to their account
	if (Geodigs_User::is_logged_in()) {
		$items .= '<li id="gd-my-account-link" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children"><a href="/' . GD_URL_ACCOUNT . '">My Account</a>';
			$items .= '<ul class="sub-menu">';
				$items .= '<li><a id="gd-my-account-home-link" class="menu-item menu-item-type-custom menu-item-object-custom" href="/' . GD_URL_ACCOUNT . '">Home</a></li>';
				$items .= '<li><a id="gd-my-account-favorites-link" class="menu-item menu-item-type-custom menu-item-object-custom" href="' . home_url('/' . GD_URL_FAVORITES, 'http') . '">Favorites</a></li>';
				$items .= '<li><a id="gd-my-account-alerts-link" class="menu-item menu-item-type-custom menu-item-object-custom" href="' . home_url('/' . GD_URL_LISTING_ALERTS, 'http') . '">Listing Alerts</a></li>';
				$items .= '<li><a id="gd-my-account-settings-link" class="menu-item menu-item-type-custom menu-item-object-custom" href="' . home_url('/' . GD_URL_ACCOUNT_SETTINGS, 'http') . '">Settings</a></li>';
				$items .= '<li><a id="gd-user-log-out-link" class="menu-item menu-item-type-custom menu-item-object-custom" href="' . home_url('/' . GD_URL_LOG_OUT, 'http') . '">Log Out</a></li>';
			$items .= '</ul>';
		$items .= '</li>';
	}
	else {
		$items .= '<li id="gd-user-login-link"><a href="' . home_url('/' . GD_URL_LOGIN, 'http') . '">Login</a></li>';
	}

	return $items;
}
add_filter( 'wp_nav_menu_items', 'gd_add_nav_menu_items' );


##############################################################################
#SECTION 2.0 Scripts and Styles
##############################################################################

function gd_enqueue_scripts() {
	wp_enqueue_script('gd_main', GD_URL_JS . 'main.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'gd_enqueue_scripts');

function gd_enqueue_styles() {
	wp_enqueue_style('geodigs', GD_URL_CSS . 'styles.css');
}
add_action('wp_enqueue_scripts', 'gd_enqueue_styles', 100);

function gd_enqueue_admin_styles() {
	wp_enqueue_style('geodigs', GD_URL_CSS . 'styles.css');
}
add_action('admin_enqueue_scripts', 'gd_enqueue_admin_styles');

// Add geodigs class to body
function gd_class($classes) {
	$classes[] = 'gd';
	return $classes;
}
add_filter( 'body_class', 'gd_class' );

##############################################################################
#SECTION 3.0 Widgets
##############################################################################

require_once 'widgets/featured-listings.php';
require_once 'widgets/mortgage-calculator.php';
require_once 'widgets/quick-search.php';
require_once 'widgets/quick-search-modal.php';

function gd_init_widgets() {
	register_widget('Geodigs_Featured_Listings_Widget');
	register_widget('Geodigs_Mortgage_Calculator_Widget');
	register_widget('Geodigs_Quick_Search_Widget');
	register_widget('Geodigs_Quick_Search_Modal_Widget');
}
add_action('widgets_init', 'gd_init_widgets');

// If our widgets contain shortcodes make sure we handle them
add_filter('widget_text', 'do_shortcode');


##############################################################################
#SECTION 4.0 Shortcodes
##############################################################################

require_once 'shortcodes/advanced-search.php';
require_once 'shortcodes/featured-listings.php';
require_once 'shortcodes/listings.php';
require_once 'shortcodes/mortgage-calculator.php';
require_once 'shortcodes/our-listings.php';
require_once 'shortcodes/quick-search.php';

function gd_register_shortcodes() {
	add_shortcode( 'gd_advanced_search', 'gd_advanced_search_shortcode_handler' );
	add_shortcode( 'gd_featured', 'gd_featured_shortcode_handler' );
	add_shortcode( 'gd_listings', 'gd_listings_shortcode_handler' );
	add_shortcode( 'gd_mortgage_calculator', 'gd_mortgage_calculator_shortcode_handler' );
	add_shortcode( 'gd_our_listings', 'gd_our_listings_shortcode_handler' );
	add_shortcode( 'gd_quick_search', 'gd_quick_search_shortcode_handler' );
}
add_action('init', 'gd_register_shortcodes');


##############################################################################
#SECTION 5.0 Misc. Functions
##############################################################################

function gd_debug($line, $file, $info) {
	global $gd_show_debug;

	if ($gd_show_debug) {
		echo '<h3>GD DEBUG</br>LINE: ' . $line . '</br>FILE: ' . $file . '</br>';
		var_dump($info);
	}
}