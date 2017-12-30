<?php

/**
 * Admin form for the widget (accessible on widget config page [Appearance >> Widgets on admin menu]).
 *
 * This markup generates the administration form of the widget.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes/views
 */
?>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'post-notif' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'call_to_action' ) ); ?>"><?php esc_html_e( 'Call to Action:', 'post-notif' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'call_to_action' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action' ) ); ?>" type="text" value="<?php echo esc_attr( $call_to_action ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>"><?php esc_html_e( 'Button Label:', 'post-notif' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>" type="text" value="<?php echo esc_attr( $button_label ); ?>" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'first_name_field_size' ) ); ?>"><?php _e( 'First Name field size:', 'post-notif' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'first_name_field_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'first_name_field_size' ) ); ?>" type="text" value="<?php echo esc_attr( $first_name_field_size ); ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'first_name_placeholder' ) ); ?>"><?php esc_html_e( 'First Name Placeholder:', 'post-notif' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'first_name_placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'first_name_placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $first_name_placeholder ); ?>" />
	</p>
	<p>
		<input id="<?php echo esc_attr( $this->get_field_id( 'require_first_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'require_first_name' ) ); ?>" type="checkbox" value="1" <?php checked( $require_first_name ); ?> />
 		<label for="<?php echo esc_attr( $this->get_field_id( 'require_first_name' ) ); ?>"><?php _e( 'Require First Name?', 'post-notif' ); ?></label>
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'email_addr_field_size' ) ); ?>"><?php _e( 'Email Address field size:', 'post-notif' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'email_addr_field_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email_addr_field_size' ) ); ?>" type="text" value="<?php echo esc_attr( $email_addr_field_size ); ?>" size="3" />
	</p>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'email_addr_placeholder' ) ); ?>"><?php esc_html_e( 'Email Address Placeholder:', 'post-notif' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email_addr_placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email_addr_placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $email_addr_placeholder ); ?>" />
	</p>
	<p>
		<input class="pn-chkoverridetheme" id="<?php echo esc_attr( $this->get_field_id( 'override_theme_css' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'override_theme_css' ) ); ?>" type="checkbox" value="1" <?php checked( $override_theme_css ); ?> />
 		<label for="<?php echo esc_attr( $this->get_field_id( 'override_theme_css' ) ); ?>"><?php _e( 'Override theme CSS?', 'post-notif' ); ?></label>
	</p>
	<div class="pn-overridetheme" id="id_divPostNotifOverrideTheme" name="divPostNotifOverrideTheme" <?php echo ( checked( $override_theme_css ) ? '' : 'style="display: none"' ); ?> >
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'stylesheet_filename' ) ); ?>"><?php _e( 'Stylesheet filename:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'stylesheet_filename' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'stylesheet_filename' ) ); ?>" type="text" value="<?php echo esc_attr( $stylesheet_filename ); ?>" />
		</p>
        <p class="description">
        	<?php esc_html_e( 'This file must be located in the ../post-notif/public/css directory. If this field is populated, all override fields below will be ignored.', 'post-notif' ); ?>
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'call_to_action_font_family' ) ); ?>"><?php _e( 'Call to Action font family:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'call_to_action_font_family' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action_font_family' ) ); ?>" type="text" value="<?php echo esc_attr( $call_to_action_font_family ); ?>" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'call_to_action_font_size' ) ); ?>"><?php _e( 'Call to Action font size [include unit of measure]:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'call_to_action_font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action_font_size' ) ); ?>" type="text" value="<?php echo esc_attr( $call_to_action_font_size ); ?>" size="3" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label style="vertical-align: top;" for="<?php echo esc_attr( $this->get_field_id( 'call_to_action_font_color' ) ); ?>"><?php _e( 'Call to Action font color:', 'post-notif' ); ?></label>
			&nbsp;&nbsp;<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'call_to_action_font_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action_font_color' ) ); ?>" type="text" value="<?php echo esc_attr( $call_to_action_font_color ); ?>" size="75" />
		</p>	
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder_font_family' ) ); ?>"><?php _e( 'Placeholder font family:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'placeholder_font_family' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder_font_family' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder_font_family ); ?>" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder_font_size' ) ); ?>"><?php _e( 'Placeholder font size [include unit of measure]:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'placeholder_font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder_font_size' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder_font_size ); ?>" size="3" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label style="vertical-align: top;" for="<?php echo esc_attr( $this->get_field_id( 'placeholder_font_color' ) ); ?>"><?php _e( 'Placeholder font color:', 'post-notif' ); ?></label>
			&nbsp;&nbsp;<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'placeholder_font_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder_font_color' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder_font_color ); ?>" size="75" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'input_fields_font_family' ) ); ?>"><?php _e( 'Input fields font family:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'input_fields_font_family' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_fields_font_family' ) ); ?>" type="text" value="<?php echo esc_attr( $input_fields_font_family ); ?>" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'input_fields_font_size' ) ); ?>"><?php _e( 'Input fields font size [include unit of measure]:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'input_fields_font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_fields_font_size' ) ); ?>" type="text" value="<?php echo esc_attr( $input_fields_font_size ); ?>" size="3" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label style="vertical-align: top;" for="<?php echo esc_attr( $this->get_field_id( 'input_fields_font_color' ) ); ?>"><?php _e( 'Input fields font color:', 'post-notif' ); ?></label>
			&nbsp;&nbsp;<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'input_fields_font_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_fields_font_color' ) ); ?>" type="text" value="<?php echo esc_attr( $input_fields_font_color ); ?>" size="75" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'error_font_family' ) ); ?>"><?php _e( 'Error message font family:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'error_font_family' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'error_font_family' ) ); ?>" type="text" value="<?php echo esc_attr( $error_font_family ); ?>" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'error_font_size' ) ); ?>"><?php _e( 'Error message font size [include unit of measure]:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'error_font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'error_font_size' ) ); ?>" type="text" value="<?php echo esc_attr( $error_font_size ); ?>" size="3" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label style="vertical-align: top;" for="<?php echo esc_attr( $this->get_field_id( 'error_font_color' ) ); ?>"><?php _e( 'Error message font color:', 'post-notif' ); ?></label>
			&nbsp;&nbsp;<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'error_font_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'error_font_color' ) ); ?>" type="text" value="<?php echo esc_attr( $error_font_color ); ?>" size="75" />
		</p>		
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'message_font_family' ) ); ?>"><?php _e( 'Success message font family:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'message_font_family' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'message_font_family' ) ); ?>" type="text" value="<?php echo esc_attr( $message_font_family ); ?>" size="18" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'message_font_size' ) ); ?>"><?php _e( 'Success message font size [include unit of measure]:', 'post-notif' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'message_font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'message_font_size' ) ); ?>" type="text" value="<?php echo esc_attr( $message_font_size ); ?>" size="3" />
		</p>
		<p>
			&nbsp;&nbsp;&nbsp;<label style="vertical-align: top;" for="<?php echo esc_attr( $this->get_field_id( 'message_font_color' ) ); ?>"><?php _e( 'Success message font color:', 'post-notif' ); ?></label>
			&nbsp;&nbsp;<input class="color-picker" id="<?php echo esc_attr( $this->get_field_id( 'message_font_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'message_font_color' ) ); ?>" type="text" value="<?php echo esc_attr( $message_font_color ); ?>" size="75" />
		</p>	
	</div>
