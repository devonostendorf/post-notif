(function( $ ) {
	'use strict';
	
	$(function() {		
	 	$(".pn-btn-subscriber-form-submit").click(function(e) { 
	 			
	 		var submitButtonID = $(this).attr('id');
	 		var formID = submitButtonID.substring(33);
	 		
	 		var first_name = document.getElementById("id_pn_txt_first_name_"+formID).value;
	 		var email_addr = document.getElementById("id_pn_eml_email_addr_"+formID).value;

	 		// Hide form fields and submit button during processing
	 		$("#id_pn_lbl_call_to_action_"+formID).hide();
	 		$("#id_pn_txt_first_name_"+formID).hide();
	 		$("#id_pn_eml_email_addr_"+formID).hide();
	 		$("#id_pn_btn_subscriber_form_submit_"+formID).hide();
	 		
	 		// Hide error message span during processing
	 		$("#id_pn_spn_error_msg_"+formID).hide();
	 		
	 		// Set success message span to contain processing message
	 		$("#id_pn_spn_success_msg_"+formID).text(post_notif_subscriber_form_ajax_obj.processing_msg);
	 		
	 		// Show processing message
	 		$("#id_pn_spn_success_msg_"+formID).show();

	 		$.post(post_notif_subscriber_form_ajax_obj.ajax_url, {
	 			_ajax_nonce: post_notif_subscriber_form_ajax_obj.nonce,
	 			action: "post_notif_subscriber_form",
	 			form_id: formID,
	 			form_data: {
	 				first_name: first_name,
	 				email_addr: email_addr
	 			}
	 		}, function(data) {
	 			if (data.success) {
	 					  
	 				// Set success message span to contain confirmation message 
	 				$("#id_pn_spn_success_msg_"+formID).text(data.message);

	 				// Show confirmation message
	 				$("#id_pn_spn_success_msg_"+formID).show();
	 			}
	 			else {
	 					  
	 				// Hide success message span
	 				$("#id_pn_spn_success_msg_"+formID).hide();

	 				// Show form fields and submit button
	 				$("#id_pn_lbl_call_to_action_"+formID).show();
	 				$("#id_pn_txt_first_name_"+formID).show();
	 				$("#id_pn_eml_email_addr_"+formID).show();
	 				$("#id_pn_btn_subscriber_form_submit_"+formID).show();
	 				
	 				// Set error message span to contain error message
	 				$("#id_pn_spn_error_msg_"+formID).text(data.message);
	 				
	 				// Show error message
	 				$("#id_pn_spn_error_msg_"+formID).show();
	 			}
	 		});
	 	});
	});
	
})( jQuery );
