(function( $ ) {
	'use strict';
	$(function() {
		$('#id_chkCatID_0').click(function(event) {
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
