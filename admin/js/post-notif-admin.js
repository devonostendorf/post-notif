(function( $ ) {
	'use strict';
	
	$(function() {
	 	$("#id_btnPostNotifSend").click(function(e) { 
	 		var post_id = document.getElementById("id_hdnPostID").value;

 			// Hide Send button  	
 			jQuery('#id_btnPostNotifSend').hide();
 			
 			// Set Post Notif status message span to contain processing message
	 		jQuery("#id_spnPostNotifStatus").text(post_notif_send_ajax_obj.processing_msg);	 			 		
 			 		
	 		var send_post_notif_now = document.getElementById('id_radSendPostNotifNow');
	 		if (send_post_notif_now.checked) {

	 			// Send NOW	

	 			// Hide radio buttons
	 			jQuery('#id_spnPostNotifSchedRadioButtons').hide();
 		 			
	 			// Call Post Notif send init function
	 			$.post(ajaxurl, {
	 				action: 'init_post_notif_send',
	 				post_id: post_id,
	 			}, function(data) {

	 				if ('-1' === data.status) {

	 					// Process is already running - STOP PROCESSING!
	 					jQuery("#id_spnPostNotifStatus").text(data.message);	 					
	 				} 
	 				else {

	 					// All is well, carry on
	 			
	 					// Create a jQuery Progressbar
	 					jQuery("#id_post_notif_progress_bar").progressbar();

	 					// Check status of process every second
	 					var processCheckTimer = setInterval(function() {
	 				
	 						// Get the current status of the send process
	 						$.post(ajaxurl, {
	 							action: 'get_post_notif_send_status',
	 							post_id: post_id,
	 						}, function(response) {

	 							if ('-1' === response) {

	 								// Set the progress bar to display 100% complete
	 								jQuery("#id_divSendPostNotifProgressBar").progressbar({
	 									value: 100
	 								});
	 			
	 								// Kill timer
	 								clearInterval(processCheckTimer);
	 						
	 								// Hardcode progress bar label to 100% 
	 								jQuery("#id_spnSendPostNotifProgressBarLabel").text( "100%" );
	 							} 
	 							else {

	 								// Update the progress bar to display appropriate percent complete
	 								var percentComplete = Math.floor(100 * response);
	 								jQuery("#id_divSendPostNotifProgressBar").progressbar({
	 									value: percentComplete
	 								});
	 						
	 								// Set progress bar label to appropriate percent complete 
	 								jQuery("#id_spnSendPostNotifProgressBarLabel").text( percentComplete + "%" );
	 							}	 					
	 						});

	 					}, 1000);

	 					$.post(post_notif_send_ajax_obj.ajax_url, {
	 						_ajax_nonce: post_notif_send_ajax_obj.nonce,
	 						action: "post_notif_send",
	 						post_id: post_id,
	 					}, function(data) {

	 						// Update Post Notif status message span with total count of notifs sent
	 						jQuery("#id_spnPostNotifStatus").text(data.message);
	 						
	 						// Update and show Post Notif last sent span with last run timestamp
	 						jQuery("#id_spnPostNotifLastSent").text(data.timestamp);
	 						jQuery("#id_spnPostNotifLastSent").show();	 			
	 					});
	 				} 					

	 			});
	 		}
	 		else {
	 			
	 			// Schedule to run
	 			
	 			// Validate date/time fields
	 			
	 			// Grab datetime pieces from user's entries into input fields
	 			var schedYear = $('#id_postNotifSchedYear').val();
				var schedMonth = $('#id_postNotifSchedMonth').val();
				var schedDay = $('#id_postNotifSchedDay').val();
				var schedHour = $('#id_postNotifSchedHour').val();
				var schedMinute = $('#id_postNotifSchedMinute').val();
			
				// Attempt to build a Date object
				var scheduleDatetime = new Date( schedYear, schedMonth - 1, schedDay, schedHour, schedMinute );
				if (scheduleDatetime.getFullYear() != schedYear
					|| (1 + scheduleDatetime.getMonth()) != schedMonth
					|| scheduleDatetime.getDate() != schedDay
					|| scheduleDatetime.getMinutes() != schedMinute) {	
				
					// This is an invalid datetime
					jQuery("#id_spnPostNotifStatus").text(post_notif_send_ajax_obj.invalid_date_format_msg);	
					jQuery('#id_btnPostNotifSend').show();
					return false;
				}
					 			
	 			// Hide radio buttons
	 			jQuery('#id_spnPostNotifSchedRadioButtons').hide();

	 			var localDatetime = (1 + scheduleDatetime.getMonth()) + ':'
	 				+ scheduleDatetime.getDate() + ':'
	 				+ scheduleDatetime.getFullYear() + ':'
	 				+ scheduleDatetime.getHours() + ':'
	 				+ scheduleDatetime.getMinutes();
	 		
	 			$.post(post_notif_send_ajax_obj.ajax_url, {
	 				_ajax_nonce: post_notif_send_ajax_obj.nonce,
	 				action: "schedule_post_notif_send",
	 				post_id: post_id,
	 				datetime_local: localDatetime,
	 			}, function(data) {
	 				
	 				if (data.valid_datetime) {
	 					
	 					// All's well
	 					
	 					// Hide schedule time input fields
	 					jQuery("#id_spnPostNotifSendSchedTimestamp").hide();
	 				
	 					// Update and show Post Notif scheduled for message span with scheduled for timestamp
	 					jQuery("#id_spnPostNotifScheduledFor").text(data.timestamp);
	 					jQuery("#id_spnPostNotifScheduledFor").show();
	 				
	 					// Show Cancel button
	 					jQuery('#id_btnPostNotifCancelSchedSend').show();
	 				}
	 				else {
	 					 					
	 					// Attempted to schedule in the past
	 					jQuery('#id_spnPostNotifSchedRadioButtons').show();
	 					jQuery('#id_btnPostNotifSend').show();
	 				}
	 					 			
	 				// Set Post Notif status message span with appropriate message
	 				jQuery("#id_spnPostNotifStatus").text(data.message);
	 			});	 			
	 		}
	 	});
	});

	$(function() {
		$('#id_radSendPostNotifNow').click(function(event) {
			if (this.checked) {
					
				// Hide schedule time input fields
				jQuery('#id_spnPostNotifSendSchedTimestamp').hide();
				
				// Set button label to appropriate value
				jQuery('#id_btnPostNotifSend').prop('value', jQuery('#id_hdnPostNotifSendNowLabel').val());
			} 
	 	});
		$('#id_radSendPostNotifSched').click(function(event) {
			if (this.checked) {
					  
				// Show schedule time input fields
				jQuery('#id_spnPostNotifSendSchedTimestamp').show();
				
				// Set button label to appropriate value
				jQuery('#id_btnPostNotifSend').prop('value', jQuery('#id_hdnPostNotifSchedSendLabel').val());				
			}
	 	});
	}); 
	
	$(function() {
	 	$("#id_btnPostNotifCancelSchedSend").click(function(e) { 
	 		var post_id = document.getElementById("id_hdnPostID").value;
	 			
 			// Hide Cancel button
 			jQuery('#id_btnPostNotifCancelSchedSend').hide();
	 		
	 		$.post(post_notif_cancel_send_ajax_obj.ajax_url, {
	 			_ajax_nonce: post_notif_cancel_send_ajax_obj.nonce,
	 			action: "unschedule_post_notif_send",
	 			post_id: post_id,
	 		}, function(data) {

	 			// Hide Scheduled For span			
	 			jQuery('#id_spnPostNotifScheduledFor').hide();
	 			
	 			if (data.update_last_sent) {
	 				
	 				// Post notification process ran while page got stale
	 				jQuery("#id_spnPostNotifLastSent").text(data.update_last_sent_text);
	 				jQuery("#id_spnPostNotifLastSent").show();
	 			}
	 			
	 			if (data.cancelled) {
	 				
	 				// Scheduled run was successfully cancelled - give user option to run now
	 				jQuery('#id_radSendPostNotifNow').prop('checked', true);
	 				jQuery('#id_radSendPostNotifSched').prop('checked', false);
	 				jQuery('#id_spnPostNotifSchedRadioButtons').show();
	 				jQuery('#id_btnPostNotifSend').prop('value', jQuery('#id_hdnPostNotifSendNowLabel').val());
	 				jQuery('#id_btnPostNotifSend').show();	 				
	 			}
	 			
	 			// Update Post Notif status message span with appropriate message
	 			jQuery("#id_spnPostNotifStatus").text(data.message);	 			
	 		});	 			
	 	});
	});

	$(function() {
	 	$("#id_btnPostNotifTestSend").click(function(e) { 
	 		var post_id = $('#id_hdnPostID').val();
	 		var recipients = $('#id_emlPostNotifTestSendRecipients').val();
	 			
 			// Set Test Post Notif status message span to contain processing message
	 		jQuery("#id_spnTestPostNotifStatus").text(post_notif_test_send_ajax_obj.processing_msg);	 			 		
	 		
	 		$.post(post_notif_test_send_ajax_obj.ajax_url, {
	 			_ajax_nonce: post_notif_test_send_ajax_obj.nonce,
	 			action: "test_post_notif_send",
	 			post_id: post_id,
	 			recipients: recipients,
	 		}, function(data) {

	 			var sentEmailAddrMessage = '';
	 			var invalidEmailAddrMessage = '';
	 			
	 			if (data.sent_email_arr.length > 0) {
	 				
	 				// There are valid email addresses which had emails sent to them
	 				sentEmailAddrMessage = '<strong>' + data.successfully_sent_label + '</strong><br />';
	 				data.sent_email_arr.forEach(function(item){
	 					sentEmailAddrMessage = sentEmailAddrMessage + item + '<br />';
	 				});	
	 				sentEmailAddrMessage = sentEmailAddrMessage + '<br /><br />';
	 			}
	 			
	 			if (data.invalid_email_arr.length > 0) {
	 				
	 				// There are invalid email addresses
	 				invalidEmailAddrMessage = '<strong>' + data.invalid_email_address_label + '</strong><br />';
	 				data.invalid_email_arr.forEach(function(item){
	 					invalidEmailAddrMessage = invalidEmailAddrMessage + item + '<br />';
	 				});	
	 			}

	 			// Update Test Post Notif status message span with appropriate message (including additional HTML)
	 			jQuery("#id_spnTestPostNotifStatus").html(data.process_complete_message + '<br /><br />' + sentEmailAddrMessage + invalidEmailAddrMessage);	 			
	 		});	 			
	 	});
	});

	$(function() {
		$('#id_lnkPostNotifSchedAuto').click(function(event) {
			if ($(this).hasClass('pn-auto')) {
				
				// Is set to Auto, switch to Manual
				$(this).removeClass("pn-auto").addClass("pn-manual");
				$("#id_spnPostNotifSchedActive").text('Manual');
				$("#id_spnPostNotifSchedInactive").text('Auto');
				$("#id_hdnPostNotifSchedAuto").val('no');
			} 
			else {         		  
				
				// Is set to Manual, switch to Auto
				$(this).removeClass("pn-manual").addClass("pn-auto");
				$("#id_spnPostNotifSchedActive").text('Auto');
				$("#id_spnPostNotifSchedInactive").text('Manual');
				$("#id_hdnPostNotifSchedAuto").val('yes');
			}
	 	});
	}); 
	
	$(function() {
		$('#available_categories\\[0\\]').click(function(event) {
			if (this.checked) {
					  
				// "All" (pseudo-) category selected, select all children and gray them out
				$('.cats').each(function() {
					this.checked = true;
					this.disabled = true;
				});
			} 
			else {
         		  
				// "All" (pseudo-) category UNselected, UNselect all children and make them selectable
				$('.cats').each(function() {
					this.checked = false;                     
					this.disabled = false;
				});        
			}
	 	});
	}); 

})( jQuery );
