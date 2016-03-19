<?php

/**
 * Contents of Post Notif translation nag screen.
 *
 * This markup generates the contents of the translation nag screen.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.6
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/partials
 */
			
printf(
	'<div class="notice notice-info is-dismissible language-detect-nag-dismiss"><p><strong>%s</strong></p><p>%s</p></div>'
	,esc_html( $plugin['Name'] )
	,sprintf(
		( 'This plugin does not have a translation for <strong>%1$s</strong>.&nbsp;&nbsp;<a href="https://devonostendorf.com" target="_blank">Please contact me</a> if you\'d like to provide a full translation.&nbsp;&nbsp;Alternatively, please consider helping to collobaratively translate the plugin at <a href="%2$s" target="_blank">%2$s</a>.' )
		,$translations[ $language ]['native_name']
		,esc_url( 'https://translate.wordpress.org/projects/wp-plugins/' . $plugin['TextDomain'] )
	)
);
?>
