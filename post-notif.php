<?php

/**
 * Post Notif
 *
 * Notify subscribers when you publish new posts.
 *
 * Inspired by the (no longer maintained) Post Notification plugin
 *	(https://wordpress.org/plugins/post-notification)
 *
 *	This plugin was built using the exquisite WordPress Plugin Boilerplate 
 *	(https://github.com/DevinVinson/WordPress-Plugin-Boilerplate) written 
 * by Tom McFarlin (http://tommcfarlin.com), Devin Vinson, and friends.
 *	The widget functionality was built based on the equally tremendous WordPress
 *	Widget Boilerplate, also written by Tom McFarlin.
 *
 * @link				https://devonostendorf.com/projects/#post-notif
 * @since				1.0.0
 * @package				Post_Notif
 *
 * @wordpress-plugin
 * Plugin Name:			Post Notif
 * Plugin URI:			https://devonostendorf.com/projects/#post-notif
 * Description:			Notify subscribers when you publish new posts.
 * Version:				1.3.0
 * Author:				Devon Ostendorf <devon@devonostendorf.com>
 * Author URI:			https://devonostendorf.com/
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:			post-notif
 * Domain Path:			/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-post-notif-activator.php
 */
function activate_post_notif() {
		  
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-notif-activator.php';
	Post_Notif_Activator::activate();
	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-post-notif-deactivator.php
 */
function deactivate_post_notif() {
		  
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-notif-deactivator.php';
	Post_Notif_Deactivator::deactivate();
	
}

register_activation_hook( __FILE__, 'activate_post_notif' );
register_deactivation_hook( __FILE__, 'deactivate_post_notif' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-post-notif.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_post_notif() {

	$plugin = new Post_Notif();
	$plugin->run();

}
run_post_notif();
