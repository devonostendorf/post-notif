<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
 
// Define set of plugin-specific options
$option_arr = array(
	'post_notif_settings'
	,'post_notif_widget_defaults'
	,'post_notif_db_version'
	,'widget_post-notif'
	,'post_notif_count_to_send'
	,'post_notif_count_sent'
);

// Define set of plugin-specific custom tables
global $wpdb;
$custom_table_arr = array(
	'post_notif_subscriber'
	,'post_notif_sub_cat'
	,'post_notif_post'
	,'post_notif_subscriber_stage'
	,'post_notif_sub_cat_stage'
);
 
if ( !is_multisite() ) {
		
	// NOT multisite
					  
	// Delete options
	foreach ( $option_arr as $option_name)
	{
		delete_option( $option_name );
	}
	
	// Delete language detector
	delete_option( 'post_notif_language_detector_' . get_locale() );
	
	// Delete custom tables
	foreach ( $custom_table_arr as $custom_table_name)
	{
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}" . $custom_table_name );			  
	}
} 
else {
		  
	// IS multisite
	
   $site_id_arr = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
   $root_blog_id = get_current_blog_id();

   // Iterate through sites in network
   foreach ( $site_id_arr as $site_id ) 
   {
   	switch_to_blog( $site_id );
	
   	// Delete options
      foreach ( $option_arr as $option_name)
      {
      	delete_option( $option_name );
      }
	
     // Delete language detector
      delete_option( 'post_notif_language_detector_' . get_locale() );
	
      // Delete custom tables
      foreach ( $custom_table_arr as $custom_table_name)
      {
      	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}" . $custom_table_name );			  
      }
   }
   switch_to_blog( $root_blog_id );
}
