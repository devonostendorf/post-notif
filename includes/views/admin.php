<?php

/**
 * Admin form for the widget (accessible on widget config page [Appearance >> Widgets on admin menu]).
 *
 * This markup generates the administration form of the widget.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/includes/views
 */
?>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'post-notif' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'call_to_action' ) ); ?>"><?php _e( 'Call to Action:', 'post-notif' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'call_to_action' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action' ) ); ?>" type="text" value="<?php echo esc_attr( $call_to_action ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>"><?php _e( 'Button Label:', 'post-notif' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>" type="text" value="<?php echo esc_attr( $button_label ); ?>" />
	</p>
