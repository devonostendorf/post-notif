(function( $ ) {
	'use strict';
	$(function() {		
	 	$("#id_btnSubmit").click(function(e) { 
	 		var first_name = document.getElementById("id_txtFirstName").value;
	 		var email_addr = document.getElementById("id_txtEmailAddr").value;
	 		$.post(post_notif_widget_ajax_obj.ajax_url, {
	 			_ajax_nonce: post_notif_widget_ajax_obj.nonce,
	 			action: "post_notif_widget",
	 			form_data: {
	 				first_name: first_name,
	 				email_addr: email_addr
	 			}
	 		}, function(data) {
	 			if (data.success) {
	 					  
	 				// Replace form fields with confirmation message    	
	 				jQuery('#id_lblCallToAction').hide();
	 				jQuery('#id_txtFirstName').hide();
	 				jQuery('#id_txtEmailAddr').hide();
	 				jQuery('#id_btnSubmit').hide();
	 				jQuery("#id_spnErrorMsg").hide();
	 				jQuery("#id_spnSuccessMsg").text(data.message);
	 			}
	 			else {
	 					  
	 				// Display error message
	 				jQuery("#id_spnErrorMsg").text(data.message);
	 			}
	 		});
	 	});
	});	 
})( jQuery );
