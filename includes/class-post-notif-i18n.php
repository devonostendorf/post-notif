<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_i18n {

	/**
	 * The domain specified for this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string	$domain	The domain identifier for this plugin.
	 */
	private $domain;
	
	/**
	 * Does a translation exist for the current language?
	 *
	 * @since	1.0.6
	 * @access	private
	 * @var		string	$loaded	Whether a translation exists for the current language.
	 */
	private static $loaded = false;
	
	/**
	 * Get boolean indicating whether a translation exists for the current language.
	 *
	 * @since	1.0.6
	 * @return	boolean	Does translation exist for the current language?
	 */
	public function is_loaded() {
		
		return self::$loaded;
		
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since	1.0.0
	 */
	public function load_plugin_textdomain() {

		self::$loaded = 
		load_plugin_textdomain(
			$this->domain
			,false
			,dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since	1.0.0
	 * @param	string	$domain	The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
			  
		$this->domain = $domain;
		
	}

}
