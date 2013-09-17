<?php
class CM_PL_Admin_Main {

	public function __construct() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
	}

	public static function admin_init() {
		add_filter( 'comment_row_actions', array( __CLASS__, 'comment_row_actions' ), 10, 2 );

		$user_email = ( isset( $_GET['email'] ) && !empty( $_GET['email'] ) ) ? $_GET['email'] : false;
		$users_moderated = get_option( 'moderation_keys' );
		$users_blacklisted = get_option( 'blacklist_keys' );

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "blacklist" && $user_email != false ) {
			if ( !strstr($users_blacklisted, $user_email) ) {
				if( empty( $users_blacklisted ) ){
					update_option( 'blacklist_keys', $user_email );
				}else{
					update_option( 'blacklist_keys', $users_blacklisted."\r\n".$user_email );
				}
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "autorize" && $user_email != false ) {
			$pos = strpos("\r\n".$user_email, $users_blacklisted );
			if( $pos === true ){
				update_option( 'blacklist_keys', str_replace("\r\n".$user_email, "", $users_blacklisted )  );
			}else{
				update_option( 'blacklist_keys', str_replace( $user_email, "", $users_blacklisted ) );
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "moderate" && $user_email != false ) {
			if ( !strstr($users_moderated, $user_email) ) {
				
				if( empty( $users_moderated ) ){
					update_option( 'moderation_keys', $user_email );
				}else{
					update_option( 'moderation_keys', $users_moderated."\r\n".$user_email );
				}
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "unmoderate" && $user_id != false ) {
			$pos = strpos("\r\n".$user_email, $users_moderated );
			if( $pos === true ){
				update_option( 'blacklist_keys', str_replace("\r\n".$user_email, "", $users_moderated )  );
			}else{
				update_option( 'blacklist_keys', str_replace( $user_email, "", $users_moderated ) );
			}
		}
	}

	public static function comment_row_actions( $actions, $comment ) {
		$user_email = $comment->comment_author_email;
		$users_moderated = get_option( 'moderation_keys' );
		$users_blacklisted = get_option( 'blacklist_keys' );
		
		if ( empty( $comment ) || empty( $user_email ) ) {
			return false;
		}
		
		if ( strstr( $users_blacklisted, $user_email) ) {
			$probation = "<a href='".add_query_arg( array( 'cm_action' => 'autorize', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Allow to comment', 'comments-moderator' )."'>".__( 'Authorize', 'comments-moderator' )."</a>";
		}else{
			$probation = "<a href='".add_query_arg( array( 'cm_action' => 'blacklist', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Blacklist', 'comments-moderator' )."'>".__( 'Blacklist', 'comments-moderator' )."</a>";
		}
		
		if( strstr($users_moderated, $user_email) ) {
			$probation2 = "<a href='".add_query_arg( array( 'cm_action' => 'unmoderate', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Remove from the list of members whose comments are moderated', 'comments-moderator' )."'>".__( 'Do not restrain', 'comments-moderator' )."</a>";
		}else{
			$probation2 = "<a href='".add_query_arg( array( 'cm_action' => 'moderate', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Add to the list of members whose comments are moderated', 'comments-moderator' )."'>".__( 'Restrain', 'comments-moderator' )."</a>";
		}
		
		$actions['edit'] .= ( isset( $probation ) ) ? '</span><span class="hide-if-no-js"> | ' . $probation : "";
		$actions['edit'] .= ( isset( $probation2 ) ) ? '</span><span class="hide-if-no-js"> | ' . $probation2  : "";
		
		return $actions;
	}
}