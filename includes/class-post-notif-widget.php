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

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

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

		$post_notif_options_arr = get_option( 'post_notif_settings' );
		
		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;
		
		ob_start();		
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		include( plugin_dir_path( __FILE__ ) . 'partials/post-notif-includes-widget-title.php' );
		echo do_shortcode( '[post_notif_subscribe'
			. ' is_widget="yes"'
			. ' id="widget"'
			. ' call_to_action="' . $instance['call_to_action'] . '"'
			. ' button_label="' . $instance['button_label'] . '"'
			. ' first_name_field_size="' . $instance['first_name_field_size'] . '"'
			. ' first_name_placeholder="' . $instance['first_name_placeholder'] . '"'
			. ' email_addr_field_size="' . $instance['email_addr_field_size'] . '"'
			. ' email_addr_placeholder="' . $instance['email_addr_placeholder'] . '"'
			. ' require_first_name="' . ( ( 1 == $instance['require_first_name'] ) ? 'yes': 'no' ) . '"'
			. ' override_theme_css="' . ( ( 1 == $instance['override_theme_css'] ) ? 'yes': 'no' ) . '"'
			. ' stylesheet_filename="' . $instance['stylesheet_filename'] . '"'
			. ' call_to_action_font_family="' . $instance['call_to_action_font_family'] . '"'
			. ' call_to_action_font_size="' . $instance['call_to_action_font_size'] . '"'
			. ' call_to_action_font_color="' . $instance['call_to_action_font_color'] . '"'
			. ' placeholder_font_family="' . $instance['placeholder_font_family'] . '"'
			. ' placeholder_font_size="' . $instance['placeholder_font_size'] . '"'
			. ' placeholder_font_color="' . $instance['placeholder_font_color'] . '"'
			. ' input_fields_font_family="' . $instance['input_fields_font_family'] . '"'
			. ' input_fields_font_size="' . $instance['input_fields_font_size'] . '"'
			. ' input_fields_font_color="' . $instance['input_fields_font_color'] . '"'
			. ' error_font_family="' . $instance['error_font_family'] . '"'
			. ' error_font_size="' . $instance['error_font_size'] . '"'
			. ' error_font_color="' . $instance['error_font_color'] . '"'
			. ' message_font_family="' . $instance['message_font_family'] . '"'
			. ' message_font_size="' . $instance['message_font_size'] . '"'
			. ' message_font_color="' . $instance['message_font_color'] . '"'
			. ' error_reqd_first_name_blank="' . $post_notif_options_arr['widget_error_reqd_first_name_blank'] . '"'
			. ' error_email_addr_blank="' . $post_notif_options_arr['widget_error_email_addr_blank'] . '"'
			. ' error_email_addr_invalid="' . $post_notif_options_arr['widget_error_email_addr_invalid'] . '"'
			. ' info_message_processing="' . $post_notif_options_arr['widget_info_message_processing'] .'"'
			. ' info_message_already_subscribed="' . $post_notif_options_arr['widget_info_message_already_subscribed'] . '"'
			. ' failure_message="' . $post_notif_options_arr['widget_failure_message'] . '"'
			. ' success_message="' . $post_notif_options_arr['widget_success_message'] . '"'
			. ']' 
		);
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
		$instance['stylesheet_filename'] = strip_tags( $new_instance['stylesheet_filename'] );
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
		$stylesheet_filename = isset( $instance['stylesheet_filename'] ) ? esc_attr( $instance['stylesheet_filename'] ) : $post_notif_widget_defaults_arr['stylesheet_filename_default'];
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

} // end class
add_action( 'widgets_init', create_function( '', 'register_widget("Post_Notif_Widget");' ) );
