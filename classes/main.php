<?php
class CM_PL_Main {

	public function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'wp_insert_comment', array( __CLASS__, 'wp_insert_comment' ), 10, 2 );
		add_filter( 'pre_comment_approved', array( __CLASS__, 'pre_comment_approved' ), 99, 2 );
	}

	public static function init() {
		// Load translations
		load_plugin_textdomain( 'comments-moderator', false, basename( CM_PL_DIR ) . '/languages' );
	}

	public static function pre_comment_approved( $approved, $commentdata ) {
		$user_id = $commentdata['user_ID'];
		$ids_moderated = (array) get_option( 'ids_moderated' );

		if ( (int) $user_id > 0 && in_array( $user_id, $ids_moderated ) ) {
			return 0;
		} else {
			return $approved;
		}
	}

	// Remove comment for blacklisted users 
	public static function wp_insert_comment( $id, $comment ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$is_blacklisted = get_user_meta( $user_id, "blacklist", true );
			if ( ( (int) $user_id > 0 && $is_blacklisted == true ) ) {
				wp_delete_comment( $id );
			}
		} elseif ( isset( $comment->comment_author_email ) && in_array( $comment->comment_author_email, get_option( "emails_banned" ) ) ) {
			wp_delete_comment( $id );
		}
	}

}