<?php

class Geodigs_Calendars {
	public $table;
	
	public function __construct() {
		// This makes calling the WP database object easier
		global $wpdb;
		$this->db = $wpdb;
		
/* 		START Create tables */
		
		$this->table = 'gd_calendars';
		$create_table_query = "CREATE TABLE IF NOT EXISTS {$this->table} (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) default NULL,
  `name` varchar(100) NOT NULL default '',
  `link` TEXT default NULL,
  `modDate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		$this->db->query($create_table_query);
		
/* 		END Create tables */
	}
	
	public function get_calendars() {
		$query = "SELECT id, userId, name, link, modDate FROM {$this->table}";
		return $this->db->get_results($query);
	}
	
	public function get_calendar($id) {
		$query    = $this->db->prepare("SELECT id, userId, name, link, modDate FROM {$this->table} WHERE id = %s", $id);
		$calendar = $this->db->get_row($query);
		
		return $calendar;
	}
	
	public function get_calendars_for_user($userId) {
		$query     = $this->db->prepare("SELECT id, userId, name, link, modDate FROM {$this->table} WHERE userId = %s", $userId);
		$calendars = $this->db->get_results($query);
		
		return $calendars;
	}
	
	public function add_calendar($userId, $name, $link) {
		$this->db->insert(
			$this->table,
			array(
				'userId' => $userId,
				'name'   => $name,
				'link'   => $link,
			)
		);
	}
	
	public function update_calendar($id, $userId, $name, $link) {
		$this->db->update(
			$this->table,
			array(
				'userId' => $userId,
				'name'   => $name,
				'link'   => $link,
			),
			array(
				'id' => $id
			)
		);
	}
	
	public function delete_calendar($id) {
		$this->db->delete($this->table, array('id' => $id));
	}
}