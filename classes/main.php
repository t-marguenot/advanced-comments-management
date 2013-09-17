<?php
class CM_PL_Main {

	public function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}

	public static function init() {
		// Load translations
		load_plugin_textdomain( 'comments-moderator', false, basename( CM_PL_DIR ) . '/languages' );
	}
}