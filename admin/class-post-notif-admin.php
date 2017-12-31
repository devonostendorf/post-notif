<?php

/**
 * The admin-specific functionality of the plugin.
 *					
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin
 */
		 		
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and enqueues the admin-specific
 *	JavaScript.
 *
 * @since		1.0.0
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin
 * @author		Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Admin {
		  	
	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		string	$plugin_name	The ID of this plugin.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/post-notif-admin.min.js', array( 'jquery' ), $this->version, false );
		 
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

	/**
	 * Determine whether Post Notif has a translation for this site's current language.
	 *
	 * @since	1.0.6
	 */	
	public function translation_check() {
	
		// Skip translation check if core update is in progress (or was just completed)
		// NOTE: The "about" page is where funny business with get_locale() occurs such that inaccurate errors
		//		are generated if this translation check is NOT skipped.  Displaying the nag on the "update-core"
		//		page is just ugly and annoying!
		$current_screen = get_current_screen();
		if ( ( "update-core" !== $current_screen->id ) && ( "about" !== $current_screen->id ) ) {
		
			// Get the current language locale
			$language = get_locale();
		
			// Check if the nag screen has been disabled for this language (or if current language is US or UK English or Czech or Dutch)
			if ( ( 'en_US' !== $language ) && ( 'en_UK' !== $language ) && ( 'cs_CZ' !== $language ) && ( 'nl_NL' !== $language )
				&& ( false === get_option( 'post_notif_language_detector_' . $language, false ) ) ) {
			
				// Nag screen, for current language, has NOT been dismissed 
				$plugin_i18n = new Post_Notif_i18n();
				if ( $plugin_i18n->is_loaded() ) {

					// BUT, a translation file, for current language, DOES exist
					// Disable nag screen for current language
					update_option( 'post_notif_language_detector_' . $language, true, true );
					return;
				}
				else {
		
					// Display nag screen until admin dismisses it OR translation, for current language, is installed
					$this->display_translation_nag_screen( $language );
				}					
			}	
		}
	}
	
	/**
	 * Display the translation nag screen, soliciting translation help.
	 *
	 * @since	1.0.6
	 * @access	private
	 * @param	string	$language	The site's current language.
	 */
	private function display_translation_nag_screen( $language ) {

		// Add script, to handle nag dismissal, to page footer
		add_action( 'admin_footer', array( $this, 'add_translation_nag_screen_dismissal_script' ) );
		
		// We need the translation data from core to display human readable locale names
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		$translations = wp_get_available_translations();
		$plugin = get_plugin_data( dirname( plugin_dir_path( __FILE__ ) ) . '/post-notif.php' );
		include( plugin_dir_path( __FILE__ ) . 'partials/post-notif-admin-view-translation-nag.php' );
		
	}

	/**
	 * Add JavaScript, to handle translation nag screen dismissal, to page footer.
	 *
	 * @since	1.0.6
	 */
	public function add_translation_nag_screen_dismissal_script() {
		
		include( plugin_dir_path( __FILE__ ) . 'js/post-notif-admin-translation-nag.js' );
		
	}
	
	/**
	 * Disable translation nag screen for current language.
	 *
	 * @since	1.0.6
	 */	
	 public function translation_nag_screen_ajax_handler() {

	 	 // Disable nag screen for current language
	 	 update_option( 'post_notif_language_detector_' . get_locale(), true );
	 	 wp_die();
	 }
	
		
	// Functions related to adding Post Notif option to Publish meta box on Edit Post page
	
	/**
	 * Add Post Notif option to Publish meta box on Edit Post page.
	 *
	 * @since	1.1.3
	 * @return	null	If this is not a post
	 */	
	public function add_post_notif_option_to_publish_box() {
		
		global $post;
		
		if ( 'post' != $post->post_type ) {
			
			return;
		}
		
		if ( 'publish' == $post->post_status ) {
			
			// Posts that have been published always have Post Notif default to "Manual" as this prevents
			//	accidental re-sending of post notifications on updates to post
			$send_notif_on_publish = 'no';
			update_post_meta( $post->ID, 'send_notif_on_publish', $send_notif_on_publish );
		}
		elseif ( ! $send_notif_on_publish = get_post_meta( $post->ID, 'send_notif_on_publish', true ) ) {
				
			// Default from Post Notif settings since no value found in post meta for this post
			$post_notif_options_arr = get_option( 'post_notif_settings' );
			$send_notif_on_publish = $post_notif_options_arr['send_notif_on_publish'];
		}			
		
		if ( 'yes' == $send_notif_on_publish ) {
			$active = __( 'Auto', 'post-notif' );
			$class = 'pn-auto';
			$inactive = __( 'Manual', 'post-notif' );
		}
		else {
			$active = __( 'Manual', 'post-notif' );
			$class = 'pn-manual';
			$inactive = __( 'Auto', 'post-notif' );
		}
		
		// Add Post Notif option to core Publish meta box	
		include( plugin_dir_path( __FILE__ ) . 'partials/post-notif-admin-publish-meta-box-addon.php' );			
		
	}

	/**
	 * Save post notif configuration for specified post to post meta.
	 *
	 * @since	1.1.3
	 * @param	int		$post_id	Post to save post notif configuration for.
	 * @return	null	If this is an autosave or an unauthorized user or not a post
	 */
	public function save_send_notif_on_publish_post_meta( $post_id ) {

		if ( ( wp_is_post_autosave( $post_id ) ) ||
			( ! current_user_can( 'edit_page', $post_id ) ) ||
			( empty( $post_id ) ) || 
			( 'post' != get_post_type( $post_id ) ) 
		) {
		
			// This is an autosave or an unauthorized user or not a post
			return;
			
		}
		
		if ( isset( $_POST['hdnPostNotifSchedAuto'] ) ) {
			update_post_meta( $post_id, 'send_notif_on_publish', $_POST['hdnPostNotifSchedAuto'] );
		}
		
	}
	
	/**
	 * Send post notification if specified post has been configured to send automatically on publish.
	 *
	 * @since	1.1.3
	 * @param	string	$new_status	New post status after an update.
	 * @param	string	$old_status	Previous post status.
	 * @param	WP_Post	$post	The object for the current post/page.
	 */
	public function send_notif_on_publish_if_auto( $new_status, $old_status, $post ) {
	
		if ( 'publish' == $new_status ) {

			// NOTE: Because this function is hooked into transition_post_status action, and it, in turn, calls
			//		the save_post action, which the save_send_notif_on_publish_post_meta() function (above) is hooked 
			//		into, if the post notif option has changed since the last (which could be the first!) time draft was
			//		saved, that value will not be current in the post meta data.  So force that save.				
			$this->save_send_notif_on_publish_post_meta( $post->ID );			
			
			$send_notif_on_publish = get_post_meta( $post->ID, 'send_notif_on_publish', true );

			if ( 'yes' == $send_notif_on_publish ) {
				
				// Cancel any active process as user has explicitly chosen to have publish process initiate post notif
				//		sends!				
				if ( $active_send_notif_row_arr = $this->get_active_send_notif_row( $post->ID ) ) {
					
					// Update status on post_notif_post table row
					$send_process_update_columns_arr = array(
						'notif_end_dttm' => gmdate( "Y-m-d H:i:s" )
						,'send_status' => 'X'
					);
					$send_process_where_clause_arr = array(
						'post_id' => $post->ID
						,'notif_sent_dttm' => $active_send_notif_row_arr['notif_sent_dttm']
					);
					$this->update_post_notif_send_process_status( $send_process_update_columns_arr, $send_process_where_clause_arr );
				
					// Unschedule from WP cron
					$this->unschedule_future_post_notif_send( $post->ID );
				}
				
				if ( $current_user = get_current_user_id() ) {
					
					// User found means manually triggered
					$scheduled = 0;
				}
				else {
					
					// NO user found means system scheduled
					$scheduled = 1;
				}
											
				// Process is actually starting, initialize process tracking
				$create_new_send_process_arr = array(
					'post_id' => $post->ID
					,'notif_sent_dttm' => gmdate( "Y-m-d H:i:s" )
					,'notif_schedule_dttm' => $scheduled ? gmdate( "Y-m-d H:i:s" ) : null
					,'sent_by' => $current_user
					,'send_status' => 'I'
					,'num_recipients' => 0
					,'scheduled' => $scheduled
				);			
				$this->create_post_notif_send_process_row( $create_new_send_process_arr );

				$this->schedule_immediate_resumption_of_send( $post->ID );

			}
		}		
		
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
		
		if ( 'auto-draft' != $post->post_status ) {
	
			// "Test Send" functionality is available for any post status other than 'auto-draft'
							
			$post_published_or_scheduled = false;
			$auto_send_selected = true;
			$previously_sent = false;
			$already_scheduled = false;
			$process_running = false;
			$status_message = '';
			$manage_post_notifs_sent_url = '<a href="' . admin_url( 'admin.php?page=post-notif-manage-posts-sent' ) . '">' . __( 'Post Notif >> Manage Post Notifs Sent', 'post-notif' ) . '</a>';
			$maintain_notifs_sent_info_message = sprintf(
				/* translators: %s will be clickable link, with literal label of 'Post Notif >> Manage Post Notifs Sent' */
				__( 'Go to: %s to check the status of, pause, or cancel, post notification processes (both running and scheduled).', 'post-notif' ),
				$manage_post_notifs_sent_url
			);
						
			$post_scheduled = ('future' == $post->post_status );
			$post_published = ('publish' == $post->post_status );
			
			if ( $post_scheduled || $post_published ) {

				// Post has been scheduled to be published or has actually been published already, so allow Post Notif send		
				$post_published_or_scheduled = true;
				if ( ! $send_notif_on_publish = get_post_meta( $post->ID, 'send_notif_on_publish', true ) ) {
				
					// Default from Post Notif settings since no value found in post meta for this post
					$post_notif_options_arr = get_option( 'post_notif_settings' );
					$send_notif_on_publish = $post_notif_options_arr['send_notif_on_publish'];
				}	
			
				if ( 'no' == $send_notif_on_publish ) {
				
					// Post Notif send option is set to manual so make Schedule (and possibly Send Now) available
					$auto_send_selected = false;
					
					global $wpdb;			
		
					// Determine if post notifs have already been sent for this post id
					$previously_sent = false;
					$notif_sent_dttm = $wpdb->get_var( 
						$wpdb->prepare(
							"
								SELECT MAX(notif_end_dttm) AS notif_sent_dttm
								FROM " . $wpdb->prefix.'post_notif_post'
								. " WHERE post_id = %d
							"
							,$post->ID
						)		
					);
					if ( null !== $notif_sent_dttm ) {
						$previously_sent = true;	
					}

					// Determine if post notif send is already scheduled for this post
					$already_scheduled = false;
					$notif_scheduled_for_dttm = wp_next_scheduled( 'post_notif_send_scheduled_post_notif', array( $post->ID ) ); 
					if ( false !== $notif_scheduled_for_dttm ) {
						$already_scheduled = true;
					}
					elseif ( $this->get_active_send_notif_key( $post->ID ) ) {
			
						// Process IS running now
						$process_running = true;						
						$status_message = __( 'Post notif for this post is pending.', 'post-notif' );
					}
					
				}
			}
			
			// Render meta box	
			include( plugin_dir_path( __FILE__ ) . 'partials/post-notif-admin-meta-box.php' );			

		}
	
	}
		
	/**
	 * Enqueue AJAX script that fires when "Send Notif" button (in meta box on Edit Post page) is pressed.
	 *
	 * @since	1.0.0
	 * @param	string	$hook	The current page name.
	 * @return	null	If this is not post.php page
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
				,'processing_msg' => __( 'Processing...', 'post-notif' )
				,'invalid_date_format_msg' => __( 'Invalid date and/or time! Please try again.', 'post-notif' )
			)
		);  

	}

 	/**
	 * Initializes manually-run post notification send process.
	 *
	 * @since	1.1.0
	 * @return	int	Always exits with a value of -1
	 */	
	public function init_post_notif_send() {

		// Get post ID for current notification
		$post_id = $_POST['post_id'];
		
		if ( ! ( $this->post_notif_already_running( $post_id ) ) ) {

			// Process is actually starting, initialize process tracking
			$create_new_send_process_arr = array(
				'post_id' => $post_id
				,'notif_sent_dttm' => gmdate( "Y-m-d H:i:s" )
				,'sent_by' => get_current_user_id()
				,'send_status' => 'I'
				,'num_recipients' => 0
				,'scheduled' => 0
			);
			$this->create_post_notif_send_process_row( $create_new_send_process_arr );
			
			$this->schedule_immediate_resumption_of_send( $post_id );			

			wp_send_json( array( 'status' => '1', 'message' => __( 'Post notif for this post is pending.', 'post-notif' ) ) );
		}
		else {
			wp_send_json( array( 'status' => '-1', 'message' => __( 'Post notification process, for this post, is already running!', 'post-notif' ) ) );
		}
	}

	/**
	 * Check if post notification is currently processing for specified post.
	 *
	 * @since	1.1.0
	 * @access	private
	 * @param	int		$post_id	Post to check processing status for.
	 * @return	bool	Is post notification process already running for specified post?
	 */
	private function post_notif_already_running( $post_id ) {
		
		global $wpdb;

    	$post_notif_already_running = $wpdb->get_var(
    		$wpdb->prepare(
    			"SELECT COUNT(post_id) FROM " . $wpdb->prefix.'post_notif_post' 
    			. " WHERE post_id = %d"
    			. " AND send_status = 'I'"
    			, $post_id
    		)
    	);
				
		return ( 1 == $post_notif_already_running );
		
	}    
	
	/**
	 * Perform post notification.
	 *
	 * @since	1.1.0
	 * @access	private
	 * @param	int		$post_id	Post to send notifications for.
	 * @return	array	Boolean indicator of whether send process completed, integer count of post notifications sent and
	 *						(UTC) datetime process ran.
	 */
	private function process_post_notif_send( $post_id ) {
     			
		$max_execution_secs = ini_get('max_execution_time');
		$timeout_exists = ( true == $max_execution_secs );
		$execution_upper_limit = time() + $max_execution_secs - 5;

		global $wpdb;
   
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
		$post_notif_post_tbl = $wpdb->prefix.'post_notif_post';
		
		$post_notif_count_sent = 0;
		$post_notif_send_complete = false;
		
		// Find categories this post is associated with
		$post_categories_arr = wp_get_post_categories( $post_id ); 
		$post_category_clause = '(';
		foreach ( $post_categories_arr as $post_category ) {   		
			$post_category_clause .= $post_category . ',';
		}
   		
		// Tack on "All" category too
		$post_category_clause .= '0)';
				
		$post_notif_options_arr = get_option( 'post_notif_settings' );
				
		$available_categories = array_key_exists( 'available_categories', $post_notif_options_arr ) ? $post_notif_options_arr['available_categories'] : '-1';
		if ( '-1' != $available_categories ) {
					
			// Categories ARE available for subscribers to choose from
			//	so join sub cat tbl to only notify subscribers about
			//	categories they care about
			$sub_cat_tbl_join = 
				"
					JOIN $post_notif_sub_cat_tbl
					ON $post_notif_subscriber_tbl.id = $post_notif_sub_cat_tbl.id
				"
			;
			$cat_id_clause = "AND cat_id IN $post_category_clause";
		}
		else {
					
			// Categories are NOT being used with Post Notif, so send
			//	notification to all (confirmed) subscribers
			$sub_cat_tbl_join = '';
			$cat_id_clause = '';
		}	
		
		if ( $post_notif_process_details_arr = $this->get_in_process_send_notif_row( $post_id ) ) {
			$last_subscriber_id_sent = $post_notif_process_details_arr['last_subscriber_id_sent'];
			$notif_intiated_dttm = $post_notif_process_details_arr['notif_sent_dttm'];
			$post_notif_count_sent = $post_notif_process_details_arr['num_notifs_sent'];
			
			if ( 0 != $last_subscriber_id_sent ) {
				$subscriber_count = $post_notif_process_details_arr['num_recipients'];
			}
			else {

				// Get count of subscribers to send for first time this is run
    		
				$subscriber_count = $wpdb->get_var( 
					"
						SELECT DISTINCT COUNT($post_notif_subscriber_tbl.id)
						FROM $post_notif_subscriber_tbl
						$sub_cat_tbl_join
						WHERE confirmed = 1
							$cat_id_clause
					"
				);
    		
				// Store number of post notifs to send
				$send_process_update_num_recipients_arr = array(
					'num_recipients' => $subscriber_count
				);
				$send_process_update_num_recipients_where_clause_arr = array(
					'post_id' => $post_id
					,'notif_sent_dttm' => $notif_intiated_dttm
					,'send_status' => 'I'
				);
				$this->update_post_notif_send_process_status( $send_process_update_num_recipients_arr, $send_process_update_num_recipients_where_clause_arr );

    		}
    					

		}
		else {
			$last_subscriber_id_sent = -1;
			$notif_intiated_dttm = -1;	
		}
		
		// Add batch size limit
		$limit_clause = '';
		$batch_mode = array_key_exists( 'enable_batch_send_options', $post_notif_options_arr );
		if ( $batch_mode ) {		
			
			$batch_size = $post_notif_options_arr['batch_size'];
			if ( is_numeric( $batch_size ) && ( is_int( $batch_size + 0 ) ) && ( $batch_size > 0 ) ) {
				$limit_clause = 'LIMIT ' . $batch_size;
			}
			else {
				$batch_size = 0;	
			}
			
			$batch_pause = $post_notif_options_arr['batch_pause'];
			if ( ! is_numeric( $batch_pause ) || ( ! is_int( $batch_pause + 0 ) ) || ( $batch_pause <= 0 ) ) {
				$batch_pause = 0;	
			}
		}
		
		if ( -1 !== $last_subscriber_id_sent ) {
			
			// This must be non-NULL to avoid accidentally starting at the beginning of the subscriber list,
			//	in the case where this is a restart!
			
			// Find subscribers to this/these ^^^ category/s
			$subscribers_arr = $wpdb->get_results(
				"
					SELECT DISTINCT $post_notif_subscriber_tbl.id AS id
						,email_addr 
						,first_name
						,authcode
					FROM $post_notif_subscriber_tbl
					$sub_cat_tbl_join
					WHERE confirmed = 1
						$cat_id_clause
					AND $post_notif_subscriber_tbl.id > $last_subscriber_id_sent
					ORDER BY $post_notif_subscriber_tbl.id
					$limit_clause
				"
			);
		}
		else {
			
			// This must be assigned as an (empty) array so that count() will not fail
			$subscribers_arr = array();	
		}
   							
		if ( count( $subscribers_arr ) > 0 ) {
 
			// Compose emails

			// Resolve non-personalized email variables 
			list( $headers, $post_notif_email_subject, $post_notif_email_body_template ) = $this->resolve_post_notification_email_vars( $post_id );

			// Generate generic subscriber URL base
			$subscriber_url_template = Post_Notif_Misc::generate_subscriber_url_base();
		
			$curr_batch_post_notif_count_sent = 0;
 				
			// Iterate through subscribers, tailoring links (change prefs, unsubscribe) to each subscriber
			foreach ( $subscribers_arr as $subscriber ) {
  				
				if ( ! $this->post_notif_already_running( $post_id ) ) {

					// Process has been paused or cancelled
					return array( $post_notif_send_complete, $post_notif_count_sent, gmdate( "Y-m-d H:i:s" ) );
				
				}

				$subscriber_url = $subscriber_url_template . '?email_addr=' . $subscriber->email_addr . '&authcode=' . $subscriber->authcode;
				$prefs_url = str_replace( 'ACTION_PLACEHOLDER', 'manage_prefs', $subscriber_url );
				$unsubscribe_url = str_replace( 'ACTION_PLACEHOLDER', 'unsubscribe', $subscriber_url );

				$post_notif_email_body = $post_notif_email_body_template;
				$post_notif_email_body = str_replace( '@@firstname', ( '[Unknown]' != $subscriber->first_name ) ? $subscriber->first_name : '', $post_notif_email_body );
				$post_notif_email_body = str_replace( '@@prefsurl', '<a href="' . $prefs_url . '">' . $prefs_url . '</a>', $post_notif_email_body );
				$post_notif_email_body = str_replace( '@@unsubscribeurl', '<a href="' . $unsubscribe_url . '">' . $unsubscribe_url . '</a>', $post_notif_email_body );
    				
				// Physically send email
				$mail_sent = wp_mail( $subscriber->email_addr, $post_notif_email_subject, $post_notif_email_body, $headers );   			

				$send_process_update_columns_arr = array(
					'num_notifs_sent' => ++$post_notif_count_sent
					,'last_subscriber_id_sent' => $subscriber->id
				);
				$send_process_where_clause_arr = array(
					'post_id' => $post_id
					,'notif_sent_dttm' => $notif_intiated_dttm
				);
				$this->update_post_notif_send_process_status( $send_process_update_columns_arr, $send_process_where_clause_arr );			
			
				++$curr_batch_post_notif_count_sent;
				
				if ( $post_notif_count_sent == $subscriber_count ) {
					
					// Send process has completed
		
					// Store all sent datetimes in database as UTC
					$notif_end_dttm = gmdate( "Y-m-d H:i:s" );
 					
					$send_process_set_status_complete_arr = array(
						'notif_end_dttm' => $notif_end_dttm
						,'send_status' => 'C'
					);
					$send_process_set_status_complete_where_clause_arr = array(
						'post_id' => $post_id
						,'notif_sent_dttm' => $notif_intiated_dttm
					);
					$this->update_post_notif_send_process_status( $send_process_set_status_complete_arr, $send_process_set_status_complete_where_clause_arr ); 	
		
					$post_notif_send_complete = true;

					return array( $post_notif_send_complete, $post_notif_count_sent, $notif_end_dttm );
				}
					
				if ( $batch_mode ) {
					
					if ( ( $batch_size > 0 ) && ( 0 == $post_notif_count_sent % $batch_size ) ) {
						
						// Batch size hit
					
						if ( $batch_pause > 0 ) {
					
							// Batch pause is defined, schedule WP cron event to
							//	resume process after specified pause + exit
							$this->schedule_next_batch_after_pause( $post_id );
							return;
						}
						else {
						
							// Batch pause is NOT defined, resume process
							//	(via wp_remote_post() call) immediately + exit						
							$this->trigger_immediate_resumption_of_send( $post_id );
							return;
						}
					}
					/*
					else {	// batch size == 0 OR we've not hit batch size
						// NOOP -> Keep going (drop through here to "out of time or memory test")!!
					*/
				}
				
				// NOTE: At this point we know we have NOT hit a batch size limit!

				if ( ( $timeout_exists ) && ( time() >= $execution_upper_limit ) ) {
					
					// We ARE about to run out of time or memory
					
					if ( $batch_mode ) {
						if ( ( 0 == $batch_size ) && ( $batch_pause > 0 ) ) {
					
							// If batch size is not defined but batch pause IS,
							//	schedule WP cron event to resume process after specified pause + exit
							$this->schedule_next_batch_after_pause( $post_id );
							return;
						}
						/*
						else {	// batch pause == 0
							// NOOP -> Drop to resume process immediately + exit
						}
						*/
					}
					
					// Resume process (via wp_remote_post() call) immediately + exit
					$this->trigger_immediate_resumption_of_send( $post_id );
					return;
				}
				/*
				else {
					// NOOP -> Keep going
				}
				*/
			
			}
		}
	}
    
 	/**
	 * Resolve post notification email subject and body generic variables.
	 *
	 * @since	1.1.0
	 * @param	int		$post_id	Post to resolve variables for.
	 * @return	string	Email headers, email subject, and email body template.
	 */
	private function resolve_post_notification_email_vars( $post_id ) {
		
		$post_notif_options_arr = get_option( 'post_notif_settings' );

		// Replace variables in both the post notif email subject and body 
   		
		// Get post title and author's name
		$post_attribs = get_post( $post_id ); 
		$post_title = $post_attribs->post_title;
   				
		$post_author_data = get_userdata( $post_attribs->post_author );
		$post_author = $post_author_data->display_name;
   		
		// Get post categories
		$category_arr = get_the_category( $post_id );
		$category_list = '';
		foreach ( $category_arr as $category ) {
			$category_list .= $category->name . ', ';
		}
		$category_list = rtrim( $category_list, ', ');

		// NOTE: This is in place to minimize chance that, due to email client settings, subscribers
		//		will be unable to see and/or click the URL links within their email
		$post_permalink = get_permalink( $post_id );
		
		$post_notif_email_subject = $post_notif_options_arr['post_notif_eml_subj'];
		$post_notif_email_subject = str_replace( '@@blogname', get_bloginfo('name'), $post_notif_email_subject );
		$post_notif_email_subject = str_replace( '@@posttitle', $post_title, $post_notif_email_subject );
		$post_notif_email_subject = str_replace( '@@postauthor', $post_author, $post_notif_email_subject );
		$post_notif_email_subject = str_replace( '@@postcategory', $category_list, $post_notif_email_subject );

		// Tell PHP mail() to convert both double and single quotes from their respective HTML entities to their applicable characters
		$post_notif_email_subject = html_entity_decode (  $post_notif_email_subject, ENT_QUOTES, 'UTF-8' );
   			
		$post_notif_email_body_template = $post_notif_options_arr['post_notif_eml_body'];
		$post_notif_email_body_template = str_replace( '@@blogname', get_bloginfo('name'), $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@posttitle', $post_title, $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@postauthor', $post_author, $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@postcategory', $category_list, $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@permalinkurl', $post_permalink, $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@permalink', '<a href="' . $post_permalink . '">' . $post_permalink . '</a>', $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@postexcerptauto', Post_Notif_Misc::generate_excerpt( $post_id, 'auto' ), $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@postexcerptmanual', Post_Notif_Misc::generate_excerpt( $post_id, 'manual' ), $post_notif_email_body_template );

		// NOTE: @@postexcerpt is deprecated, use @@postexcerptmanual instead!
   		$post_notif_email_body_template = str_replace( '@@postexcerpt', Post_Notif_Misc::generate_excerpt( $post_id, 'manual' ), $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@postteaser', Post_Notif_Misc::generate_excerpt( $post_id, 'teaser' ), $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@featuredimage', ( ( has_post_thumbnail( $post_id ) ) ? get_the_post_thumbnail( $post_id, 'thumbnail' ) : '' ), $post_notif_email_body_template );
		$post_notif_email_body_template = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $post_notif_email_body_template );

		// Set sender name and email address
		$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
			. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
  		
		// Specify HTML-formatted email
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		return array ( $headers, $post_notif_email_subject, $post_notif_email_body_template );
		
	}
	
	/**
	 * Schedule WP cron event to send post notifications for specified post.
	 *
	 * @since	1.1.0
	 */
	public function schedule_post_notif_send() {
		
		// Confirm matching nonce
		check_ajax_referer( 'post_notif_send' );

		$post_id = $_POST['post_id'];
		$datetime_local = $_POST['datetime_local'];
		$publish_datetime_local = $_POST['publish_datetime_local'];

		$timestamp_utc = wp_next_scheduled( 'post_notif_send_scheduled_post_notif', array( $post_id ) );
		if ( false === $timestamp_utc ) {
			
			// Not yet scheduled

			// Get individual pieces of current datetime
			$datetime_local_arr = explode( ':', $datetime_local ); 

			// Generate UNIX timestamp in local timezone			
			$timestamp_local = mktime( $datetime_local_arr[3], $datetime_local_arr[4], 0, $datetime_local_arr[0], $datetime_local_arr[1], $datetime_local_arr[2] );
		
			// Convert to UTC for WP cron
			$timestamp_utc = $timestamp_local - Post_Notif_Misc::offset_from_UTC();
			
			// Get individual pieces of publish datetime
			$publish_datetime_local_arr = explode( ':', $publish_datetime_local ); 
			
			// Generate UNIX timestamp in local timezone
			$timestamp_publish_datetime_local = mktime( $publish_datetime_local_arr[3], $publish_datetime_local_arr[4], 0, $publish_datetime_local_arr[0], $publish_datetime_local_arr[1], $publish_datetime_local_arr[2] );
		
			// Convert to UTC for comparison to current timestamp
			$timestamp_publish_datetime_utc = $timestamp_publish_datetime_local - Post_Notif_Misc::offset_from_UTC();
	
			// Get current (UTC) timestamp
			$current_timestamp_utc = time();

			if ( ( $current_timestamp_utc < $timestamp_utc ) && ( $timestamp_publish_datetime_utc <  $timestamp_utc ) ) {
			
				// Scheduled post notif is scheduled for later than current datetime AND later than scheduled post publish!!			

				// Create datetimes for display to user (local) and storage in DB (UTC)
				$local_current_datetime = date( 'Y-m-d H:i:s', $current_timestamp_utc );
				$local_datetime = date( 'Y-m-d H:i:s', $timestamp_local );
				$utc_datetime = date( 'Y-m-d H:i:s', $timestamp_utc );
			
				// Process is being scheduled, initialize process tracking
				$create_new_send_process_arr = array(
					'post_id' => $post_id
					,'notif_sent_dttm' => $local_current_datetime
					,'notif_schedule_dttm' => $utc_datetime
					,'sent_by' => get_current_user_id()
					,'send_status' => 'S'
					,'num_recipients' => 0
					,'scheduled' => 1
				);
				$this->create_post_notif_send_process_row( $create_new_send_process_arr );				

				wp_schedule_single_event( $timestamp_utc, 'post_notif_send_scheduled_post_notif', array( $post_id ) );	
				wp_send_json( array( 'message' => __( 'Post notification has been scheduled for this post!', 'post-notif' ), 'timestamp' => __( 'Scheduled for:', 'post-notif' ) . ' ' . Post_Notif_Misc::UTC_to_local_datetime( $utc_datetime ), 'valid_datetime' => 1 ) );				
			}
			elseif ( $current_timestamp_utc >= $timestamp_utc ) {

				// Cannot schedule process to be run in the past
				wp_send_json( array( 'message' => __( 'Date cannot be in the past! Please try again.', 'post-notif' ), 'valid_datetime' => 0 ) );
			}
			else {

				// Cannot schedule process to be run prior to scheduled post publish
				wp_send_json( array( 'message' => __( 'Please choose a date and time after the scheduled publish date and time.', 'post-notif' ), 'valid_datetime' => 0 ) );
			}
		}
		else {
		
			// Already scheduled
			$utc_datetime = date( 'Y-m-d H:i:s', $timestamp_utc );
			$local_datetime = Post_Notif_Misc::UTC_to_local_datetime( $utc_datetime );
			wp_send_json( array( 'message' => __( 'Post notification is ALREADY scheduled for this post!', 'post-notif' ), 'timestamp' => $local_datetime, 'valid_datetime' => 1 ) );
		}

	}
    
	/**
	 * Execute scheduled post notification send for specified post.
	 *
	 * @since	1.1.0
	 * @param	int		$post_id	Post to execute post notification for.
	 */
	public function execute_scheduled_post_notif_send( $post_id ) {
	
		if ( 'publish' == get_post_status( $post_id ) ) {
			
			// Only send post notifs if post is published!

			// Process is actually starting		
			$send_process_set_status_initiate_arr = array(
				'send_status' => "'I'"
			);
			$send_process_set_status_initiate_where_clause = 
				"post_id = " . $post_id
				. " AND send_status IN ('B','S')";
			$this->update_post_notif_send_process_status_flexible( $send_process_set_status_initiate_arr, $send_process_set_status_initiate_where_clause );

			$this->process_post_notif_send( $post_id );			
		}
		
	}   
	
	/**
	 * Create new post notification send process row.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	array	$insert_column_arr	Set of values mapped to columns in table.
	 * @return	int		Number of rows inserted.
	 */	
	private function create_post_notif_send_process_row( $insert_column_arr ) {

		global $wpdb;
	  
		$notif_row_inserted = $wpdb->insert(
			$wpdb->prefix.'post_notif_post' 
			,array(
				'post_id' => $insert_column_arr['post_id']
				,'notif_sent_dttm' => $insert_column_arr['notif_sent_dttm']
				,'sent_by' => $insert_column_arr['sent_by']
				,'notif_schedule_dttm' => array_key_exists( 'notif_schedule_dttm', $insert_column_arr ) 
					? $insert_column_arr['notif_schedule_dttm'] 
					: null
				,'send_status' => $insert_column_arr['send_status']
				,'num_recipients' => $insert_column_arr['num_recipients']
				,'scheduled' => $insert_column_arr['scheduled']
			)
		);   
		
		return $notif_row_inserted;
		
	}
	
	/**
	 * Update post notification send process row.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	array	$columns_to_update_arr	Set of values mapped to columns, in table, to update.
	 * @param	array	$where_clause_columns_arr	Set of values mapped to columns, in table, for use in SQL WHERE clause.
	 * @return	int		Number of rows updated.
	 */	
	private function update_post_notif_send_process_status( $columns_to_update_arr, $where_clause_columns_arr ) {
		
		global $wpdb;
	  
		$set_columns_arr = array();
		
		foreach ( $columns_to_update_arr as $column_key => $column_value ) {
			$set_columns_arr[ $column_key ] = $column_value;
		}
		
		$where_clause_arr = array();
		
		foreach ( $where_clause_columns_arr as $column_key => $column_value ) {
			$where_clause_arr[ $column_key ] = $column_value;
		}

		$num_rows_updated = $wpdb->update( 
			$wpdb->prefix.'post_notif_post'
			,$set_columns_arr
			,$where_clause_arr			
		);
												
		if ( $num_rows_updated ) {
			return $num_rows_updated;
		}
		else {
			return 0;
		}
		
	}

	/**
	 * Update post notification send process row. [Required to handle ORs in WHERE clause!]
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	array	$columns_to_update_arr	Set of values mapped to columns, in table, to update.
	 * @param	array	[$where_clause_columns_arr	Set of values mapped to columns, in table, for use in SQL WHERE clause.]
	 * @return	int		Number of rows updated.
	 */	
	private function update_post_notif_send_process_status_flexible( $columns_to_update_arr, $where_clause ) {
		
		global $wpdb;
	  
		$set_columns_clause = '';
		
		foreach ( $columns_to_update_arr as $column_key => $column_value ) {
			$set_columns_clause .= $column_key . ' = ' . $column_value . ', ';
		}
		
		if ( ! empty( $set_columns_clause ) ) {
			$set_columns_clause = substr( $set_columns_clause, 0, -2 );
		}
		
		$num_rows_updated = $wpdb->query( 
    			"
    				UPDATE " . $wpdb->prefix.'post_notif_post'
    				. " SET " . $set_columns_clause
    				. " WHERE " . $where_clause
 		);			
		
		if ( $num_rows_updated ) {
			return $num_rows_updated;
		}
		else {
			return 0;
		}
		
	}

	/**
	 * Get active post notification send process row's notif_sent_dttm column value for specified post.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to get active row's notif_sent_dttm column value for.
	 * @return	datetime	Notification start datetime.
	 */	
	private function get_active_send_notif_key( $post_id ) {
		
		global $wpdb;

    	$notif_sent_dttm = $wpdb->get_var(
    		$wpdb->prepare(
    			"
    				SELECT notif_sent_dttm
    				FROM " . $wpdb->prefix.'post_notif_post'
    				. " WHERE post_id = %d
    				AND send_status IN ('B','I','P','S')
    			"
    			, $post_id
    		)
    	);
				
		return $notif_sent_dttm;
		
	}    

	/**
	 * Get active post notification send process row for specified post.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to get active post notification send process row for.
	 * @return	array	Set of columns contained in post notification send process row.
	 */	
	private function get_active_send_notif_row( $post_id ) {
		
		global $wpdb;

    	$post_notif_process_details = $wpdb->get_row(
    		$wpdb->prepare(
    			"
   					SELECT 
   						notif_sent_dttm
   						,send_status
   						,num_recipients
   						,num_notifs_sent
   						,last_subscriber_id_sent
   					FROM " . $wpdb->prefix.'post_notif_post'
   					. " WHERE post_id = %d
    				AND send_status IN ('B','I','P','S')
    			"
    			, $post_id
   			)
   			,ARRAY_A
   		);
   		
		return $post_notif_process_details;
		
	}    

	/**
	 * Get in-process post notification send process row for specified post.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to get in-process post notification send process row for.
	 * @return	array	Set of columns contained in post notification send process row.
	 */	
	private function get_in_process_send_notif_row( $post_id ) {
		
		global $wpdb;

   		$post_notif_process_details = $wpdb->get_row(
   			$wpdb->prepare(
   				"
   					SELECT 
   						notif_sent_dttm
   						,num_recipients
   						,num_notifs_sent
   						,last_subscriber_id_sent
   					FROM " . $wpdb->prefix.'post_notif_post'
   					. " WHERE post_id = %d
   					AND send_status = 'I'
   				"
   				,$post_id
   			)
   			,ARRAY_A
   		);
				
		return $post_notif_process_details;

	}
	
	/**
	 * Schedule WP cron event to immediately send post notifications for specified post.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to schedule post notification send process row for.
	 */	
	private function schedule_immediate_resumption_of_send( $post_id ) {
		
		$this->unschedule_future_post_notif_send( $post_id );
		
		// Get start dttm from "Send Now" initiation
		$post_notif_process_details_arr = $this->get_in_process_send_notif_row( $post_id );
		$timestamp_local = $post_notif_process_details_arr['notif_sent_dttm'];
			
		// Convert to UTC for WP cron
		$timestamp_utc = $timestamp_local - Post_Notif_Misc::offset_from_UTC();
						
		// Schedule past due event (using start dttm from "Send Now" initiation) so that WP cron picks up immediately!
		wp_schedule_single_event( $timestamp_utc, 'post_notif_send_scheduled_post_notif', array( $post_id ) );
		
		spawn_cron( time() );
		
	}

	/**
	 * Unschedule future WP cron event to send post notifications for specified post.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to unschedule post notification send process row for.
	 */	
	private function unschedule_future_post_notif_send( $post_id ) {

		$notif_scheduled_for_dttm = wp_next_scheduled( 'post_notif_send_scheduled_post_notif', array( $post_id ) );
		if ( false !== $notif_scheduled_for_dttm ) {
			
			// Cancel future scheduled send for this post
			wp_unschedule_event( $notif_scheduled_for_dttm, 'post_notif_send_scheduled_post_notif', array( $post_id ) );
		}
		
	}

	/**
	 * Use wp_remote_post to avoid PHP script timeout while processing subscriber set.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to trigger resumption of post notification for.
	 */
 	private function trigger_immediate_resumption_of_send( $post_id ) {
	
		$query_args = array(
			'action' => 'resume_post_notif_send'
			,'nonce' => wp_create_nonce( 'post_notif_resume_immediately' )
		);		
		$url = add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) );
		
		$post_args = array(
			'timeout' => 0.01
			,'blocking' => false
			,'body' => array(
				'post_id' => $post_id
			)
			,'cookies' => $_COOKIE
			,'sslverify' => apply_filters( 'https_local_ssl_verify', false )
		);
		wp_remote_post( esc_url_raw( $url ), $post_args );

	}
	
	/**
	 * Execute resumption of post notification send process for post specified by AJAX.
	 *
	 * @since	1.2.0
	 */	
 	public function execute_immediate_resumption_of_send() {
	
		session_write_close();

		check_ajax_referer( 'post_notif_resume_immediately', 'nonce' );

		$this->process_post_notif_send( $_POST['post_id'] );

		wp_die();
		
	}

	/**
	 * Schedule resumption of post notification send, for specified post, following specified pause duration.
	 *
	 * @since	1.2.0
	 * @access	private
	 * @param	int		$post_id	Post to schedule next batch for.
	 */
	private function schedule_next_batch_after_pause( $post_id ) {
		
		$post_notif_options_arr = get_option( 'post_notif_settings' );
		
		$batch_pause_mins = $post_notif_options_arr['batch_pause'];
		if ( ! is_numeric( $batch_pause_mins ) || ( ! is_int( $batch_pause_mins + 0 ) ) || ( $batch_pause_mins <= 0 ) ) {
			$batch_pause_mins = 0;	
		}
		$batch_pause_secs = $batch_pause_mins * MINUTE_IN_SECONDS;

		// Apply batch pause to current timestamp
		$timestamp_utc = time() + $batch_pause_secs;
						
		// Update status on post_notif_post table row
		$send_process_update_columns_arr = array(
			'send_status' => 'B'
			,'notif_schedule_dttm' => date( 'Y-m-d H:i:s', $timestamp_utc )
		);
		$send_process_where_clause_arr = array(
			'post_id' => $post_id
			,'send_status' => 'I'
		);
		$this->update_post_notif_send_process_status( $send_process_update_columns_arr, $send_process_where_clause_arr );

		$this->unschedule_future_post_notif_send( $post_id );
		
		// Schedule resumption of send process following admin-configured batch pause
		wp_schedule_single_event( $timestamp_utc, 'post_notif_send_scheduled_post_notif', array( $post_id ) );
		
	}
	
 	/**
	 * Enqueue AJAX script that fires when "Test Send" button (in meta box on Edit Post page) is pressed.
	 *
	 * @since	1.1.0
	 * @param	string	$hook	The current page name.
	 * @return	null	If this is not post.php page
	 */
	public function test_post_notif_send_enqueue( $hook ) {

		if ( 'post.php' != $hook ) {

			return;
		}
		
		$post_notif_test_send_nonce = wp_create_nonce( 'post_notif_test_send' );
		wp_localize_script(
			$this->plugin_name
			,'post_notif_test_send_ajax_obj'
			,array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
				,'nonce' => $post_notif_test_send_nonce
				,'processing_msg' => __( 'Processing...', 'post-notif' )
			)
		);  

	}
	
	/**
	 * Send test post notifications, for current post, to (valid) email addresses specified.
	 *
	 * @since	1.1.0
	 */	
	public function test_post_notif_send() {

		// Confirm matching nonce
		check_ajax_referer( 'post_notif_test_send' );

		// Get current post ID
		$post_id = $_POST['post_id'];

		// Get recipients
		$recipients = $_POST['recipients'];
		
		// Resolve comma-delimited recipient list
		$recipients_arr = explode( ',', $recipients );
		
		$sent_email_arr = array();
		$invalid_email_arr = array();
				
		// Resolve non-personalized email variables 
		list( $headers, $post_notif_email_subject, $post_notif_email_body_template ) = $this->resolve_post_notification_email_vars( $post_id );
		
		foreach( $recipients_arr as $recipient_email_addr ) {
			if ( false != trim( $recipient_email_addr ) ) {
				if ( preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i' , $recipient_email_addr ) ) {
			
					// Address IS valid, physically send email
					$mail_sent = wp_mail( $recipient_email_addr, $post_notif_email_subject, $post_notif_email_body_template, $headers );
					$sent_email_arr[] = $recipient_email_addr;
				}
				else {
				
					// Address is NOT valid
					$invalid_email_arr[] = $recipient_email_addr;
				}
			}
		}
		
		wp_send_json( array(
			'process_complete_message' => __( 'Processing...complete.', 'post-notif' )
			,'successfully_sent_label' => __( 'Successfully sent to:', 'post-notif' )
			,'sent_email_arr' => $sent_email_arr
			,'invalid_email_address_label' => __( 'Skipped invalid email addresses:', 'post-notif' )
			,'invalid_email_arr' => $invalid_email_arr 
		) );
		
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
			
		// NOTE: This must have a slug (fourth parameter) matching the first submenu_page's slug, in order to suppress
		//		display of a selectable page for multisite
		add_menu_page(
			'Post Notif'
			,'Post Notif'
			,'edit_others_posts'
			,'post-notif-view-subs'
			,null
			,'dashicons-email'
			,$post_notif_options_arr['admin_menu_position']
		);
		
		add_submenu_page(
			'post-notif-view-subs'
			,__( 'View Subscribers', 'post-notif' )
			,__( 'View Subscribers', 'post-notif' )
			,'edit_others_posts'	// admin and editor roles have this capability
			,'post-notif-view-subs'
			,array( $this, 'define_view_subscribers_page' )			
		);

		add_submenu_page(
			'post-notif-view-subs'
			,__( 'Manage Post Notifs Sent', 'post-notif' )
			,__( 'Manage Post Notifs Sent', 'post-notif' )
			,'edit_others_posts'	// admin and editor roles have this capability
			,'post-notif-manage-posts-sent'			
			,array( $this, 'render_manage_post_notifs_sent_page' )
		);

		add_submenu_page(
			'post-notif-view-subs'
			,__( 'Manage Subscribers', 'post-notif' )
			,__( 'Manage Subscribers', 'post-notif' )
			,'manage_options'	// ONLY admin role has this capability
			,'post-notif-manage-subs'
			,array( $this, 'define_manage_subscribers_page' )
		);

		add_submenu_page(
			'post-notif-view-subs'
			,__( 'Import Subscribers', 'post-notif' )
			,__( 'Import Subscribers', 'post-notif' )
			,'manage_options'	// ONLY admin role has this capability
			,'post-notif-import-subs'
			,array( $this, 'render_import_subscribers_page' )
		);

		add_submenu_page(
			'post-notif-view-subs'
			,__( 'Staged Subscribers', 'post-notif' )
			,__( 'Staged Subscribers', 'post-notif' )
			,'manage_options'	// ONLY admin role has this capability
			,'post-notif-staged-subs'
			,array( $this, 'define_staged_subscribers_page' )
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
		
		// Confirm matching nonce
		check_admin_referer( 'import_subscribers', 'post-notif-import_subscribers' );
		
		global $wpdb;
		
		$file_row_delimiter = "/\\r\\n|\\r|\\n/";
		$textarea_row_delimiter = chr( 13 );
		$subscriber_arr = array();
		
		// Tack prefix on to table names 
		$post_notif_subscriber_stage_tbl = $wpdb->prefix.'post_notif_subscriber_stage';
		$post_notif_sub_stage_cat_tbl = $wpdb->prefix.'post_notif_sub_cat_stage';
		
		
		// Read import file, if populated		
		if ( ( ! empty( $_FILES ) ) && ( ! empty( $_FILES['btnSubscriberFile']['tmp_name'] ) ) ) {
				  
			$file_contents = trim( file_get_contents( $_FILES['btnSubscriberFile']['tmp_name'] ) );
			if ( ! empty( $file_contents ) ) {
						  
				// File IS populated and NOT empty
				$subscriber_arr = preg_split( $file_row_delimiter, $file_contents );
			}
		}
		if ( 0 == count( $subscriber_arr ) ) {
				  
			// No import file was selected OR it's empty, so read contents of textarea
		
			// Split out single textarea, by delimiter (newline), into array elements
			$subscriber_arr = explode( $textarea_row_delimiter, trim( $_POST['tarSubscriberData'] ) );
		}
		
		if ( count( $subscriber_arr ) > 0 ) {
				  
			// There IS subscriber data to validate and (potentially) load
			
			$import_row_number = 1;
				  
			// Retrieve Post Notif-available system categories (for validation below)
			
			$post_notif_options_arr = get_option( 'post_notif_settings' );
				
			$existing_categories_arr = array();
			if ( array_key_exists( 'available_categories', $post_notif_options_arr ) ) {
				
				if ( 1 != $post_notif_options_arr['available_categories'][0] ) {
				
					// Individual categories ARE available for subscribers to choose from
					foreach ( $post_notif_options_arr['available_categories'] as $available_category => $discard ) {			
						$existing_categories_arr[] = $available_category;
					}
				}
				else {
				
					// "All" categories are selected as available in Post Notif settings
					$category_args = array(
						'orderby' => 'name',
						'order' => 'ASC',
						'hide_empty' => 0
					);
					$existing_categories = get_categories( $category_args );
					$existing_categories_arr = array();
					foreach ( $existing_categories as $existing_category ) {			
						$existing_categories_arr[] = $existing_category->cat_ID;
					}
				}
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
						
					if ( '' == $email_addr ) {
								  
						// Blank email address is a showstopper
						$import_status = 'V';
						$status_message = __( 'Blank email address.', 'post-notif' );		  
					}
					else {
						if ( strlen( $email_addr ) > 100 ) {
							$email_addr = substr( $email_addr, 0, 100 );
							
							// Truncated email address is probably not good but as
							//		long as it is in valid format, let the user decide
							//		whether to ignore the warning
							$import_status = 'T';
							$status_message = __( 'Email address truncated (more than 100 chars).', 'post-notif' );
						}
						if ( ! preg_match( '/([-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4})/i' , $email_addr ) ) {
							
							// Invalid email address is a showstopper
							$import_status = 'V';
							$status_message .= __( ' Invalid email address.', 'post-notif' );
						}
					}

					if ( 1 == $num_subscriber_fields ) {
							  
						// NO first name field provided:
						
						// Default blank first name
						$first_name = __( '[Unknown]', 'post-notif' );
						
						// Default "All" category
						$category_arr[0] = 0;
					}
					else {
						
						// First name field HAS been provided (may be blank)
						$first_name = trim( $subscriber_data_arr[1] );
							  
						// Validate first name
						if ( '' == $first_name ) {
							  
							// Default blank first name
							$first_name = __( '[Unknown]', 'post-notif' );
						}
						elseif ( strlen( $first_name ) > 50 ) {
							$first_name = substr( $first_name, 0, 50 );
							
							// Truncated first name should only generate a warning
							//		UNLESS email addr was a showstopper, in which case
							//		do NOT override that hard error
							if ( $import_status == 'S') {
								$import_status = 'T';
							}
							$status_message .= __( ' First name truncated (more than 50 chars).', 'post-notif' );
						}
							
						if ( 2 == $num_subscriber_fields ) {
								  
							// No categories provided, default "All" category
							$category_arr[0] = 0;								  
						}
						else {
					
							// Iterate through provided categories
							for ( $subscriber_data_arr_index = 2; $subscriber_data_arr_index < $num_subscriber_fields; $subscriber_data_arr_index++ ) {
								  
								// Do not store empty category values (e.g CSV row ends with ",")
								$category_val = trim( $subscriber_data_arr[$subscriber_data_arr_index] );
								if ( '' != $category_val ) {
									$category_arr[$subscriber_data_arr_index - 2] = $category_val;
										  
									// Validate categories
									
									if ( ! is_numeric( $category_val ) ) {
							
										// Non-numeric value is a showstopper
										$import_status = 'V';
										$status_message .= __( ' Non-numeric category ', 'post-notif' ) . '(' . $category_val . ').';
										$category_arr[$subscriber_data_arr_index - 2] = -1;
									}
									elseif ( ( 0 != $category_val ) && ( ! in_array( $category_val, $existing_categories_arr) ) ) {

										// Value that does NOT match and existing category in
										//		system is a showstopper
										$import_status = 'V';
										$status_message .= __( ' Invalid category ', 'post-notif' ) . '(' . $category_val . ').';
										$category_arr[$subscriber_data_arr_index - 2] = -1;
									}
								}
							}
							if ( 0 == count( $category_arr ) ) {
								
								// No categories provided, default "All" category
								$category_arr[0] = 0;								  
							}
						}
					}

					if ( 'S' == $import_status ) {
						$status_message = __( 'Staged (pending creation)', 'post-notif' );
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
							if ( -1 != $category_val ) {
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
				
					if ( ( 'S' == $import_status ) && ( isset( $_POST['chkSkipStaging'] ) ) ) {
							  
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
			'C' => __( 'Created', 'post-notif' )					// (C)reated a new subscriber
			,'S' => __( 'Staged', 'post-notif' )					// (S)taged a new subscriber for later creation
  			,'T' => __( 'Warning', 'post-notif' )					// (T)runcated column(s)
  			,'U' => __( 'Duplicate email address', 'post-notif' )	//	D(U)plicate email address
  			,'V' => __( 'Import error', 'post-notif' )				// (V)alidation error
  			,'X' => __( 'System error', 'post-notif' )				//	This indicates a system error
		);

		$staged_subscribers_deleted = 0;
		$staged_subscribers_created = 0;
		$form_action = $_SERVER['REQUEST_URI'];		
		$sort_by_category = false;
		
		$affected_subscriber = false;
		
		if ( ! empty( $_REQUEST['subscriber'] ) ) {
				  
			// Single action needs to be processed
			$current_action = $_REQUEST['action'];
			$affected_subscriber = $_REQUEST['subscriber'];

			// Confirm matching nonce
			check_admin_referer( 'post_notif_' . $current_action . '_' . $affected_subscriber );
		}
		else {				  

			if ( isset( $_REQUEST['doaction'] ) && isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {

				// Confirm matching nonce
				check_admin_referer( 'staged_subscribers', 'post-notif-staged_subscribers' );

				// Bulk action needs to be processed
				$current_action = $_REQUEST['action'];						
			}
			elseif ( isset( $_REQUEST['doaction2'] ) && isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
				
				// Confirm matching nonce
				check_admin_referer( 'staged_subscribers', 'post-notif-staged_subscribers' );

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
				if ( true == $single_action_arr['bulk_ok'] ) {

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
				
		if ( ! empty( $_REQUEST['orderby'] ) ) {					 
			if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
					  
				// This IS a valid, sortable column
				if ( 'categories' != $_REQUEST['orderby'] ) {
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
		if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'desc' == $_REQUEST['order'] ) {
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
			'orderby' => 'name'
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
   			if ( 0 != $cat_val->cat_id ) {
    				$cat_string .= $category_name_arr[$cat_val->cat_id] . ', ';
   			}
   			else {
   				$cat_string = __( 'All', 'post-notif' );  
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
    		'singular' => 'subscriber'
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

    	$view_staged_subs_pg_list_table = new Post_Notif_List_Table( $class_settings_arr, $table_data_arr, $form_action );
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
    					,'status_message' => __( 'Duplicate email address', 'post-notif' )
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
    					,'last_modified' => gmdate( "Y-m-d H:i:s" )
    					,'date_subscribed' => gmdate( "Y-m-d H:i:s" )
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
							,'status_message' => __( 'Successfully created', 'post-notif' )
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
							,'status_message' => __( 'System error - try again later', 'post-notif' )
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
			if ( ! ( strncmp( $create_subscribers_field_name, $create_subscribers_checkbox_prefix, strlen( $create_subscribers_checkbox_prefix ) ) ) ) {
						  
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
									,'status_message' => __( 'Duplicate email address', 'post-notif' )
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
									,'last_modified' => gmdate( "Y-m-d H:i:s" )
									,'date_subscribed' => gmdate( "Y-m-d H:i:s" )
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
										,'status_message' => __( 'Successfully created', 'post-notif' )
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
										,'status_message' => __( 'System error - try again later', 'post-notif' )
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
			if ( ! ( strncmp( $del_subscribers_field_name, $del_subscribers_checkbox_prefix, strlen( $del_subscribers_checkbox_prefix ) ) ) ) {
						  
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
				,'confirm' => array(
					'label' => __( 'Confirm', 'post-notif' )
					,'single_ok' => true
					,'single_conditional' => array(
						'conditional_field' => 'confirmed'
						,'field_values' => array(
							'No'
						)
					)
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
	private function render_subscribers_page( $available_actions_arr ) {
	
		global $wpdb;
		
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		$subscribers_exported = 0;
		$subscribers_confirmed = 0;
		$subscribers_deleted = 0;
		$subscribers_resent_confirmation = 0;
		$subscribers_undeleted = 0;
		$undo_delete_url = '';
		$form_action = $_SERVER['REQUEST_URI'];		
		$sort_by_category = false;
		
		$affected_subscriber = false;
		
		if ( ! empty( $_REQUEST['subscriber'] ) ) {
				  
			// Single action needs to be processed
			$current_action = $_REQUEST['action'];
			$affected_subscriber = $_REQUEST['subscriber'];
			
			// Confirm matching nonce
			check_admin_referer( 'post_notif_' . $current_action . '_' . $affected_subscriber );
		}
		else {				  

			if ( isset( $_REQUEST['doaction'] ) && isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {

				// Confirm matching nonce
				check_admin_referer( 'manage_subscribers', 'post-notif-manage_subscribers' );

				// Bulk action needs to be processed
				$current_action = $_REQUEST['action'];						
			}
			elseif ( isset( $_REQUEST['doaction2'] ) && isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
				
				// Confirm matching nonce
				check_admin_referer( 'manage_subscribers', 'post-notif-manage_subscribers' );

				// Bulk action needs to be processed
				$current_action = $_REQUEST['action2'];
			}
			elseif ( isset( $_REQUEST['action'] ) && ( 'undo_delete' == $_REQUEST['action'] ) ) {
				$current_action = 'undo_delete';	  
			}
			else {
				$current_action = '';
				
				// Physically delete previously soft-deleted subscribers, if any exist
				$old_subscribers_deleted = $this->delete_flagged_subscribers();
			}
		}
		
		switch ( $current_action ) {
			case 'exported':
					  
				// Display subscriber export count passed from process_multiple_subscriber_export()
				$subscribers_exported = $_REQUEST['exportcount'];
				$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber', 'exported', 'exportcount' ), $_SERVER['REQUEST_URI'] ) );
				break;
 			case 'confirm':	  
				
 				// Confirm selected subscribers
			
				if ( $affected_subscriber ) {
				  
					// Confirm single subscriber
					$subscribers_confirmed = $this->process_single_subscriber_confirm( $affected_subscriber );
					$form_action = esc_url_raw( remove_query_arg( array ( 'action', 'subscriber' ), $_SERVER['REQUEST_URI'] ) );
				}
				else {
				  			 
					// Confirm multiple (selected) subscribers via bulk action
					$subscribers_confirmed = $this->process_multiple_subscriber_confirm( $_POST );
				}
				break;
			case 'delete':
					  
				// Delete(s) need to be processed
			
				if ( $affected_subscriber ) {
				  
					// Delete single subscriber
					$subscribers_deleted = $this->process_single_subscriber_delete( $affected_subscriber );

					// Replace delete action with undo_delete action and add nonce to undo delete URL
					$undo_delete_url = remove_query_arg( array ( 'action' ), $form_action );
					$undo_delete_url = add_query_arg( array ( 'action' => 'undo_delete' ), $undo_delete_url );
					$undo_delete_url = wp_nonce_url( $undo_delete_url, 'post_notif_undo_delete_' . $affected_subscriber );

					// Clean up form action by removing action and subscriber parameters
					$form_action = remove_query_arg( array ( 'action', 'subscriber' ), $form_action );
				}
				else {
				  			 
					// Delete multiple (selected) subscribers via bulk action					
					list( $subscribers_deleted, $dttm_deleted ) = $this->process_multiple_subscriber_delete( $_POST );
					
					// Replace delete action with undo_delete action and add dttm_deleted and nonce to undo delete URL
					$undo_delete_url = remove_query_arg( array ( 'action' ), $_SERVER['REQUEST_URI'] );
					$undo_delete_url = add_query_arg( array ( 'action' => 'undo_delete', 'dttm_deleted' => $dttm_deleted ), $undo_delete_url );					
					$undo_delete_url = wp_nonce_url( $undo_delete_url, 'post_notif_undo_delete_multiple_subscribers' );
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
 			case 'undo_delete':
 				
 				// Subscriber undo delete(s) need to be processed
 				
				if ( $affected_subscriber ) {
					
					// Undo single subscriber delete
					$subscribers_undeleted = $this->process_single_subscriber_undo_delete( $affected_subscriber );

					// Clean up form action by removing action, subscriber, and _wpnonce parameters
					$form_action = remove_query_arg( array ( 'action', 'subscriber', '_wpnonce' ), $form_action );
				}
				else {

					// Confirm matching nonce
					check_admin_referer( 'post_notif_undo_delete_multiple_subscribers' );

					// Undo multiple subscriber delete 
					$subscribers_undeleted = $this->process_multiple_subscriber_undo_delete( $_REQUEST['dttm_deleted'] );

					// Clean up form action by removing action, dttm_deleted, and _wpnonce parameters
					$form_action = remove_query_arg( array ( 'action', 'dttm_deleted', '_wpnonce' ), $form_action );
				} 				 				
				break;
		}

		// Define list table columns
		
		if ( is_array( $available_actions_arr ) ) {				  
			$columns_arr = array();
			foreach ( $available_actions_arr['actions'] as $single_action_arr ) {
				if ( true == $single_action_arr['bulk_ok'] ) {

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
				
		if ( ! empty( $_REQUEST['orderby'] ) ) {					 
			if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
					  
				// This IS a valid, sortable column
				if ( 'categories' != $_REQUEST['orderby'] ) {
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
  					
						return ( 'asc' === $order ) ? $result : -$result;
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
		if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'desc' == $_REQUEST['order'] ) {
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
   				WHERE to_delete = 0
   				ORDER BY $orderby $order
   			"
   			,ARRAY_A
   		);
   	
   		// Select categories each subscriber is subscribed to AND pass array to page
   		//		for display
 		$args = array(
			'orderby' => 'name'
			,'order' => 'ASC'
			,'hide_empty' => 0
		);
		$category_arr = get_categories( $args );
		$category_name_arr = array();
		foreach ( $category_arr as $category ) {
			$category_name_arr[ $category->cat_ID ] = $category->name;
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
   				if ( 0 != $cat_val->cat_id ) {
   					$cat_string .= $category_name_arr[ $cat_val->cat_id ] . ', ';
   				}
   				else {
   					$cat_string = __( 'All', 'post-notif' );	  
   					break;
   				}
   			}
   			$cat_string = rtrim ( $cat_string, ', ' );   	
  			$subscribers_arr[ $sub_key ]['categories'] = $cat_string;
  			
  			// Translate binary "Subscription Confirmed?" value to words
  			$subscribers_arr[ $sub_key ]['confirmed'] =  ( ( 1 == $sub_val['confirmed'] ) ? __( 'Yes', 'post-notif' ) : __( 'No', 'post-notif' ) );
  			$subscribers_arr[ $sub_key ]['date_subscribed'] =  Post_Notif_Misc::UTC_to_local_datetime( $sub_val['date_subscribed'] );
  		}	
		if ( $sort_by_category ) {
				  
			// Special sort for category
			usort( $subscribers_arr, 'usort_reorder' );
		}
   	
		// Build page	  
	
    	$class_settings_arr = array(
    		'singular' => 'subscriber'
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

    	$view_subs_pg_list_table = new Post_Notif_List_Table( $class_settings_arr, $table_data_arr, $form_action );
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
		
		if ( ( isset( $_REQUEST['doaction'] ) && ( 'export' == $_REQUEST['action'] ) )
			|| ( isset( $_REQUEST['doaction2'] ) && ( 'export' == $_REQUEST['action2'] ) ) ) {
   	
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
				
 			if ( ! empty( $_REQUEST['orderby'] ) ) {					 
 				if ( array_key_exists ( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
					  
 					// This IS a valid, sortable column
 					if ( 'categories' != $_REQUEST['orderby'] ) {
 						$orderby = $_REQUEST['orderby'];		 
 					}
 					else {
 						$orderby = 'id';
 						$sort_by_category = true;
				
 						// Sort by category requires some special handling since category data is not
 						//		retrieved by original query
 						function usort_reorder( $a, $b ) {
 							$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
 							$result = strcmp( $a['categories'], $b['categories'] );
  					
 							return ( 'asc' === $order ) ? $result : -$result;
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
 			if ( ! empty( $_REQUEST['order'] ) ) {
 				if ( 'desc' == $_REQUEST['order'] ) {
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
 				if ( ! ( strncmp( $exp_subscribers_field_name, $exp_subscribers_checkbox_prefix, strlen( $exp_subscribers_checkbox_prefix ) ) ) ) {
						  
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
   					$subscribers_arr[ $sub_key ][] = $cat_val->cat_id;
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
	 * Perform single subscriber (force-)confirm.
	 *
	 * @since	1.1.0
	 * @access	private
	 * @param	int	$sub_id	ID of subscriber to confirm.
	 * @return	int	Number of confirmations performed.
	 */	
	private function process_single_subscriber_confirm( $sub_id ) {

		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
		
		// Update user's subscriber row so they will now receive post notifs
		$results = $wpdb->update( 
			$post_notif_subscriber_tbl
			,array( 
				'confirmed' => 1
				,'last_modified' => gmdate( "Y-m-d H:i:s" )
			)
			,array( 
				'id' => $sub_id
			)    			
		);

		// Auto assign them to receive All categories (cat_id = 0)
		$num_subs_confirmed = $wpdb->insert(
			$post_notif_sub_cat_tbl
			,array( 
				'id' => $sub_id
				,'cat_id' => 0
			)
		);

		if ( $num_subs_confirmed ) {
				  
			return $num_subs_confirmed;
		}
		else {
				  
		  return 0;
		}

	}
 
	/**
	 * Perform multiple subscriber (force-)confirm.
	 *
	 * @since	1.1.0
	 * @access	private
	 * @param	array	$form_post	The collection of global query vars.
	 * @return	int	Number of confirmations performed.
	 */	
	private function process_multiple_subscriber_confirm( $form_post ) {
			  			  
		global $wpdb;

		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';

		// Define checkbox prefix
		$conf_subscribers_checkbox_prefix = 'chkKey_';
		$subscribers_confirmed = 0;
		
		// For each selected subscriber on submitted form:
		// 	Update their row in subscribers table 
		// 	Insert a new row into the category table
		foreach ( $form_post as $conf_subscribers_field_name => $conf_subscribers_value ) {
			if ( ! ( strncmp( $conf_subscribers_field_name, $conf_subscribers_checkbox_prefix, strlen( $conf_subscribers_checkbox_prefix ) ) ) ) {
						  
				// This is a Subscriber checkbox
				if ( isset( $conf_subscribers_field_name ) ) {

					// Checkbox IS selected

					// Confirm subscriber unless already confirmed
					$user_not_confirmed = $wpdb->get_var(
						$wpdb->prepare(
							"
								SELECT COUNT(id)
								FROM $post_notif_subscriber_tbl
								WHERE id = %d
								AND confirmed = 0
							"
							,$conf_subscribers_value
						)
					);
	
					if ( $user_not_confirmed ) {
						
						// User has NOT yet been confirmed

						// Update user's subscriber row so they will now receive post notifs
						$results = $wpdb->update( 
							$post_notif_subscriber_tbl
							,array( 
								'confirmed' => 1
								,'last_modified' => gmdate( "Y-m-d H:i:s" )
							)
							,array( 
								'id' => $conf_subscribers_value
							)    			
						);

						// Auto assign them to receive All categories (cat_id = 0)
						$num_subs_confirmed = $wpdb->insert(
							$post_notif_sub_cat_tbl
							,array( 
								'id' => $conf_subscribers_value
								,'cat_id' => 0
							)
						);
										
						if ( $num_subs_confirmed )
						{
							  
							// OK, wise-guy, I know you're saying there should never be more than
							//		one subscriber per id!
							$subscribers_confirmed += $num_subs_confirmed;
						}
					}
				}					  
			}
		}
		
		return $subscribers_confirmed;

	}
	
	/**
	 * Perform single subscriber (soft-)delete.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	int	$sub_id	ID of subscriber to (soft-)delete.
	 * @return	int	Number of subscribers (soft-)deleted.
	 */	
	private function process_single_subscriber_delete( $sub_id ) {

		global $wpdb;
	  						
		// Soft-delete subscriber row					
		$num_subs_deleted = $wpdb->update( 
			$wpdb->prefix.'post_notif_subscriber'
			,array( 
				'to_delete' => 1
				,'last_update_dttm' => gmdate( "Y-m-d H:i:s" )
			)    			
			,array( 
				'id' => $sub_id
				,'to_delete' => 0
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
	 * Perform multiple subscriber (soft-)delete.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	array	$form_post	The collection of global query vars.
	 * @return	int	Number of subscribers (soft-)deleted.
	 */	
	private function process_multiple_subscriber_delete( $form_post ) {
			  			  
		global $wpdb;
	  
		// Define checkbox prefix
		$del_subscribers_checkbox_prefix = 'chkKey_';
		$subscribers_deleted = 0;
		
		// For each selected subscriber on submitted form:
		// 	Delete their category rows from preferences table
		// 	Delete their row from subscribers table
		foreach ( $form_post as $del_subscribers_field_name => $del_subscribers_value ) {
			if ( ! ( strncmp( $del_subscribers_field_name, $del_subscribers_checkbox_prefix, strlen( $del_subscribers_checkbox_prefix ) ) ) ) {
						  
				// This is a Subscriber checkbox
				if ( isset( $del_subscribers_field_name ) ) {
										
					// Soft-delete subscriber row
					$last_updt_dttm = gmdate( "Y-m-d H:i:s" );
					$num_subs_deleted = $wpdb->update( 
						$wpdb->prefix.'post_notif_subscriber'
						,array( 
							'to_delete' => 1
							,'last_update_dttm' => $last_updt_dttm
						)    			
						,array( 
							'id' => $del_subscribers_value
							,'to_delete' => 0
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
		
		return array( $subscribers_deleted, $last_updt_dttm );
	}
	
	/**
	 * Perform single subscriber undo delete.
	 *
	 * @since	1.0.5
	 * @access	private
	 * @param	int	$sub_id	ID of subscriber to undo delete of.
	 * @return	int	Number of subscribers affected by undo delete.
	 */	
	private function process_single_subscriber_undo_delete( $sub_id ) {

		global $wpdb;
						
		$undo_delete_count = 0;

		// Un(soft-)delete subscriber row					
		$undo_delete_count = $wpdb->update( 
			$wpdb->prefix.'post_notif_subscriber'
			,array( 
				'to_delete' => 0
				,'last_update_dttm' => gmdate( "Y-m-d H:i:s" )
			)    			
			,array( 
				'id' => $sub_id
				,'to_delete' => 1
			)    			
		);
		
		return $undo_delete_count;
		
	}
	
	/**
	 * Perform multiple subscriber undo delete.
	 *
	 * @since	1.0.5
	 * @access	private
	 * @param	string	$dttm_deleted	The date/time when batch of subscribers was deleted.
	 * @return	int	Number of subscribers affected by undo delete.
	 */	
	private function process_multiple_subscriber_undo_delete( $dttm_deleted ) {
			  		
		global $wpdb;
	  
		$undo_delete_count = 0;
								
		// Un(soft-)delete all (flagged) subscriber rows				
		$undo_delete_count = $wpdb->update( 
			$wpdb->prefix.'post_notif_subscriber'
			,array( 
				'to_delete' => 0
				,'last_update_dttm' => gmdate( "Y-m-d H:i:s" )
			)    			
			,array( 
				'to_delete' => 1
				,'last_update_dttm' => $dttm_deleted
			)    			
		);
		
		return $undo_delete_count;	
		
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
				,'last_modified' => gmdate( "Y-m-d H:i:s" )
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
			if ( ! ( strncmp( $rec_subscribers_field_name, $rec_subscribers_checkbox_prefix, strlen( $rec_subscribers_checkbox_prefix ) ) ) ) {
						  
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
							,'last_modified' => gmdate( "Y-m-d H:i:s" )
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
	 * Render Manage Post Notifs Sent page.
	 *
	 * @since	1.2.0
	 */	
	public function render_manage_post_notifs_sent_page() {
			  			
		global $wpdb;
		
		// Tack prefix on to table names
		$post_notif_post_tbl = $wpdb->prefix.'post_notif_post';
		$users_tbl = $wpdb->base_prefix.'users';
				
		// Define possible status descrs
		$send_status_descr_arr = array(
			'B' => __( 'Batch paused', 'post-notif' )				// (B)atch paused 	
			,'C' => __( 'Send completed', 'post-notif' )			// Send (C)ompleted successfully
			,'I' => __( 'In process', 'post-notif' )				// (I)n process
  			,'P' => __( 'Paused', 'post-notif' )					// (P)aused
  			,'S' => __( 'Scheduled', 'post-notif' )					// (S)cheduled
  			,'X' => __( 'Cancelled', 'post-notif' )					// Process has been cancelled
		);

		if ( ! empty( $_REQUEST['notification'] ) ) {
			
			// Single action needs to be processed
			$current_action = $_REQUEST['action'];
			$affected_notification = $_REQUEST['notification'];

			// Confirm matching nonce
			check_admin_referer( 'post_notif_' . $current_action . '_' . $affected_notification );
		}
		else {
			$current_action = '';
		}

		switch ( $current_action ) {
			case 'cancel':
					  
				// Cancel needs to be processed
				
				// Update status on post_notif_post table row
				$send_process_update_columns_arr = array(
					'notif_end_dttm' => "'". gmdate( "Y-m-d H:i:s" ) . "'"
					,'send_status' => "'X'"
				);
				$send_process_where_clause = 
					"post_id = " . $affected_notification
					. " AND send_status IN ('B','P','S')";
				$this->update_post_notif_send_process_status_flexible( $send_process_update_columns_arr, $send_process_where_clause );
				
				// Unschedule from WP cron
				$this->unschedule_future_post_notif_send( $affected_notification );
				
				break;
 			case 'pause':	  
				
 				// Pause needs to be processed
						
				// Update status on post_notif_post table row
				$send_process_update_columns_arr = array(
					'send_status' => 'P'
				);
				$send_process_where_clause_arr = array(
					'post_id' => $affected_notification
					,'send_status' => 'I'
				);
				$this->update_post_notif_send_process_status( $send_process_update_columns_arr, $send_process_where_clause_arr );
				
				break;
			case 'resume':
					  
				// Resume needs to be processed

				// Update status on post_notif_post table row to use WP cron to finish
				$send_process_update_columns_arr = array(
					'send_status' => "'S'"
				);
				$send_process_where_clause = 
					"post_id = " . $affected_notification
					. " AND send_status IN ('B','P')";
				$this->update_post_notif_send_process_status_flexible( $send_process_update_columns_arr, $send_process_where_clause );
				
				// Restart IMMEDIATELY via WP cron
				$this->schedule_immediate_resumption_of_send( $affected_notification );
			
				break;
		}
		
		// Define available actions
		
		$available_actions_arr = array(
			'actionable_column_name' => 'post_id'
			,'actions' => array(
				'pause' => array(
					'label' => __( 'Pause', 'post-notif' )
					,'single_ok' => true
					,'single_conditional' => array(
						'conditional_field' => 'send_status'
						,'field_values' => array(
							'I'
						)
					)
					,'bulk_ok' => false
				)
				,'resume' => array(
					'label' => __( 'Resume', 'post-notif' )
					,'single_ok' => true
					,'single_conditional' => array(
						'conditional_field' => 'send_status'
						,'field_values' => array(
							'B'
							,'P'
						)
					)
					,'bulk_ok' => false
				)
				,'cancel' => array(
					'label' => __( 'Cancel', 'post-notif' )
					,'single_ok' => true
					,'single_conditional' => array(
						'conditional_field' => 'send_status'
						,'field_values' => array(
							'B'
							,'P'
							,'S'
						)
					)
					,'bulk_ok' => false
				)
			)
		);
		
		// Define list table columns
		
    	$columns_arr = array(
    		'post_id' => __( 'Post ID', 'post-notif' )
    		,'post_title' => __( 'Post Title', 'post-notif' )
    		,'author' => __( 'Author', 'post-notif' )
    		,'notif_sent_dttm' => __( 'Start Date/Time', 'post-notif' )
    		,'notif_schedule_dttm' => __( 'Schedule Date/Time', 'post-notif' )
    		,'notif_end_dttm' => __( 'End Date/Time', 'post-notif' )
    		,'sent_by_login' => __( 'Sender', 'post-notif' )
    		,'send_status_descr' => __( 'Status', 'post-notif' )
    		,'x_notifs_sent_to_y_subs' => __( 'Number of Notifs Sent', 'post-notif' )
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
    		,'notif_schedule_dttm' => array(
    			'notif_schedule_dttm'
    			,false
    		)
    		,'notif_end_dttm' => array(
    			'notif_end_dttm'
    			,false
    		)
    		,'send_status_descr' => array(
    			'send_status_descr'
    			,false
    		)
    	);
				
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			if ( array_key_exists( $_REQUEST['orderby'], $sortable_columns_arr ) ) {
	
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
		if ( ! empty( $_REQUEST['order'] ) ) {
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
		
		// Display warning message if Sent By ID cannot be tied to a user
		$user_login_not_found_msg = __( "*** Can''t find user ID ", 'post-notif' );
   			
		// If sent_by == 0 this means it was run by a scheduled event
		$sent_by_cron_msg = __( "System (scheduled)", 'post-notif' );
								
		/* translators: used in the Sender column, on the Manage Post Notifs Sent page, next to the user's name: i.e., "Devon (scheduled)" */
		$scheduled_text = __( "scheduled", 'post-notif' );
		/* translators: used in the Sender column, on the Manage Post Notifs Sent page, next to the user's name: i.e., "Devon (manual)" */
		$manual_text = __( "manual", 'post-notif' );
		/* translators: "Not Applicable" */
		$not_applicable_text = __( "N/A", 'post-notif' );
		/* translators: used in the Number of Notifs Sent column, on the Manage Post Notifs Sent page, between number of notifs sent and number of total subs: i.e., "3 of 1500" */
		$of_text = __( "of", 'post-notif' );

		// Get post notifs sent(, initiated, scheduled, cancelled, paused)
		$post_notifs_sent_arr = $wpdb->get_results(
			"
   				SELECT post_id AS id
   					,post_id
   					,notif_sent_dttm 
   					,sent_by
    				,IF (user_login IS NOT NULL
    					,CONCAT(user_login, ' (' , IF (scheduled = 1, '" . $scheduled_text . "', '" . $manual_text . "'), ')' )
 	  					,IF (sent_by = 0, CONCAT('" . $sent_by_cron_msg . "', '')
   							,CONCAT('" . $user_login_not_found_msg . "', sent_by) 
   						)
    				) AS sent_by_login
    				,notif_schedule_dttm
   					,notif_end_dttm
    				,send_status
    				,send_status AS send_status_descr
    				,IF (num_recipients = -1, '" . $not_applicable_text . "'
    				, CONCAT(num_notifs_sent, ' " . $of_text . " ', num_recipients)
    				) AS x_notifs_sent_to_y_subs
   					,scheduled
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
   			$post_notifs_sent_arr[ $notif_key ]['post_title'] = $post_object->post_title;
   		
    		$post_author_data = get_userdata( $post_object->post_author );
    		$post_notifs_sent_arr[ $notif_key ]['author'] = $post_author_data->user_login;
    		
   			$post_notifs_sent_arr[ $notif_key ]['notif_sent_dttm'] = Post_Notif_Misc::UTC_to_local_datetime( $notif_val['notif_sent_dttm'] );
    		if ( $post_notifs_sent_arr[ $notif_key ]['notif_schedule_dttm'] ) {
    			$post_notifs_sent_arr[ $notif_key ]['notif_schedule_dttm'] = Post_Notif_Misc::UTC_to_local_datetime( $notif_val['notif_schedule_dttm'] );
    		}
    		else {
    			$post_notifs_sent_arr[ $notif_key ]['notif_schedule_dttm'] = __( 'N/A', 'post-notif' );
    		}
     		if ( $post_notifs_sent_arr[ $notif_key ]['notif_end_dttm'] ) {
     			$post_notifs_sent_arr[ $notif_key ]['notif_end_dttm'] = Post_Notif_Misc::UTC_to_local_datetime( $notif_val['notif_end_dttm'] );
     		}
     		else {
    			$post_notifs_sent_arr[ $notif_key ]['notif_end_dttm'] = __( 'N/A', 'post-notif' );
     		}
    		
  			// Map send_status to descriptive words/phrases
  			$post_notifs_sent_arr[ $notif_key ]['send_status_descr'] = $send_status_descr_arr[ $notif_val['send_status'] ];    		
    	}
  	
		// Build page	  
    
    	$class_settings_arr = array(
    		'singular' => __( 'notification', 'post-notif' )
    		,'plural' => __( 'notifications', 'post-notif' )
			,'ajax' => false
    		,'available_actions_arr' => $available_actions_arr
    	);
    
     	// Single array containing the various arrays to pass to the list table class constructor
    	$table_data_arr = array(
    		'columns_arr' => $columns_arr
    		,'hidden_columns_arr' => array()
    		,'sortable_columns_arr' => $sortable_columns_arr
    		,'rows_per_page' => 0		// NOTE: Pass 0 for single page with all data (i.e. NO pagination)
    		,'table_contents_arr' => $post_notifs_sent_arr    			  
    	);
    	
    	$view_post_notif_list_table = new Post_Notif_List_Table( $class_settings_arr, $table_data_arr, $_SERVER['REQUEST_URI'] );
		$view_post_notif_list_table->prepare_items();		
           	    	
		// Render page
		$post_notif_manage_post_notifs_sent_pg = '';
    	ob_start();
		include( plugin_dir_path( __FILE__ ) . 'views/post-notif-admin-manage-post-notifs-sent.php' );
		$post_notif_manage_post_notifs_sent_pg .= ob_get_clean();
		print $post_notif_manage_post_notifs_sent_pg;	
			  
	}

	/**
	 * Perform physical delete of subscribers flagged for delete.
	 *
	 * @since	1.0.5
	 * @access	private
	 * @return	int	Number of subscribers deleted.
	 */	
	private function delete_flagged_subscribers() {

		global $wpdb;
	  
		// Tack prefix on to table names
		$post_notif_subscriber_tbl = $wpdb->prefix.'post_notif_subscriber';
		$post_notif_sub_cat_tbl = $wpdb->prefix.'post_notif_sub_cat';
		
		$subscribers_deleted = 0;

		// Retrieve IDs of all previously soft-deleted subscribers, if any exist
    	$flagged_subscribers_arr = $wpdb->get_col(
    		"
   				SELECT id
   				FROM $post_notif_subscriber_tbl
   				WHERE to_delete = 1
   				ORDER BY id
   			"
   		);
   				
		foreach ( $flagged_subscribers_arr as $subscriber_id ) {
		
			// There ARE subscribers flagged for deletion
			
			// Delete subscriber's preferences rows						
			$results = $wpdb->delete( 
				$post_notif_sub_cat_tbl
				,array( 
					'id' => $subscriber_id
				)    			
			);
		
			// Delete subscriber row					
			$num_subs_deleted = $wpdb->delete( 
				$post_notif_subscriber_tbl
				,array( 
					'id' => $subscriber_id
				)    			
			);
			if ( $num_subs_deleted )
			{
							  
				// OK, wise-guy, I know you're saying there should never be more than
				//		one subscriber per id!
				$subscribers_deleted += $num_subs_deleted;
			}
		}

		return $subscribers_deleted;
		
	}

}
