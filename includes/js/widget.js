(function( $ ) {
	'use strict';
	$(function() {		
	 	$("#id_btnSubmit").click(function(e) { 
	 		var first_name = document.getElementById("id_txtFirstName").value;
	 		var email_addr = document.getElementById("id_txtEmailAddr").value;

	 		// Hide form fields and submit button during processing
	 		jQuery('#id_lblCallToAction').hide();
	 		jQuery('#id_txtFirstName').hide();
	 		jQuery('#id_txtEmailAddr').hide();
	 		jQuery('#id_btnSubmit').hide();
	 		
	 		// Hide error message span during processing
	 		jQuery("#id_spnErrorMsg").hide();
	 		
	 		// Set success message span to contain processing message
	 		jQuery("#id_spnSuccessMsg").text(post_notif_widget_ajax_obj.processing_msg);
	 		
	 		// Show processing message
	 		jQuery("#id_spnSuccessMsg").show();

	 		$.post(post_notif_widget_ajax_obj.ajax_url, {
	 			_ajax_nonce: post_notif_widget_ajax_obj.nonce,
	 			action: "post_notif_widget",
	 			form_data: {
	 				first_name: first_name,
	 				email_addr: email_addr
	 			}
	 		}, function(data) {
	 			if (data.success) {
	 					  
	 				// Set success message span to contain confirmation message 
	 				jQuery("#id_spnSuccessMsg").text(data.message);

	 				// Show confirmation message
	 				jQuery("#id_spnSuccessMsg").show();
	 			}
	 			else {
	 					  
	 				// Hide success message span
	 				jQuery("#id_spnSuccessMsg").hide();

	 				// Show form fields and submit button
	 				jQuery('#id_lblCallToAction').show();
	 				jQuery('#id_txtFirstName').show();
	 				jQuery('#id_txtEmailAddr').show();
	 				jQuery('#id_btnSubmit').show();
	 				
	 				// Set error message span to contain error message
	 				jQuery("#id_spnErrorMsg").text(data.message);
	 				
	 				// Show error message
	 				jQuery("#id_spnErrorMsg").show();
	 			}
	 		});
	 	});
	});	 
})( jQuery );
