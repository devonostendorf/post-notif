(function( $ ) {
	'use strict';
	
	$(function() {
	 	$("#id_btnPostNotifSend").click(function(e) { 
	 		var post_id = document.getElementById("id_hdnPostID").value;

 			// Hide Send button  	
 			$('#id_btnPostNotifSend').hide();
 			
 			// Set Post Notif status message span to contain processing message
	 		$("#id_spnPostNotifStatus").text(post_notif_send_ajax_obj.processing_msg);	 			 		
 			 		
	 		if ($("#id_spnPostNotifSendNowOrSchedActive").hasClass('pn-sendnow')) {
	
	 			// Send NOW	

	 			// Hide manual send controls
	 			$("#id_divPostNotifManualSendControls").hide();
	 			
	 			// Call Post Notif send init function
	 			$.post(ajaxurl, {
	 				action: 'init_post_notif_send',
	 				post_id: post_id,
	 			}, function(data) {

	 				if ('-1' === data.status) {

	 					// Process is already running - STOP PROCESSING!
	 					$("#id_spnPostNotifStatus").text(data.message);	 					
	 				} 
	 				else {

	 					// All is well, carry on
	 			
	 					// Create a jQuery Progressbar
	 					$("#id_post_notif_progress_bar").progressbar();

	 					// Check status of process every second
	 					var processCheckTimer = setInterval(function() {
	 				
	 						// Get the current status of the send process
	 						$.post(ajaxurl, {
	 							action: 'get_post_notif_send_status',
	 							post_id: post_id,
	 						}, function(response) {

	 							if ('-1' === response) {

	 								// Set the progress bar to display 100% complete
	 								$("#id_divPostNotifProgressBar").progressbar({
	 									value: 100
	 								});
	 			
	 								// Kill timer
	 								clearInterval(processCheckTimer);
	 						
	 								// Hardcode progress bar label to 100% 
	 								$("#id_spnPostNotifProgressBarLabel").text( "100%" );
	 							} 
	 							else {

	 								// Update the progress bar to display appropriate percent complete
	 								var percentComplete = Math.floor(100 * response);
	 								$("#id_divPostNotifProgressBar").progressbar({
	 									value: percentComplete
	 								});
	 						
	 								// Set progress bar label to appropriate percent complete 
	 								$("#id_spnPostNotifProgressBarLabel").text( percentComplete + "%" );
	 							}	 					
	 						});

	 					}, 1000);

	 					$.post(post_notif_send_ajax_obj.ajax_url, {
	 						_ajax_nonce: post_notif_send_ajax_obj.nonce,
	 						action: "post_notif_send",
	 						post_id: post_id,
	 					}, function(data) {

	 						// Update Post Notif status message span with total count of notifs sent
	 						$("#id_spnPostNotifStatus").text(data.message);
	 						
	 						// Update and show Post Notif last sent span with last run timestamp
	 						$("#id_spnPostNotifLastSent").text(data.timestamp);
	 						$("#id_spnPostNotifLastSent").show();	 			
	 					});
	 				} 					

	 			});
	 		}
	 		else {
	 			
	 			// Schedule to run
	 			
	 			// Validate date/time fields
	 			
	 			// Grab datetime pieces from user's entries into input fields
	 			var schedYear = $('#id_selPostNotifSchedYear').val();
				var schedMonth = $('#id_selPostNotifSchedMonth').val();
				var schedDay = $('#id_selPostNotifSchedDay').val();
				var schedHour = $('#id_selPostNotifSchedHour').val();
				var schedMinute = $('#id_selPostNotifSchedMinute').val();
			
				// Attempt to build a Date object
				var scheduleDatetime = new Date( schedYear, schedMonth - 1, schedDay, schedHour, schedMinute );
				if (scheduleDatetime.getFullYear() != schedYear
					|| (1 + scheduleDatetime.getMonth()) != schedMonth
					|| scheduleDatetime.getDate() != schedDay
					|| scheduleDatetime.getMinutes() != schedMinute) {	
				
					// This is an invalid datetime
					$("#id_spnPostNotifStatus").text(post_notif_send_ajax_obj.invalid_date_format_msg);	
					$('#id_btnPostNotifSend').show();
					return false;
				}
					 			
	 			// Hide manual send controls
	 			$("#id_divPostNotifManualSendControls").hide();
	 			 			
	 			var localDatetime = (1 + scheduleDatetime.getMonth()) + ':'
	 				+ scheduleDatetime.getDate() + ':'
	 				+ scheduleDatetime.getFullYear() + ':'
	 				+ scheduleDatetime.getHours() + ':'
	 				+ scheduleDatetime.getMinutes();
	 				
	 			var localPublishDatetime = $('#hidden_mm').val() + ':'
	 				+ $('#hidden_jj').val() + ':'
	 				+ $('#hidden_aa').val() + ':'
	 				+ $('#hidden_hh').val() + ':'
	 				+ $('#hidden_mm').val() + ':'
	 				+ $('#hidden_mn').val();
	 		
	 			$.post(post_notif_send_ajax_obj.ajax_url, {
	 				_ajax_nonce: post_notif_send_ajax_obj.nonce,
	 				action: "schedule_post_notif_send",
	 				post_id: post_id,
	 				datetime_local: localDatetime,
	 				publish_datetime_local: localPublishDatetime,
	 			}, function(data) {
	 				
	 				if (data.valid_datetime) {
	 					
	 					// All's well
	 					
	 					// Hide schedule time input fields
	 					$("#id_spnPostNotifSendSchedTimestamp").hide();
	 				
	 					// Update and show Post Notif scheduled for message span with scheduled for timestamp
	 					$("#id_spnPostNotifScheduledFor").text(data.timestamp);
	 					$("#id_spnPostNotifScheduledFor").show();
	 				}
	 				else {
	 					 					
	 					// Attempted to schedule in the past
	 					$("#id_divPostNotifManualSendControls").show();
	 					$('#id_btnPostNotifSend').show();
	 				}
	 					 			
	 				// Set Post Notif status message span with appropriate message
	 				$("#id_spnPostNotifStatus").text(data.message);
	 			});	 			
	 		}
	 	});
	});

	$(function() {
	 	$("#id_btnPostNotifTestSend").click(function(e) { 
	 		var post_id = $('#id_hdnPostID').val();
	 		var recipients = $('#id_emlPostNotifTestSendRecipients').val();
	 			
 			// Set Test Post Notif status message span to contain processing message
	 		jQuery("#id_spnPostNotifTestSendStatus").text(post_notif_test_send_ajax_obj.processing_msg);	 			 		
	 		
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
	 			jQuery("#id_spnPostNotifTestSendStatus").html(data.process_complete_message + '<br /><br />' + sentEmailAddrMessage + invalidEmailAddrMessage);	 			
	 		});	 			
	 	});
	});

	$(function() {
		$('#id_lnkPostNotifSchedAuto').click(function(event) {
			if ($(this).hasClass('pn-auto')) {

				// Is set to Auto, switch to Manual
				$(this).removeClass("pn-auto").addClass("pn-manual");
				$("#id_spnPostNotifSchedActive").text($('#id_hdnPostNotifManualOptionLabel').val());
				$("#id_spnPostNotifSchedInactive").text($('#id_hdnPostNotifAutoOptionLabel').val());
				$("#id_hdnPostNotifSchedAuto").val('no');
				$("#id_divPostNotifManualSend").show();
			} 
			else {         		  
				
				// Is set to Manual, switch to Auto
				$(this).removeClass("pn-manual").addClass("pn-auto");
				$("#id_spnPostNotifSchedActive").text($('#id_hdnPostNotifAutoOptionLabel').val());
				$("#id_spnPostNotifSchedInactive").text($('#id_hdnPostNotifManualOptionLabel').val());
				$("#id_hdnPostNotifSchedAuto").val('yes');
				$("#id_divPostNotifManualSend").hide();
				
			}
	 	});
	}); 

	$(function() {
		$('#id_lnkPostNotifSendNowOrSched').click(function(event) {
			$("#id_spnPostNotifStatus").text('');	 			
			if ($("#id_spnPostNotifSendNowOrSchedActive").hasClass('pn-schedule')) {
				
				// Is set to Schedule, switch to Send Now
				$("#id_spnPostNotifSendNowOrSchedActive").removeClass("pn-schedule").addClass("pn-sendnow");
				$("#id_spnPostNotifSendNowOrSchedActive").text($('#id_hdnPostNotifSendNowOptionLabel').val());
				$("#id_spnPostNotifSendNowOrSchedInactive").text($('#id_hdnPostNotifScheduleOptionLabel').val());
				$("#id_spnPostNotifSendSchedTimestamp").hide();

				// Set button label to appropriate value
				$('#id_btnPostNotifSend').prop('value', jQuery('#id_hdnPostNotifSendNowButtonLabel').val());
			} 
			else {         		  
				
				// Is set to Send Now, switch to Schedule
				$("#id_spnPostNotifSendNowOrSchedActive").removeClass("pn-sendnow").addClass("pn-schedule");
				$("#id_spnPostNotifSendNowOrSchedActive").text($('#id_hdnPostNotifScheduleOptionLabel').val());
				$("#id_spnPostNotifSendNowOrSchedInactive").text($('#id_hdnPostNotifSendNowOptionLabel').val());
				$("#id_spnPostNotifSendSchedTimestamp").show();

				// Set button label to appropriate value
				$('#id_btnPostNotifSend').prop('value', jQuery('#id_hdnPostNotifScheduleButtonLabel').val());				
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
	
	function checkOverrideTheme() {
		$('.pn-chkoverridetheme').click(function(event) {
			if (this.checked) {
				
				// "Override theme CSS" selected, display all override options
				$('.pn-overridetheme').slideDown();
			} 
			else {
				
				// "Override theme CSS" UNselected, hide all override options
				$('.pn-overridetheme').slideUp();
			}
	 	});
	};

	$(document).ready(function(){
		checkOverrideTheme();
	});
	
	$(document).ajaxComplete(function () {
		checkOverrideTheme();
	});

})( jQuery );
