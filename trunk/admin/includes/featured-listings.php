<?
global $gd_api;

wp_enqueue_script('geodigs_update_api_on_save', DIR_GD_ADMIN . 'js/featured-listings.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Featured_Listings_Table extends WP_List_Table {
	
	function __construct(){
		global $status, $page;
				
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'listing',     //singular name of the listed records
			'plural'    => 'listings',    //plural name of the listed records
			'ajax'      => true        //does this table support ajax?
		) );
		
	}
	
	function column_default($item, $column_name){
		switch($column_name){
			case 'address':
				return $item->address->readable;
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}
	
	function column_mls($item){
		
		//Build row actions
		$actions = array(
			// 'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>',$_REQUEST['page'],'edit', $item->id),
			'delete'    => sprintf('<a href="?page=%s&action=%s&listing=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->id),
		);
		
		//Return the title contents
		return sprintf('%1$s%2$s',
			/*$1%s*/ $item->mlsNumber,
			/*$2%s*/ $this->row_actions($actions)
		);
	}
	
	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item->id                //The value of the checkbox should be the record's id
		);
	}

	function column_img($item){
		global $gd_api;

		return sprintf(
			'<img src="%1$s" alt="%2$s" class="gd-featured-thumb"/>',
			/*$1%s*/ $gd_api->url . 'listings/' . $item->id. '/photo/1',
			/*$2%s*/ 'MLS ' . $item->mlsNumber
		);
	}
	
	function get_columns(){
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'mls'     => 'MLS #',
			'img'    => 'Image',
			'address'  => 'Address'
		);
		return $columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
		);
		return $actions;
	}
	
	function process_bulk_action() {
		global $gd_api;
		
		//Detect when a bulk action is being triggered...
		if( 'delete'===$this->current_action() ) {
			// Delete each selected listing
			if (isset($_REQUEST['listing']) && ! empty($_REQUEST['listing'])) {
				if (is_array($_REQUEST['listing'])) {
					foreach ($_REQUEST['listing'] as $id) {
						$gd_api->call('DELETE', 'featured/' . $id);
					}
				} else {
					$gd_api->call('DELETE', 'featured/' . $_REQUEST['listing']);
				}
			}
		}

		// if( 'edit'===$this->current_action() ) {
		// 	// Delete each selected listing
		// 	if (isset($_REQUEST['listing']) && ! empty($_REQUEST['listing'])) {
		// 		if (is_array($_REQUEST['listing'])) {
		// 			foreach ($_REQUEST['listing'] as $id) {
		// 				$gd_api->call('DELETE', 'featured/' . $id);
		// 			}
		// 		} else {
		// 			$gd_api->call('DELETE', 'featured/' . $_REQUEST['listing']);
		// 		}
		// 	}
		// }
		
	}
	
	public function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="' . $item->id . '" ' . $row_class . '>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
	function prepare_items() {
		global $gd_api;

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$this->process_bulk_action();
		
		$api_results = $gd_api->call('GET', 'featured');
		$data = $api_results->listings;
		
		$current_page = $this->get_pagenum();
		
		$total_items = count($data);
		
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		
		$this->items = $data;
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}


}
// Did we add a new listing?
if (isset($_POST['gd_add_featureds']) && $_POST['gd_add_featureds'] != '') {
	$successes = array();

	$featureds = explode("\n", trim($_POST['gd_add_featureds']));
	$filtered = array_filter($featureds, 'trim');
	foreach ($filtered as $listing) {
		$response = $gd_api->call('POST', 'featured/' . $_SESSION['gd_agent']->mlsCode->source . '::' . $listing);
		if (isset($response->error)) {
			add_settings_error('geodigs_featured_listings', 'invalid_mls', $response->message);
			// Since this isn't a real setting we need to call this manually
			settings_errors();
		}
		else {
			$successes[] = $listing;
		}
	}

	if (count($successes) > 0) {
		$listings = implode(', ', $successes);
		add_settings_error('geodigs_featured_listings_select_random', 'update_featured_success', 'Successfully added listing(s) ' . $listings . ' to featured listings', 'updated');
		// Since this isn't a real setting we need to call this manually
		settings_errors();
	}
}

// Did we reorder our listings?
if (isset($_POST['gd_featured_sort_order']) && $_POST['gd_featured_sort_order'] != '') {
	$sort = 0;
	$listings = explode(',', $_POST['gd_featured_sort_order']);
	foreach ($listings as $listing) {
		$gd_api->call('PUT', 'featured/' . $listing . '?sort=' . $sort);
		$sort++;
	}
}

/** Create table of currently featured listings **/
 
//Create an instance of our package class...
$featured_listings_table = new Featured_Listings_Table();
// //Fetch, prepare, sort, and filter our data...
$featured_listings_table->prepare_items();
// // Display our data
$featured_listings_table->display();