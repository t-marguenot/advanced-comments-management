<?php
class CM_PL_Admin_Main {

	public function __construct() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

		if ( get_option( "ids_moderated" ) == false ) {
			$ids_moderated = array( );
			update_option( 'ids_moderated', $ids_moderated );
		}
		
		if ( get_option( "emails_banned" ) == false ) {
			$emails_banned = array( );
			update_option( 'emails_banned', $emails_banned );
		}
	}

	public static function admin_init() {
		add_filter( 'comment_row_actions', array( __CLASS__, 'comment_row_actions' ), 10, 2 );

		$user_id = ( isset( $_GET['userid'] ) && (int) $_GET['userid'] > 0 ) ? $_GET['userid'] : false;
		$user_email = ( isset( $_GET['email'] ) && !empty( $_GET['email'] ) ) ? $_GET['email'] : false;
		$emails_banned = (array) get_option( 'emails_banned' );
		$ids_moderated = (array) get_option( 'ids_moderated' );

		if ( !in_array( $user_email, $emails_banned ) ) {
			if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "blacklist" && $user_id != false ) {
				update_user_meta( $user_id, "blacklist", true );
			}

			if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "autorize" && $user_id != false ) {
				update_user_meta( $user_id, "blacklist", false );
			}
		}

		if ( isset( $_GET['cm_action'] ) && $user_email != false ) {

			if ( $_GET['cm_action'] == "blacklist" ) {
				$emails_banned[] = $user_email;
				update_option( 'emails_banned', $emails_banned );
			} elseif ( $_GET['cm_action'] == "autorize" ) {
				unset( $emails_banned[array_search( $user_email, $emails_banned )] );
				update_option( 'emails_banned', $emails_banned );
			}
		}

		if ( !in_array( $user_id, $ids_moderated ) ) {
			if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "moderate" && $user_id != false ) {
				$ids_moderated[] = $user_id;
				update_option( 'ids_moderated', $ids_moderated );
			}
		}

		if ( isset( $_GET['cm_action'] ) && $_GET['cm_action'] == "unmoderate" && $user_id != false ) {
			unset( $ids_moderated[array_search( $user_id, $ids_moderated )] );
			update_option( 'ids_moderated', $ids_moderated );
		}
	}

	public static function comment_row_actions( $actions, $comment ) {
		$user_email = $comment->comment_author_email;
		$user_id = (int) $comment->user_id;
		
		if ( empty( $comment ) ) {
			return false;
		}
		
		if ( $user_id > 0 ){
			if ( get_user_meta( $user_id, "blacklist", true ) != true ) {
				$probation = "<a href='".add_query_arg( array( 'cm_action' => 'blacklist', 'userid' => $user_id ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Blacklist', 'comments-moderator' )."'>".__( 'Blacklist', 'comments-moderator' )."</a>";
			}else{
				$probation = "<a href='".add_query_arg( array( 'cm_action' => 'autorize', 'userid' => $user_id ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Allow to comment', 'comments-moderator' )."'>".__( 'Authorize', 'comments-moderator' )."</a>";
			}
			
			if ( in_array( $user_id, get_option( "ids_moderated" ) ) ) {
				$probation2 = "<a href='".add_query_arg( array( 'cm_action' => 'unmoderate', 'userid' => $user_id ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Remove from the list of members whose comments are moderated', 'comments-moderator' )."'>".__( 'Remove', 'comments-moderator' )."</a>";
			}else{
				$probation2 = "<a href='".add_query_arg( array( 'cm_action' => 'moderate', 'userid' => $user_id ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Add to the list of members whose comments are moderated', 'comments-moderator' )."'>".__( 'Add', 'comments-moderator' )."</a>";
			}
			
		}elseif ( !empty( $user_email ) ) {
			if ( in_array( $user_email, get_option( "emails_banned" ) ) ) {
				$probation = "<a href='".add_query_arg( array( 'cm_action' => 'autorize', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Allow to comment (e-mail)', 'comments-moderator' )."'>".__( 'Authorize', 'comments-moderator' )."</a>";
			}else{
				$probation = "<a href='".add_query_arg( array( 'cm_action' => 'blacklist', 'email' => $user_email ), admin_url( 'edit-comments.php' ) )."' title='".__( 'Blacklists (email)', 'comments-moderator' )."'>".__( 'Blacklist', 'comments-moderator' )."</a>";
			}
		}
		
		$actions['edit'] .= ( isset( $probation ) ) ? '</span><span class="hide-if-no-js"> | ' . $probation : "";
		$actions['edit'] .= ( isset( $probation2 ) ) ? '</span><span class="hide-if-no-js"> | ' . $probation2  : "";
		
		return $actions;
	}
}