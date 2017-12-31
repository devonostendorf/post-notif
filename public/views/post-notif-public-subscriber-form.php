<?php

/**
 * Public-facing subscriber form.
 *
 * This markup generates the public-facing subscriber form.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.3.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/public/views
 */
?>

<?php	if ( 'no' == $clean_atts['is_widget'] ) : ?>
	<aside id="id_pn_aside_subscriber_form_<?php echo $clean_atts['id']; ?>" class="widget class-post-notif">
<?php		if ( ! empty( $clean_atts['title'] ) ) : ?>
				<h1 id="id_pn_h1_title_<?php echo $clean_atts['id']; ?>" class="widget-title"><?php echo esc_html( $clean_atts['title'] ); ?></h1>
<?php 		endif; ?>
<?php 	endif; ?>
		<form id="id_pn_frm_subscriber_form_<?php echo $clean_atts['id']; ?>" class="pn-frm-subscriber-form">
			<span id="id_pn_spn_success_msg_<?php echo $clean_atts['id']; ?>" class="pn-spn-success-msg"></span>
			<label id="id_pn_lbl_call_to_action_<?php echo $clean_atts['id']; ?>" class="pn-lbl-call-to-action"><?php echo esc_html( $clean_atts['call_to_action'] ); ?></label>
			<br />
			<input type="text" id="id_pn_txt_first_name_<?php echo $clean_atts['id']; ?>" class="pn-txt-first-name" name="pn_txt_first_name_<?php echo $clean_atts['id']; ?>" size="<?php echo esc_attr( $clean_atts['first_name_field_size'] ); ?>" maxlength="50" placeholder="<?php echo esc_attr( $clean_atts['first_name_placeholder'] ); ?>" <?php if ( 'yes' == $clean_atts['require_first_name'] ) echo 'required'; ?>/>
			<br />
			<input type="email" id="id_pn_eml_email_addr_<?php echo $clean_atts['id']; ?>" class="pn-eml-email-addr" name="pn_eml_email_addr_<?php echo $clean_atts['id']; ?>" size="<?php echo esc_attr( $clean_atts['email_addr_field_size'] ); ?>" maxlength="100" placeholder="<?php echo esc_attr( $clean_atts['email_addr_placeholder'] ); ?>" required/>	
			<ul>
				<button type="button" id="id_pn_btn_subscriber_form_submit_<?php echo esc_attr( $clean_atts['id'] ); ?>" class="pn-btn-subscriber-form-submit" name="pn_btn_subscriber_form_submit_<?php echo $clean_atts['id']; ?>"><?php echo $clean_atts['button_label']; ?></button>
			</ul>
			<span id="id_pn_spn_error_msg_<?php echo $clean_atts['id']; ?>" class="pn-spn-error-msg"></span>
		</form>
<?php	if ( 'no' == $clean_atts['is_widget'] ) : ?>
	</aside>
<?php 	endif; ?>
