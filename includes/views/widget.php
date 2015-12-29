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
		<input type="text" id="id_txtFirstName" name="txtFirstName" size="20" maxlength="50" placeholder="<?php echo esc_attr( $instance['first_name_placeholder'] ); ?>" />
		<br />
		<input type="email" id="id_txtEmailAddr" name="txtEmailAddr" size="30" maxlength="100" placeholder="<?php echo esc_attr( $instance['email_addr_placeholder'] ); ?>" required/>	
		<ul>
			<input type="button" id="id_btnSubmit" name="btnSubmit" value="<?php echo esc_attr( $instance['button_label'] ); ?>" />
		</ul>
		<span id="id_spnErrorMsg"></span>
	</form>
