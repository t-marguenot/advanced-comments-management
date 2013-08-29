<?php
/*
 Plugin Name: Comments moderator
 Version: 0.2
 Plugin URI: https://github.com/herewithme/bea-plugin-boilerplate
 Description: Comments management > blacklisting access and moderation
 Author: Beapi
 Author URI: http://www.beapi.fr
 Domain Path: languages
 Network: false
 Text Domain: comments-moderator

 Copyright 2013 Amaury Balmer (amaury@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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