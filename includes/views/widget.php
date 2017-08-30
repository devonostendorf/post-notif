<?php

/**
 * Public form for the widget.
 *
 * This markup generates the public-facing widget.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes/views
 */

if ( ! empty ($instance['title'] )) {
	echo $args['before_title'] . $title . $args['after_title'];
} 
?>
	<form id="id_frmSubscribe">
		<span id="id_spnSuccessMsg"></span>
		<label id="id_lblCallToAction"><?php echo esc_html( $instance['call_to_action'] ); ?></label>
		<br />
		<input type="text" id="id_txtFirstName" name="txtFirstName" size="<?php echo $instance['first_name_field_size']; ?>" maxlength="50" placeholder="<?php echo esc_attr( $instance['first_name_placeholder'] ); ?>" <?php if ( $instance['require_first_name'] ) echo 'required'; ?>/>
		<br />
		<input type="email" id="id_txtEmailAddr" name="txtEmailAddr" size="<?php echo $instance['email_addr_field_size']; ?>" maxlength="100" placeholder="<?php echo esc_attr( $instance['email_addr_placeholder'] ); ?>" required/>	
		<ul>
			<button type="button" id="id_btnSubmit" name="btnSubmit"><?php echo $instance['button_label']; ?></button>
		</ul>
		<span id="id_spnErrorMsg"></span>
	</form>
