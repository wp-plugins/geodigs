<!-- START document-store.php -->
<?php
$ds = new Geodigs_Document_store();

// Sanitize vars
$id          = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : sanitize_text_field($_GET['id']);
$file_name   = sanitize_text_field($_POST['fileName']);
$description = sanitize_text_field($_POST['description']);
$user        = sanitize_text_field($_POST['user']);

// Do our action
switch ($_GET['action']) {
	case 'upload':
		if (count($_FILES) > 0) {
			// Are we updating or adding a file?
			if ($_POST['id']) {
				$ds->update_file($id, $file_name, $description, $user, 'active');
			}
			else {
				$ds->add_file($file_name, $description, $user, 'active');
			}
		}
		break;
	case 'download':
		$ds->download_file($id);
		break;
	case 'edit':
		$file = $ds->get_file($id);
		break;
	case 'delete':
		$ds->delete_file($id);
		break;
}

if ($_POST['notify'] == true) {
	$ds->notify_user($file_name, $user);
}

// Get users
$users = $ds->get_users();

/** Create table of documents **/
 
//Create an instance of our package class...
$document_store_files = new Geodigs_Doucment_Store_Table($ds, $users);
// //Fetch, prepare, sort, and filter our data...
$document_store_files->prepare_items();
// // Display our data
$document_store_files->display();
?>
<br>
<!-- Upload file form -->
<h3>Upload Document</h3>
<form action="?page=<?=$_REQUEST['page']?>&action=upload" method="post" enctype="multipart/form-data">
<!-- 	Used for editing a file -->
	<input type="hidden" id="gd-ds-file-id" name="id" value="<?=$file->id?>">
	<table class="form-table">
		<tr>
			<th>Name*</th>
			<td>
				<input type="text" id="gd-ds-file-name" name="fileName" value="<?=$file->name?>" required>
			</td>
		</tr>
		<tr>
			<th>Description*</th>
			<td>
				<input type="text" id="gd-ds-file-description" name="description" value="<?=$file->description?>" required>
			</td>
		</tr>
			<th>User*</th>
			<td>
				<select name="user" id="gd-ds-user">
					<?php foreach ($users as $user): ?>
						<option value="<?=$user->id?>" <?php selected($file->userId, $user->id); ?>><?=$user->name?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Choose a File</th>
			<td>
				<input type="file" id="gd-ds-file" name="file" required>
			</td>
		</tr>
		<tr>
			<th>Notify User</th>
			<td>
				<input type="checkbox" id="gd-ds-notify" name="notify">
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>
<?php
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Geodigs_Doucment_Store_Table extends WP_List_Table {
	public $ds; // Document store
	public $users; // Agent's users

	function __construct($ds, $users){
		global $status, $page;
		
		$this->ds = $ds;
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
			'download' => sprintf('<a href="/%s%s">Download</a>', GD_URL_DOCUMENT_STORE, $item->id),
			'edit'     => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->id),
			'delete'   => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->id),
		);
		
		//Return the name contents
		return sprintf('%1$s%2$s',
			/*$1%s*/ $item->name,
			/*$2%s*/ $this->row_actions($actions)
		);
	}

	function column_desc($item){
		//Return the description contents
		return sprintf('%1$s', $item->description);
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

	function get_columns(){
		$columns = array(
			'cb'   => '<input type="checkbox" />', //Render a checkbox instead of text
			'name' => 'Name',
			'desc' => 'Description',
			'user' => 'User',
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
		
		// Get files from document store
		$data = $this->ds->get_files();
		
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
<!-- END document-store.php -->