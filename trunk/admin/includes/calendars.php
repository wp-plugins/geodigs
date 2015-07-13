<!-- START calendars.php -->
<?php
global $gd_api;

// Create calendar manager
$cm = new Geodigs_Calendars();
$calendar;
$action = 'add';

switch ($_GET['action']) {
	case 'add':
		if ($_POST['user'] && $_POST['calendarName'] && $_POST['link']) {
			$user          = sanitize_text_field($_POST['user']);
			$calendar_name = sanitize_text_field($_POST['calendarName']);
			$link          = sanitize_text_field($_POST['link']);
			$cm->add_calendar($user, $calendar_name, $link);
		}
		break;
	case 'edit':
		$calendar = $cm->get_calendar($_GET['id']);
		$action = 'update';
		break;
	case 'update':
		if ($_POST['id'] && $_POST['user'] && $_POST['calendarName'] && $_POST['link']) {
			$id            = sanitize_text_field($_POST['id']);
			$user          = sanitize_text_field($_POST['user']);
			$calendar_name = sanitize_text_field($_POST['calendarName']);
			$link          = sanitize_text_field($_POST['link']);
			$cm->update_calendar($id, $user, $calendar_name, $link);
		}
		break;
	case 'delete':
		if ($_GET['id']) {
			$id = sanitize_text_field($_GET['id']);
			$cm->delete_calendar($id);
		}
		break;
}


// Get users
$users = $gd_api->call('GET', 'users');

/** Create table of calendars **/
 
//Create an instance of our package class...
$calendar_table = new Geodigs_Calendar_Table($cm, $users);
// //Fetch, prepare, sort, and filter our data...
$calendar_table->prepare_items();
// // Display our data
$calendar_table->display();
?>
<br>
<!-- Upload file form -->
<h3>Add Calendar</h3>
<form action="?page=<?=$_REQUEST['page']?>&action=<?=$action?>" method="post" enctype="multipart/form-data">
<!-- 	Used for editing a file -->
	<input type="hidden" id="gd-calendar-id" name="id" value="<?=$calendar->id?>">
	<table class="form-table">
		<tr>
			<th>Name*</th>
			<td>
				<input type="text" id="gd-calendar-name" name="calendarName" value="<?=$calendar->name?>" required>
			</td>
		</tr>
		<tr>
			<th>User*</th>
			<td>
				<select name="user" id="gd-calendar-user">
					<?php foreach ($users as $user): ?>
						<option value="<?=$user->id?>" <?php selected($calendar->userId, $user->id); ?>><?=$user->name?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Link</th>
			<td>
				<input type="text" id="gd-calendar-link" name="link" value="<?=$calendar->link?>" required>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>
<?php
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Geodigs_Calendar_Table extends WP_List_Table {
	public $cm; // Calendar manager
	public $users; // Agent's users

	function __construct($cm, $users){
		global $status, $page;
		
		$this->cm    = $cm;
		$this->users = (array) $users;
				
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

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item->id                //The value of the checkbox should be the record's id
		);
	}

	function column_name($item){
		//Build row actions
		$actions = array(
			'edit'     => sprintf('<a href="?page=%s&action=%s&id=%s" class="gd-edit-calendar" >Edit</a>', $_REQUEST['page'], 'edit', $item->id),
			'delete'   => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->id),
		);
		
		//Return the name contents
		return sprintf('%1$s%2$s',
			/*$1%s*/ $item->name,
			/*$2%s*/ $this->row_actions($actions)
		);
	}

	function column_user($item){
		$name = '';
		foreach ($this->users as $user) {
			if ($user->id == $item->userId) {
				$name = $user->name;
			}
		}
		
		//Return the user contents
		return sprintf('%1$s', $name);
	}

	function column_link($item){
		//Return the description contents
		return sprintf('%1$s', $item->link);
	}

	function get_columns(){
		$columns = array(
			'cb'   => '<input type="checkbox" />', //Render a checkbox instead of text
			'name' => 'Name',
			'user' => 'User',
			'link' => 'Link',
		);
		return $columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
		);
		return $actions;
	}

	public function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="' . $item->id . '" ' . $row_class . '>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	function prepare_items() {
		$per_page = 10;
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		// Get calendars
		$data = $this->cm->get_calendars();
		
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
<!-- END calendars.php -->