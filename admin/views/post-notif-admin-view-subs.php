<?php

/**
 * View[/Delete] Subscribers (accessible via Post Notif >> View Subscribers and Post Notif >> Delete Subscribers on admin menu sidebar).
 *
 * This markup generates both the View Subscribers AND Delete Subscribers (List Table) pages.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/admin/views
 */
?>
	<div class="wrap">
		<?php if ( $subscribers_deleted ) : ?>
			<div class="updated fade">
				<p>
					<strong><?php echo sprintf( _n( 'Subscriber deleted.', '%s subscribers deleted.', $subscribers_deleted, 'post-notif' ), number_format_i18n( $subscribers_deleted ) ); ?></strong>
				</p>
			</div>
		<?php elseif ( $subscribers_resent_confirmation ) : ?>
			<div class="updated fade">
				<p>
					<strong><?php echo sprintf( _n( 'Confirmation email re-sent to subscriber.', 'Confirmation emails re-sent to %s subscribers.', $subscribers_resent_confirmation, 'post-notif' ), number_format_i18n( $subscribers_resent_confirmation ) ); ?></strong>
				</p>
			</div>
      <?php endif; ?>
      <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>           
		<div id="post-body-content">
        <form id="list_subscribers" method="post" action="<?php echo esc_attr( $form_action ); ?>">
            <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
            <?php $view_subs_pg_list_table->display() ?>
        </form>        
      </div>
   </div>
