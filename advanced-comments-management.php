<?php
/*
 Plugin Name: Advanced Comments Management
 Version: 0.2
 Plugin URI: https://github.com/t-marguenot/comments-moderator
 Description: This plugin allows you to moderate and blacklist users directly in the edit page of comments.
 Author: Thomas MARGUENOT
 Domain Path: languages
 Network: false
 Text Domain: advanced-comments-management
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

// Plugin constants
define('CM_PL_VERSION', '0.1');

// Plugin URL and PATH
define('CM_PL_URL', plugin_dir_url ( __FILE__ ));
define('CM_PL_DIR', plugin_dir_path( __FILE__ ));

// Function for easy load files
function _cm_PL_load_files($dir, $files, $prefix = '') {
	foreach ($files as $file) {
		if ( is_file($dir . $prefix . $file . ".php") ) {
			require_once($dir . $prefix . $file . ".php");
		}
	}
}

// Plugin client classes
_cm_pl_load_files(CM_PL_DIR . 'classes/', array('main'));

// Plugin admin classes
if (is_admin()) {
	_cm_pl_load_files(CM_PL_DIR . 'classes/admin/', array('main'));
}

add_action('plugins_loaded', 'init_bea_pl_plugin');
function init_bea_pl_plugin() {
	// Admin
	if (is_admin()) {
		new CM_PL_Admin_Main();
	}
	
	new CM_PL_Main();
}