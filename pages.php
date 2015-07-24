<?php

class Geodigs_Page_Handler {
	public $listings_page;
	
	public function __construct() {
		if (!is_admin()) {
			// Removes comments section
			add_filter("comments_template", array($this, 'remove_comments'), 500);
			// Removes post background
			add_filter('post_class', array($this, 'reset_classes'));
			// Keep the post from being edited
			add_filter('get_edit_post_link', array($this, 'disable_editing'), 10, 3);
			// Replace post content with our content
			add_filter('the_posts', array($this, 'display_page'), 100);

			// Remove's WP default styling
			remove_filter('the_content', 'wptexturize');
			remove_filter('the_content', 'convert_smilies');
			remove_filter('the_content', 'convert_chars');
			remove_filter('the_content', 'wpautop');
			remove_filter('the_content', 'prepend_attachment');
		}
	}

	public function display_page($posts) {
		global $gd_api;
		global $wp_query;
		
		// Removes link from title and comment section
		$wp_query->found_posts		= 0;
		$wp_query->max_num_pages	= 0;
		$wp_query->is_page			= 1;
		$wp_query->is_home			= null;
		$wp_query->is_singular		= 1;
		if (!isset($posts[0])) {
			$posts[0] = object;
/* 			return $posts; */
		}
		
		// Get first post and save its details
		$page_data    = $posts[0];
		$post_id      = $page_data->ID ? $page_data->ID : time();
		$page_content = trim($page_data->post_content);

		// Empty the post data
		$page_data = '';
		
		// By default always show the footer (disclaimers)
		$show_footer = true;
		
		// Figure out what kind of page to display
		if (isset($wp_query->query_vars['gd_action'])) {
			switch (urldecode($wp_query->query_vars['gd_action'])) {
				case 'account-home':
					// Make sure we are using SSL
					gd_require_ssl();
					Geodigs_User::require_login($_SERVER['REQUEST_URI']);
					$show_footer = false;
				
					ob_start();
					include GD_DIR_INCLUDES . 'account-home.php';

					$page_data = array(
						'content' => ob_get_contents(),
						'description' => 'User home page',
						'title' => 'My Account',
					);
					ob_end_clean();
					break;
				
				case 'account-settings':
					Geodigs_User::require_login($_SERVER['REQUEST_URI']);
					$show_footer = false;
					$page_data   = $this->display_account_settings_page();
					break;
				
				case 'add-favorite':
					Geodigs_User::require_login($_SERVER['REQUEST_URI']);
					Geodigs_User::add_favorite($_GET['listing_id']);
					break;
				
				case 'advanced-search':
					$show_footer = false;
					$page_data   = array(
						'content' => '[gd_advanced_search]',
						'description' => 'Real estate advanced search tool',
						'title' => 'Advanced Search',
					);
					break;
				
				case 'delete-favorite':
					Geodigs_User::require_login($_SERVER['REQUEST_URI']);
					Geodigs_User::delete_favorite($_GET['listing_id']);
					break;
				
				case 'details':
					if (isset($wp_query->query_vars['gd_listing_id'])) {
						$listing_id = $wp_query->query_vars['gd_listing_id'];
						
						if (isset($_SESSION['gd_user']) || gd_under_detail_view_limit($listing_id) == true) {
							$listing = $gd_api->call('GET', 'listings/' . $listing_id);

							ob_start();

							// Gets the HTML for our results.
							include GD_DIR_INCLUDES . 'listing-detail.php';

							$page_data = array(
								'content' => ob_get_contents(),
								'description' => 'Real estate listing details',
								'title' => $listing->address->readable,
							);

							ob_end_clean();
						}
						else {
							$_SESSION['gd_redirect_url'] = $_SERVER['REQUEST_URI'];
							
							ob_start();
							include GD_DIR_INCLUDES . 'detail-view-count-reached.php';
							
							$page_data = array(
								'content' => ob_get_contents(),
								'description' => 'Require login to view more',
								'title' => 'View Limit Reached',
							);
							ob_end_clean();
						}
					}
					break;
				
				case 'download-document':
					global $wpdb;
				
					$id    = $wp_query->query_vars['doc_id'];
					$query = $wpdb->prepare("SELECT CONCAT(fileName, '.', extension) AS fileName, size FROM `gd_ds_files` WHERE id = %s", $id);
					$file  = $wpdb->get_row($query);
				
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . $file->fileName . '"');
					header('Content-Length: ' . $file->size);

					ob_clean();
					flush();
					readfile(GD_DIR_DOCUMENT_STORE . $file->fileName);
					exit;
				
				case 'favorites':
					Geodigs_User::require_login($_SERVER['REQUEST_URI']);
					$page_data = $this->display_favorites_page();
					break;
				
				case 'forgot-password':
					// Logs out the user if they are logged in but doesn't redirect them to the home page
					Geodigs_User::log_out(false);
					$show_footer = false;
					$page_data   = $this->display_forgot_password_page();
					break;
				
				case 'home-worth':
					$show_footer = false;
				
					ob_start();
					include GD_DIR_INCLUDES . 'home-worth-form.php';
					$page_data = array(
						'content'		=> ob_get_contents(),
						'description'	=> 'Geodigs What\'s My Home Worth Calculator',
						'title'			=> 'What\'s My Home Worth',
					);

					ob_end_clean();
					break;
				
				case 'listing-alerts':
					Geodigs_User::require_login($_SERVER['REQUEST_URI']);
				
					ob_start();
				
					include GD_DIR_INCLUDES . 'listing-alerts.php';

					$page_data = array(
						'content'		=> ob_get_contents(),
						'description'	=> 'Geodigs Listing Alerts Page',
						'title'			=> $page_title,
					);
				
					ob_end_clean();
					break;
				
				case 'login':
					$show_footer = false;
				
					// Logs out the user if they are logged in but doesn't redirect them to the home page
					Geodigs_User::log_out(false);
					$page_data = $this->display_login_page();
					break;
				
				case 'log-out':
					Geodigs_User::log_out();
					break;
				
				case 'more-info':
					//Geodigs_User::require_login($_SERVER['REQUEST_URI']);
				
					ob_start();
				
					include GD_DIR_INCLUDES . 'more-info-form.php';

					$show_footer = false;
					$page_data   = array(
						'content'     => ob_get_contents(),
						'description' => 'Get more info for a listing from the realtor',
						'title'       => 'More Information',
					);
				
					ob_end_clean();
					break;
				
				case 'more-info-requested':
					//Geodigs_User::require_login($_SERVER['REQUEST_URI']);
				
					ob_start();
				
					include GD_DIR_INCLUDES . 'more-info-requested.php';

					$show_footer = false;
					$page_data   = array(
						'content'     => ob_get_contents(),
						'description' => 'Show success message for more info request',
						'title'       => 'Request Sent',
					);
				
					ob_end_clean();
					break;
				
				case 'our-listings':
					$page_data = array(
						'content' => '[gd_our_listings]',
						'description' => 'Our listings',
						'title' => 'Our Listings',
					);
					break;
				
				case 'proxy-api':
					header('Content-Type: application/json');
				
					switch (urldecode($wp_query->query_vars['api_action'])) {
						case 'get-statuses':
							echo json_encode($gd_api->call("GET", "listings/statuses"));
							exit;
						
						case 'get-styles':
							echo json_encode($gd_api->call("GET", "listings/styles"));
							exit;
						
						case 'get-types':
							echo json_encode($gd_api->call("GET", "listings/types"));
							exit;
					}
					break;
				
				case 'search':
					$results = $gd_api->call('GET', 'listings');
				
					$page_data = $this->display_search_results($results);
					break;
				
				case 'signup':
					// Logs out the user if they are logged in but doesn't redirect them to the home page
					Geodigs_User::log_out(false);
					$show_footer = false;
					$page_data   = $this->display_sign_up_page();
					break;
				
				default:
					// Do nothing
					break;
			}
			
			// Get footer
			if ($show_footer) {
				ob_start();
				include GD_DIR_INCLUDES . 'footer.php';
				$page_data['content'] .= ob_get_contents();
				ob_end_clean();
			}

			// Setup the post
			$post = (object)array(
				"ID"				=> $post_id,
				"post_content"		=> $page_data['content'],
				"post_name"			=> "geodigs",
				"post_title"		=> $page_data['title'],
				"post_status"		=> "publish",
				"post_author"		=> 1,
				"ping_status"		=> "closed",
				"post_parent"		=> 0,
				"post_type"			=> 'page',
				"post_excerpt"		=> $page_data['description'],
				"post_date"			=> date("c"),
				"post_date_gmt"		=> gmdate("c"),
				"comment_count"		=> 0,
				"comment_status"	=> "closed",
			);

			wp_cache_set( $post_id, $post, 'posts');

			$posts = array($post);
		}

		return $posts;
	}
	
	private function display_favorites_page() {
		global $gd_api;
		
		$hide_count       = true;
		$hide_sort        = true;
		$hide_edit_search = true;
		$hide_pagination  = true;
		$results          = $gd_api->call('GET', 'favorites');
		
		// Begin output of page
		ob_start();
	
		include_once GD_DIR_INCLUDES . 'listings-results.php';
		
		$page_data = array(
			'content'     => ob_get_contents(),
			'description' => 'Geodigs User Favorites Page',
			'title'       => 'Favorites',
		);
		
		ob_end_clean();
		return $page_data;
	}
	
	private function display_account_settings_page() {
		ob_start();
		include_once GD_DIR_INCLUDES . 'account-settings.php';
		$content = ob_get_contents();
		ob_end_clean();
		
		$page_data = array(
			'content'		=> $content,
			'description'	=> 'Geodigs User Account Settings Page',
			'title'			=> 'Account Settings',
		);

		return $page_data;
	}
	
	private function display_forgot_password_page() {
		global $gd_api;
		
		$errors = array();
		
		// Send API call and figure out what page to display
		if ($_POST['email']) {
			$forgot_pw_request = $gd_api->call('GET', 'forgot/' . $_POST['email']);
			
			if (isset($forgot_pw_request->error)) {
				$page_type = 'form';
				$errors['not_found'] = $forgot_pw_request->message;
			}
			else {
				$page_type = 'success';
			}
		}
		else {
			$page_type = 'form';
		}
		
		// Output HTML
		ob_start();
		
		include_once GD_DIR_INCLUDES . 'forgot-password.php';
		
		$page_data = array(
			'content'		=> ob_get_contents(),
			'description'	=> 'Geodigs User Forgot Password Page',
			'title'			=> 'Forgot Password',
		);

		ob_end_clean();
		return $page_data;
	}
	
	private function display_sign_up_page() {
		global $gd_api;
		
		$errors = array();
		
		// If we are submitting the form process it
		if (
			isset($_POST['firstName']) &&
			isset($_POST['lastName']) &&
			isset($_POST['phone']) &&
			isset($_POST['email']) &&
			isset($_POST['password'])
		) {
			// If our email and emailConfirm fields do not match return an error
			if ($_POST['email'] != $_POST['emailConfirm']) {
				$errors['emails'] = 'Emails do not match';
			}
			// If our password and passwordConfirm fields do not match return an error
			if ($_POST['password'] != $_POST['passwordConfirm']) {
				$errors['passwords'] = 'Passwords do not match';
			}
			// If our password is not valid return an error
			if (gd_is_valid_password($_POST['password']) == false) {
				$errors['password_invalid'] = 'Password must contain at least 6 characters with at least 1 letter and 1 number';
			}
			// If our phone number is not 10 digits
			$_POST['phone'] = preg_replace('/\D+/', '', $_POST['phone']);
			if (strlen($_POST['phone']) != 10) {
					$errors['phone_length'] = 'Invalid phone number';
			}
			
			// If we don't have any errors at this point call the API
			if (count($errors) == 0) {
				$create_user = $gd_api->call('POST', 'users');
				
				// If we have an error let the user know (we should never get this at this point but it's here as a catch)
				if (isset($create_user->error)) {
					wp_die("Uh oh something went wrong signing up!  Please go back and try again or report this message to the webmaster.\nError: " . $create_user->message);
				}
				// If all is good show our confirmation page
				else {
					// Send confirmation email
					$to			= $_POST['email'];
					$subject	= get_bloginfo('name') . ': Thanks for Signing Up!';
					$headers	= 'From: ' . get_bloginfo('admin_email') . "\r\n" . 'Reply-To: ' . get_bloginfo('admin_email') . "\r\n" . 'X-Mailer: PHP/' . phpversion();
					// Get email contents
					$name = $_POST['firstName']; // this is used in the email
					ob_start();
					include_once 'emails/signup-confirmation.php';
					$message = ob_get_contents();
					ob_end_clean();
					// Send it
					mail($to, $subject, $message, $headers);
					
					$title = 'Success!';
					ob_start();
					include_once GD_DIR_INCLUDES . 'signup-success.php';
					$content = ob_get_contents();
					ob_end_clean();
				}
			}
			// If we have errors show our form with them
			else {
				$title   = 'Sign Up';
				$content = GeodigsTemplates::loadTemplate(
					'account/register.php',
					array(
						'errors' => $errors,
					),
					false
				);
			}
		}
		// We aren't processing the form so show it
		else {
			$title   = 'Sign Up';
			$content = GeodigsTemplates::loadTemplate(
				'account/register.php',
				array(
					'errors' => $errors,
				),
				false
			);
		}
		
		$page_data = array(
			'content'		=> $content,
			'description'	=> 'Geodigs User Sign Up Page',
			'title'			=> $title,
		);

		return $page_data;
	}
	
	private function display_login_page() {
		global $gd_api;
		
		// If our login was successful redirect the page to the home
		if (isset($_POST['email']) && isset($_POST['password'])) {
			$login_request = $gd_api->call('POST', 'users/login');
			
			if (isset($login_request->error)) {
				// Return to login form with the error
				return $this->create_login_form($login_request->message);
			}
			else {
				Geodigs_User::login($login_request);
			}
		}
		else {
			return $this->create_login_form();
		}
	}
	
	private function create_login_form($error = null) {
		$content = GeodigsTemplates::loadTemplate(
			'account/login.php',
			array(
				'error' => $error,
			),
			false
		);
		
		$page_data = array(
			'content' => $content,
			'description' => 'Geodigs User Login Page',
			'title' => 'Login',
		);

		return $page_data;
	}

	private function display_search_results($results) {
		global $gd_api;
		
		// Save our search link
		$_POST['results_url'] = $_SERVER['REQUEST_URI'];
		
		// Begin output of page
		ob_start();
	
		include_once GD_DIR_INCLUDES . 'listings-results.php';
		
		$page_data = array(
			'content' => ob_get_contents(),
			'description' => 'Real estate search results',
			'title' => 'Search Results',
		);
		
		ob_end_clean();
		return $page_data;
	}

	public function reset_classes($classes) {
		global $wp_query;

		if (isset($wp_query->query['gd_action'])) {
			$classes = array_diff($classes, array('hentry'));
		}
		return $classes;
	}
	
	public function remove_comments($path){
		global $wp_query;

		if (isset($wp_query->query['gd_action'])) {
			return GD_DIR_INCLUDES . 'comments.php';
		}
		return $path;
	}

	public function disable_editing($edit_link, $post_id, $context) {
		return;
	}
}

