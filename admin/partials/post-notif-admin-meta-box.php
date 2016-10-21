<?php

/**
 * Contents of Post Notif meta box.
 *
 * This markup generates the contents of the Post Notif meta box.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.1.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/partials
 */
?>
	<input type="hidden" name="hdnPostID" id="id_hdnPostID" value="<?php echo esc_attr( $post->ID ); ?>" />
	<?php if ( $previously_sent ) : ?>
		<span id="id_spnPostNotifLastSent"><?php esc_html_e( 'Last sent:', 'post-notif' ); ?>&nbsp;<?php echo Post_Notif_Misc::UTC_to_local_datetime( $notif_sent_dttm ); ?></span>	

		<?php /* Make accurate button text available to JS */ ?>
		<input type="hidden" id="id_hdnPostNotifSendNowLabel" value="<?php esc_attr_e( 'RESEND', 'post-notif' ); ?>">		 
		<input type="hidden" id="id_hdnPostNotifSchedSendLabel" value="<?php esc_attr_e( 'Schedule RESEND', 'post-notif' ); ?>">
	<?php else : ?>		
		<span id="id_spnPostNotifLastSent" style="display: none"></span>

		<?php /* Make accurate button text available to JS */ ?>
		<input type="hidden" id="id_hdnPostNotifSendNowLabel" value="<?php esc_attr_e( 'Send', 'post-notif' ); ?>">
		<input type="hidden" id="id_hdnPostNotifSchedSendLabel" value="<?php esc_attr_e( 'Schedule Send', 'post-notif' ); ?>">
	<?php endif; ?>
	<?php if ( $already_scheduled ) : ?>
		<br />
		<span id="id_spnPostNotifScheduledFor"><?php esc_html_e( 'Scheduled for:' , 'post-notif' ); ?>&nbsp;<?php echo date( "F j, Y", $notif_scheduled_for_dttm + Post_Notif_Misc::offset_from_UTC() ) . ' @ ' . date( "g:i:s A", $notif_scheduled_for_dttm + Post_Notif_Misc::offset_from_UTC() ); ?></span>
		
		<?php /* Hide "Send Now" and "Schedule" radio buttons */ ?>
		<span id="id_spnPostNotifSchedRadioButtons" style="display: none">
	<?php else : ?>
		<br />
		<span id="id_spnPostNotifScheduledFor" style="display: none"></span>

		<?php /* Show "Send Now" and "Schedule" radio buttons */ ?>
		<span id="id_spnPostNotifSchedRadioButtons"<?php echo ( $hide_buttons ? ' style="display: none" ' : ' ' ); ?> >				
	<?php endif; ?>

			<?php /* Show radio buttons for "Send Now" (selected, by default) and "Schedule [for later]" */ ?>
			<table class="form-table">
				<tr valign="top">
					<td>
						<span class="block-element">
							<input type="radio" name="radSendPostNotif" id="id_radSendPostNotifNow" checked="checked">
							<label for="id_radSendPostNotifNow"><?php esc_html_e( 'Send Now', 'post-notif' ); ?></label>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</span>
						<span class="block-element">
							<input type="radio" name="radSendPostNotif" id="id_radSendPostNotifSched">
							<label for="id_radSendPostNotifSched"><?php esc_html_e( 'Schedule', 'post-notif' ); ?></label>
						</span>
					</td>
				</tr>
			</table>
			<br />
		</span>
		<span id="id_spnPostNotifSendSchedTimestamp" style="display: none">
			<legend class="screen-reader-text">Datetime to schedule post notification to run at</legend>
			<div>
				<label>
					<span class="screen-reader-text">Month</span>
					<select id="id_postNotifSchedMonth" name="postNotifSchedMonth">
<?php
	$current_datetime_arr = explode( ':', current_time( 'm:d:Y:H:i' ) );

	for ( $month = 1; $month <= 12; $month++ ) {
		$zero_padded_month = str_pad( $month, 2, '0', STR_PAD_LEFT);
		$month_short_descr = date( "M", mktime( 0, 0, 0, $month ) );
		echo '<option value="' . esc_attr( $zero_padded_month ) . '" data-text="' . esc_attr( $month_short_descr ) .  '"' . ( $month == $current_datetime_arr[0] ? ' selected="selected"' : '' ) . '>' . esc_html( $zero_padded_month ) . '-' . esc_html( $month_short_descr ) . '</option>';
	}
?>
					</select>
				</label> 
				<label>
					<span class="screen-reader-text">Day</span>
					<input type="text" id="id_postNotifSchedDay" name="postNotifSchedDay" value="<?php echo esc_attr( $current_datetime_arr[1] ); ?>" size="2" maxlength="2" autocomplete="off" />
				</label>, 
				<label>
					<span class="screen-reader-text">Year</span>
					<input type="text" id="id_postNotifSchedYear" name="postNotifSchedYear" value="<?php echo esc_attr( $current_datetime_arr[2] ); ?>" size="4" maxlength="4" autocomplete="off" />
				</label>@
				<label>
					<span class="screen-reader-text">Hour</span>
					<input type="text" id="id_postNotifSchedHour" name="postNotifSchedHour" value="<?php echo esc_attr( $current_datetime_arr[3] ); ?>" size="2" maxlength="2" autocomplete="off" />
				</label>:
				<label>
					<span class="screen-reader-text">Minute</span>
					<input type="text" id="id_postNotifSchedMinute" name="postNotifSchedMinute" value="<?php echo esc_attr( $current_datetime_arr[4] ); ?>" size="2" maxlength="2" autocomplete="off" />
				</label>
			</div>
		</span>
	<?php if ( $already_scheduled ) : ?>
	
		<?php /* Display Send Post Notif button */ ?>
		<input type="button" name="btnPostNotifCancelSchedSend" id="id_btnPostNotifCancelSchedSend" value="<?php esc_attr_e( 'Cancel', 'post-notif' ); ?>" />
		<?php if ( $previously_sent ) : ?>
			<br />

			<?php /* Already sent, display RESEND Post Notif button */ ?>
			<input type="button" name="btnPostNotifSend" id="id_btnPostNotifSend" value="<?php esc_attr_e( 'RESEND', 'post-notif' ); ?>" style="display: none" />
		<?php else : ?>
			<br />
		
			<?php /* Display Send Post Notif button */ ?>
			<input type="button" name="btnPostNotifSend" id="id_btnPostNotifSend" value="<?php esc_attr_e( 'Send', 'post-notif' ); ?>" style="display: none" />
		<?php endif; ?>
	<?php else : ?>
		<input type="button" name="btnPostNotifCancelSchedSend" id="id_btnPostNotifCancelSchedSend" value="<?php esc_attr_e( 'Cancel', 'post-notif' ); ?>" style="display: none" />
		<?php if ( $previously_sent ) : ?>
			<br />

			<?php /* Already sent, display RESEND Post Notif button */ ?>
			<input type="button" name="btnPostNotifSend" id="id_btnPostNotifSend" value="<?php esc_attr_e( 'RESEND', 'post-notif' ); ?>"<?php echo ( $hide_buttons ? ' style="display: none" ' : ' ' ); ?> />
		<?php else : ?>
			<br />
		
			<?php /* Display Send Post Notif button */ ?>
			<input type="button" name="btnPostNotifSend" id="id_btnPostNotifSend" value="<?php esc_attr_e( 'Send', 'post-notif' ); ?>"<?php echo ( $hide_buttons ? ' style="display: none" ' : ' ' ); ?> />				  
		<?php endif; ?>
	<?php endif; ?>	
	<br />
	<br />
	<br />
	<?php /* Add progress bar and label to page */ ?>
	<div id="id_divSendPostNotifProgressBar"><span id="id_spnSendPostNotifProgressBarLabel"></span></div>
			
	<?php /* Add status message to page */ ?> 
	<span id="id_spnPostNotifStatus"><?php echo esc_html( $status_message ); ?></span>
