<?php

class Geodigs_Document_Store {
	public $db;
	public $files_table;
	
	
	public function __construct() {
		// This makes calling the WP database object easier
		global $wpdb;
		$this->db = $wpdb;
		
		// Create the directory
		if (!file_exists(GD_DIR_DOCUMENT_STORE)) {
			mkdir(GD_DIR_DOCUMENT_STORE, 0777, true);
		}
		
/* 		START Create tables */
		
		$this->files_table = 'gd_ds_files';
		$create_files_table_query = "CREATE TABLE IF NOT EXISTS `{$this->files_table}` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) default NULL,
  `name` varchar(100) NOT NULL default '',
  `keywords` varchar(255) default NULL,
  `extension` varchar(10) default NULL,
  `description` varchar(255) default NULL,
  `fileName` varchar(100) NOT NULL default '',
  `sort` bigint(20) NOT NULL default '99999',
  `size` bigint(20) default NULL,
  `modDate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `owner` varchar(100) default NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `fileName` (`fileName`),
  FULLTEXT KEY `keywords` (`keywords`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		$this->db->query($create_files_table_query);
		
/* 		END Create tables */
	}
	
	function get_users() {
		global $gd_api;
		
		return $gd_api->call('GET', 'users');
	}
	
	function notify_user($name, $user_id) {
		global $gd_api;
		
		$user = $gd_api->call('GET', 'users/' . $user_id);
		
		$to			= $user->email;
		$subject	= get_bloginfo('name') . ': A File Was Added to Your Document Store!';
		$headers	= 'From: ' . get_bloginfo('admin_email') . "\r\n" . 'Reply-To: ' . get_bloginfo('admin_email') . "\r\n" . 'X-Mailer: PHP/' . phpversion();
		// Get email contents
		ob_start();
		include_once 'emails/new-file-added-to-document-store.php';
		$message = ob_get_contents();
		ob_end_clean();
		// Send it
		mail($to, $subject, $message, $headers);
	}
	
	function download_file($id) {
		if (isset($id)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $this->get_file_name($id, 'WITH_EXTENSION') . '"');
			header('Content-Length: ' . $this->get_file_size($id));

			ob_clean();
			flush();
			readfile($this->get_file_path($id));
			exit;
		}
		else {
			echo 'No document ID specified.';
			exit;
		}
	}
	
	function get_files($user_id = null) {
		$query = "SELECT * FROM {$this->files_table}";
		if (isset($user_id)) {
			$query .= " WHERE userId = {$user_id}";
		}
		
		$files = $this->db->get_results($query);
		if ($files) {
			return $files;
		}
		else {
			$this->db->print_error();
			return false;
		}
	}
	
	function get_file($id) {
		$query = $this->db->prepare("SELECT * FROM {$this->files_table} WHERE id=%s", $id);
		return $this->db->get_row($query);
	}
	
	function add_file($name, $description, $user_id, $active = 'active') {
		// If we can successfully upload the file add it to the DB
		$file_path_info = pathinfo($_FILES['file']['name']);
		$this->db->insert(
			$this->files_table,
			array(
				'userId'      => $user_id,
				'name'        => stripslashes($name),
				'extension'   => $file_path_info['extension'],
				'description' => stripslashes($description),
				'fileName'    => $file_path_info['filename'],
				'size'        => $_FILES['file']['size'],
				'owner'       => $_SESSION['gd_agent']->name->full,
				'active'      => ($active == 'active'),
			)
		);
			
		if ($this->upload_file($this->db->insert_id)) {
			echo 'Document uploaded successfully';
		}
	}
	
	function update_file($id, $name, $description, $user_id, $active = 'active') {
		// Get file path
		$file = $this->get_file_path($id);
		
		// Delete the file
		if (unlink($file)) {
			$file_path_info = pathinfo($_FILES['file']['name']);
			$this->db->update(
				$this->files_table,
				array(
					'userId'      => $user_id,
					'name'        => stripslashes($name),
					'extension'   => $file_path_info['extension'],
					'description' => stripslashes($description),
					'fileName'    => $file_path_info['filename'],
					'size'        => $_FILES['file']['size'],
					'owner'       => $_SESSION['gd_agent']->name->full,
					'active'      => ($active == 'active'),
				),
				array(
					'id' => $id,
				)
			);
			
			if ($this->upload_file($id)) {
				echo 'Document updated successfully';
			}
		}
	}
	
	public function delete_file($id) {
		// Get file path
		$file = $this->get_file_path($id);
		
		// Delete the file
		if (unlink($file)) {
			// Delete DB entry
			$this->db->delete($this->files_table, array('id' => $id));
			echo 'File deleted successfully';
		}
		else {
			echo 'Could not delete file';
		}
	}
	
	private function get_file_path($id) {
		return GD_DIR_DOCUMENT_STORE . $id . '.' . $this->get_file_extension($id);
	}
	
	private function get_file_name($id, $flag) {
		switch ($flag) {
			// returns name.type
			case 'WITH_EXTENSION':
				$query = $this->db->prepare("SELECT CONCAT(fileName, '.', extension) AS fileName FROM `{$this->files_table}` WHERE id = {$id}");
				return $this->db->get_var($query);
				break;
			// returns name
			case 'NO_EXTENSION':
				$query = $this->db->prepare("SELECT fileName FROM `{$this->files_table}` WHERE id = {$id}");
				return $this->db->get_var($query);
			default:
		}
	}
	
	private function get_file_extension($id) {
		$query = $this->db->prepare("SELECT extension FROM `{$this->files_table}` WHERE id = %s", $id);
		return $this->db->get_var($query);
	}
	
	private function get_file_size($id) {
		$query = $this->db->prepare("SELECT size FROM `{$this->files_table}` WHERE id = %s", $id);
		return $this->db->get_var($query);
	}
	
	private function upload_file($name) {
		try {
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($_FILES['file']['error']) ||
				is_array($_FILES['file']['error'])
			) {
				throw new RuntimeException('Invalid parameters.');
			}

			// Check $_FILES['file']['error'] value.
			switch ($_FILES['file']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}

			// You should also check filesize here. 
			if ($_FILES['file']['size'] > 1000000) {
				throw new RuntimeException('Exceeded filesize limit.');
			}
			
			// Get the id of the last file uploaded and add 1 to it to get the new file name
			$file_path_info = pathinfo($_FILES['file']['name']);
			$file_name = $name . '.' . $file_path_info['extension'];
			
			if (!move_uploaded_file(
				$_FILES['file']['tmp_name'],
				GD_DIR_DOCUMENT_STORE . $file_name
			)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}

			return true;

		} catch (RuntimeException $e) {

			echo $e->getMessage();
			return false;
		}
	}
}