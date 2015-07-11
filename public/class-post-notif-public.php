<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the public-facing
 *	JavaScript.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/public
 * @author     Devon Ostendorf <devon@devonostendorf.com>
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-notif-public.js', array( 'jquery' ), $this->version, false );

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
	 *	@return	array	All widgets.
	 */
	public function hide_post_notif_widgets( $all_widgets ) {

		foreach ( $all_widgets as $sidebar_key => $sidebar ) {
			if ( $sidebar_key != 'wp_inactive_widgets' ) {
				if ( count( $sidebar ) ) {
						  
					// Sidebar contains widgets, so iterate through them, looking for 
					//		Post Notif and Recent Posts widgets
					foreach ( $sidebar as $widget_index => $widget ) {
						if ( ( strpos( $widget, 'post-notif' ) !== false ) 
						OR ( strpos( $widget, 'recent-posts' ) !== false ) ) {
							unset( $all_widgets[$sidebar_key][$widget_index] );
						}
					}
				}
			}
		}
		
		return $all_widgets;
		
	}


	// Functions related to handling Post Notif URLs
	
	/**
	 * Add Post Notif-specific query vars.
	 *
	 * @since	1.0.0
	 * @param	array	$vars	The collection of global query vars.
	 * @return	array	The collection of global query vars.
	 */	
	public function add_query_vars( $vars ) {
		
		$vars[] = "email_addr";
		$vars[] = "authcode";
		
		return $vars;

	}
		
	/**
	 * Route Post Notif-related URL to appropriate handler.
	 *
	 * @since	1.0.0
	 */	
	public function url_controller() {
		  
		global $wp_query;
			  
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
			
		add_filter( 'the_posts', array ( $this, 'create_subscription_confirmed_page' ) );
	 
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
		global $wp_query;

		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Get parms passed in URL
		
		if ( isset( $wp_query->query_vars['email_addr'] ) ) {
			$email_addr = $wp_query->query_vars['email_addr'];
		}

		if ( isset( $wp_query->query_vars['authcode'] ) ) {
		 	$authcode = $wp_query->query_vars['authcode'];
		}
	 
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
   			$after_conf_email_subject = html_entity_decode(  $after_conf_email_subject, ENT_QUOTES, 'UTF-8' );
   			
   			$after_conf_email_body = $post_notif_options_arr['eml_to_sub_after_conf_body'];
   			$after_conf_email_body = str_replace( '@@blogname', get_bloginfo('name'), $after_conf_email_body );
   			$after_conf_email_body = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $after_conf_email_body );

				// Set sender name and email address
				$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
					. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
  		
   			// Specify HTML-formatted email
   			$headers[] = 'Content-Type: text/html; charset=UTF-8';

   			//	Physically send email
   				
   			// Tailor links (change prefs, unsubscribe) to subscriber
  				// Include or omit trailing "/", in URLs, based on blog's current permalink settings
   			$permalink_structure = get_option( 'permalink_structure', '' );
   			if ( empty( $permalink_structure ) || ( ( substr( $permalink_structure, -1) ) == '/' ) ) {
   				$prefs_url = get_site_url() . '/post_notif/manage_prefs/?email_addr=' . $subscriber->email_addr . '&authcode=' . $subscriber->authcode;
   				$unsubscribe_url = get_site_url() . '/post_notif/unsubscribe/?email_addr=' . $subscriber->email_addr . '&authcode=' . $subscriber->authcode;
   			}
   			else {
    				$prefs_url = get_site_url() . '/post_notif/manage_prefs?email_addr=' . $subscriber->email_addr . '&authcode=' . $subscriber->authcode;
   				$unsubscribe_url = get_site_url() . '/post_notif/unsubscribe?email_addr=' . $subscriber->email_addr . '&authcode=' . $subscriber->authcode;
   			}

   			$after_conf_email_body = str_replace( '@@firstname', ($subscriber->first_name != '[Unknown]') ? $subscriber->first_name : __( 'there', 'post-notif' ), $after_conf_email_body );
   			$after_conf_email_body = str_replace( '@@prefsurl', '<a href="' . $prefs_url . '">' . $prefs_url . '</a>', $after_conf_email_body );
    			$after_conf_email_body = str_replace( '@@unsubscribeurl', '<a href="' . $unsubscribe_url . '">' . $unsubscribe_url . '</a>', $after_conf_email_body );
    				
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
			  
		add_filter('the_posts', array ( $this, 'create_manage_preferences_page' ) );
		
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
		global $wp_query;

   	// Tack prefix on to table names
   	$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
   	$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Get parms passed in URL

		if ( isset( $wp_query->query_vars['email_addr'] ) ) {
			$email_addr = $wp_query->query_vars['email_addr'];
		}

		if ( isset( $wp_query->query_vars['authcode'] ) ) {
		 	$authcode = $wp_query->query_vars['authcode'];
		}
		
		// Get subscriber
		$subscriber_id = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT id FROM " . $post_notif_subscriber_tbl . " WHERE email_addr = %s AND authcode = %s"
				,$email_addr
				,$authcode
			)
		);   	   	
   	if ( $subscriber_id != null ) {
			  			
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

		add_filter('the_posts', array ( $this, 'create_update_preferences_page' ) );
		
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
		if ( $subscriber_row != null ) {
			  
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
				if ( !(strncmp($post_notif_field_name, $post_notif_cat_checkbox_prefix, strlen( $post_notif_cat_checkbox_prefix ) ) ) ) {
						  
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
						if ( $post_notif_value == 0 ) {
								  
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
			
   		// Include or omit trailing "/", in URL, based on blog's current permalink settings
   		$permalink_structure = get_option( 'permalink_structure', '' );
   		if ( empty( $permalink_structure ) || ( ( substr( $permalink_structure, -1) ) == '/' ) ) {
				header( 'Location: ' . site_url() . '/post_notif/update_prefs/?email_addr=' . $subscriber_row['email_addr'] . '&authcode=' . $subscriber_row['authcode'] );
			}
			else {
				header( 'Location: ' . site_url() . '/post_notif/update_prefs?email_addr=' . $subscriber_row['email_addr'] . '&authcode=' . $subscriber_row['authcode'] );
			}
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

		add_filter( 'the_posts', array ( $this, 'create_unsubscribe_page' ) );
			  		 
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
		global $wp_query;
		 
   	// Tack prefix on to table names
   	$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
   	$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Get parms passed in URL

		if ( isset( $wp_query->query_vars['email_addr'] ) ) {
		 	$email_addr = $wp_query->query_vars['email_addr'];
		}

		if ( isset( $wp_query->query_vars['authcode'] ) ) {
		 	$authcode = $wp_query->query_vars['authcode'];
		}
	 
		// Get subscriber
		$subscriber_id = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT id FROM " . $post_notif_subscriber_tbl . " WHERE email_addr = %s AND authcode = %s"
				,$email_addr
				,$authcode
			)
		);
   	if ( $subscriber_id != null ) {
   		  
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
