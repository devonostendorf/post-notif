(function( $ ) {
	'use strict';
	$(function() {
	 	$("#id_btnSendNotif").click(function(e) { 
	 		var post_id = document.getElementById("id_hdnPostID").value;
	 		$.post(post_notif_send_ajax_obj.ajax_url, {
	 			_ajax_nonce: post_notif_send_ajax_obj.nonce,
	 			action: "post_notif_send",
	 			post_id: post_id,
	 		}, function(data) {
	 				  
	 			// Hide Send button and display confirmation message    	
	 			jQuery('#id_btnSendNotif').hide();
	 			jQuery("#id_spnPostNotifLastSent").text(data.message);       	
	 		});
	 	});
	});	 
})( jQuery );
