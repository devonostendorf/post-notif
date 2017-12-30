<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the public-facing
 *	JavaScript.
 *
 * @since		1.0.0
 * @package		Post_Notif
 * @subpackage	Post_Notif/public
 * @author		Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var     string	$plugin_name	The ID of this plugin.
	 */
	private $plugin_name;
									
	/**
	 * The version of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string	$version	The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string	$plugin_name	The name of the plugin.
	 * @param	string	$version	The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Post_Notif_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Post_Notif_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-notif-public.min.js', array( 'jquery' ), $this->version, false );
		
	}

	
	// Functions related to subscriber form shortcode

	/**
	 * Register shortcode to render subscriber form and hook in AJAX handler for
	 *	both logged in users and unknown visitors.
	 *
	 * @since	1.3.0
	 */
   	public function register_shortcode() {
   		
   		add_shortcode( 'post_notif_subscribe', array( $this, 'render_subscriber_form' ) );
 		add_action( 'wp_ajax_post_notif_subscriber_form', array( $this, 'subscriber_form_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_post_notif_subscriber_form', array( $this, 'subscriber_form_ajax_handler' ) );
  		
   	}

	/**
	 * Render subscriber form.
	 *
	 * @since	1.3.0
	 * @param	array	$atts	An associative array of attributes, or an empty string if no attributes are given.
	 * @param	string	$content	The enclosed content (if the shortcode is used in its enclosing form).
	 * @return	string	The HTML to render subscriber form.
	 */
   	public function render_subscriber_form( $atts, $content = null ) {
   		
   		if ( ( is_singular() ) || 
   			( ( array_key_exists( 'is_widget', $atts ) ) && ( 'yes' == $atts['is_widget'] ) ) ) {
   		
   			// Only render subscriber form if this is a singular post or page OR if this is a widget call
   		
   			$post_notif_options_arr = get_option( 'post_notif_settings' );

   			$clean_atts = shortcode_atts(
   				array(
   					'is_widget' => 'no'
   					,'title' => $post_notif_options_arr['shortcode_title']
   					,'id' => '1'
   					,'call_to_action' => $post_notif_options_arr['shortcode_call_to_action']
   					,'button_label' => $post_notif_options_arr['shortcode_button_label']
   					,'first_name_field_size' => $post_notif_options_arr['shortcode_first_name_field_size']
   					,'first_name_placeholder' => $post_notif_options_arr['shortcode_first_name_placeholder']
   					,'email_addr_field_size' => $post_notif_options_arr['shortcode_email_addr_field_size']
   					,'email_addr_placeholder' => $post_notif_options_arr['shortcode_email_addr_placeholder']
   					,'require_first_name' => array_key_exists( 'shortcode_require_first_name', $post_notif_options_arr ) ? 'yes' : 'no'				
   					,'override_theme_css' => array_key_exists( 'shortcode_override_theme_css', $post_notif_options_arr ) ? 'yes' : 'no'
   					,'stylesheet_filename' => $post_notif_options_arr['shortcode_stylesheet_filename']
   					,'call_to_action_font_family' => $post_notif_options_arr['shortcode_call_to_action_font_family']
   					,'call_to_action_font_size' => $post_notif_options_arr['shortcode_call_to_action_font_size']
   					,'call_to_action_font_color' => $post_notif_options_arr['shortcode_call_to_action_font_color']
   					,'placeholder_font_family' => $post_notif_options_arr['shortcode_placeholder_font_family']
   					,'placeholder_font_size' => $post_notif_options_arr['shortcode_placeholder_font_size']
   					,'placeholder_font_color' => $post_notif_options_arr['shortcode_placeholder_font_color']
   					,'input_fields_font_family' => $post_notif_options_arr['shortcode_input_fields_font_family']
   					,'input_fields_font_size' => $post_notif_options_arr['shortcode_input_fields_font_size']
   					,'input_fields_font_color' => $post_notif_options_arr['shortcode_input_fields_font_color']
   					,'error_font_family' => $post_notif_options_arr['shortcode_error_font_family']
   					,'error_font_size' => $post_notif_options_arr['shortcode_error_font_size']
   					,'error_font_color' => $post_notif_options_arr['shortcode_error_font_color']
   					,'message_font_family' => $post_notif_options_arr['shortcode_message_font_family']
   					,'message_font_size' => $post_notif_options_arr['shortcode_message_font_size']
   					,'message_font_color' => $post_notif_options_arr['shortcode_message_font_color']
   					,'error_reqd_first_name_blank' => $post_notif_options_arr['shortcode_error_reqd_first_name_blank']
   					,'error_email_addr_blank' => $post_notif_options_arr['shortcode_error_email_addr_blank']
   					,'error_email_addr_invalid' => $post_notif_options_arr['shortcode_error_email_addr_invalid']
   					,'info_message_processing' => $post_notif_options_arr['shortcode_info_message_processing']
   					,'info_message_already_subscribed' => $post_notif_options_arr['shortcode_info_message_already_subscribed']
   					,'failure_message' => $post_notif_options_arr['shortcode_failure_message']
   					,'success_message' => $post_notif_options_arr['shortcode_success_message']
   				)
   				,$atts
   				,'post_notif_subscribe'
   			);

   			// Set up AJAX to create a new subscriber
   			wp_enqueue_script( $this->plugin_name.'-shortcode', plugin_dir_url( __FILE__ ) . 'js/post-notif-public-subscriber-form.js', array( 'jquery' ), $this->version, true );
   			$this->localize_subscriber_form_handler_script( $clean_atts );
   			
   			// Deal with CSS overrides
   			if ( 'yes' == $clean_atts['override_theme_css'] ) {
   				if ( ! empty( $clean_atts['stylesheet_filename'] )) {
			
   					// Must be in ../post-notif/public/css
   					if ( file_exists( plugin_dir_path( __FILE__ ) . 'css/' . $clean_atts['stylesheet_filename'] ) ) {
				
   						// Enqueue CSS stylesheet as specified by shortcode user
   						wp_enqueue_style( $this->plugin_name.'-shortcode-' . $clean_atts['id'], plugin_dir_url( __FILE__ ) . 'css/' . $clean_atts['stylesheet_filename'], array(), $this->version, 'all' );
   					}
   				}
   				else {
		
   					// Define all configurable styling
   					$this->override_theme_css( $clean_atts );
   				}
   			}
   		
   			$post_notif_subscriber_form = '';
   			ob_start();
   			include( plugin_dir_path( __FILE__ ) . 'views/post-notif-public-subscriber-form.php' );
   			$post_notif_subscriber_form .= ob_get_clean();
  		
   			return $post_notif_subscriber_form;
   			
  		}
   	}

	/**
	 * Localize AJAX script that fires when submit button (in subscriber form) is pressed.
	 *
	 * @since	1.3.0
	 * @param	array	$atts	An associative array of attributes, or an empty string if no attributes are given.
	 */
   	public function localize_subscriber_form_handler_script( $atts ) {
   		
		// Store form attributes (so unique messages are accessible to AJAX handler) in options
		update_option( 'post_notif_shortcode_atts_' . $atts['id'], $atts );
		
		// Store list of attribute sets stored in options (so they can be deleted on plugin deactivation)
		$shortcode_atts_set_names_arr = get_option( 'post_notif_shortcode_atts_set_names', array() );
		$shortcode_atts_set_names_arr[] = 'post_notif_shortcode_atts_' . $atts['id'];
		update_option( 'post_notif_shortcode_atts_set_names', $shortcode_atts_set_names_arr );
		
		$post_notif_nonce = wp_create_nonce( 'post_notif_subscriber_form' );
		wp_localize_script( 
			$this->plugin_name . '-shortcode'
			,'post_notif_subscriber_form_ajax_obj'
			,array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
				,'nonce'    => $post_notif_nonce
				,'processing_msg' => $atts['info_message_processing']
			)
		);
   		
   	}
 
	/**
	 * Handle AJAX event sent when submit button (in subscriber form) is pressed.
	 *
	 * @since	1.3.0
	 */
   	public function subscriber_form_ajax_handler() {
   		
		// Confirm matching nonce
		check_ajax_referer( 'post_notif_subscriber_form' );
 
		// Get user's first name and email address from submitted form
		$first_name = substr( trim( $_POST['form_data']['first_name'] ), 0, 50 );
		$email_addr = substr( trim( $_POST['form_data']['email_addr'] ), 0, 100 );
		$form_id = trim( $_POST['form_id'] );
		
		// Get stored attributes, for this id, from options table
		$atts = get_option( 'post_notif_shortcode_atts_' . $form_id, null );
		
		$error = '';

		if ( 'yes' == $atts['require_first_name'] ) {				
			// Confirm that first name is not blank
			if ( '' == $first_name ) {
				$error = $atts['error_reqd_first_name_blank'] . ' ';
			}
		}
		
		// Confirm that email addr is valid
		if ( '' == $email_addr ) {
			$error .= $atts['error_email_addr_blank'];
		} 
		elseif ( ! preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i', $email_addr ) ) {
			$error .= $atts['error_email_addr_invalid'];
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
				wp_send_json( array( 'success' => true, 'message' => $atts['info_message_already_subscribed'] ) );					  
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
					wp_send_json( array( 'success' => true, 'message' => $atts['success_message'] ) );
				}
				else {
				
					// Subscriber creation failed
					
					// Send admin email
					Post_Notif_Misc::send_admin_failed_subscriber_creation_email( $subscriber_arr );
					wp_send_json( array( 'success' => true, 'message' => $atts['failure_message'] ) );					
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
    
	/**
	 * Register the (blank) stylesheet and attach admin-configured overrides for
	 * the shortcode, if necessary.
	 *
	 * @since	1.3.0
	 * @access	private
	 * @param	array	$atts	An associative array of attributes, or an empty string if no attributes are given.
	 */
	private function override_theme_css( $atts ) {

		// Define formatting variables
		$selector_indent = "\t\t\t";
		$property_indent = "\t\t\t\t";
		$newline = PHP_EOL;
		
		// NOTE: This is a blank stylesheet, merely used as an attachment point for wp_add_inline_style()
		wp_enqueue_style( $this->plugin_name . '-shortcode-' . $atts['id'], plugin_dir_url( __FILE__ ) . 'css/post-notif-public-subscriber-form.css', array(), $this->version, 'all' );
								
		// Define all configurable styling
		
		$settings_arr = array(
			array( 
				'name' => 'call_to_action'
				,'selector' => $selector_indent . '#id_pn_lbl_call_to_action_' . $atts['id'] . ' {' . $newline
			)
			,array(
				'name' => 'placeholder'
				,'selector' => $selector_indent . '#id_pn_txt_first_name_' . $atts['id'] . '::placeholder,' . $newline . $selector_indent . '#id_pn_eml_email_addr_' . $atts['id'] . '::placeholder {' . $newline
			)
			,array(
				'name' => 'input_fields'
				,'selector' => $selector_indent . '#id_pn_txt_first_name_' . $atts['id'] . ',' . $newline . $selector_indent . '#id_pn_eml_email_addr_' . $atts['id'] . ' {' . $newline
			)
			,array(
				'name' => 'error'
				,'selector' => $selector_indent . '#id_pn_spn_error_msg_' . $atts['id'] . ' {' . $newline
			)
			,array(
				'name' => 'message'
				,'selector' => $selector_indent . '#id_pn_spn_success_msg_' . $atts['id'] . ' {' . $newline
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
				if ( false != trim( $atts[ $setting_full_name ] ) ) {
					
					// This property has been overridden, so add it to current rule
					$ruleset_arr[ $current_setting_arr['name'] ] .= $property_indent . $properties_arr['properties'][ $index ] . ': ' . esc_html( $atts[ $setting_full_name ] ) . ';' . $newline;
				}		
			}
			if ( false != trim( $ruleset_arr[ $current_setting_arr['name'] ] ) ) {
				$ruleset_arr[ $current_setting_arr['name'] ] = $current_setting_arr['selector'] . $ruleset_arr[ $current_setting_arr['name'] ] . $selector_indent . '}' . $newline;
			}	
		}
		
		$shortcode_style = '';
		foreach ( $ruleset_arr as $rule ) {
			$shortcode_style .= $rule;
		}

		if ( false != trim( $shortcode_style ) ) {
			$shortcode_style = $newline . $shortcode_style;
			wp_add_inline_style( $this->plugin_name.'-shortcode-'.$atts['id'], $shortcode_style );
		}

	}  

	
	// Functions related to selectively suppressing Post Notif and Recent Posts widgets from sidebar
	
	/**
	 * Add filter to suppress the Post Notif and Recent Posts widgets from sidebar.
	 *
	 * @since	1.0.0
	 * @access	private
	 */	
	private function get_sidebar_minus_post_notif_recent_posts_widgets() {

		// NOTE: This add_filter is deliberately buried in here as we ONLY want it applied
		//		when the post-notif-public-display-sub-prefs view calls this function
		add_filter( 'sidebars_widgets', array( $this, 'hide_post_notif_widgets' ) );		  
		
	}

	/**
	 * Remove Post Notif and Recent Posts widget from all active sidebars.
	 *
	 * @since	1.0.0
	 * @param	array	$all_widgets	All widgets.
	 * @return	array	All widgets.
	 */
	public function hide_post_notif_widgets( $all_widgets ) {

		foreach ( $all_widgets as $sidebar_key => $sidebar ) {
			if ( 'wp_inactive_widgets' != $sidebar_key ) {
				if ( count( $sidebar ) ) {
						  
					// Sidebar contains widgets, so iterate through them, looking for 
					//		Post Notif and Recent Posts widgets
					foreach ( $sidebar as $widget_index => $widget ) {
						if ( ( false !== strpos( $widget, 'post-notif' ) ) 
						|| ( false !== strpos( $widget, 'recent-posts' ) ) ) {
							unset( $all_widgets[ $sidebar_key ][ $widget_index ] );
						}
					}
				}
			}
		}
		
		return $all_widgets;
		
	}


	// Functions related to handling Post Notif URLs

	/**
	 * Route Post Notif-related URL to appropriate handler.
	 *
	 * @since	1.0.0
	 */	
	public function url_controller() {
		  
		// Handle post notif URLs

		if ( $this->detect_subscription_confirmation_url() ) {

			// This is a subscription confirmation URL - add filter to process
			$this->add_filter_for_subscription_confirmation();
		}
		elseif ( $this->detect_manage_preferences_url() ) {
		
			// This is a manage preferences URL - add filter to process
			$this->add_filter_for_manage_preferences();			  
		}
		elseif ( $this->detect_update_preferences_url() ) {
			  
			// This is an update preferences URL - add filter to process
			$this->add_filter_for_update_preferences();			  
		}
		elseif ( $this->detect_unsubscribe_url() ) {
		
			// This is an unsubscribe URL - add filter to process
			$this->add_filter_for_unsubscribe();			  
		}	  
		
	}
	
	/**
	 * Detect subscription confirmation URL.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @return	bool	Is this a subscription confirmation URL?
	 */	
	private function detect_subscription_confirmation_url() {
			  
		// If this is a subscription confirmation URL return true
		return false !== strpos( $_SERVER['REQUEST_URI'], '/post_notif/confirm' );
		
	}

	/**
	 * Add hook to fire when subscription confirmation URL is detected.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function add_filter_for_subscription_confirmation() {
			
		add_filter( 'the_posts', array( $this, 'create_subscription_confirmed_page' ) );
	 
	}
	
	/**
	 * Process subscription confirmation URL, set subscriber as confirmed to 
	 *	receive post notifications, set their preferences to receive all categories
	 *	by default and prep preferences page with admin-defined page template
	 *	variables.
	 *
	 * @since	1.0.0
	 * @param	array	$posts	The current (pseudo) page.
	 * @return	array	The current (pseudo) page, rendered.
	 */	
	public function create_subscription_confirmed_page( $posts ) {
		
		global $wpdb;
		
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Get query variables from URL
		list( $email_addr, $authcode ) = $this->get_query_vars();
	 
		// Get subscriber
		$subscriber = $wpdb->get_row(
			$wpdb->prepare(
				"
   					SELECT 
   						id
   						,email_addr 
   						,first_name
   						,authcode
   					FROM $post_notif_subscriber_tbl
   					WHERE email_addr = %s
   					AND authcode = %s
   					AND confirmed = 0
   				"
				,$email_addr
				,$authcode
			)
		);

		if ( $subscriber ) {
  		  
			// This IS a valid authcode
   	
			// Update user's subscriber row so they will now receive post notifs
			$result = $wpdb->update( 
				$post_notif_subscriber_tbl
				,array( 
					'confirmed' => 1
					,'last_modified' => date( "Y-m-d H:i:s" )
				)
				,array( 
					'id' => $subscriber->id
				)    			
			);

			// Auto assign them to receive All categories (cat_id = 0)
			$result = $wpdb->insert(
				$post_notif_sub_cat_tbl
				,array( 
					'id' => $subscriber->id
					,'cat_id' => 0
				)
			);

  			$post_notif_options_arr = get_option( 'post_notif_settings' );
			
			// If admin has chosen to activate this functionality, send email after
			//		subscription is confirmed
			if ( array_key_exists( 'send_eml_to_sub_after_conf', $post_notif_options_arr ) ) {
		
				//	Compose email
   		   		
				// Replace variables in both the subject and body of the email to subscriber
   		
				$after_conf_email_subject = $post_notif_options_arr['eml_to_sub_after_conf_subj'];
				$after_conf_email_subject = str_replace( '@@blogname', get_bloginfo('name'), $after_conf_email_subject );
 
				// Tell PHP mail() to convert both double and single quotes from their respective HTML entities to their applicable characters
				$after_conf_email_subject = html_entity_decode( $after_conf_email_subject, ENT_QUOTES, 'UTF-8' );
   			
				$after_conf_email_body = $post_notif_options_arr['eml_to_sub_after_conf_body'];
				$after_conf_email_body = str_replace( '@@blogname', get_bloginfo('name'), $after_conf_email_body );
				$after_conf_email_body = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $after_conf_email_body );

				// Set sender name and email address
				$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
					. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
  		
				// Specify HTML-formatted email
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				// Generate generic subscriber URL base
				$subscriber_url_template = Post_Notif_Misc::generate_subscriber_url_base();

				// Tailor links (change prefs, unsubscribe) for current subscriber
				$subscriber_url = $subscriber_url_template . '?email_addr=' . $subscriber->email_addr . '&authcode=' . $subscriber->authcode;
				$prefs_url = str_replace( 'ACTION_PLACEHOLDER', 'manage_prefs', $subscriber_url );
				$unsubscribe_url = str_replace( 'ACTION_PLACEHOLDER', 'unsubscribe', $subscriber_url );

    			$after_conf_email_body = str_replace( '@@firstname', ($subscriber->first_name != '[Unknown]') ? $subscriber->first_name : '', $after_conf_email_body );
    			$after_conf_email_body = str_replace( '@@prefsurl', '<a href="' . $prefs_url . '">' . $prefs_url . '</a>', $after_conf_email_body );
    			$after_conf_email_body = str_replace( '@@unsubscribeurl', '<a href="' . $unsubscribe_url . '">' . $unsubscribe_url . '</a>', $after_conf_email_body );
    				
				//	Physically send email
   				$mail_sent = wp_mail( $subscriber->email_addr, $after_conf_email_subject, $after_conf_email_body, $headers );   			
    		}
				  
			// Retrieve options to populate page
			
			// @@blogname is a valid variable for both page title and greeting
			$post_notif_options_arr = get_option( 'post_notif_settings' );
			$sub_confirmed_page_title = $post_notif_options_arr['sub_confirmed_page_title'];
			$sub_confirmed_page_title = str_replace( '@@blogname', get_bloginfo( 'name' ), $sub_confirmed_page_title );
			$sub_confirmed_page_greeting = $post_notif_options_arr['sub_confirmed_page_greeting'];
			$sub_confirmed_page_greeting = str_replace( '@@blogname', get_bloginfo( 'name' ), $sub_confirmed_page_greeting );
			
			$params_arr = array(
				'email_addr' => $email_addr
				,'authcode' => $authcode
				,'subscriber_id' => $subscriber->id
				,'page_title' => $sub_confirmed_page_title
				,'page_greeting' => $sub_confirmed_page_greeting
			);

			// Create fake page
			return $this->create_fake_page( $posts, 'render_preferences_page', $params_arr );
		}
		// implicit else: bad URL, page not found will be displayed		
	}

	/**
	 * Core translates pluses ("+") to spaces (" ") in query variable values so
	 *	we cannot assume using $wp_query->query_vars will properly handle email
	 *	addresses.
	 *
	 * @since	1.1.5
	 * @access	private
	 * @return	array	Email address and authcode.
	 */	
	private function get_query_vars() {

		defined ( 'EMAIL_ADDR_QUERY_VAR' ) || define( 'EMAIL_ADDR_QUERY_VAR', 'email_addr=' );
		defined ( 'AUTHCODE_QUERY_VAR' ) || define( 'AUTHCODE_QUERY_VAR', 'authcode=' );
		$email_addr_query_var_len = strlen( EMAIL_ADDR_QUERY_VAR );
		$authcode_query_var_len = strlen( AUTHCODE_QUERY_VAR );

		// Extract query vars from full URL		
		$query_vars_string = substr( $_SERVER['REQUEST_URI'], strpos( $_SERVER['REQUEST_URI'], EMAIL_ADDR_QUERY_VAR ) );
		$query_vars_arr = explode( '&', $query_vars_string );
		
		// Validate query vars
		
		if ( 2 != count( $query_vars_arr ) ) {
			return array( '', '' );
		}
		else {
			$email_addr = substr( $query_vars_arr[0], $email_addr_query_var_len );
			$authcode = substr( $query_vars_arr[1], $authcode_query_var_len );
			
			if ( ! preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i', $email_addr ) ) {
				return array( '', '' );
			}
			elseif ( ! preg_match( '/([0-9a-zA-Z]{32})/i', $authcode ) ) {
				return array( '', '' );
			}
			else {
				return array( $email_addr, $authcode );
			}
		}
		
	}
		
	/**
	 * Create fake page object, to apply blog's current theme page template to 
	 *	all Post Notif-related page data.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	array	$posts	The current (pseudo) page.
	 * @param	string	$content_function	The function that generates content for current (pseudo) page.
	 * @param	array	$params_arr	The parameters for DB operations and page title/greeting.
	 * @return	array	The current (pseudo) page.
	 */	
	private function create_fake_page( $posts, $content_function, $params_arr ) {
			  
		$posts = null;
		
		$post = new stdClass();
		$post->post_content = $this->$content_function( $params_arr );
		$post->post_title = $params_arr['page_title'];
		
		//	Add page object properties to prevent attributes (category, author, and
		//		post date/time) and functionality (add comment) from appearing on
		//		subscriber preferences pages
		$post->post_type = 'page';
		$post->comment_status = 'closed';
		
		$posts[] = $post;
		
		return $posts;

	}
	
	/**
	 * Retrieve subscriber's selected preferences, prep preferences page with
	 *	admin-defined page template variables, and render page.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	array	$params_arr	The parameters for DB operations and page title/greeting.
	 * @return	string	The HTML to render current (pseudo) page.
	 */	
 	private function render_preferences_page( $params_arr ) {

 		global $wpdb;

 		// Tack prefix on to table names
 		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		$email_addr = $params_arr['email_addr'];
		$authcode = $params_arr['authcode'];
		$subscriber_id = $params_arr['subscriber_id'];
  
  		// Get user's selected preferences   	
  		$selected_cats_arr = $wpdb->get_results( 
  			"
  				SELECT cat_id 
  				FROM $post_notif_sub_cat_tbl
  				WHERE id = $subscriber_id
  				ORDER BY cat_id
  			"
  		);

  		$category_selected_arr = array();
  		foreach ( $selected_cats_arr as $cat_row ) {
  			$category_selected_arr[] = $cat_row->cat_id;
  		}
   	
  		$sub_prefs_greeting = $params_arr['page_greeting'];
   	
		// Retrieve options to populate page
   	
		// @@blogname is a valid variable for both preferences selection instructions AND
		//		unsubscribe link label
		$post_notif_options_arr = get_option( 'post_notif_settings' );
		$sub_pref_selection_instrs = $post_notif_options_arr['sub_pref_selection_instrs'];
		$sub_pref_selection_instrs = str_replace( '@@blogname', get_bloginfo( 'name' ), $sub_pref_selection_instrs );
		$unsub_link_label = $post_notif_options_arr['unsub_link_label'];
		$unsub_link_label = str_replace( '@@blogname', get_bloginfo( 'name' ), $unsub_link_label );

  		// Generate subscription preferences page contents
  		$post_notif_sub_prefs_pg = '';
  		ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-public-display-sub-prefs.php' );
  		$post_notif_sub_prefs_pg .= ob_get_clean();
  		
  		return $post_notif_sub_prefs_pg;	

  	}
    
	/**
	 * Detect manage preferences URL.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @return	bool	Is this a manage_preferences URL?
	 */	
	private function detect_manage_preferences_url() {
		  
		// If this is a manage preferences URL return true
		return false !== strpos( $_SERVER['REQUEST_URI'], '/post_notif/manage_prefs' );	

	}

	/**
	 * Add hook to fire when manage preferences URL is detected.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function add_filter_for_manage_preferences() {
			  
		add_filter( 'the_posts', array( $this, 'create_manage_preferences_page' ) );
		
	}
	
	/**
	 * Prep manage preferences page with admin-defined page template variables.
	 *
	 * @since	1.0.0
	 * @param	array	$posts	The current (pseudo) page.
	 * @return	array	The current (pseudo) page, rendered.
	 */	
	public function create_manage_preferences_page( $posts ) {
	
		// Retrieve options to populate page
			  
		// @@blogname is a valid variable for BOTH page title and page greeting
		$post_notif_options_arr = get_option( 'post_notif_settings' );
		$curr_sub_prefs_page_title = $post_notif_options_arr['curr_sub_prefs_page_title'];
		$curr_sub_prefs_page_title = str_replace( '@@blogname', get_bloginfo( 'name' ), $curr_sub_prefs_page_title );
		$curr_sub_prefs_page_greeting = $post_notif_options_arr['curr_sub_prefs_page_greeting'];
		$curr_sub_prefs_page_greeting = str_replace( '@@blogname', get_bloginfo( 'name' ), $curr_sub_prefs_page_greeting );

		$params_arr = $this->create_preferences_page( $curr_sub_prefs_page_title, $curr_sub_prefs_page_greeting );
		if ( $params_arr ) {

			// Create fake page
			return $this->create_fake_page( $posts, 'render_preferences_page', $params_arr );
		}
		// implicit else: bad URL, page not found will be displayed
		
	}

	/**
	 * Process both manage preferences AND update preferences URLs, and prep
	 *	preferences page settings.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	string	$page_title	The page title for the current (pseudo) page.
	 * @param	string	$page_greeting	The page greeting for the current (pseudo) page.
	 * @return	array	The parameters for DB operations and page title/greeting.
	 */	
	private function create_preferences_page( $page_title, $page_greeting ) {
			  
		global $wpdb;

		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Get query variables from URL
		list( $email_addr, $authcode ) = $this->get_query_vars();
		
		// Get subscriber
		$subscriber_id = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT id FROM " . $post_notif_subscriber_tbl . " WHERE email_addr = %s AND authcode = %s"
				,$email_addr
				,$authcode
			)
		);   	   	
		if ( null != $subscriber_id ) {
			  			
			// This IS a valid authcode
			
			$params_arr = array(
				'email_addr' => $email_addr
				,'authcode' => $authcode
				,'subscriber_id' => $subscriber_id
				,'page_title' => $page_title
				,'page_greeting' => $page_greeting
			);
			
			return $params_arr;
		}
			  
	}   
	  
	/**
	 * Detect update preferences URL.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @return	bool	Is this an update preferences URL?
	 */	
	private function detect_update_preferences_url() {
			  
		// If this is an update preferences URL return true
		return false !== strpos( $_SERVER['REQUEST_URI'], '/post_notif/update_prefs' );
		
	}

	/**
	 * Add hook to fire when update preferences URL is detected.
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function add_filter_for_update_preferences() {

		add_filter( 'the_posts', array( $this, 'create_update_preferences_page' ) );
		
	}
	
	/**
	 * Prep update preferences page with admin-defined page template variables.
	 *
	 * @since	1.0.0
	 * @param	array	$posts	The current (pseudo) page.
	 * @return	array	The current (pseudo) page, rendered.
	 */	
	public function create_update_preferences_page( $posts ) {

		// Retrieve options to populate page
		
		// @@blogname is a valid variable for BOTH page title and page greeting
		$post_notif_options_arr = get_option( 'post_notif_settings' );
		$sub_prefs_updated_page_title = $post_notif_options_arr['sub_prefs_updated_page_title'];
		$sub_prefs_updated_page_title = str_replace( '@@blogname', get_bloginfo( 'name' ), $sub_prefs_updated_page_title );
		$sub_prefs_updated_page_greeting = $post_notif_options_arr['sub_prefs_updated_page_greeting'];
		$sub_prefs_updated_page_greeting = str_replace( '@@blogname', get_bloginfo( 'name' ), $sub_prefs_updated_page_greeting );

		$params_arr = $this->create_preferences_page( $sub_prefs_updated_page_title, $sub_prefs_updated_page_greeting );
		if ( $params_arr ) {

			// Create fake page
			return $this->create_fake_page( $posts, 'render_preferences_page', $params_arr );			
		}
		// implicit else: bad URL, page not found will be displayed
			
	}

	/**
	 * Update subscriber's preferences based on their selections.
	 *
	 * @since	1.0.0
	 */
	public function process_preferences_update() {
		  	
		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
	
		// Define checkbox prefix
		$post_notif_cat_checkbox_prefix = 'chkCatID_';
	
		$subscriber_row = $wpdb->get_row( 
			$wpdb->prepare( 
				"SELECT * FROM " . $post_notif_subscriber_tbl . " WHERE email_addr = %s AND authcode = %s"
				,$_POST['hdnEmailAddr']
				,$_POST['hdnConfCd']
			)
			,ARRAY_A
		);		
		if ( null != $subscriber_row ) {
			  
			// This IS a valid authcode
   	
			// Update user's subscriber row to reflect (pending) update to preferences has occurred
			$result = $wpdb->update( 
				$post_notif_subscriber_tbl 
				,array( 
					'last_modified' => date("Y-m-d H:i:s")
				),
				array( 
					'id' => $subscriber_row['id']
				)    			
			);
			  
			// Delete user's existing preferences	
			$result = $wpdb->delete( 
				$post_notif_sub_cat_tbl 
				,array( 
					'id' => $subscriber_row['id']
				)    			
			);

			// For each selected category on submitted form:
			// 	Insert category into preferences table
			$category_selected_arr = array();
			foreach ( $_POST as $post_notif_field_name => $post_notif_value ) {
				if ( ! ( strncmp( $post_notif_field_name, $post_notif_cat_checkbox_prefix, strlen( $post_notif_cat_checkbox_prefix ) ) ) ) {
						  
					// This is a Category ID checkbox
					if ( isset( $post_notif_field_name ) ) {
				
						// Checkbox IS selected, insert					
						$result = $wpdb->insert(
							$post_notif_sub_cat_tbl 
							,array( 
								'id' => $subscriber_row['id']
								,'cat_id' => $post_notif_value
							)
						);
						if ( 0 == $post_notif_value ) {
								  
							// This is "All" pseudo-category, so only add a single row in prefs tbl
							break;		  
						}
					}					  
				}
			}
	 	
			// Redirect to manage_prefs // for the sake of nice URL :)
			// NOTE: Had to use header() because both wp_redirect AND wp_safe_redirect 
			//		filter out the "@" in the email addr which in turn caused rewrite 
			//		rule to fail, resulting in page not found
			
			// Generate generic subscriber URL base
			$subscriber_url_template = Post_Notif_Misc::generate_subscriber_url_base();
			
			// Tailor update prefs URL for current subscriber
			$subscriber_url = $subscriber_url_template . '?email_addr=' . $subscriber_row['email_addr'] . '&authcode=' . $subscriber_row['authcode'];
			$prefs_url = str_replace( 'ACTION_PLACEHOLDER', 'update_prefs', $subscriber_url );
			header( 'Location: ' . $prefs_url );
			exit;
		}
		
	}
   
	/**
	 * Detect unsubscribe URL.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @return	bool	Is this an unsubscribe URL?
	 */	
	private function detect_unsubscribe_url() {
		  
		// If this is an unsubscribe URL return true
		return false !== strpos( $_SERVER['REQUEST_URI'], '/post_notif/unsubscribe' );		

	}
   
	/**
	 * Add hook to fire when unsubscribe URL is detected.
	 *
	 * @since	1.0.0
	 * @access	private
	 */	
	private function add_filter_for_unsubscribe() {

		add_filter( 'the_posts', array( $this, 'create_unsubscribe_page' ) );
			  		 
	}
	
	/**
	 * Process unsubscribe URL and prep unsubscription confirmed page with
	 *	admin-defined page template variables.
	 *
	 * @since	1.0.0
	 * @param	array	$posts	The current (pseudo) page.
	 * @return	array	The current (pseudo) page, rendered.
	 */	
	public function create_unsubscribe_page( $posts ) {
  
		global $wpdb;
		 
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Get query variables from URL
		list( $email_addr, $authcode ) = $this->get_query_vars();
	 
		// Get subscriber
		$subscriber_id = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT id FROM " . $post_notif_subscriber_tbl . " WHERE email_addr = %s AND authcode = %s"
				,$email_addr
				,$authcode
			)
		);
		if ( null != $subscriber_id ) {
   		  
   		// This IS a valid authcode
			  
   		// Retrieve options to populate page
			
			// @@blogname is a valid variable for BOTH page title and greeting
			$post_notif_options_arr = get_option( 'post_notif_settings' );
			$unsub_confirmation_page_title = $post_notif_options_arr['unsub_confirmation_page_title'];
			$unsub_confirmation_page_title = str_replace( '@@blogname', get_bloginfo( 'name' ), $unsub_confirmation_page_title );
			$unsub_confirmation_page_greeting = $post_notif_options_arr['unsub_confirmation_page_greeting'];
			$unsub_confirmation_page_greeting = str_replace( '@@blogname', get_bloginfo( 'name' ), $unsub_confirmation_page_greeting );

			$params_arr = array(
				'email_addr' => $email_addr
				,'authcode' => $authcode
				,'subscriber_id' => $subscriber_id
				,'page_title' => $unsub_confirmation_page_title
				,'page_greeting' => $unsub_confirmation_page_greeting
			);

			// Create fake page
			return $this->create_fake_page( $posts, 'render_unsubscribe_page', $params_arr );
		}
		
	}   

	/**
	 * Delete subscriber, delete the categories they were subscribed to, and 
	 *	render unsubscription confirmed page.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	array	$params_arr	The parameters for DB operations and page title/greeting.
	 * @return	string	The HTML to render current (pseudo) page.
	 */
	private function render_unsubscribe_page( $params_arr ) {
		
		global $wpdb;

		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
 
		$email_addr = $params_arr['email_addr'];
		$authcode = $params_arr['authcode'];
		$subscriber_id = $params_arr['subscriber_id'];
   	
  		// Delete subscriber's preferences
  		$result = $wpdb->delete( 
  			$post_notif_sub_cat_tbl
  			,array( 
  				'id' => $subscriber_id
  			)    			
  		);
   	
  		// Delete subscriber
  		$result = $wpdb->delete( 
  			$post_notif_subscriber_tbl 
  			,array( 
  				'id' => $subscriber_id
  			)    			
  		);
    	
  		$unsub_greeting = $params_arr['page_greeting'];
  		   		
  		// Generate unsubscribed page contents
  		$post_notif_unsub_pg = '';
  		ob_start();
  		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-public-display-unsub.php' );
  		$post_notif_unsub_pg .= ob_get_clean();
   		
  		return $post_notif_unsub_pg;	  	  
   	
   	}

}
