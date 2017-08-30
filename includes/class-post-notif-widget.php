<?php
/**
 * Post Notif Widget
 *
 * This plugin was built using the exceptional WordPress Widget Boilerplate 
 *	(https://github.com/tommcfarlin/WordPress-Widget-Boilerplate) written by Tom 
 *	McFarlin (http://tommcfarlin.com).
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
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
 * @since		1.0.0
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin
 * @author		Devon Ostendorf <devon@devonostendorf.com>
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

		if ( $post_notif_widget_settings_arr = $this->get_post_notif_widget_settings() ) {
			if ( 1 == $post_notif_widget_settings_arr['override_theme_css'] ) {

				// Apply stylesheet overrides
				$this->override_theme_css( $post_notif_widget_settings_arr );
			}
		}
		
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
	 * Get widget's settings.
	 *
	 * @since	1.1.5
	 * @access	private
	 * @return	array|boolean	Array of widget's settings or false if widget not found.
	 */
	private function get_post_notif_widget_settings() {
				
		$post_notif_widget_arr = get_option( 'widget_post-notif');
		if ( $post_notif_widget_arr ) {
				  
			// Widget IS defined
			
			// Find index containing title so we can access other settings
			foreach( $post_notif_widget_arr as $arr_key => $arr_item ) {
				if ( is_array( $arr_item ) ) {
					if ( array_key_exists( 'title', $arr_item ) ) {
						
						return $arr_item;
					}
				}
			}
		}
		
		return false;
	
	}
	
	/**
	 * Register the (blank) stylesheet and attach admin-configured overrides for
	 * the widget, if necessary.
	 *
	 * @since	1.1.5
	 * @access	private
	 * @param	array	$post_notif_widget_settings_arr	The admin-configured settings for the widget.
	 */
	private function override_theme_css( $post_notif_widget_settings_arr ) {

		// Define formatting variables
		$selector_indent = "\t\t\t";
		$property_indent = "\t\t\t\t";
		$newline = PHP_EOL;
		
		// NOTE: This is a blank stylesheet, merely used as an attachment point for wp_add_inline_style()
		wp_enqueue_style( $this->get_widget_slug(), plugin_dir_url( __FILE__ ) . 'css/post-notif-widget.css', array(), '1.1.5', 'all' );

		
		// Define all configurable styling
		
		$settings_arr = array(
			array( 
				'name' => 'call_to_action'
				,'selector' => $selector_indent . '#id_lblCallToAction {' . $newline
			)
			,array(
				'name' => 'placeholder'
				,'selector' => $selector_indent . '#id_txtFirstName::placeholder,' . $newline . $selector_indent . '#id_txtEmailAddr::placeholder {' . $newline
			)
			,array(
				'name' => 'input_fields'
				,'selector' => $selector_indent . '#id_txtFirstName,' . $newline . $selector_indent . '#id_txtEmailAddr {' . $newline
			)
			,array(
				'name' => 'error'
				,'selector' => $selector_indent . '#id_spnErrorMsg {' . $newline
			)
			,array(
				'name' => 'message'
				,'selector' => $selector_indent . '#id_spnSuccessMsg {' . $newline
			)
		);
		
		$properties_arr = array(
			'properties' => array(
				'font-family'
				,'font-size'
				,'color'
			)
			,'setting_types' => array(
				'font_family'
				,'font_size'
				,'font_color'
			)
		);
			
		$ruleset_arr = array();

		// Iterate through settings
		foreach ( $settings_arr as $current_setting_arr ) {
			$ruleset_arr[ $current_setting_arr['name'] ] = '';
			
			// Iterate through properties by setting types
			foreach ( $properties_arr['setting_types'] as $index => $value ) {
				$setting_full_name = $current_setting_arr['name'] . '_' . $value;			
				if ( false != trim( $post_notif_widget_settings_arr[ $setting_full_name ] ) ) {
					
					// This property has been overridden, so add it to current rule
					$ruleset_arr[ $current_setting_arr['name'] ] .= $property_indent . $properties_arr['properties'][ $index ] . ': ' . esc_html( $post_notif_widget_settings_arr[ $setting_full_name ] ) . ';' . $newline;
				}		
			}
			if ( false != trim( $ruleset_arr[ $current_setting_arr['name'] ] ) ) {
				$ruleset_arr[ $current_setting_arr['name'] ] = $current_setting_arr['selector'] . $ruleset_arr[ $current_setting_arr['name'] ] . $selector_indent . '}' . $newline;
			}	
		}
		
		$widget_style = '';
		foreach ( $ruleset_arr as $rule ) {
			$widget_style .= $rule;
		}

		if ( false != trim( $widget_style ) ) {
			$widget_style = $newline . $widget_style;
			wp_add_inline_style( $this->get_widget_slug(), $widget_style );
		}

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
	 * @param	array	$args	The array of form elements
	 * @param	array	$instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {
		
		// Check if there is a cached output
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( ! is_array( $cache ) ) {
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
	 * @return	array	The values, entered into widget fields by user, to be saved. 
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// Update widget's old values with the new, incoming values
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['call_to_action'] = strip_tags( $new_instance['call_to_action'] );
		$instance['button_label'] = strip_tags( $new_instance['button_label'], '<img>' );
		$instance['first_name_field_size'] = strip_tags( $new_instance['first_name_field_size'] );
		$instance['first_name_placeholder'] = strip_tags( $new_instance['first_name_placeholder'] );
		$instance['require_first_name'] = isset( $new_instance['require_first_name'] ) ? (bool) $new_instance['require_first_name'] : false;
		$instance['email_addr_field_size'] = strip_tags( $new_instance['email_addr_field_size'] );
		$instance['email_addr_placeholder'] = strip_tags( $new_instance['email_addr_placeholder'] );
		$instance['override_theme_css'] = isset( $new_instance['override_theme_css'] ) ? (bool) $new_instance['override_theme_css'] : false;
		$instance['call_to_action_font_family'] = strip_tags( $new_instance['call_to_action_font_family'] );
		$instance['call_to_action_font_size'] = strip_tags( $new_instance['call_to_action_font_size'] );
		$instance['call_to_action_font_color'] = strip_tags( $new_instance['call_to_action_font_color'] );
		$instance['placeholder_font_family'] = strip_tags( $new_instance['placeholder_font_family'] );
		$instance['placeholder_font_size'] = strip_tags( $new_instance['placeholder_font_size'] );
		$instance['placeholder_font_color'] = strip_tags( $new_instance['placeholder_font_color'] );
		$instance['input_fields_font_family'] = strip_tags( $new_instance['input_fields_font_family'] );
		$instance['input_fields_font_size'] = strip_tags( $new_instance['input_fields_font_size'] );
		$instance['input_fields_font_color'] = strip_tags( $new_instance['input_fields_font_color'] );
		$instance['error_font_family'] = strip_tags( $new_instance['error_font_family'] );
		$instance['error_font_size'] = strip_tags( $new_instance['error_font_size'] );
		$instance['error_font_color'] = strip_tags( $new_instance['error_font_color'] );
		$instance['message_font_family'] = strip_tags( $new_instance['message_font_family'] );
		$instance['message_font_size'] = strip_tags( $new_instance['message_font_size'] );
		$instance['message_font_color'] = strip_tags( $new_instance['message_font_color'] );
		$this->flush_widget_cache();
		
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions[ $this->get_widget_slug() ] ) ) {
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
		$first_name_field_size = isset( $instance['first_name_field_size'] ) ? esc_attr( $instance['first_name_field_size'] ) : $post_notif_widget_defaults_arr['first_name_field_size_default'];
		$first_name_placeholder = isset( $instance['first_name_placeholder'] ) ? esc_attr( $instance['first_name_placeholder'] ) : $post_notif_widget_defaults_arr['first_name_placeholder_default'];
		$require_first_name = isset( $instance['require_first_name'] ) ? (bool) $instance['require_first_name'] : (bool) $post_notif_widget_defaults_arr['require_first_name_default'];
		$email_addr_field_size = isset( $instance['email_addr_field_size'] ) ? esc_attr( $instance['email_addr_field_size'] ) : $post_notif_widget_defaults_arr['email_addr_field_size_default'];
		$email_addr_placeholder = isset( $instance['email_addr_placeholder'] ) ? esc_attr( $instance['email_addr_placeholder'] ) : $post_notif_widget_defaults_arr['email_addr_placeholder_default'];
		$override_theme_css = isset( $instance['override_theme_css'] ) ? (bool) $instance['override_theme_css'] : (bool) $post_notif_widget_defaults_arr['override_theme_css_default'];
		$call_to_action_font_family = isset( $instance['call_to_action_font_family'] ) ? esc_attr( $instance['call_to_action_font_family'] ) : $post_notif_widget_defaults_arr['call_to_action_font_family_default'];
		$call_to_action_font_size = isset( $instance['call_to_action_font_size'] ) ? esc_attr( $instance['call_to_action_font_size'] ) : $post_notif_widget_defaults_arr['call_to_action_font_size_default'];
		$call_to_action_font_color = isset( $instance['call_to_action_font_color'] ) ? esc_attr( $instance['call_to_action_font_color'] ) : $post_notif_widget_defaults_arr['call_to_action_font_color_default'];
		$placeholder_font_family = isset( $instance['placeholder_font_family'] ) ? esc_attr( $instance['placeholder_font_family'] ) : $post_notif_widget_defaults_arr['placeholder_font_family_default'];
		$placeholder_font_size = isset( $instance['placeholder_font_size'] ) ? esc_attr( $instance['placeholder_font_size'] ) : $post_notif_widget_defaults_arr['placeholder_font_size_default'];
		$placeholder_font_color = isset( $instance['placeholder_font_color'] ) ? esc_attr( $instance['placeholder_font_color'] ) : $post_notif_widget_defaults_arr['placeholder_font_color_default'];
		$input_fields_font_family = isset( $instance['input_fields_font_family'] ) ? esc_attr( $instance['input_fields_font_family'] ) : $post_notif_widget_defaults_arr['input_fields_font_family_default'];
		$input_fields_font_size = isset( $instance['input_fields_font_size'] ) ? esc_attr( $instance['input_fields_font_size'] ) : $post_notif_widget_defaults_arr['input_fields_font_size_default'];
		$input_fields_font_color = isset( $instance['input_fields_font_color'] ) ? esc_attr( $instance['input_fields_font_color'] ) : $post_notif_widget_defaults_arr['input_fields_font_color_default'];
		$error_font_family = isset( $instance['error_font_family'] ) ? esc_attr( $instance['error_font_family'] ) : $post_notif_widget_defaults_arr['error_font_family_default'];
		$error_font_size = isset( $instance['error_font_size'] ) ? esc_attr( $instance['error_font_size'] ) : $post_notif_widget_defaults_arr['error_font_size_default'];
		$error_font_color = isset( $instance['error_font_color'] ) ? esc_attr( $instance['error_font_color'] ) : $post_notif_widget_defaults_arr['error_font_color_default'];
		$message_font_family = isset( $instance['message_font_family'] ) ? esc_attr( $instance['message_font_family'] ) : $post_notif_widget_defaults_arr['message_font_family_default'];
		$message_font_size = isset( $instance['message_font_size'] ) ? esc_attr( $instance['message_font_size'] ) : $post_notif_widget_defaults_arr['message_font_size_default'];
		$message_font_color = isset( $instance['message_font_color'] ) ? esc_attr( $instance['message_font_color'] ) : $post_notif_widget_defaults_arr['message_font_color_default'];
 
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

		wp_enqueue_script( $this->get_widget_slug().'-script', plugins_url( 'js/widget.min.js', __FILE__ ), array('jquery') );

	}
	
	
	// Functions related to public-facing widget functionality

	/**
	 * Enqueue AJAX script that fires when "Sign me up!" button (in widget) is pressed.
	 *
	 * @since	1.0.0
	 * @param	string	$hook	The string containing the current page name.
	 */
	public function post_notif_widget_enqueue( $hook ) {
	
		// Get widget messages from options
		$post_notif_settings_arr = get_option( 'post_notif_settings' );

		$post_notif_widget_nonce = wp_create_nonce( 'post_notif_widget' );
		wp_localize_script( 
			$this->get_widget_slug().'-script'
			,'post_notif_widget_ajax_obj'
			,array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
				,'nonce'    => $post_notif_widget_nonce
				,'processing_msg' => $post_notif_settings_arr['widget_info_message_processing']
			)
		);
		
	}
	
	/**
	 * Handle AJAX event sent when "Sign me up!" button (in widget) is pressed.
	 *
	 * @since	1.0.0
	 */
	public function post_notif_widget_ajax_handler() {
		  
		// Confirm matching nonce
		check_ajax_referer( 'post_notif_widget' );
 
		// Get widget messages from options
		$post_notif_settings_arr = get_option( 'post_notif_settings' );
		
		// Get user's first name and email address from submitted form
		$first_name =  substr( trim( $_POST['form_data']['first_name'] ), 0, 50 );
		$email_addr =  substr( trim( $_POST['form_data']['email_addr'] ), 0, 100 );

		$error = '';

		if ( $post_notif_widget_settings_arr = $this->get_post_notif_widget_settings() ) {
			if ( 1 == $post_notif_widget_settings_arr['require_first_name'] ) {
				
				// Confirm that first name is not blank
				if ( '' == $first_name ) {
					$error = $post_notif_settings_arr['widget_error_reqd_first_name_blank'] . ' ';
				}
			}
		}
		
		// Confirm that email addr is valid
		if ( '' == $email_addr ) {
			$error .= $post_notif_settings_arr['widget_error_email_addr_blank'];
		} 
		elseif ( ! preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i', $email_addr ) ) {
			$error .= $post_notif_settings_arr['widget_error_email_addr_invalid'];
		} 
   
		if ( empty( $error ) ) {
      
			// Generate authcode			
			$authcode = Post_Notif_Misc::generate_authcode();

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
				wp_send_json( array( 'success' => true, 'message' => $post_notif_settings_arr['widget_info_message_already_subscribed'] ) );					  
			}
			else {
				
				// Subscriber is new
				$first_name = ( $first_name != '') ? $first_name : __( '[Unknown]', 'post-notif' );
				$subscriber_inserted = $wpdb->insert( 
					$wpdb->prefix.'post_notif_subscriber' 
					,array( 
						'id' => ''
						,'email_addr' => $email_addr
						,'first_name' => $first_name
						,'confirmed' => 0 
						,'last_modified' => gmdate( "Y-m-d H:i:s" )
						,'date_subscribed' => gmdate( "Y-m-d H:i:s" )
						,'authcode' => $authcode
						,'to_delete' => 0
						,'last_update_dttm' => gmdate( "Y-m-d H:i:s" )
					) 
				);
    
				$subscriber_arr = array(
					'email_addr' => $email_addr
					,'first_name' => $first_name
					,'authcode' => $authcode
				);

				if ( $subscriber_inserted ) {
					
					// Send confirmation email
					Post_Notif_Misc::send_confirmation_email( $subscriber_arr );
					wp_send_json( array( 'success' => true, 'message' => $post_notif_settings_arr['widget_success_message'] ) );
				}
				else {
				
					// Subscriber creation failed
					
					// Send admin email
					Post_Notif_Misc::send_admin_failed_subscriber_creation_email( $subscriber_arr );
					wp_send_json( array( 'success' => true, 'message' => $post_notif_settings_arr['widget_failure_message'] ) );					
				}
			}
		}
		else {
				  
			// Error in form validation
			wp_send_json( array( 'success' => false, 'message' => $error ) );
		}
		
		// All ajax handlers should die when finished
    	wp_die(); 
    	
    }

} // end class
add_action( 'widgets_init', create_function( '', 'register_widget("Post_Notif_Widget");' ) );
