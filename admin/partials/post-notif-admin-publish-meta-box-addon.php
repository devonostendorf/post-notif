<?php

/**
 * Add Post Notif option to Publish meta box.
 *
 * This markup generates the Post Notif option in the Publish meta box.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.1.3
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/partials
 */
?>
	<div class="misc-pub-section"> 

		<?php /* Make accurate option text available to JS */ ?>
		<input type="hidden" id="id_hdnPostNotifAutoOptionLabel" value="<?php esc_attr_e( 'Auto', 'post-notif' ); ?>">		 
		<input type="hidden" id="id_hdnPostNotifManualOptionLabel" value="<?php esc_attr_e( 'Manual', 'post-notif' ); ?>">		
		<span class="dashicons dashicons-email-alt" style="opacity: 0.5;"></span>
		&nbsp;Post Notif:&nbsp;
		<strong><span id="id_spnPostNotifSchedActive"><?php echo esc_html( $active ); ?></span></strong>
		(<?php esc_html_e( 'Change to', 'post-notif' ); ?> <a href="#" id="id_lnkPostNotifSchedAuto" class="<?php echo esc_attr( $class ); ?>">
		<span id="id_spnPostNotifSchedInactive"><?php echo esc_html( $inactive ); ?></span></a>)
		<input type="hidden" name="hdnPostNotifSchedAuto" id="id_hdnPostNotifSchedAuto" value="<?php echo esc_attr( $send_notif_on_publish ); ?>" />
	</div>
