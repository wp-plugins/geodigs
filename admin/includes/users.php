<!-- START users.php -->
<?php



/** Create table of calendars **/
 
// Create an instance of our package class...
$calendar_table = new Geodigs_User_Table();
// Fetch, prepare, sort, and filter our data...
$calendar_table->prepare_items();
// Display our data
$calendar_table->display();

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Geodigs_User_Table extends WP_List_Table {

	function __construct(){
		global $status, $page;
				
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'id',     //singular name of the listed records
			'plural'    => 'ids',    //plural name of the listed records
			'ajax'      => true        //does this table support ajax?
		) );
		
	}

	function column_default($item, $column_name){
		switch($column_name){
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_name($item){
		//Build row action
		$actions = array(
			'login'     => sprintf('<a href="?page=%s&action=%s&id=%s">Login</a>', $_REQUEST['page'], 'login', $item->id),
		);
		
		//Return the name contents
		return sprintf('%1$s%2$s',
			/*$1%s*/ $item->name,
			/*$2%s*/ $this->row_actions($actions)
		);
	}

	function column_email($item){
		return sprintf('%1$s', $item->email);
	}

	function column_phone($item){
		return sprintf('%1$s', $item->phone);
	}

	function get_columns(){
		$columns = array(
			'name'  => 'Name',
			'email' => 'Email',
			'phone' => 'Phone',
		);
		return $columns;
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
		
		$per_page = 10;
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		// Get calendars
		$data = $gd_api->call('GET', 'users');
		
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
} ?>
<!-- END users.php -->