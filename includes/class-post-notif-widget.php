<?php
/**
 * Post Notif Widget
 *
 * This plugin was built using the exceptional WordPress Widget Boilerplate 
 *	(https://github.com/tommcfarlin/WordPress-Widget-Boilerplate) written by Tom 
 *	McFarlin (http://tommcfarlin.com).
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 */
 
 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

/**
 * The widget functionality of the plugin.
 *
 * Defines both the admin and public-facing functionality of the Post Notif
 * widget.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/admin
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Widget extends WP_Widget {

	/**
    * Unique identifier for the widget.
    *
	 * @since	1.0.0
	 * @access	protected
    * @var		string	Unique identifier for the widget.
    */
   protected $widget_slug = 'post-notif';

	/**
	 * Initialize the class, specify the classname and description, instantiate
	 *	the widget, load localization files, and include necessary stylesheets
	 *	and JavaScript.
	 *
	 * @since	1.0.0
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		parent::__construct(
			$this->get_widget_slug()
			,'Post Notif'
			,array(
				'classname'  => 'class-'.$this->get_widget_slug()
				,'description' => __( 'Allow users to subscribe to post notifications.', $this->get_widget_slug() )
			)
		);

		// Register site scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		
		// AJAX actions
		add_action( 'wp_enqueue_scripts', array( $this, 'post_notif_widget_enqueue' ) );
		add_action( 'wp_ajax_post_notif_widget', array( $this, 'post_notif_widget_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_post_notif_widget', array( $this, 'post_notif_widget_ajax_handler' ) );

	}

	/**
	 * Return the widget slug.
	 *
	 * @since	1.0.0
	 * @return	string	The widget's slug.
	 */
   public function get_widget_slug() {
   		  
   	return $this->widget_slug;
        
   }

	/**
	 * Outputs the content of the widget.
	 *
	 * @since	1.0.0
	 *	@param	array	$args	The array of form elements
	 * @param	array	$instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {
		
		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset ( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
		
		if ( isset ( $cache[ $args['widget_id'] ] ) ) {
				  
			return print $cache[ $args['widget_id'] ];
		}
		
		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		ob_start();		
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );
		$widget_string .= ob_get_clean();
		$widget_string .= $after_widget;
		$cache[ $args['widget_id'] ] = $widget_string;
		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );
		print $widget_string;

	}
	
	/**
	 * Flush widget's cache.
	 *
	 * @since	1.0.0
	 */
	public function flush_widget_cache() {
			  
    	wp_cache_delete( $this->get_widget_slug(), 'widget' );
    	
	}
	
	/**
	 * Processes the widget's values to be saved.
	 *
	 * @since	1.0.0
	 * @param	array	$new_instance	The new instance of values to be generated via the update.
	 * @param	array	$old_instance	The previous instance of values before the update.
	 *	@return	array	The values, entered into widget fields by user, to be saved. 
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// Update widget's old values with the new, incoming values
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['call_to_action'] = strip_tags( $new_instance['call_to_action'] );
		$instance['button_label'] = strip_tags( $new_instance['button_label'] );
		$this->flush_widget_cache();
		
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions[$this->get_widget_slug()] ) ) {
			delete_option( $this->get_widget_slug() );
		}
		
		return $instance;

	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @since	1.0.0
	 * @param	array	$instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance );
		
		// Get widget defaults from options
		$post_notif_widget_defaults_arr = get_option( 'post_notif_widget_defaults' );
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : $post_notif_widget_defaults_arr['title_default'];
		$call_to_action = isset( $instance['call_to_action'] ) ? esc_attr( $instance['call_to_action'] ) : $post_notif_widget_defaults_arr['call_to_action_default'];
		$button_label = isset( $instance['button_label'] ) ? esc_attr( $instance['button_label'] ) : $post_notif_widget_defaults_arr['button_label_default'];

		// Display the admin form
		include( plugin_dir_path(__FILE__) . 'views/admin.php' );

	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 *
	 * @since	1.0.0
	 */
	public function widget_textdomain() {

		load_plugin_textdomain( $this->get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'language/' );

	}

	/**
	 * Registers and enqueues widget-specific scripts.
	 *
	 * @since	1.0.0
	 */
	public function register_widget_scripts() {

		wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.js', __FILE__ ), array('jquery') );

	}
	
	
	// Functions related to public-facing widget functionality

	/**
	 * Enqueue AJAX script that fires when "Sign me up!" button (in widget) is pressed.
	 *
	 * @since	1.0.0
	 * @param	string	$hook	The string containing the current page name.
	 */
	public function post_notif_widget_enqueue( $hook ) {
	
		$post_notif_widget_nonce = wp_create_nonce( 'post_notif_widget' );
		wp_localize_script( 
			$this->get_widget_slug().'-script'
			,'post_notif_widget_ajax_obj'
			,array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
				,'nonce'    => $post_notif_widget_nonce
			)
		);
		
	}
	
	/**
	 * Handle AJAX event sent when "Sign me up!" button (in widget) is pressed.
	 */
	public function post_notif_widget_ajax_handler() {
		  
		// Confirm matching nonce
		check_ajax_referer( 'post_notif_widget' );
    
		// Get user's first name and email address from submitted form
		$first_name =  substr( trim( $_POST['form_data']['first_name'] ), 0, 50 );
		$email_addr =  substr( trim( $_POST['form_data']['email_addr'] ), 0, 100 );
    
		// Confirm that email addr is valid
		if ( $email_addr == '' ) {
			$error = __( 'An email address is required', 'post-notif' );
		} 
		elseif ( ! preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i' , $email_addr ) ) {
			$error = __( 'A valid email address is required', 'post-notif' );
		} 
   
		if ( empty( $error ) ) {
      
			// Generate authcode
			
			// NOTE: Thanks to the Kohana team and their Text::random() function (system/classes/Kohana/Text.php)
			//		for inspiration behind the authcode generation code.
			/*
				Per http://kohanaframework.org/license:
				
				Copyright © 2007–2015 Kohana Team. All rights reserved.

				Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

				Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
				Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
				Neither the name of the Kohana nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

				THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.			
			*/
			
			$authcode = '';
			$authcode_length = 32;
			$authcode_pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$authcode_pool = str_split( $authcode_pool, 1 );

			// Largest possible key
			$max_authcode_length = count( $authcode_pool ) - 1;

			for ( $i = 0; $i < $authcode_length; $i++ ) {
	
				// Select a random character to add to the string
				$authcode .= $authcode_pool[mt_rand( 0, $max_authcode_length )];
			}

			if ( ctype_alpha( $authcode ) ) {
		
				// String contains ONLY letters
				
				// Add a random numeric digit
				$authcode[mt_rand( 0, $authcode_length - 1 )] = chr( mt_rand( 48, 57 ) );
			}
			elseif ( ctype_digit( $authcode ) ) {
		
				// String contains ONLY numeric digits

				// Add a random letter
				$authcode[mt_rand( 0, $authcode_length - 1 )] = chr( mt_rand( 65, 90 ) );
			}
			/*
				End - (modified) Kohana code
			*/

			global $wpdb;
			
			// Insert subscriber into table
			
			// See if subscriber is already in table
			$subscriber_exists = $wpdb->get_var( 
				$wpdb->prepare(
					"SELECT COUNT(id) FROM " . $wpdb->prefix.'post_notif_subscriber' . " WHERE email_addr = %s"
					,$email_addr
				)		
			);
			if ( $subscriber_exists ) {
					  
				// Subscriber DOES already exist
				wp_send_json( array( 'success' => true, 'message' => __( "You're already subscribed so no need to do anything further.", 'post-notif' ) ) );					  
			}
			else {
					  
				// Subscriber is new
				$result = $wpdb->insert( 
					$wpdb->prefix.'post_notif_subscriber', 
					array( 
						'id' => ''
						,'email_addr' => $email_addr
						,'first_name' => ( $first_name != '') ? $first_name : __( '[Unknown]', 'post-notif' )
						,'confirmed' => 0 
						,'last_modified' => date( "Y-m-d H:i:s" )
						,'date_subscribed' => date( "Y-m-d H:i:s" )
						,'authcode' => $authcode
					) 
				);
    
				// This is based on Post Notif settings, with placeholders replaced by user-specific/admin-specified data
    		
				// Compose confirmation email
				// NOTE: It is IMPERATIVE that there is "/" at the end of this URL or "@" will get filtered out of URL, causing all sorts of problems!
    
				$post_notif_options_arr = get_option( 'post_notif_settings' );
    
				// Replace variables in both the post notif email subject and body
   
				$conf_email_subject = $post_notif_options_arr['sub_conf_eml_subj'];
				$conf_email_subject = str_replace( '@@blogname', get_bloginfo( 'name' ), $conf_email_subject );
    		
				// Tell PHP mail() to convert both double and single quotes from their respective HTML entities to their applicable characters
				$conf_email_subject = html_entity_decode (  $conf_email_subject, ENT_QUOTES, 'UTF-8' );

				$conf_email_body = $post_notif_options_arr['sub_conf_eml_body'];
				$conf_email_body = str_replace( '@@firstname', ( $first_name != '' ) ? $first_name : __( 'there', 'post-notif' ), $conf_email_body );
				$conf_email_body = str_replace( '@@blogname', get_bloginfo( 'name' ), $conf_email_body );

				// NOTE: This is in place to minimize chance that, due to email client settings, subscribers
				//		will be unable to see and/or click the confirm URL link within their email
				$conf_url = get_site_url() . '/post_notif/confirm/?email_addr=' . $email_addr . '&authcode=' . $authcode;
				$conf_email_body = str_replace( '@@confurl', '<a href="' . $conf_url . '">' . $conf_url . '</a>', $conf_email_body );

				$conf_email_body = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $conf_email_body );
       
				// Send confirmation email to new subscriber
    		
				// Set sender name and email address
				$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
					. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
   		
				// Specify HTML-formatted email
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
   
				wp_mail( $email_addr, $conf_email_subject, $conf_email_body, $headers );   
				wp_send_json( array( 'success' => true, 'message' => __( 'Thanks.  Please check your email to confirm your subscription.', 'post-notif' ) ) );    		
			}
		}
		else
		{
				  
			// Error in form validation
			wp_send_json( array( 'success' => false, 'message' => $error ) );
		}
		
		// All ajax handlers should die when finished
    	wp_die(); 
    	
   }

} // end class
add_action( 'widgets_init', create_function( '', 'register_widget("Post_Notif_Widget");' ) );
