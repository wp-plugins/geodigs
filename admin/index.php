<?php
if ($_GET['page'] == GD_ADMIN_PAGE_DOCUMENT_STORE) {
	// Require SSL
	gd_require_ssl();
}
else {
	gd_require_http();
}

// Login as user if this information is supplied
if ($_GET['page'] == GD_ADMIN_PAGE_USERS && $_GET['action'] == 'login' && isset($_GET['id'])) {
	Geodigs_User::login_as($_GET['id']);
}

include 'agent-login.php';
include 'options/index.php';
add_action('admin_menu', array('gd_admin', 'add_pages'));
add_action('admin_init', array('gd_admin', 'register_settings'));
add_action('admin_notices', array('gd_admin', 'display_notices'));

class gd_admin {
	public static function add_pages() {
		add_menu_page('GeoDigs Options', 'GeoDigs', 'manage_options', GD_ADMIN_PAGE_GENERAL);
		add_submenu_page(GD_ADMIN_PAGE_GENERAL, 'GeoDigs General', 'General', 'manage_options', GD_ADMIN_PAGE_GENERAL, array('gd_admin', 'create_general_options_page'));
		add_submenu_page(GD_ADMIN_PAGE_GENERAL, 'GeoDigs Calendars', 'Calendars', 'manage_options', GD_ADMIN_PAGE_CALENDARS, array('gd_admin', 'create_calendar_options_page'));
		add_submenu_page(GD_ADMIN_PAGE_GENERAL, 'GeoDigs Document Store', 'Document Store', 'manage_options', GD_ADMIN_PAGE_DOCUMENT_STORE, array('gd_admin', 'create_document_store_options_page'));
		add_submenu_page(GD_ADMIN_PAGE_GENERAL, 'GeoDigs Featured Listings', 'Featured Listings', 'manage_options', GD_ADMIN_PAGE_FEATURED_LISTINGS, array('gd_admin', 'create_featured_listings_options_page'));
		add_submenu_page(GD_ADMIN_PAGE_GENERAL, 'GeoDigs Users', 'Users', 'manage_options', GD_ADMIN_PAGE_USERS, array('gd_admin', 'create_user_options_page'));
		add_submenu_page(GD_ADMIN_PAGE_GENERAL, 'GeoDigs Domains', 'Domains', 'manage_options', GD_ADMIN_PAGE_DOMAINS, array('gd_admin', 'create_domain_options_page'));
	}

	public static function register_settings() {
		if (GD_LOGIN_STATUS == 'success') {
			/** GeoDigs Page **/
			
			// General options
			$general = new Geodigs_Options_General();
			register_setting(GD_OPTIONS_GENERAL, GD_OPTIONS_GENERAL, array($general, 'validate'));
			add_settings_section(GD_ADMIN_SECTION_GENERAL, '', '', GD_ADMIN_PAGE_GENERAL);
			add_settings_field('listings_layout', 'Listing Layout', array($general, 'listings_layout_field'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_GENERAL);
			add_settings_field('max_listing_details_view_count', 'Max Listing Detail page views before login required', array($general, 'max_listing_details_view_count_field'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_GENERAL);
			add_settings_field('advanced_search_cities', 'Available cities for the Advanced Search Page', array($general, 'advanced_search_cities_field'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_GENERAL);

			// Our Listings options
			$our_listings = new Geodigs_Options_Our_Listings();
			register_setting(GD_OPTIONS_GENERAL, GD_OPTIONS_OUR_LISTINGS, array($our_listings, 'validate'));
			add_settings_section(GD_ADMIN_SECTION_OUR_LISTINGS, 'Our Listings', array($our_listings, 'create_form'), GD_ADMIN_PAGE_GENERAL);
			add_settings_field('listings_to_display', 'Listings to Display', array($our_listings, 'listings_to_display_field'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_OUR_LISTINGS);
			add_settings_field('source', 'Primary MLS Source', array($our_listings, 'source_field'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_OUR_LISTINGS);
			add_settings_field('code', '', array($our_listings, 'code_field'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_OUR_LISTINGS);
			add_settings_field('type', 'Listings Type', array($our_listings, 'type'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_OUR_LISTINGS);

			// Advanced Search options
			$advanced_search = new Geodigs_Options_Advanced_Search();
			register_setting(GD_OPTIONS_GENERAL, GD_OPTIONS_ADVANCED_SEARCH, array($advanced_search, 'validate'));
			add_settings_section(GD_ADMIN_SECTION_ADVANCED_SEARCH, 'Advanced Search', array($advanced_search, 'create_form'), GD_ADMIN_PAGE_GENERAL);
			add_settings_field('type', 'Default Listings Type', array($advanced_search, 'type'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_ADVANCED_SEARCH);

			/** Featured Listings Page **/

			// Featured Listings options
			$featured = new Geodigs_Options_Featured_Listings();
			register_setting(GD_OPTIONS_FEATURED_LISTINGS, GD_OPTIONS_FEATURED_LISTINGS, array($featured, 'validate'));
			add_settings_section(GD_ADMIN_SECTION_GENERAL, 'General Settings', array($featured, 'create_form'), GD_ADMIN_PAGE_FEATURED_LISTINGS);
			add_settings_field('toggle_random', 'Select featured listings randomly from Our Listings', array($featured, 'toggle_random'), GD_ADMIN_PAGE_FEATURED_LISTINGS, GD_ADMIN_SECTION_GENERAL);
			add_settings_field('number_of', 'Number of random featured listings', array($featured, 'number_of'), GD_ADMIN_PAGE_FEATURED_LISTINGS, GD_ADMIN_SECTION_GENERAL);
			add_settings_field('sort', 'Random featured listings sort order', array($featured, 'sort'), GD_ADMIN_PAGE_FEATURED_LISTINGS, GD_ADMIN_SECTION_GENERAL);

			// // SEO options
			// $seo = new gd_options_seo();
			// register_setting('geodigs_options', 'geodigs_seo', array($seo, 'validate'));
			// add_settings_section('geodigs_seo', 'Search Engine Optimization', array($seo, 'create_form'), 'geodigs_seo');
		}
		else {
			$login = new gd_agent_login();
			register_setting(GD_OPTIONS_LOGIN, GD_OPTIONS_LOGIN, array($login, 'login_validate'));
			add_settings_section(GD_ADMIN_SECTION_LOGIN, 'Login', array($login, 'create_login_form'), GD_ADMIN_PAGE_GENERAL);
			add_settings_field('agent_code', 'GeoDigs Agent Code', array($login, 'geodigs_agent_code'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_LOGIN);
			add_settings_field('agent_key', 'GeoDigs API Key', array($login, 'geodigs_api_key'), GD_ADMIN_PAGE_GENERAL, GD_ADMIN_SECTION_LOGIN);
		}
	}

	public static function display_notices() {
		settings_errors('geodigs_login');
		settings_errors('geodigs_add_featured');
		settings_errors('geodigs_featured_listings');
		settings_errors('geodigs_our_listings');
		settings_errors('geodigs_advanced_search');
	}

	public static function validate($input) {
		$output = $input;

		return $output;
	}

	public static function create_general_options_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		} ?>
		<div class="wrap">
			<h1>GeoDigs</h1>
			<form method="post" action="options.php">

				<?php
				if (GD_LOGIN_STATUS == 'success') {
					settings_fields(GD_OPTIONS_GENERAL);
					do_settings_sections(GD_ADMIN_PAGE_GENERAL);
					// do_settings_sections('geodigs_seo');

					$advacned_search_url = get_site_url() . '/real-estate/find/';
				}
				else {
					settings_fields(GD_OPTIONS_LOGIN);
					do_settings_sections(GD_ADMIN_PAGE_GENERAL);
				}

				submit_button(); ?>

			</form>
		</div>
	<?php }

	public static function create_featured_listings_options_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Figure out which tab to display
		$active_tab	= isset($_GET['tab']) ? $_GET['tab'] : GD_ADMIN_TAB_GENERAL;
		// If we are on the general tab we want an action to be set so WP can handle it
		$action		= $active_tab == GD_ADMIN_TAB_GENERAL ? 'options.php' : '';

		?>
		<div class="wrap">
			<h2>Featured Listings</h2>
			<h2 class="nav-tab-wrapper">
				<a href="?page=<?=GD_ADMIN_PAGE_FEATURED_LISTINGS?>&tab=<?=GD_ADMIN_SECTION_GENERAL?>" class="nav-tab <?php echo $active_tab == GD_ADMIN_TAB_GENERAL ? 'nav-tab-active' : ''; ?>">General</a>
				<a href="?page=<?=GD_ADMIN_PAGE_FEATURED_LISTINGS?>&tab=<?=GD_ADMIN_SECTION_LISTINGS?>" class="nav-tab <?php echo $active_tab == GD_ADMIN_TAB_LISTINGS ? 'nav-tab-active' : ''; ?>">Listings</a>
			</h2>
			<form id="gd-featured-listings-settings" method="post" action="<?=$action?>">

				<?php
					if ($active_tab == GD_ADMIN_SECTION_GENERAL) {
						settings_fields(GD_OPTIONS_FEATURED_LISTINGS);
						do_settings_sections(GD_ADMIN_PAGE_FEATURED_LISTINGS);
					}
					else { ?>
						<div id="gd-featured-listings-sorter">
							<!-- Force page to return here -->
							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="tab" value="<?=GD_ADMIN_TAB_LISTINGS?>" />
							<input type="hidden" name="gd_featured_sort_order">

							<!-- Now we can render the completed list table -->
							<div>
								<h3>Manage Selected Featured Listings</h3>
								<span class="description">Click and drag to rearrange</span>
								<?php require_once GD_DIR_ADMIN_INCLUDES . 'featured-listings.php'; ?>
							</div>
							<div>
								<h3>Add New Listings</h3>
								<span class="description">Seperate MLS numbers by pressing enter after each entry</span>
								<textarea name="gd_add_featureds" id="gd-new-listings" class="widefat" cols="30" rows="10"></textarea>
							</div>
						</div>
					<?php }
				?>
				
				<?php submit_button(); ?>
			</form>
		</div>
	<?php }
	
	public static function create_document_store_options_page() { ?>
		<div class="wrap">
			<h2>Document Store</h2>
			<?php require_once GD_DIR_ADMIN_INCLUDES . 'document-store.php'; ?>
		</div>
	<?php }
	
	public static function create_calendar_options_page() { ?>
		<div class="wrap">
			<h2>Calendars</h2>
			<?php require_once GD_DIR_ADMIN_INCLUDES . 'calendars.php'; ?>
		</div>
	<?php }
	
	public static function create_user_options_page() { ?>
		<div class="wrap">
			<h2>Users</h2>
			<?php require_once GD_DIR_ADMIN_INCLUDES . 'users.php'; ?>
		</div>
	<?php }

	public static function create_domain_options_page() { ?>
		<div class="wrap">
			<h2>Domains</h2>
			<?php require_once GD_DIR_ADMIN_TEMPLATES . '/new-domain.php'; ?>
		</div>
	<?php }
}

