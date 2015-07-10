<?php 

/**
 * The admin-specific functionality of the plugin.
 *					
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the admin-specific
 *	JavaScript.
 *
 * @since      1.0.0
 * @package    Post_Notif
 * @subpackage Post_Notif/admin
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Admin {
		  	
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
	 * @param	string	$plugin_name	The name of this plugin.
	 * @param	string	$version	The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since	1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Post_Notif_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Post_Notif_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-notif-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Call Post Notif Updater class in case options or tables need updating.
	 *
	 * @since	1.0.4
	 */	
	public function update_check() {
	
		// Get current installed plugin DB version
		$installed_post_notif_db_version = intval( get_option( 'post_notif_db_version', 0 ) );
		
		// Check for updates and apply, if needed
		$post_notif_updater = new Post_Notif_Updater( $installed_post_notif_db_version );
		$post_notif_updater->apply_updates_if_needed();
		
	}
	
		
	// Functions related to adding Post Notif meta box to Edit Post page
	
	/**
	 * Add meta box to Edit Post page.
	 *
	 * @since	1.0.0
	 */	
	public function add_post_notif_meta_box() {
	
		add_meta_box(
			'post_notif'
			,'Post Notif'
			,array( $this, 'render_post_notif_meta_box' )
			,'post'
		);
		
	}
	
	/**
	 * Render meta box on Edit Post page.
	 *
	 * @since	1.0.0
	 * @param	WP_Post	$post	The object for the current post/page.
	 */
	public function render_post_notif_meta_box( $post ) {
		
		if ( get_post_status($post->ID) == 'publish' ) {
			
			// Post has been published, allow Post Notif send
				  
			global $wpdb;			
		
			// Tack prefix on to table name
			$post_notif_post_tbl = $wpdb->prefix.'post_notif_post';
			
			echo '<input type="hidden" name="hdnPostID" id="id_hdnPostID" value="' . $post->ID . '" />';

			$notif_sent_dttm = $wpdb->get_var( 
				$wpdb->prepare(
					"SELECT notif_sent_dttm FROM " . $post_notif_post_tbl . " WHERE post_id = %d"
					,$post->ID
				)		
			);
			if ( $notif_sent_dttm == null ) {
		
				// Display Send Post Notif button
				echo '<input type="button" name="btnSendNotif" id="id_btnSendNotif" value="' . __( 'Send', 'post-notif' ) . '" />';
				echo '	<span id="id_spnPostNotifLastSent"></span>';
			}
			else {
				  
				// Already sent, display RESEND Post Notif button and last sent date
				echo '<input type="button" name="btnSendNotif" id="id_btnSendNotif" value="' . __( 'RESEND', 'post-notif' ) . '" />';
				echo '<span id="id_spnPostNotifLastSent">Last sent: ' . date( "F j, Y", strtotime( $notif_sent_dttm ) )
					. " at "
					. date( "g:i:s A", strtotime( $notif_sent_dttm ) )
					. "</span>"
				;
			}
		}
		else {
			_e( 'Post has not yet been published.', 'post-notif' );
		}
		
	}
	
	/**
	 * Enqueue AJAX script that fires when "Send Notif" button (in meta box on Edit Post page) is pressed.
	 *
	 * @since	1.0.0
	 * @param	string	$hook	The current page name.
	 *	@return	null	If this is not post.php page
	 */
	public function send_post_notif_enqueue( $hook ) {
	
		if ( 'post.php' != $hook ) {
				  
			return;
		}
		   
		$post_notif_send_nonce = wp_create_nonce( 'post_notif_send' );
		wp_localize_script(
			$this->plugin_name
			,'post_notif_send_ajax_obj'
			,array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
				,'nonce' => $post_notif_send_nonce
			)
		);  

	}
	
	/**
	 * Handle AJAX event sent when "Send Notif" button (in meta box on Edit Post page) is pressed.
	 *
	 * @since	1.0.0
	 *	@return	null	If unable to send emails (already in process)
	 */	
	public function send_post_notif_ajax_handler() {

		// Confirm matching nonce
		check_ajax_referer( 'post_notif_send' );
   
		global $wpdb;
   
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
		$post_notif_post_tbl = $wpdb->prefix.'post_notif_post';

		// Get current post ID
		$post_id = $_POST['post_id'];

		if ( ! ( $wpdb->get_var( "SELECT IS_FREE_LOCK('" . $wpdb->prefix.'post_notif_send_lock' . "')" ) ) ) {
   	
			// Already emailing			
			return;
		}
		else {
  
			// Set lock
			if ( ( $wpdb->get_var( "SELECT GET_LOCK('" . $wpdb->prefix.'post_notif_send_lock' . "',2)" ) ) != 1 ) {
   			  
				// Lock set failed				
				return;
			}
			else {
   			  
				// All is well
    		
				// Find categories this post is associated with
				$post_categories_arr = wp_get_post_categories( $post_id ); 
				$post_category_clause = '(';
				foreach ( $post_categories_arr as $post_category ) {   		
					$post_category_clause .= $post_category . ',';
				}
   		
				// Tack on "All" category too
				$post_category_clause .= '0)';

    			// Find subscribers to this/these ^^^ category/s
    			$subscribers_arr = $wpdb->get_results(
    				"
   					SELECT $post_notif_subscriber_tbl.id AS id
   						,email_addr 
   						,first_name
   						,authcode
   					FROM $post_notif_subscriber_tbl
   					JOIN $post_notif_sub_cat_tbl
   					ON $post_notif_subscriber_tbl.id = $post_notif_sub_cat_tbl.id
   					WHERE confirmed = 1
   						AND cat_id IN $post_category_clause
   					ORDER BY $post_notif_subscriber_tbl.id
   				"
   			);
   		   		
   			//	Compose emails
   		
   			$post_notif_options_arr = get_option( 'post_notif_settings' );
   		
   			// Replace variables in both the post notif email subject and body 
   		
   			$post_attribs = get_post( $post_id ); 
   			$post_title = $post_attribs->post_title;
   		
   			// NOTE: This is in place to minimize chance that, due to email client settings, subscribers
   			//		will be unable to see and/or click the URL links within their email
   			$post_permalink = get_permalink( $post_id );

   			$post_notif_email_subject = $post_notif_options_arr['post_notif_eml_subj'];
   			$post_notif_email_subject = str_replace( '@@blogname', get_bloginfo('name'), $post_notif_email_subject );
   			$post_notif_email_subject = str_replace( '@@posttitle', $post_title, $post_notif_email_subject );
 
   			// Tell PHP mail() to convert both double and single quotes from their respective HTML entities to their applicable characters
   			$post_notif_email_subject = html_entity_decode (  $post_notif_email_subject, ENT_QUOTES, 'UTF-8' );
   			
   			$post_notif_email_body_template = $post_notif_options_arr['post_notif_eml_body'];
   			$post_notif_email_body_template = str_replace( '@@blogname', get_bloginfo('name'), $post_notif_email_body_template );
   			$post_notif_email_body_template = str_replace( '@@posttitle', $post_title, $post_notif_email_body_template );
   			$post_notif_email_body_template = str_replace( '@@permalink', '<a href="' . $post_permalink . '">' . $post_permalink . '</a>', $post_notif_email_body_template );
   			$post_notif_email_body_template = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $post_notif_email_body_template );

				// Set sender name and email address
				$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
					. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
  		
   			// Specify HTML-formatted email
   			$headers[] = 'Content-Type: text/html; charset=UTF-8';

   			//	Physically send emails
   			foreach ( $subscribers_arr as $subscriber ) {
   				
   				// Iterate through subscribers, tailoring links (change prefs, unsubscribe) to each subscriber
   				// NOTE: This is in place to minimize chance that, due to email client settings, subscribers
   				//		will be unable to see and/or click the URL links within their email

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

   				$post_notif_email_body = $post_notif_email_body_template;
   				$post_notif_email_body = str_replace('@@firstname', ($subscriber->first_name != '[Unknown]') ? $subscriber->first_name : __( 'there', 'post-notif' ), $post_notif_email_body);
   				$post_notif_email_body = str_replace('@@prefsurl', '<a href="' . $prefs_url . '">' . $prefs_url . '</a>', $post_notif_email_body);
    				$post_notif_email_body = str_replace('@@unsubscribeurl', '<a href="' . $unsubscribe_url . '">' . $unsubscribe_url . '</a>', $post_notif_email_body);
    				
   				$mail_sent = wp_mail( $subscriber->email_addr, $post_notif_email_subject, $post_notif_email_body, $headers );   			
   			}

   			$notif_row_inserted = $wpdb->insert(
   				$post_notif_post_tbl 
   				,array(
   					'post_id' => $post_id
   					,'notif_sent_dttm' => date( "Y-m-d H:i:s" ) 
   					,'sent_by' => get_current_user_id()
   				)
   			);   		
   			
   			// Release lock
   			$wpdb->get_var( "SELECT RELEASE_LOCK('" . $wpdb->prefix.'post_notif_send_lock' . "')" );
   		}

   		if ( $notif_row_inserted ) {
   			$post_notif_sent_msg = __( 'Post notification has been sent for this post!', 'post-notif' );
   		}
   		else {
   			$post_notif_sent_msg = __( 'Post notification FAILED for this post!', 'post-notif' );
   		}
   		wp_send_json( array( 'message' => $post_notif_sent_msg ) );
   	}
   	
		// All ajax handlers should die when finished
    	wp_die(); 
   	
   }

	
	// Functions related to adding Post Notif submenu to Settings menu
	
	/**
	 * Add Post Notif menu item to Settings menu.
	 *
	 * @since	1.0.0
	 */	
	public function add_post_notif_options_page() {
	
		add_options_page(
			__( 'Post Notif Settings', 'post-notif' )
			,'Post Notif'
			,'manage_options'
			,'post-notif-slug'
			,array( $this, 'render_post_notif_options_page' )
		);
		
	}
	
	/**
	 * Register set of options configurable via Settings >> Post Notif on admin menu sidebar.
	 *
	 * @since	1.0.0
	 */	
	public function register_post_notif_settings() {
     
		register_setting(
			'post_notif_settings_group'
         ,'post_notif_settings'
      );
      
   }
   
	/**
	 * Render Post Notif options page.
	 *
	 * @since	1.0.0
	 */	
	public function render_post_notif_options_page() {

   	$post_notif_options_pg = '';
    	ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-admin-display-options.php' );
		$post_notif_options_pg .= ob_get_clean();
		print $post_notif_options_pg;	
		
	}

	
	// Functions related to adding Post Notif top level menu to the admin menu sidebar
	
	/**
	 * Add Post Notif top level menu to the admin menu sidebar.
	 *
	 * @since	1.0.0
	 */	
	public function add_post_notif_admin_menu() {
			  
		// Admin can override default admin menu position of this menu
		$post_notif_options_arr = get_option( 'post_notif_settings' );
			
		// NOTE: This will not have a selectable page associated with it (due to non-existent 'menu_only_no_selectable_item' capability)
		add_menu_page(
			'menu_only_no_selectable_item'
			,'Post Notif'
			,'menu_only_no_selectable_item'
			,'post-notif-menu'
			,null
			,''
			,$post_notif_options_arr['admin_menu_position']
		);

		add_submenu_page(
			'post-notif-menu'
			,__( 'Import Subscribers', 'post-notif' )
			,__( 'Import Subscribers', 'post-notif' )
			,'manage_options'	// ONLY admin role has this capability
			,'post-notif-import-subs'
			,array( $this, 'render_import_subscribers_page' )
		);

		add_submenu_page(
			'post-notif-menu'
			,__( 'Staged Subscribers', 'post-notif' )
			,__( 'Staged Subscribers', 'post-notif' )
			,'manage_options'	// ONLY admin role has this capability
			,'post-notif-staged-subs'
			,array( $this, 'define_staged_subscribers_page' )
		);

		add_submenu_page(
			'post-notif-menu'
			,__( 'Manage Subscribers', 'post-notif' )
			,__( 'Manage Subscribers', 'post-notif' )
			,'manage_options'	// ONLY admin role has this capability
			,'post-notif-manage-subs'
			,array( $this, 'define_manage_subscribers_page' )
		);
		
		add_submenu_page(
			'post-notif-menu'
			,__( 'View Subscribers', 'post-notif' )
			,__( 'View Subscribers', 'post-notif' )
			,'edit_others_posts'	// admin and editor roles have this capability
			,'post-notif-view-subs'
			,array( $this, 'define_view_subscribers_page' )			
		);

		add_submenu_page(
			'post-notif-menu'
			,__( 'View Post Notifs Sent', 'post-notif' )
			,__( 'View Post Notifs Sent', 'post-notif' )
			,'edit_others_posts'	// admin and editor roles have this capability
			,'post-notif-view-posts-sent'			
			,array( $this, 'render_view_post_notifs_sent_page' )
		);

	}

	/**
	 * Render Import Subscribers page.
	 *
	 * @since	1.0.4
	 */	
	public function render_import_subscribers_page() {

		// Render page	  
		$post_notif_import_subs_pg = '';
    	ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-admin-import-subs.php' );
		$post_notif_import_subs_pg .= ob_get_clean();
		print $post_notif_import_subs_pg;	
	  
	}
	
	/**
	 * Perform validation and import of raw subscriber data.
	 *
	 * @since	1.0.4
	 */	
	public function process_subscriber_import() {

		// Perform validation and (attempt) loading to staging tables
		
		global $wpdb;
		
		$row_delimiter = chr( 13 );
		
		// Tack prefix on to table names 
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';
		
		// Split out single textarea, by delimiter (newline), into array elements
		$subscriber_arr = explode( $row_delimiter, trim( $_POST['tarSubscriberData'] ) );
		
		if ( count( $subscriber_arr > 0 ) ) {
				  
			// There IS subscriber data to validate and (potentially) load
			
			$import_row_number = 1;
				  
			// Retrieve active system categories (for validation below)
			$category_args = array(
				'exclude' => 1,		// Omit Uncategorized
				'orderby' => 'name',
				'order' => 'ASC',
				'hide_empty' => 0
			);
			$existing_categories = get_categories( $category_args );
			$existing_categories_arr = array();
			foreach ( $existing_categories as $existing_category ) {			
				$existing_categories_arr[] = $existing_category->cat_ID;
			}
			
			// Attempt to process each import row
			foreach ( $subscriber_arr as $subscriber_row ) {
					  
				// Assume all is well until proven otherwise
				$import_status = 'S';
				$status_message = '';
				$category_arr = array();
			
				// Split out subscriber string, by delimiter (comma), into array
				//		elements corresponding to staging tables' columns
				$trimmed_subscriber_row = trim( $subscriber_row );
				if ( !empty( $trimmed_subscriber_row ) ) {
						  
					// This is NOT a blank row - attempt to process
					$subscriber_data_arr = explode( ',', $trimmed_subscriber_row );
					$num_subscriber_fields = count( $subscriber_data_arr );
					
					// Email address is ONLY required field
					// If there are only 2 fields it is assumed they are email address and first name
					// If there are 3 or more fields it is assumed they are email address, first name, and one or more category IDs
					
					$email_addr = trim( $subscriber_data_arr[0] );
					
					// Validate email address
						
					if ( $email_addr == '' ) {
								  
						// Blank email address is a showstopper
						$import_status = 'V';
						$status_message = 'Blank email address.';		  
					}
					else {
						if ( strlen( $email_addr ) > 100 ) {
							$email_addr = substr( $email_addr, 0, 100 );
							
							// Truncated email address is probably not good but as
							//		long as it is in valid format, let the user decide
							//		whether to ignore the warning
							$import_status = 'T';
							$status_message = 'Email address truncated (more than 100 chars).';
						}
						if ( ! preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i' , $email_addr ) ) {
							
							// Invalid email address is a showstopper
							$import_status = 'V';
							$status_message .= ' Invalid email address.';
						}
					}

					if ( $num_subscriber_fields == 1 ) {
							  
						// NO first name field provided:
						
						// Default blank first name
						$first_name = '[Unknown]';
						
						// Default "All" category
						$category_arr[0] = 0;
					}
					else {
						
						// First name field HAS been provided (may be blank)
						$first_name = trim( $subscriber_data_arr[1] );
							  
						// Validate first name
						if ( $first_name == '' ) {
							  
							// Default blank first name
							$first_name = '[Unknown]';
						}
						elseif ( strlen( $first_name ) > 50 ) {
							$first_name = substr( $first_name, 0, 50 );
							
							// Truncated first name should only generate a warning
							//		UNLESS email addr was a showstopper, in which case
							//		do NOT override that hard error
							if ( $import_status == 'S') {
								$import_status = 'T';
							}
							$status_message .= ' First name truncated (more than 50 chars).';
						}
							
						if ( $num_subscriber_fields == 2 ) {
								  
							// No categories provided, default "All" category
							$category_arr[0] = 0;								  
						}
						else {
					
							// Iterate through provided categories
							for ( $subscriber_data_arr_index = 2; $subscriber_data_arr_index < $num_subscriber_fields; $subscriber_data_arr_index++ ) {
								  
								// Do not store empty category values (e.g CSV row ends with ",")
								$category_val = trim( $subscriber_data_arr[$subscriber_data_arr_index] );
								if ( $category_val != '') {
									$category_arr[$subscriber_data_arr_index - 2] = $category_val;
										  
									// Validate categories
									
									if ( !is_numeric( $category_val ) ) {
							
										// Non-numeric value is a showstopper
										$import_status = 'V';
										$status_message .= ' Non-numeric category (' . $category_val . ').';
										$category_arr[$subscriber_data_arr_index - 2] = -1;
									}
									elseif ( ( $category_val != 0 ) && ( !in_array( $category_val, $existing_categories_arr) ) ) {

										// Value that does NOT match and existing category in
										//		system is a showstopper
										$import_status = 'V';
										$status_message .= ' Invalid category (' . $category_val . ').';
										$category_arr[$subscriber_data_arr_index - 2] = -1;
									}
								}
							}
							if ( count( $category_arr ) == 0 ) {
								
								// No categories provided, default "All" category
								$category_arr[0] = 0;								  
							}
						}
					}

					if ( $import_status == 'S' ) {
						$status_message = 'Staged (pending creation)';
					}
					
					// Insert subscriber stage row
					$num_subs_loaded = $wpdb->insert( 
						$post_notif_subscriber_stage_tbl
						,array( 
							'id' => ''
							,'email_addr' => $email_addr
							,'first_name' => $first_name
							,'import_status' => $import_status
							,'status_message' => $status_message
						) 
					);
					
					// Only load categories if a subscriber row was successfully loaded 
					if ( $num_subs_loaded ) {
			
						// Get new subscriber ID
						$subscriber_id = $wpdb->insert_id;
			
						// Insert subscriber cat stage row(s)
						foreach ( $category_arr as $category_key => $category_val ) {
								
							// Insert all VALID categories (invalid categories were
							//		set to -1
							if ( $category_val != -1 ) {
								$num_cats_loaded = $wpdb->insert( 
									$post_notif_sub_stage_cat_tbl
									,array( 
										'id' => $subscriber_id
										,'cat_id' => $category_val
									) 
								);
							}
						}
					}
				
					if ( ( $import_status == 'S' ) && ( isset( $_POST['chkSkipStaging'] ) ) ) {
							  
						// "Skip staging of clean rows?" was set - create actual
						//		subscriber row and real category row(s) too!
						$this->process_single_staged_subscriber_create( $subscriber_id );
					}
									
					// NOTE: Allegedly pre-increment is faster than post?!
					++$import_row_number;
				}				
			}	
		}
		
		// Redirect to Staged Subscribers page
		wp_redirect( site_url() . '/wp-admin/admin.php?page=post-notif-staged-subs' );
		exit;
	}
	
	/**
	 * Define single and bulk actions for Staged Subscribers page.
	 *
	 * @since	1.0.4
	 */	
	public function define_staged_subscribers_page() {

		$available_actions_arr = array(
			'actionable_column_name' => 'first_name'
			,'actions' => array(
				'create' => array(
					'label' => __( 'Create', 'post-notif' )
					,'single_ok' => true
					,'single_conditional' => array(
						'conditional_field' => 'import_status'
						,'field_values' => array(
							'S'
							,'T'
						)
					)
					,'bulk_ok' => true
				)
				,'delete' => array(
					'label' => __( 'Delete', 'post-notif' )
					,'single_ok' => true
					,'bulk_ok' => true
				)
			)
		);
		$this->render_staged_subscribers_page( $available_actions_arr );		
	}

	/**
	 * Render Staged Subscribers page.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @param	array	$available_actions_arr	The available actions for the list table items.
	 */	
	private function render_staged_subscribers_page ( $available_actions_arr ) {
			  	
		global $wpdb;
		
		// Tack prefix on to table names
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';
		
		// Define possible status descrs
		$import_status_descr_arr = array(
			'C' => __( 'Created', 'post-notif' )						// (C)reated a new subscriber
			,'S' => __( 'Staged', 'post-notif' )						// (S)taged a new subscriber for later creation
  			,'T' => __( 'Warning', 'post-notif' )						// (T)runcated column(s)
  			,'U' => __( 'Duplicate email address', 'post-notif' )	//	D(U)plicate email address
  			,'V' => __( 'Import error', 'post-notif' )				// (V)alidation error
  			,'X' => __( 'System error', 'post-notif' )				//	This indicates a system error
		);

		$staged_subscribers_deleted = 0;
		$staged_subscribers_created = 0;
		$form_action = $_SERVER['REQUEST_URI'];		
		$sort_by_category = false;
		
		$affected_subscriber = false;
		
		if ( !empty( $_REQUEST['subscriber'] ) ) {
				  
			// Single action needs to be processed
			$current_action = $_REQUEST['action'];
			$affected_subscriber = $_REQUEST['subscriber'];
		}
		else {				  
			if ( isset( $_REQUEST['doaction'] ) && isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {

				// Bulk action needs to be processed
				$current_action = $_REQUEST['action'];						
			}
			elseif ( isset( $_REQUEST['doaction2'] ) && isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
				
				// Bulk action needs to be processed
				$current_action = $_REQUEST['action2'];
			}
			else {
				$current_action = '';	  
			}
		}
		
		switch ( $current_action ) {
 			case 'create':	  
				
 				// Creates need to be processed
			
				if ( $affected_subscriber ) {
				  
					// Create single staged subscriber
					$staged_subscribers_created = $this->process_single_staged_subscriber_create( $affected_subscriber );
					$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber' ), $_SERVER['REQUEST_URI'] ) );
				}
				else {
				  			 
					// Create multiple (selected) staged subscribers via bulk action
					$staged_subscribers_created = $this->process_multiple_staged_subscriber_create( $_POST );
				}
 			break;
			case 'delete':
					  
				// Delete(s) need to be processed
			
				if ( $affected_subscriber ) {
				  
					// Delete single staged subscriber
					$staged_subscribers_deleted = $this->process_single_staged_subscriber_delete( $affected_subscriber );
					$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber' ), $_SERVER['REQUEST_URI'] ) );
				}
				else {
				  			 
					// Delete multiple (selected) staged subscribers via bulk action
					$staged_subscribers_deleted = $this->process_multiple_staged_subscriber_delete( $_POST );
				}					  
			break;
		}

		// Define list table columns
		
		if ( is_array( $available_actions_arr ) ) {				  
			$columns_arr = array();
			foreach ( $available_actions_arr['actions'] as $single_action_arr ) {
				if ( $single_action_arr['bulk_ok'] == true ) {

					// There are bulk actions, add checkbox column
					$columns_arr['cb'] = '<input type="checkbox" />';	 
					break;
				}
			}
		}	 		
		$columns_arr['first_name'] = __( 'First Name', 'post-notif' );
		$columns_arr['email_addr'] = __( 'Email Address', 'post-notif' );
		$columns_arr['import_status_descr'] = __( 'Status', 'post-notif' );
		$columns_arr['status_message'] = __( 'Message', 'post-notif' );
		$columns_arr['categories'] = __( 'Categories', 'post-notif' );

		// NOTE: Third parameter indicates whether column data is already sorted 
		$sortable_columns_arr = array(
         'first_name' => array( 
         	'first_name'
         	,false
         )
         ,'email_addr' => array(
         	'email_addr'
         	,false
         )
         ,'import_status_descr' => array(
         	'import_status_descr'
         	,false
         )
         ,'status_message' => array(
         	'status_message'
         	,false
         )
         ,'categories' => array(
         	'categories'
         	,false
         )
      );    
				
		if ( !empty( $_REQUEST['orderby'] ) ) {					 
			if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
					  
				// This IS a valid, sortable column
				if ( $_REQUEST['orderby'] != 'categories' ) {
					$orderby = $_REQUEST['orderby'];		 
				}
				else {
					$orderby = 'id';
					$sort_by_category = true;
				
					// Sort by category requires some special handling since category data is not
					//		retrieved by original query
					function usort_reorder( $a, $b ) {
						$order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
						$result = strcmp( $a['categories'], $b['categories'] );
  					
						return ( $order === 'asc' ) ? $result : -$result;
					}
				}
			}
			else {
					  
				// This is NOT a valid, sortable column					  
				$orderby = 'id';
			}
		}
		else {
				  
			// No orderby specified
			$orderby = 'id';
		}
		if ( !empty( $_REQUEST['order'] ) ) {
			if ( $_REQUEST['order'] == 'desc' ) {
				$order = 'desc';
			}
			else {
					  
				// This is NOT a valid order				  
				$order = 'asc';
			}
		}
		else {
			
			// No order specified
			$order = 'asc';
		}
		
		// Get subscribers
		$subscribers_arr = $wpdb->get_results(
			"
   			SELECT 
   				id
   				,first_name
   				,email_addr 
   				,import_status
   				,import_status AS import_status_descr
   				,status_message
   			FROM $post_notif_subscriber_stage_tbl
   			ORDER BY $orderby $order
   		"
   		,ARRAY_A
   	);
   	
   	// Select categories each subscriber is subscribed to AND pass array to page
   	//		for display
 		$args = array(
			'exclude' => 1		// Omit Uncategorized
			,'orderby' => 'name'
			,'order' => 'ASC'
			,'hide_empty' => 0
		);
		$category_arr = get_categories( $args );
		$category_name_arr = array();
		foreach ( $category_arr as $category )
		{
			$category_name_arr[$category->cat_ID] = $category->name;
		}

   	$subscriber_cats_arr = array();
   	foreach ( $subscribers_arr as $sub_key => $sub_val ) {
   		$selected_cats_arr = $wpdb->get_results( 
   			"
   				SELECT cat_id 
   				FROM $post_notif_sub_stage_cat_tbl
   				WHERE id = " . $sub_val['id']
   				. " ORDER BY cat_id
   			"
   		);
   		
   		$cat_string = '';
   		foreach ( $selected_cats_arr as $cat_key => $cat_val ) { 
   			if ($cat_val->cat_id != 0) {
    				$cat_string .= $category_name_arr[$cat_val->cat_id] . ', ';
   			}
   			else {
   				$cat_string = 'All';	  
   				break;
   			}
   		}
  	   	$cat_string = rtrim ( $cat_string, ', ' );   	
  			$subscribers_arr[$sub_key]['categories'] = $cat_string;
  			
  			// Translate import_status to descriptive words/phrases
  			$subscribers_arr[$sub_key]['import_status_descr'] = $import_status_descr_arr[$sub_val['import_status']];
  		}	
		if ( $sort_by_category ) {
				  
			// Special sort for category
			usort( $subscribers_arr, 'usort_reorder' );
		}
   	
		// Build page	  
	
    	$class_settings_arr = array(
    		'singular' => __( 'subscriber', 'post-notif' )
    		,'plural' => __( 'subscribers', 'post-notif' )
    		,'ajax' => false
    		,'available_actions_arr' => $available_actions_arr
    	);
    
    	// Single array containing the various arrays to pass to the list table class constructor
    	$table_data_arr = array(
    		'columns_arr' => $columns_arr
    		,'hidden_columns_arr' => array()
    		,'sortable_columns_arr' => $sortable_columns_arr
    		,'rows_per_page' => 0		// NOTE: Pass 0 for single page with all data (i.e. NO pagination)
    		,'table_contents_arr' => $subscribers_arr    			  
    	);

    	$view_staged_subs_pg_list_table = new Post_Notif_List_Table( $class_settings_arr, $table_data_arr );
		$view_staged_subs_pg_list_table->prepare_items();		
         
      // Render page	  
		$post_notif_view_staged_subs_pg = '';
    	ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-admin-view-staged-subs.php' );
		$post_notif_view_staged_subs_pg .= ob_get_clean();
		print $post_notif_view_staged_subs_pg;	
		
   }
	
   /**
	 * Perform single staged subscriber create.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @param	int	$sub_id	ID of staged subscriber to create.
	 *	@return	int	Number of staged subscribers created.
	 */	
	private function process_single_staged_subscriber_create( $sub_id ) {

		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
		
		// (Attempt to) insert staged subscriber row UNLESS already (C)reated, has
		//		a d(U)plicate email addr error or has a (V)alidation error
		$staged_subscriber_row = $wpdb->get_row(
			$wpdb->prepare(
				"
   				SELECT 
   					id
   					,email_addr 
   					,first_name
   				FROM $post_notif_subscriber_stage_tbl
   				WHERE id = %d
   				AND import_status NOT IN ('C','U','V')
   			"
   			,$sub_id
   		)
   	);
   	
   	if ( $staged_subscriber_row ) {
   			  
   		// Staged subscriber row, to attempt to create, found
		
   		// Does email addr already exist in subscriber table?
    		$subscriber_exists = $wpdb->get_var( 
				"SELECT COUNT(id) FROM " . $post_notif_subscriber_tbl 
				. " WHERE email_addr = '" . $staged_subscriber_row->email_addr . "'"
			);
			if ( $subscriber_exists ) {
				
				// Subscriber DOES already exist
		
				// Update status of stage subscriber row to "U", with message of "Duplicate email address"
				$result = $wpdb->update( 
					$post_notif_subscriber_stage_tbl
					,array( 
						'import_status' => 'U'
						,'status_message' => 'Duplicate email address'
					)
					,array( 
						'id' => $staged_subscriber_row->id
					)    			
				);
			}
			else {
			
				// Subscriber is new

				// Generate authcode			
				$authcode = Post_Notif_Misc::generate_authcode();
						
				// Insert new subscriber row
				$num_subs_created = $wpdb->insert( 
					$wpdb->prefix.'post_notif_subscriber' 
					,array( 
						'id' => ''
						,'email_addr' => $staged_subscriber_row->email_addr
						,'first_name' => $staged_subscriber_row->first_name
						,'confirmed' => 1
						,'last_modified' => date( "Y-m-d H:i:s" )
						,'date_subscribed' => date( "Y-m-d H:i:s" )
						,'authcode' => $authcode
					) 
				);
				if ( $num_subs_created ) {
			
					// Get new subscriber ID
					$subscriber_id = $wpdb->insert_id;
				
					// Get staged category rows for subscriber
					$staged_cats_arr = $wpdb->get_results( 
						"
							SELECT cat_id 
							FROM $post_notif_sub_stage_cat_tbl
							WHERE id = $staged_subscriber_row->id
							ORDER BY cat_id
						"
					);
				
					// Insert category row(s)
					foreach ( $staged_cats_arr as $staged_cat ) {
						$result = $wpdb->insert(
							$post_notif_sub_cat_tbl 
							,array( 
								'id' => $subscriber_id
								,'cat_id' => $staged_cat->cat_id
							)
						);								  
					}
				
					// Update status of stage subscriber row to "C", with 
					//		message of "Successfully created"
					$result = $wpdb->update( 
						$post_notif_subscriber_stage_tbl
						,array( 
							'import_status' => 'C'
							,'status_message' => 'Successfully created'
						)
						,array( 
							'id' => $staged_subscriber_row->id
						)    			
					);
												
					// Return count var
					if ( $num_subs_created ) {
						return $num_subs_created;
					}
					else {
						return 0;
					}
				}
				else {

					// Else, update status of stage subscriber row to "X", with 
					//		message of "System error - try again later"
					$result = $wpdb->update( 
						$post_notif_subscriber_stage_tbl
						,array( 
							'import_status' => 'X'
							,'status_message' => 'System error - try again later'
						)
						,array( 
							'id' => $staged_subscriber_row->id
						)    			
					);
				
					return 0;
					  
				}				
			}
		}
		else {
					  
			// No staged subscriber row found
			return 0;
		}
		
	}	  
	
	/**
	 * Perform multiple subscriber staged subscriber create.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @param	array	$form_post	The collection of global query vars.
	 *	@return	int	Number of staged subscribers created.
	 */	
	private function process_multiple_staged_subscriber_create( $form_post ) {
			  
		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
		
		// Define checkbox prefix
		$create_subscribers_checkbox_prefix = 'chkKey_';
		$subscribers_created = 0;

		// For each selected staged subscriber on submitted form:
		//		Retrieve existing staged subscriber row
		// 	Attempt to insert a new subscriber row with data from staged
		//			subscriber row
		//		Retrieve existing staged subscriber's category row(s)
		// 	Attempt to insert a new category row with data from each staged
		//			subscriber category row
		foreach ( $form_post as $create_subscribers_field_name => $create_subscribers_value ) {
			if ( !(strncmp($create_subscribers_field_name, $create_subscribers_checkbox_prefix, strlen( $create_subscribers_checkbox_prefix ) ) ) ) {
						  
				// This is a Subscriber checkbox
				if ( isset( $create_subscribers_field_name ) ) {
				
					// Checkbox IS selected
					
					// (Attempt to) insert staged subscriber into subscriber table
					//		UNLESS already (C)reated, has a d(U)plicate email addr
					//		error or has a (V)alidation error
					$staged_subscriber_row = $wpdb->get_row(
						$wpdb->prepare(
							"
								SELECT 
									id
									,email_addr 
									,first_name
								FROM $post_notif_subscriber_stage_tbl
								WHERE id = %d
								AND import_status NOT IN ('C','U','V')
							"
							,$create_subscribers_value
						)
					);

					if ( $staged_subscriber_row ) {
   			  
						// Staged subscriber row, to attempt to create, found
		
						// Does email addr already exist in subscriber table?
						$subscriber_exists = $wpdb->get_var( 
							"SELECT COUNT(id) FROM " . $post_notif_subscriber_tbl 
							. " WHERE email_addr = '" . $staged_subscriber_row->email_addr . "'"
						);
						if ( $subscriber_exists ) {
				
							// Subscriber DOES already exist
		
							// Update status of stage subscriber row to "U", with message of "Duplicate email address"
							$result = $wpdb->update( 
								$post_notif_subscriber_stage_tbl
								,array( 
									'import_status' => 'U'
									,'status_message' => 'Duplicate email address'
								)
								,array( 
									'id' => $staged_subscriber_row->id
								)    			
							);
						}
						else {
			
							// Subscriber is new

							// Generate authcode			
							$authcode = Post_Notif_Misc::generate_authcode();
						
							// Insert new subscriber row
							$num_subs_created = $wpdb->insert( 
								$wpdb->prefix.'post_notif_subscriber' 
								,array( 
									'id' => ''
									,'email_addr' => $staged_subscriber_row->email_addr
									,'first_name' => $staged_subscriber_row->first_name
									,'confirmed' => 1
									,'last_modified' => date( "Y-m-d H:i:s" )
									,'date_subscribed' => date( "Y-m-d H:i:s" )
									,'authcode' => $authcode
								) 
							);
							if ( $num_subs_created ) {
			
								// Get new subscriber ID
								$subscriber_id = $wpdb->insert_id;
				
								// Get staged category rows for subscriber
								$staged_cats_arr = $wpdb->get_results( 
									"
										SELECT cat_id 
										FROM $post_notif_sub_stage_cat_tbl
										WHERE id = $staged_subscriber_row->id
										ORDER BY cat_id
									"
								);
				
								// Insert category row(s)
								foreach ( $staged_cats_arr as $staged_cat ) {
									$result = $wpdb->insert(
										$post_notif_sub_cat_tbl 
										,array( 
											'id' => $subscriber_id
											,'cat_id' => $staged_cat->cat_id
										)
									);								  
								}
				
								// Update status of stage subscriber row to "C", with 
								//		message of "Successfully created"
								$result = $wpdb->update( 
									$post_notif_subscriber_stage_tbl
									,array( 
										'import_status' => 'C'
										,'status_message' => 'Successfully created'
									)
									,array( 
										'id' => $staged_subscriber_row->id
									)    			
								);
												
								// Return count var
								if ( $num_subs_created ) {
									// OK, wise-guy, I know you're saying there should never be more than
									//		one subscriber per id!
									$subscribers_created += $num_subs_created;
								}
							}
							else {

								// Else, update status of stage subscriber row to "X", with 
								//		message of "System error - try again later"
								$result = $wpdb->update( 
									$post_notif_subscriber_stage_tbl
									,array( 
										'import_status' => 'X'
										,'status_message' => 'System error - try again later'
									)
									,array( 
										'id' => $staged_subscriber_row->id
									)    			
								);									  
							}				
						}
					}
				}					  
			}
		}
		
		return $subscribers_created;

	}	
   
   /**
	 * Perform single staged subscriber delete.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @param	int	$sub_id	ID of staged subscriber to delete.
	 *	@return	int	Number of staged subscribers deleted.
	 */	
	private function process_single_staged_subscriber_delete( $sub_id ) {

		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';

		// Delete staged subscriber's preferences rows						
		$results = $wpdb->delete( 
			$post_notif_sub_stage_cat_tbl
			,array( 
				'id' => $sub_id
			)    			
		);
						
		// Delete staged subscriber row					
		$num_subs_deleted = $wpdb->delete( 
			$post_notif_subscriber_stage_tbl
			,array( 
				'id' => $sub_id
			)    			
		);
		if ( $num_subs_deleted ) {
				  
			return $num_subs_deleted;
		}
		else {
				  
		  return 0;
		}
		
	}
	
	/**
	 * Perform multiple staged subscriber delete.
	 *
	 * @since	1.0.4
	 * @access	private
	 * @param	array	$form_post	The collection of global query vars.
	 *	@return	int	Number of staged subscribers deleted.
	 */	
	private function process_multiple_staged_subscriber_delete( $form_post ) {
			  			  
		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';
	
		// Define checkbox prefix
		$del_subscribers_checkbox_prefix = 'chkKey_';
		$subscribers_deleted = 0;
		
		// For each selected staged subscriber on submitted form:
		// 	Delete their staged category rows
		// 	Delete their row from staged subscribers table
		foreach ( $form_post as $del_subscribers_field_name => $del_subscribers_value ) {
			if ( !(strncmp($del_subscribers_field_name, $del_subscribers_checkbox_prefix, strlen( $del_subscribers_checkbox_prefix ) ) ) ) {
						  
				// This is a Subscriber checkbox
				if ( isset( $del_subscribers_field_name ) ) {
				
					// Checkbox IS selected
						
					// Delete subscriber's preferences rows						
					$results = $wpdb->delete( 
						$post_notif_sub_stage_cat_tbl
						,array( 
							'id' => $del_subscribers_value
						)    			
					);
						
					// Delete subscriber row					
					$num_subs_deleted = $wpdb->delete( 
						$post_notif_subscriber_stage_tbl 
						,array( 
							'id' => $del_subscribers_value
						)    			
					);
					if ( $num_subs_deleted )
					{
							  
						// OK, wise-guy, I know you're saying there should never be more than
						//		one subscriber per id!
						$subscribers_deleted += $num_subs_deleted;
					}
				}					  
			}
		}
		
		return $subscribers_deleted;
		
	}
	
	/**
	 * Define single and bulk actions for Manage Subscribers page.
	 *
	 * @since	1.0.2
	 */	
	public function define_manage_subscribers_page() {

		$available_actions_arr = array(
			'actionable_column_name' => 'first_name'
			,'actions' => array(
				'export' => array(
					'label' => __( 'Export', 'post-notif' )
					,'single_ok' => false
					,'bulk_ok' => true
				)
				,'delete' => array(
					'label' => __( 'Delete', 'post-notif' )
					,'single_ok' => true
					,'bulk_ok' => true
				)
				,'resend' => array(
					'label' => __( 'Resend Confirmation', 'post-notif' )
					,'single_ok' => true
					,'bulk_ok' => true
				)
			)
		);
		$this->render_subscribers_page( $available_actions_arr );		
	}
	
	/**
	 * Define single and bulk actions (NONE) for View Subscribers page.
	 *
	 * @since	1.0.0
	 */	
	public function define_view_subscribers_page() {

		$available_actions_arr = null;
		$this->render_subscribers_page( $available_actions_arr );
		
	}
	
	/**
	 * Render Subscribers [View or Manage] page.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	array	$available_actions_arr	The available actions for the list table items.
	 */	
	private function render_subscribers_page ( $available_actions_arr ) {
	
		global $wpdb;
		
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		$subscribers_exported = 0;
		$subscribers_deleted = 0;
		$subscribers_resent_confirmation = 0;
		$form_action = $_SERVER['REQUEST_URI'];		
		$sort_by_category = false;
		
		$affected_subscriber = false;
		
		if ( !empty( $_REQUEST['subscriber'] ) ) {
				  
			// Single action needs to be processed
			$current_action = $_REQUEST['action'];
			$affected_subscriber = $_REQUEST['subscriber'];
		}
		else {				  
			if ( isset( $_REQUEST['doaction'] ) && isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {

				// Bulk action needs to be processed
				$current_action = $_REQUEST['action'];						
			}
			elseif ( isset( $_REQUEST['doaction2'] ) && isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
				
				// Bulk action needs to be processed
				$current_action = $_REQUEST['action2'];
			}
			else {
				$current_action = '';	  
			}
		}
		
		switch ( $current_action ) {
			case 'exported':
					  
				// Display subscriber export count passed from process_multiple_subscriber_export()
				$subscribers_exported = $_REQUEST['exportcount'];
				$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber', 'exported', 'exportcount' ), $_SERVER['REQUEST_URI'] ) );
			break;
			case 'delete':
					  
				// Delete(s) need to be processed
			
				if ( $affected_subscriber ) {
				  
					// Delete single subscriber
					$subscribers_deleted = $this->process_single_subscriber_delete( $affected_subscriber );
					$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber' ), $_SERVER['REQUEST_URI'] ) );
				}
				else {
				  			 
					// Delete multiple (selected) subscribers via bulk action
					$subscribers_deleted = $this->process_multiple_subscriber_delete( $_POST );
				}					  
			break;
 			case 'resend':	  
				
 				// Resend confirmation(s) need to be processed
			
				if ( $affected_subscriber ) {
				  
					// Resend confirmation to single subscriber
					$subscribers_resent_confirmation = $this->process_single_subscriber_resend( $affected_subscriber );
					$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber' ), $_SERVER['REQUEST_URI'] ) );
				}
				else {
				  			 
					// Resend confirmations to multiple (selected) subscribers via bulk action
					$subscribers_resent_confirmation = $this->process_multiple_subscriber_resend( $_POST );
				}
 			break;
		}

		// Define list table columns
		
		if ( is_array( $available_actions_arr ) ) {				  
			$columns_arr = array();
			foreach ( $available_actions_arr['actions'] as $single_action_arr ) {
				if ( $single_action_arr['bulk_ok'] == true ) {

					// There are bulk actions, add checkbox column
					$columns_arr['cb'] = '<input type="checkbox" />';	 
					break;
				}
			}
		}	 		
		$columns_arr['first_name'] = __( 'First Name', 'post-notif' );
		$columns_arr['email_addr'] = __( 'Email Address', 'post-notif' );
		$columns_arr['confirmed'] = __( 'Confirmed?', 'post-notif' );
		$columns_arr['date_subscribed'] = __( 'Date Subscribed', 'post-notif' );
		$columns_arr['categories'] = __( 'Categories', 'post-notif' );

		// NOTE: Third parameter indicates whether column data is already sorted 
		$sortable_columns_arr = array(
         'first_name' => array( 
         	'first_name'
         	,false
         )
         ,'email_addr' => array(
         	'email_addr'
         	,false
         )
         ,'confirmed' => array(
         	'confirmed'
         	,false
         )
         ,'date_subscribed' => array(
         	'date_subscribed'
         	,false
         )
         ,'categories' => array(
         	'categories'
         	,false
         )
      );    
				
		if ( !empty( $_REQUEST['orderby'] ) ) {					 
			if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
					  
				// This IS a valid, sortable column
				if ( $_REQUEST['orderby'] != 'categories' ) {
					$orderby = $_REQUEST['orderby'];		 
				}
				else {
					$orderby = 'id';
					$sort_by_category = true;
				
					// Sort by category requires some special handling since category data is not
					//		retrieved by original query
					function usort_reorder( $a, $b ) {
						$order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
						$result = strcmp( $a['categories'], $b['categories'] );
  					
						return ( $order === 'asc' ) ? $result : -$result;
					}
				}
			}
			else {
					  
				// This is NOT a valid, sortable column					  
				$orderby = 'first_name';
			}
		}
		else {
				  
			// No orderby specified
			$orderby = 'first_name';
		}
		if ( !empty( $_REQUEST['order'] ) ) {
			if ( $_REQUEST['order'] == 'desc' ) {
				$order = 'desc';
			}
			else {
					  
				// This is NOT a valid order				  
				$order = 'asc';
			}
		}
		else {
			
			// No order specified
			$order = 'asc';
		}
		
		// Get subscribers
		$subscribers_arr = $wpdb->get_results(
			"
   			SELECT 
   				id
   				,first_name
   				,email_addr 
   				,confirmed
   				,date_subscribed
   			FROM $post_notif_subscriber_tbl
   			ORDER BY $orderby $order
   		"
   		,ARRAY_A
   	);
   	
   	// Select categories each subscriber is subscribed to AND pass array to page
   	//		for display
 		$args = array(
			'exclude' => 1		// Omit Uncategorized
			,'orderby' => 'name'
			,'order' => 'ASC'
			,'hide_empty' => 0
		);
		$category_arr = get_categories( $args );
		$category_name_arr = array();
		foreach ( $category_arr as $category )
		{
			$category_name_arr[$category->cat_ID] = $category->name;
		}

   	$subscriber_cats_arr = array();
   	foreach ( $subscribers_arr as $sub_key => $sub_val ) {
   		$selected_cats_arr = $wpdb->get_results( 
   			"
   				SELECT cat_id 
   				FROM $post_notif_sub_cat_tbl
   				WHERE id = " . $sub_val['id']
   				. " ORDER BY cat_id
   			"
   		);
   		
   		$cat_string = '';
   		foreach ( $selected_cats_arr as $cat_key => $cat_val ) { 
   			if ($cat_val->cat_id != 0) {
   				$cat_string .= $category_name_arr[$cat_val->cat_id] . ', ';
   			}
   			else {
   				$cat_string = 'All';	  
   				break;
   			}
   		}
  	   	$cat_string = rtrim ( $cat_string, ', ' );   	
  			$subscribers_arr[$sub_key]['categories'] = $cat_string;
  			
  			// Translate binary "Subscription Confirmed?" value to words
  			$subscribers_arr[$sub_key]['confirmed'] =  ( ( $sub_val['confirmed'] == 1 ) ? __( 'Yes', 'post-notif' ) : __( 'No', 'post-notif' ) );  			
  		}	
		if ( $sort_by_category ) {
				  
			// Special sort for category
			usort( $subscribers_arr, 'usort_reorder' );
		}
   	
		// Build page	  
	
    	$class_settings_arr = array(
    		'singular' => __( 'subscriber', 'post-notif' )
    		,'plural' => __( 'subscribers', 'post-notif' )
    		,'ajax' => false
    		,'available_actions_arr' => $available_actions_arr
    	);
    
    	// Single array containing the various arrays to pass to the list table class constructor
    	$table_data_arr = array(
    		'columns_arr' => $columns_arr
    		,'hidden_columns_arr' => array()
    		,'sortable_columns_arr' => $sortable_columns_arr
    		,'rows_per_page' => 0		// NOTE: Pass 0 for single page with all data (i.e. NO pagination)
    		,'table_contents_arr' => $subscribers_arr    			  
    	);

    	$view_subs_pg_list_table = new Post_Notif_List_Table( $class_settings_arr, $table_data_arr );
		$view_subs_pg_list_table->prepare_items();		
         
      // Render page	  
		$post_notif_view_subs_pg = '';
    	ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-admin-view-subs.php' );
		$post_notif_view_subs_pg .= ob_get_clean();
		print $post_notif_view_subs_pg;	
		
   }
 
	/**
	 * Perform multiple subscriber export.
	 *
	 * @since	1.0.4
	 */	
   public function process_multiple_subscriber_export() {
   		 
   	if ( ( isset( $_REQUEST['doaction'] ) && ($_REQUEST['action'] == 'export') )
   		|| ( isset( $_REQUEST['doaction2'] ) && ($_REQUEST['action2'] == 'export') ) ) {
   	
   		$suggested_filename = 'subscriber_export.' . date( 'Y-m-d' ) . '_' . date( 'Hi' ) . '.csv';

 			// Specifying these headers will force the export file to be downloaded, not displayed
 			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
 			header( 'Content-Disposition: attachment; filename=' . $suggested_filename );
 
 			global $wpdb;
	  
 			// Tack prefix on to table names
 			$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
 			$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
	
 			// Define checkbox prefix
 			$exp_subscribers_checkbox_prefix = 'chkKey_';
		
 			$subscriber_arr = array();
		
 			// NOTE: Third parameter indicates whether column data is already sorted 
 			$sortable_columns_arr = array(
 				'first_name' => array( 
 					'first_name'
 					,false
 				)
 				,'email_addr' => array(
 					'email_addr'
 					,false
 				)
 				,'confirmed' => array(
 					'confirmed'
 					,false
 				)
 				,'date_subscribed' => array(
 					'date_subscribed'
 					,false
 				)
 				,'categories' => array(
 					'categories'
 					,false
 				)
 			);    
				
 			if ( !empty( $_REQUEST['orderby'] ) ) {					 
 				if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
					  
 					// This IS a valid, sortable column
 					if ( $_REQUEST['orderby'] != 'categories' ) {
 						$orderby = $_REQUEST['orderby'];		 
 					}
 					else {
 						$orderby = 'id';
 						$sort_by_category = true;
				
 						// Sort by category requires some special handling since category data is not
 						//		retrieved by original query
 						function usort_reorder( $a, $b ) {
 							$order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
 							$result = strcmp( $a['categories'], $b['categories'] );
  					
 							return ( $order === 'asc' ) ? $result : -$result;
 						}
 					}
 				}
 				else {
					  
 					// This is NOT a valid, sortable column					  
 					$orderby = 'first_name';
 				}
 			}
 			else {
				  
 				// No orderby specified
 				$orderby = 'first_name';
 			}
 			if ( !empty( $_REQUEST['order'] ) ) {
 				if ( $_REQUEST['order'] == 'desc' ) {
 					$order = 'desc';
 				}
 				else {
					  
 					// This is NOT a valid order				  
 					$order = 'asc';
 				}
 			}
 			else {
			
 				// No order specified
 				$order = 'asc';
 			}

 			// For each selected subscriber on submitted form:
 			//		Add their ID to IN clause
 			//				
 			foreach ( $_POST as $exp_subscribers_field_name => $exp_subscribers_value ) {
 				if ( !(strncmp($exp_subscribers_field_name, $exp_subscribers_checkbox_prefix, strlen( $exp_subscribers_checkbox_prefix ) ) ) ) {
						  
 					// This is a Subscriber checkbox
 					if ( isset( $exp_subscribers_field_name ) ) {
				
 						// Checkbox IS selected
					
 						// Add subscriber's ID to list
 						$subscriber_arr[] = $exp_subscribers_value;
 					}					  
 				}
 			}
		
 			// prepare() needs to handle any number of subscribers
 			$id_clause_string = rtrim( str_repeat( '%d,', count( $subscriber_arr ) ), ',' );

 			// Select subscribers 
 			$subscribers_arr = $wpdb->get_results(
 				$wpdb->prepare(
 					"
   					SELECT 
   						id
   						,email_addr 
   						,first_name
   					FROM $post_notif_subscriber_tbl
   					WHERE id IN ( $id_clause_string )
   					ORDER BY $orderby $order
   				"
   				,$subscriber_arr
   			)
   			,ARRAY_A
   		);
   		
   		// Remove submitted action from URL
   		$new_url = esc_url_raw( remove_query_arg( array ( 'action' ), $_SERVER['REQUEST_URI'] ) );
   		
   		// Add new query args so that exported subscriber count is displayed on page following file save
   		$new_url = esc_url_raw( add_query_arg( array ( 'doaction' => 1, 'action' => 'exported', 'exportcount' => count( $subscribers_arr ) ), $new_url ) );
   		  		
   		// Reroute to new URL
   		header( 'refresh:1; URL="' . $new_url . '"' );  			  
   		
   		// Get each subscriber's categories
   		// NOTE: 0 means All and unconfirmed subscribers have NO categories
   		$subscriber_cats_arr = array();
   		foreach ( $subscribers_arr as $sub_key => $sub_val ) {
   			$selected_cats_arr = $wpdb->get_results( 
   				"
   					SELECT cat_id 
   					FROM $post_notif_sub_cat_tbl
   					WHERE id = " . $sub_val['id']
   					. " ORDER BY cat_id
   				"
   			);
   		   			
   			foreach ( $selected_cats_arr as $cat_key => $cat_val ) { 
   				$subscribers_arr[$sub_key][] = $cat_val->cat_id;
   			}
   		}
 			
 			// Create a file pointer to the output stream
  			$file_pointer = fopen('php://output', 'w');

 			foreach ($subscribers_arr as $fields_key => $fields_val ) 
 			{
 					  
 				// Suppress output of ID column by popping it off of the front of the array
 				$trash = array_shift( $fields_val );
 				
 				// Write subscriber row, in CSV format, to output stream
 				fputcsv( $file_pointer, $fields_val );
 			}
 			fclose( $file_pointer );
 			exit;
 		}
 	}
	
	/**
	 * Perform single subscriber delete.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	int	$sub_id	ID of subscriber to delete.
	 *	@return	int	Number of subscribers deleted.
	 */	
	private function process_single_subscriber_delete( $sub_id ) {

		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Delete subscriber's preferences rows						
		$results = $wpdb->delete( 
			$post_notif_sub_cat_tbl
			,array( 
				'id' => $sub_id
			)    			
		);
						
		// Delete subscriber row					
		$num_subs_deleted = $wpdb->delete( 
			$post_notif_subscriber_tbl
			,array( 
				'id' => $sub_id
			)    			
		);
		if ( $num_subs_deleted ) {
				  
			return $num_subs_deleted;
		}
		else {
				  
		  return 0;
		}
		
	}
			 
	/**
	 * Perform multiple subscriber delete.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	array	$form_post	The collection of global query vars.
	 *	@return	int	Number of subscribers deleted.
	 */	
	private function process_multiple_subscriber_delete( $form_post ) {
			  			  
		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
	
		// Define checkbox prefix
		$del_subscribers_checkbox_prefix = 'chkKey_';
		$subscribers_deleted = 0;
		
		// For each selected subscriber on submitted form:
		// 	Delete their category rows from preferences table
		// 	Delete their row from subscribers table
		foreach ( $form_post as $del_subscribers_field_name => $del_subscribers_value ) {
			if ( !(strncmp($del_subscribers_field_name, $del_subscribers_checkbox_prefix, strlen( $del_subscribers_checkbox_prefix ) ) ) ) {
						  
				// This is a Subscriber checkbox
				if ( isset( $del_subscribers_field_name ) ) {
				
					// Checkbox IS selected
						
					// Delete subscriber's preferences rows						
					$results = $wpdb->delete( 
						$post_notif_sub_cat_tbl
						,array( 
							'id' => $del_subscribers_value
						)    			
					);
						
					// Delete subscriber row					
					$num_subs_deleted = $wpdb->delete( 
						$post_notif_subscriber_tbl 
						,array( 
							'id' => $del_subscribers_value
						)    			
					);
					if ( $num_subs_deleted )
					{
							  
						// OK, wise-guy, I know you're saying there should never be more than
						//		one subscriber per id!
						$subscribers_deleted += $num_subs_deleted;
					}
				}					  
			}
		}
		
		return $subscribers_deleted;
		
	}
 
	/**
	 * Resend confirmation to single subscriber.
	 *
	 * @since	1.0.2
	 * @access	private
	 * @param	int	$sub_id	ID of subscriber to reconfirm.
	 *	@return	int	Number of confirmations resent.
	 */	
	private function process_single_subscriber_resend( $sub_id ) {

		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Delete subscriber's preferences rows						
		$results = $wpdb->delete( 
			$post_notif_sub_cat_tbl
			,array( 
				'id' => $sub_id
			)    			
		);
		
		// Generate authcode
		$authcode = Post_Notif_Misc::generate_authcode();
								
		// Update subscriber row					
		$num_confirms_resent = $wpdb->update( 
			$post_notif_subscriber_tbl
			,array( 
				'confirmed' => 0
				,'authcode' => $authcode
				,'last_modified' => date( "Y-m-d H:i:s" )
			)
			,array( 
				'id' => $sub_id
			)    			
		);
		
		// Retrieve (subset of columns from) subscriber's row
		$subscriber_row = $wpdb->get_row(
			"
   			SELECT 
   				email_addr 
   				,first_name
   				,authcode
   			FROM $post_notif_subscriber_tbl
   			WHERE id = $sub_id
   		"
   		,ARRAY_A
   	);

		// Send confirmation email
		Post_Notif_Misc::send_confirmation_email( $subscriber_row );
		
		if ( $num_confirms_resent ) {
				  
			return $num_confirms_resent;
		}
		else {
				  
		  return 0;
		}
		
	}
	
	/**
	 * Resend confirmation to multiple subscribers.
	 *
	 * @since	1.0.2
	 * @access	private
	 * @param	array	$form_post	The collection of global query vars.
	 *	@return	int	Number of confirmations resent.
	 */	
	private function process_multiple_subscriber_resend( $form_post ) {
			  			  
		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
	
		// Define checkbox prefix
		$rec_subscribers_checkbox_prefix = 'chkKey_';
		$confirmations_resent = 0;
		
		// For each selected subscriber on submitted form:
		// 	Delete their category rows from preferences table
		// 	Update subscriber row with CONFIRMED = 0 and new AUTHCODE
		//		Send new subscription confirmation email
		foreach ( $form_post as $rec_subscribers_field_name => $rec_subscribers_value ) {
			if ( !(strncmp($rec_subscribers_field_name, $rec_subscribers_checkbox_prefix, strlen( $rec_subscribers_checkbox_prefix ) ) ) ) {
						  
				// This is a Subscriber checkbox
				if ( isset( $rec_subscribers_field_name ) ) {
				
					// Checkbox IS selected
						
					// Delete subscriber's preferences rows						
					$results = $wpdb->delete( 
						$post_notif_sub_cat_tbl
						,array( 
							'id' => $rec_subscribers_value
						)    			
					);
						
					// Generate authcode
					$authcode = Post_Notif_Misc::generate_authcode();
								
					// Update subscriber row					
					$num_confirms_resent = $wpdb->update( 
						$post_notif_subscriber_tbl
						,array( 
							'confirmed' => 0
							,'authcode' => $authcode
							,'last_modified' => date( "Y-m-d H:i:s" )
						)
						,array( 
							'id' => $rec_subscribers_value
						)    			
					);
		
					// Retrieve (subset of columns from) subscriber's row
					$subscriber_row = $wpdb->get_row(
						"
							SELECT 
								email_addr 
								,first_name
								,authcode
							FROM $post_notif_subscriber_tbl
							WHERE id = $rec_subscribers_value
						"
						,ARRAY_A
					);

					// Send confirmation email
					Post_Notif_Misc::send_confirmation_email( $subscriber_row );

					if ( $num_confirms_resent )
					{
							  
						// OK, wise-guy, I know you're saying there should never be more than
						//		one subscriber per id!
						$confirmations_resent += $num_confirms_resent;
					}
				}					  
			}
		}
		
		return $confirmations_resent;
		
	}
	
	/**
	 * Render View Post Notifs Sent page.
	 *
	 * @since	1.0.0
	 */	
	public function render_view_post_notifs_sent_page() {
			  			
		global $wpdb;
		
		// Tack prefix on to table names
		$post_notif_post_tbl = $wpdb->prefix.'post_notif_post';
		$users_tbl = $wpdb->prefix.'users';
		
		// Define list table columns
		
    	$columns_arr = array(
    		'post_id' => __( 'Post ID', 'post-notif' )
    		,'post_title' => __( 'Post Title', 'post-notif' )
    		,'author' => __( 'Author', 'post-notif' )
    		,'notif_sent_dttm' => __( 'Sent Date/Time', 'post-notif' )
    		,'sent_by_login' => __( 'Sent By', 'post-notif' )
    	);

 		// NOTE: Third parameter indicates whether column data is already sorted 
    	$sortable_columns_arr = array(
    		'post_id' => array(
    			'post_id'
    			,false
    		)
         ,'notif_sent_dttm' => array(
         	'notif_sent_dttm'
         	,false
         )
      );
				
		if ( !empty( $_REQUEST['orderby'] ) ) {
			if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
	
				// This IS a valid, sortable column
				$orderby = $_REQUEST['orderby'];
			}
			else {
  				// This is NOT a valid, sortable column					  
  				$orderby = 'notif_sent_dttm';
  			}
		}
		else {

			// No orderby specified				  
			$orderby = 'notif_sent_dttm';		  
		}
		if ( !empty( $_REQUEST['order'] ) ) {
			if ( $_REQUEST['order'] == 'asc' ) {
				$order = 'asc';
			}
			else {
					  
				// This is NOT a valid order				  
				$order = 'desc';
			}
		}
		else {
			
			// No order specified
			$order = 'desc';
		} 		
		
		// Get post notifs sent
		
      // Display warning message if Sent By ID cannot be tied to a user
		$post_notifs_sent_arr = $wpdb->get_results(
			"
   			SELECT post_id
   				,notif_sent_dttm 
   				,sent_by
   				,IFNULL(user_login, CONCAT('*** Can''t find user ID ', sent_by)) AS sent_by_login
   			FROM $post_notif_post_tbl   			
   			LEFT OUTER JOIN $users_tbl
   				ON ($post_notif_post_tbl.sent_by = $users_tbl.ID)
   			ORDER BY $orderby $order
   		"
   		,ARRAY_A
   	);
 	
   	// Get post titles, authors' names, and post notif senders' names
   	foreach ( $post_notifs_sent_arr as $notif_key => $notif_val ) {
   		$post_object = get_post( $notif_val['post_id'] );
   		$post_notifs_sent_arr[$notif_key]['post_title'] = $post_object->post_title;
   		
    		$post_author_data = get_userdata( $post_object->post_author );
   		$post_notifs_sent_arr[$notif_key]['author'] = $post_author_data->user_login;
    	}
  	
		// Build page	  
    
    	$class_settings_arr = array(
    		'singular' => __( 'notification', 'post-notif' )
    		,'plural' => __( 'notifications', 'post-notif' )
			,'ajax' => false
    		// NOTE: Page is read-only, so no available actions
    		,'available_actions_arr' => null
    	);
    
     	// Single array containing the various arrays to pass to the list table class constructor
    	$table_data_arr = array(
    		'columns_arr' => $columns_arr
    		,'hidden_columns_arr' => array()
    		,'sortable_columns_arr' => $sortable_columns_arr
    		,'rows_per_page' => 0		// NOTE: Pass 0 for single page with all data (i.e. NO pagination)
    		,'table_contents_arr' => $post_notifs_sent_arr    			  
    	);
    	
    	$view_post_notif_list_table = new Post_Notif_List_Table( $class_settings_arr, $table_data_arr );
		$view_post_notif_list_table->prepare_items();		
           	    	
      // Render page	  
		$post_notif_view_posts_pg = '';
    	ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-admin-view-post-notifs-sent.php' );
		$post_notif_view_posts_pg .= ob_get_clean();
		print $post_notif_view_posts_pg;	
			  
	}			
	
}
