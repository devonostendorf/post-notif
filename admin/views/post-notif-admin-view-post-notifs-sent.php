<?php

/**
 * View Post Notifs Sent (accessible via Post Notif >> View Post Notifs Sent on admin menu sidebar).
 *
 * This markup generates the View Post Notifs Sent (List Table) page.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/admin/views
 */
?>
	<div class="wrap">
		<div id="post-body-content">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>           
			<?php $view_post_notif_list_table->display() ?>
		</div>
	</div>
