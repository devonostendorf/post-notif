<?php

/**
 * Fired during plugin deactivation
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Deactivator {

	/**
	 * Deactivate plugin.
	 *
	 * @since	1.0.0
	 */	 
	public static function deactivate() {
	
		global $wpdb;
	
 		// Unschedule WordPress Cron events for future (scheduled and batch-paused) post notifications
  		$post_id_arr = $wpdb->get_col(
   			"
   				SELECT post_id
   				FROM " . $wpdb->prefix.'post_notif_post'
   				. " WHERE send_status IN ('B','S') 
   				ORDER BY post_id
   			"
   		);
		
		foreach ( $post_id_arr as $post_id ) {
			wp_clear_scheduled_hook( 'post_notif_send_scheduled_post_notif', array( $post_id ) );
		}
		
		// Cancel batch-paused and scheduled notifs
	  	$num_rows_updated = $wpdb->query(
    		"
    			UPDATE " . $wpdb->prefix.'post_notif_post'
    			. " SET send_status = 'X'
    			, notif_end_dttm = '" . gmdate( "Y-m-d H:i:s" ) . "' 
    			WHERE send_status IN ('B','S') 
    		"
 		);
 		
 		// Delete shortcode attribute sets from options table
 		$shortcode_atts_set_names_arr = get_option( 'post_notif_shortcode_atts_set_names', array() );
 		foreach ( $shortcode_atts_set_names_arr as $shortcode_atts_set ) {
 			delete_option( $shortcode_atts_set );	
 		}
 		delete_option( 'post_notif_shortcode_atts_set_names' );

	}

}
