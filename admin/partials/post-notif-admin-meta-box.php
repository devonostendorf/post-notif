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
	<em> 
		<?php echo $maintain_notifs_sent_info_message; ?>	
	</em>
	<br />
	<br />
<?php if ( $post_published_or_scheduled ) : ?>
	
	<?php /* Make accurate option text available to JS */ ?>
	<input type="hidden" id="id_hdnPostNotifSendNowOptionLabel" value="<?php esc_attr_e( 'Send Now', 'post-notif' ); ?>">		 
	<input type="hidden" id="id_hdnPostNotifScheduleOptionLabel" value="<?php esc_attr_e( 'Schedule', 'post-notif' ); ?>">		
<?php 	if ( $previously_sent ) : ?>
	<span id="id_spnPostNotifLastSent">
		<?php esc_html_e( 'Last processed:', 'post-notif' ); ?>&nbsp;<?php echo Post_Notif_Misc::UTC_to_local_datetime( $notif_sent_dttm ); ?>
		<br />
	</span>	

	<?php /* Make accurate button text available to JS */ ?>
	<input type="hidden" id="id_hdnPostNotifSendNowButtonLabel" value="<?php esc_attr_e( 'RESEND', 'post-notif' ); ?>">		 
	<input type="hidden" id="id_hdnPostNotifScheduleButtonLabel" value="<?php esc_attr_e( 'Schedule RESEND', 'post-notif' ); ?>">
<?php 	else : ?>		
	<span id="id_spnPostNotifLastSent" style="display: none"></span>

	<?php /* Make accurate button text available to JS */ ?>
	<input type="hidden" id="id_hdnPostNotifSendNowButtonLabel" value="<?php esc_attr_e( 'Send', 'post-notif' ); ?>">
	<input type="hidden" id="id_hdnPostNotifScheduleButtonLabel" value="<?php esc_attr_e( 'Schedule Send', 'post-notif' ); ?>">
<?php 	endif; ?>	
	<div id="id_divPostNotifManualSend"<?php echo ( ( $auto_send_selected || $process_running ) ? ' style="display: none" ' : '' ); ?> >
<?php 	if ( $already_scheduled ) : ?>
		<span id="id_spnPostNotifScheduledFor"><?php esc_html_e( 'Scheduled for:' , 'post-notif' ); ?>&nbsp;<?php echo date( "F j, Y", $notif_scheduled_for_dttm + Post_Notif_Misc::offset_from_UTC() ) . ' @ ' . date( "g:i:s A", $notif_scheduled_for_dttm + Post_Notif_Misc::offset_from_UTC() ); ?>
			<br />
		</span>		
		<br />
<?php 	else : ?>
		<span id="id_spnPostNotifScheduledFor" style="display: none"></span>
<?php	endif; ?>	
		<br />
		<div id="id_divPostNotifManualSendControls"<?php echo ( $already_scheduled ? ' style="display: none" ' : '' ); ?> >
		
<?php /* NOTE: "Send Now" option ONLY available if post_published == true */ ?>
<?php	if ( $post_published ) : ?>
			<strong><span id="id_spnPostNotifSendNowOrSchedActive" class="pn-sendnow"<?php echo ( $already_scheduled ? ' style="display: none" ' : ' ' ); ?> />		
				<?php esc_html_e( 'Send Now', 'post-notif' ); ?>
			</span></strong>
			<span id="id_spnPostNotifSendNowOrSchedChangeTo">(<?php esc_html_e( 'Change to', 'post-notif' ); ?></span> 
			<a href="#" id="id_lnkPostNotifSendNowOrSched"><span id="id_spnPostNotifSendNowOrSchedInactive"><?php esc_html_e( 'Schedule', 'post-notif' ); ?></span></a>)
<br />		
			<div id="id_spnPostNotifSendSchedTimestamp" style="display: none">
<?php 	else : ?>
			<strong><span id="id_spnPostNotifSendNowOrSchedActive" class="pn-schedule">
				Schedule
			</span></strong>
			<div id="id_spnPostNotifSendSchedTimestamp">
<?php 	endif; ?>
				<legend class="screen-reader-text"><?php esc_html_e( 'Datetime to schedule post notification to run at', 'post-notif' ); ?></legend>
				<label>
					<span class="screen-reader-text"><?php esc_html_e( 'Month', 'post-notif' ); ?></span>
					<select id="id_selPostNotifSchedMonth" name="selPostNotifSchedMonth">
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
					<span class="screen-reader-text"><?php esc_html_e( 'Day', 'post-notif' ); ?></span>
					<input type="text" id="id_selPostNotifSchedDay" name="selPostNotifSchedDay" value="<?php echo esc_attr( $current_datetime_arr[1] ); ?>" maxlength="2" autocomplete="off" style="width: 30px;" />
				</label>, 
				<label>
					<span class="screen-reader-text"><?php esc_html_e( 'Year', 'post-notif' ); ?></span>
					<input type="text" id="id_selPostNotifSchedYear" name="selPostNotifSchedYear" value="<?php echo esc_attr( $current_datetime_arr[2] ); ?>" maxlength="4" autocomplete="off" style="width: 50px;" />
				</label>@
				<label>
					<span class="screen-reader-text"><?php esc_html_e( 'Hour', 'post-notif' ); ?></span>
					<input type="text" id="id_selPostNotifSchedHour" name="selPostNotifSchedHour" value="<?php echo esc_attr( $current_datetime_arr[3] ); ?>" maxlength="2" autocomplete="off" style="width: 30px;" />
				</label>:
				<label>
					<span class="screen-reader-text"><?php esc_html_e( 'Minute', 'post-notif' ); ?></span>
					<input type="text" id="id_selPostNotifSchedMinute" name="selPostNotifSchedMinute" value="<?php echo esc_attr( $current_datetime_arr[4] ); ?>" maxlength="2" autocomplete="off" style="width: 30px;" />
				</label>
			</div>
<?php 	if ( $previously_sent ) : ?>
			<input type="button" name="btnPostNotifSend" id="id_btnPostNotifSend" value="<?php $post_published ? esc_attr_e( 'RESEND', 'post-notif' ) : esc_attr_e( 'Schedule RESEND', 'post-notif' ); ?>"<?php echo ( $already_scheduled ? ' style="display: none" ' : ' ' ); ?> />
<?php	else : ?>
			<input type="button" name="btnPostNotifSend" id="id_btnPostNotifSend" value="<?php $post_published ? esc_attr_e( 'Send', 'post-notif' ) : esc_attr_e( 'Schedule Send', 'post-notif' ); ?>"<?php echo ( $already_scheduled ? ' style="display: none" ' : ' ' ); ?> />				  
<?php	endif; ?>
		</div>
		<br />
		
<?php /* Add progress bar and label to page */ ?>
		<div id="id_divPostNotifProgressBar"><span id="id_spnPostNotifProgressBarLabel"></span></div>			
	</div>
	
<?php /* Add status message to page */ ?> 
	<span id="id_spnPostNotifStatus"><?php echo esc_html( $status_message ); ?></span>	
<?php endif; ?>
	<br />
	<br />
	
<?php /* Add Test Send section to page*/ ?>
	<fieldset>
		<legend style="font-weight: bold;"><?php esc_html_e( 'Test Send', 'post-notif' ); ?></legend>
		<label><?php esc_html_e( 'Send to:', 'post-notif' ); ?>
			<input type="email" name="emlPostNotifTestSendRecipients" id="id_emlPostNotifTestSendRecipients" />
		</label>
		<input type="button" name="btnPostNotifTestSend" id="id_btnPostNotifTestSend" value="<?php esc_attr_e( 'Test Send', 'post-notif' ); ?>" />
		<br />
		<span id="id_spnPostNotifTestSendStatus"></span>
	</fieldset>
