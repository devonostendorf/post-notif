<?php

/**
 * Fired during plugin activation
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Activator {

	/**
	 * Create plugin's options and custom DB tables.
	 *
	 * @since	1.0.0
	 */
	public static function activate() {

		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );	// dbDelta function lives in here
	
		// Create/upgrade custom tables
		$post_notif_db_version = '1.0';
			  
		// Get current installed plugin DB version
		$installed_post_notif_db_version = get_option( 'post_notif_db_version' );
		if ( $installed_post_notif_db_version < $post_notif_db_version ) {
				  
			// Need to create/upgrade tables
			
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE {$wpdb->prefix}post_notif_subscriber (
				id mediumint(9) NOT NULL AUTO_INCREMENT
				,email_addr varchar( 100 ) NOT NULL
				,first_name varchar( 50 ) NOT NULL
				,confirmed tinyint( 1 ) NOT NULL
				,last_modified timestamp NOT NULL
				,date_subscribed datetime NOT NULL
				,authcode varchar( 32 ) NOT NULL
				,PRIMARY KEY  ( id )
				,UNIQUE KEY  ( email_addr )			
			) $charset_collate;";
			dbDelta( $sql );		

			$sql = "CREATE TABLE {$wpdb->prefix}post_notif_sub_cat (
				id mediumint(9) NOT NULL
				,cat_id bigint(20) NOT NULL	
				,PRIMARY KEY  ( id, cat_id )
			) $charset_collate;";		
			dbDelta( $sql );

			$sql = "CREATE TABLE {$wpdb->prefix}post_notif_post (
				post_id bigint(20) UNSIGNED NOT NULL
				,notif_sent_dttm datetime NOT NULL
				,sent_by bigint(20) UNSIGNED NOT NULL
				,PRIMARY KEY  ( post_id, notif_sent_dttm )
			) $charset_collate;";		
			dbDelta( $sql );		
		}
		
		// Add options		
		update_option( 'post_notif_db_version', $post_notif_db_version );
		
		$post_notif_settings_arr = array(
			'eml_sender_name' => '[ENTER SENDER NAME HERE]'
			,'eml_sender_eml_addr' => '[ENTER EMAIL ADDRESS HERE]'
			,'sub_conf_eml_subj' => '@@blogname: Confirmation needed for subscription request'
			,'sub_conf_eml_body' => "Hi @@firstname,<br />\n<br />\nPlease click the link below to confirm your subscription to posts on @@blogname:<br />\n<br />\n@@confurl<br />\n<br />\n@@signature"
			,'post_notif_eml_subj' => 'New post on @@blogname: @@posttitle'
			,'post_notif_eml_body' => "Hi @@firstname,<br />\n<br />\nHere's the direct link to the post:<br />@@permalink<br />\n<br />\n@@signature<br /><br />\n<br />\nIf you'd like to change the categories you're subscribed to, click here:<br />\n@@prefsurl<br />\n<br />\nIf you'd like to unsubscribe from all future notification, click here:<br />\n@@unsubscribeurl"
			,'@@signature' => 'Thanks,<br />[ENTER NAME HERE]'
			,'sub_confirmed_page_title' => 'Post Notification Preferences'
			,'sub_confirmed_page_greeting' => 'You are now all set to receive post notifications!'
			,'sub_pref_selection_instrs' => "If you'd like to change the categories for which you'll be notified when new posts are published, please do so below:"
			,'curr_sub_prefs_page_title' => 'Post Notification Preferences'
			,'curr_sub_prefs_page_greeting' => "Here's what you're currently subscribed to."
			,'sub_prefs_updated_page_title' => 'Post Notification Preferences'
			,'sub_prefs_updated_page_greeting' => 'Your preferences have been updated!'
			,'unsub_link_label' => 'Unsubscribe from post notification'
			,'unsub_confirmation_page_title' => 'Post Notification - Unsubscribed'
			,'unsub_confirmation_page_greeting' => "You've been unsubscribed (sorry to see you go!)"
		);
		add_option( 'post_notif_settings', $post_notif_settings_arr );
				
		$post_notif_widget_defaults_arr = array(
			'title_default' => 'Subscribe'
			,'call_to_action_default' => 'Notify me when new posts are published:'
			,'button_label_default' => 'Sign me up!'
		);
		add_option( 'post_notif_widget_defaults', $post_notif_widget_defaults_arr );

	}
	
}
