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
		$installed_post_notif_db_version = get_option( 'post_notif_db_version', 0 );
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
			,'widget_error_email_addr_blank' => 'An email address is required.'
			,'widget_error_email_addr_invalid' => 'A valid email address is required.'
			,'widget_info_message_already_subscribed' => "You're already subscribed so no need to do anything further."
			,'widget_success_message' => 'Thanks.  Please check your email to confirm your subscription.'
		);
		// Cleanly future-proof the addition of settings by
		//		iterating through defaults array and only updating 
		//		'post_notif_settings_arr' values that are new to site (thus 
		//		avoiding overwriting any that an admin has chosen to customize)
		$curr_post_notif_settings_arr = get_option( 'post_notif_settings', array() );
		foreach ( $post_notif_settings_arr as $setting_key => $setting_val ) {
			if ( !array_key_exists( $setting_key, $curr_post_notif_settings_arr) ) {
				$curr_post_notif_settings_arr[$setting_key] = $setting_val;
			}				  
		}
		// Replace add_option() call with update_option() as the latter will also 
		//		handle special case of first-time activation of plugin (adding a
		//		brand new 'post_notif_widget_defaults' option)
		update_option( 'post_notif_settings', $curr_post_notif_settings_arr );
				
		$post_notif_widget_defaults_arr = array(
			'title_default' => 'Subscribe'
			,'call_to_action_default' => 'Notify me when new posts are published:'
			,'button_label_default' => 'Sign me up!'
			,'first_name_placeholder_default' => 'First Name (optional)'
			,'email_addr_placeholder_default' => 'Email Address'
		);
		// Replace add_option() call with update_option() as these are strictly
		//		defaults - user updates to the widget settings are not stored back
		//		in here
		update_option( 'post_notif_widget_defaults', $post_notif_widget_defaults_arr );

		// TODO: Change this to a better comment once I'm convinced this is working properly
		$curr_widget_post_notif_arr = get_option( 'widget_post-notif');
		if ( $curr_widget_post_notif_arr ) {
				  
			// Widget IS defined
			
			// Find index containing title so we can (potentially) add new options
			$options_index = false;
			foreach( $curr_widget_post_notif_arr as $arr_key => $arr_item ) {
				if ( array_key_exists( 'title', $arr_item ) ) {
					$options_index = $arr_key;
					break;
				}
			}

			//	Iterate through widget settings array and only update values that 
			//		are new to site (thus avoiding overwriting any that an admin has
			//		chosen to customize)
			if ( $options_index ) {
				foreach ( $post_notif_widget_defaults_arr as $default_key => $default_val ) {
					$trimmed_key = substr( $default_key, 0, strlen( $default_key ) - 8 ); 
					if ( !array_key_exists( $trimmed_key, $curr_widget_post_notif_arr[$options_index] ) ) {
						$curr_widget_post_notif_arr[$options_index][$trimmed_key] = $default_val;
					}				  
				}
				update_option( 'widget_post-notif', $curr_widget_post_notif_arr );
			}
		}
		
	}
	
}
