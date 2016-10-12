(function( $ ) {
	'use strict';
	
	$(function() {
	 	$("#id_btnSendNotif").click(function(e) { 
	 		var post_id = document.getElementById("id_hdnPostID").value;

 			// Hide Send button  	
 			jQuery('#id_btnSendNotif').hide();
 			
	 		// Set Post Notif last sent message span to contain processing message
	 		jQuery("#id_spnPostNotifLastSent").text(post_notif_send_ajax_obj.processing_msg);
	 			 		
	 		// Call post notif send init function
	 		$.get(ajaxurl, {
	 			action: 'init_post_notif_send'
	 		}, function(response) {});	 		
			
 			// Create a jQuery Progressbar
 			jQuery("#id_post_notif_progress_bar").progressbar();

 			// Check status of process every second
	 		var processCheckTimer = setInterval(function() {

	 				// Get the current status of the send process
	 				$.get(ajaxurl, {
	 						action: 'get_post_notif_send_status'
	 				}, function(response) {

	 					if ( '-1' === response ) {

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

	 			// Update Post Notif last sent message span with total count of notifs sent
	 			jQuery("#id_spnPostNotifLastSent").text(data.message);
	 			
	 		});
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
