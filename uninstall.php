<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
	exit();

global $wpdb;

// Delete geodigs options from DB
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'geodigs%'");