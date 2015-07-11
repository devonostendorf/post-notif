<?php

/**
 * Ensures plugin-specific options and tables are current.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.4
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 */

/**
 * Ensures plugin-specific options and tables are current.
 *
 * This class handles all updates to plugin-specific options and tables both
 *	during plugin activation and following an upgrade to a new version of the 
 *	plugin.
 *
 * @since      1.0.4
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Updater {
		  	
	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @var     string	$post_notif_db_version	Current version of this plugin.
	 */	
	private $post_notif_db_version;
	
	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @var     string	$installed_post_notif_db_version	The version of this plugin last installed.
	 */	
	private $installed_post_notif_db_version;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.4
	 * @param	string	$installed_post_notif_db_version	The version of this plugin last installed.
	 */
	public function __construct( $installed_post_notif_db_version ) {

		$this->post_notif_db_version = 2;
		$this->installed_post_notif_db_version = $installed_post_notif_db_version;
		  
	}

	/**
	 * Determine whether to apply updates to plugin's options and custom DB tables.
	 *
	 * @since	1.0.4
	 */
	public function apply_updates_if_needed() {

		if ( $this->installed_post_notif_db_version < $this->post_notif_db_version ) {
				  
			// Need to create/upgrade options and/or tables
			$this->apply_updates();
		}
			  
	}
	
	/**
	 * Update options and/or custom DB tables to current version of Post Notif
	 *	settings.
	 *
	 * @since	1.0.4
	 * @access	private
	 */
	private function apply_updates() {
			  
		// NOTE: Set options here, since if it has been determined that an update
		//		needs to be applied (because user's installed DB version is less
		//		than current release's DB version), then we need to be sure they
		//		have all current options in place too!
		
		// Add options		
		
		$post_notif_settings_arr = array(
			'eml_sender_name' => '[ENTER SENDER NAME HERE]'
			,'eml_sender_eml_addr' => '[ENTER EMAIL ADDRESS HERE]'
			,'sub_conf_eml_subj' => '@@blogname: Confirmation needed for subscription request'
			,'sub_conf_eml_body' => "Hi @@firstname,<br />\n<br />\nPlease click the link below to confirm your subscription to posts on @@blogname:<br />\n<br />\n@@confurl<br />\n<br />\n@@signature"
			,'eml_to_sub_after_conf_subj' => 'Your subscription to @@blogname has been confirmed!'
			,'eml_to_sub_after_conf_body' => "Hi @@firstname,<br />\n<br />\nThanks for subscribing to the posts on @@blogname.<br />\n@@signature<br /><br />\n<br />\nIf you'd like to change the categories you're subscribed to, click here:<br />\n@@prefsurl<br />\n<br />\nIf you'd like to unsubscribe from all future notification, click here:<br />\n@@unsubscribeurl"
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
			,'admin_menu_position' => '3.389'
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

		// If widget is already defined, add any new settings found in default
		//		array since last activation of plugin
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
		
		// DB version for currently installed version of plugin (if any [could be
		//		initial install]) needs updating - apply all DB updates sequentially
			
		// NOTE: In order for this all to work going forward need to:
		//		1) Increment DB version if there are options changes AND/OR table 
		//			structure changes
		//		2) When there are table changes, add them to new 
		//			"post_notif_db_update_version_" function
		//		3) When there are ONLY options changes, add them above AND create
		//			new, empty "post_notif_db_update_version_" function so that
		//			logic below works in all cases	

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );	// dbDelta function lives in here
		
		$version_iterator = $this->installed_post_notif_db_version + 1;
		while ( $version_iterator <= $this->post_notif_db_version ) {
			$update_method = "post_notif_db_update_version_{$version_iterator}";
			if ( method_exists( $this, $update_method ) ) {
				$this->$update_method();
			}					  
			++$version_iterator;
		}
			
		// Database changes were made, update post_notif_db_version accordingly
		update_option( 'post_notif_db_version', $this->post_notif_db_version );			
    	
	}	
	
	/**
	 * Apply changes to get database schema to version 1.
	 *
	 * @since	1.0.4
	 * @access	private
	 */
	private function post_notif_db_update_version_1() {
			
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE {$wpdb->prefix}post_notif_subscriber (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			email_addr varchar(100) NOT NULL,
			first_name varchar(50) NOT NULL,
			confirmed tinyint(1) NOT NULL,
			last_modified timestamp NOT NULL,
			date_subscribed datetime NOT NULL,
			authcode varchar(32) NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY (email_addr)			
		) $charset_collate;";
		dbDelta( $sql );		

		$sql = "CREATE TABLE {$wpdb->prefix}post_notif_sub_cat (
			id mediumint(9) NOT NULL,
			cat_id bigint(20) NOT NULL,
			PRIMARY KEY  (id,cat_id)
		) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE {$wpdb->prefix}post_notif_post (
			post_id bigint(20) UNSIGNED NOT NULL,
			notif_sent_dttm datetime NOT NULL,
			sent_by bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY  (post_id,notif_sent_dttm)
		) $charset_collate;";
		dbDelta( $sql );	
			  
	}

	/**
	 * Apply changes to get database schema to version 2.
	 *
	 * @since	1.0.4
	 * @access	private
	 */
	private function post_notif_db_update_version_2() {
			
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE {$wpdb->prefix}post_notif_subscriber_stage (
			id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
			email_addr varchar(100) NOT NULL,
			first_name varchar(50) NOT NULL,
			import_status char(1) NOT NULL,
			status_message varchar(250) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );		

		$sql = "CREATE TABLE {$wpdb->prefix}post_notif_sub_cat_stage (
			id mediumint(9) NOT NULL,
			cat_id bigint(20) NOT NULL,
			PRIMARY KEY  (id,cat_id)
		) $charset_collate;";
		dbDelta( $sql );
		
	}
		  		  		  
}
