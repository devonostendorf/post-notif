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
		  
		// Get current installed plugin DB version
		$installed_post_notif_db_version = intval( get_option( 'post_notif_db_version', 0 ) );
		
		// Check for updates and apply, if needed
		$post_notif_updater = new Post_Notif_Updater( $installed_post_notif_db_version );
		$post_notif_updater->apply_updates_if_needed();
				
	}
	
}
