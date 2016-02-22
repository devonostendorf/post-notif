<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$( ".notice.is-dismissible.language-detect-nag-dismiss" ).on( "click", ".notice-dismiss", function() {
			$.post(ajaxurl, {
				action : 'language_nag_dismiss'
			});
		});
	});
</script>
