<?php
class CM_PL_Admin_Main {

	public function __construct() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_filter( 'comment_row_actions', array( __CLASS__, 'comment_row_actions' ), 10, 2 );
	}
	
	// moderates and blacklist email addresses based on the actions
	public static function admin_init() {

		$user_email = ( isset( $_GET['email'] ) && !empty( $_GET['email'] ) ) ? $_GET['email'] : false;
		$users_moderated = get_option( 'moderation_keys' );
		$users_blacklisted = get_option( 'blacklist_keys' );

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "blacklist" && $user_email != false ) {
			if ( stripos( $users_blacklisted, $user_email ) === false ) {
				update_option( 'blacklist_keys', $users_blacklisted."\r\n".$user_email );
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "autorize" && $user_email != false ) {
			if( stripos( $users_blacklisted, $user_email ) !== false ){
				update_option( 'blacklist_keys', str_replace( $user_email, "", $users_blacklisted ) );
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "moderate" && $user_email != false ) {
			if ( stripos( $users_moderated, $user_email ) === false ) {
				update_option( 'moderation_keys', $users_moderated."\r\n".$user_email );
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "unmoderate" && $user_email != false ) {
			if( stripos( $users_moderated, $user_email ) !== false ){
				update_option( 'moderation_keys', str_replace( $user_email, "", $users_moderated ) );
			}
		}
	}
	
	// adds buttons to the edit page for moderate comments and blacklist
	public static function comment_row_actions( $actions, $comment ) {
		$user_email = $comment->comment_author_email;
		$users_moderated = get_option( 'moderation_keys' );
		$users_blacklisted = get_option( 'blacklist_keys' );
		
		if ( empty( $comment ) || empty( $user_email ) ) {
			return false;
		}
		
		if ( strstr( $users_blacklisted, $user_email) ) {
			$probation = "<a href='".add_query_arg( array( 'cm_action' => 'autorize', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Allow to comment', 'advanced-comments-management' )."'>".__( 'Authorize', 'advanced-comments-management' )."</a>";
		}else{
			$probation = "<a href='".add_query_arg( array( 'cm_action' => 'blacklist', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Blacklist', 'advanced-comments-management' )."'>".__( 'Blacklist', 'advanced-comments-management' )."</a>";
		}
		
		if( strstr($users_moderated, $user_email) ) {
			$probation2 = "<a href='".add_query_arg( array( 'cm_action' => 'unmoderate', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Remove from the list of members whose comments are moderated', 'advanced-comments-management' )."'>".__( 'Do not restrain', 'advanced-comments-management' )."</a>";
		}else{
			$probation2 = "<a href='".add_query_arg( array( 'cm_action' => 'moderate', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Add to the list of members whose comments are moderated', 'advanced-comments-management' )."'>".__( 'Restrain', 'advanced-comments-management' )."</a>";
		}
		
		$actions['edit'] .= ( isset( $probation ) ) ? '</span><span class="hide-if-no-js"> | ' . $probation : "";
		$actions['edit'] .= ( isset( $probation2 ) ) ? '</span><span class="hide-if-no-js"> | ' . $probation2  : "";
		
		return $actions;
	}
}