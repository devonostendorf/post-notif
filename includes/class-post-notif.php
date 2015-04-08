<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since	1.0.0
	 * @access	protected
	 * @var		Post_Notif_Loader	$loader	Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since	1.0.0
	 * @access	protected
	 * @var		string	$plugin_name	The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since	1.0.0
	 * @access	protected
	 * @var		string	$version	The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since	1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'post-notif';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Post_Notif_Loader. Orchestrates the hooks of the plugin.
	 * - Post_Notif_i18n. Defines internationalization functionality.
	 * - Post_Notif_Admin. Defines all hooks for the admin area.
	 * - Post_Notif_Public. Defines all hooks for the public side of the site.
	 * - Post_Notif_Widget. Defines all widget functionality.
	 * - Post_Notif_List_Table. Defines enhanced WP List Table functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-notif-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-notif-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-post-notif-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-post-notif-public.php';

		/**
		 * The class responsible for creating widget.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-notif-widget.php';

		/**
		 * The class responsible for cloning the core List Table class and making it a little more developer-friendly.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-notif-list-table.php';
	
		$this->loader = new Post_Notif_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Post_Notif_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function set_locale() {

		$plugin_i18n = new Post_Notif_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Post_Notif_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	
		// Send notif (from Edit Post page) functionality
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_post_notif_meta_box' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'send_post_notif_enqueue' );
		$this->loader->add_action( 'wp_ajax_post_notif_send', $plugin_admin, 'send_post_notif_ajax_handler' );
		
		// Add submenu to Settings menu 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_post_notif_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_post_notif_settings' );

		// Add Post Notif top level menu to the admin menu sidebar
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_post_notif_admin_menu' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function define_public_hooks() {

		$plugin_public = new Post_Notif_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Handle URLs (confirm subscription, manage prefs, update prefs, unsubscribe)
		$this->loader->add_filter( 'query_vars', $plugin_public, 'add_query_vars' );			
		$this->loader->add_action( 'init', $plugin_public, 'url_controller' );
		$this->loader->add_action( 'admin_post_sub-prefs-form', $plugin_public, 'process_preferences_update' );
		$this->loader->add_action( 'admin_post_nopriv_sub-prefs-form', $plugin_public, 'process_preferences_update' );		

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since	1.0.0
	 */
	public function run() {
			  
		$this->loader->run();
		
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since	1.0.0
	 * @return	string	The name of the plugin.
	 */
	public function get_plugin_name() {
			  
		return $this->plugin_name;
		
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since	1.0.0
	 * @return	Post_Notif_Loader	Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
			  
		return $this->loader;
		
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since	1.0.0
	 * @return	string	The version number of the plugin.
	 */
	public function get_version() {
			  
		return $this->version;
		
	}

}
