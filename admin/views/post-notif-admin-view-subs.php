<?php

/**
 * View[/Delete] Subscribers (accessible via Post Notif >> View Subscribers and Post Notif >> Delete Subscribers on admin menu sidebar).
 *
 * This markup generates both the View Subscribers AND Delete Subscribers (List Table) pages.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/views
 */
?>
	<div class="wrap">
<?php if ( $subscribers_exported ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( esc_html( _n( 'Subscriber exported.', '%s subscribers exported.', $subscribers_exported, 'post-notif' ) ), number_format_i18n( $subscribers_exported ) ); ?></strong>
			</p>
		</div>
<?php elseif ( $subscribers_confirmed ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( esc_html( _n( 'Subscriber confirmed.', '%s subscribers confirmed.', $subscribers_confirmed, 'post-notif' ) ), number_format_i18n( $subscribers_confirmed ) ); ?></strong>
			</p>
		</div>
<?php elseif ( $subscribers_deleted ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( esc_html( _n( 'Subscriber deleted.', '%s subscribers deleted.', $subscribers_deleted, 'post-notif' ) ), number_format_i18n( $subscribers_deleted ) ); ?>&nbsp;<a href="<?php echo esc_url( $undo_delete_url ); ?>"><?php esc_html_e( 'Undo', 'post-notif' ); ?></a></strong>
			</p>
		</div>
<?php elseif ( $subscribers_undeleted ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( esc_html( _n( 'Deletion of subscriber has been undone.', 'Deletion of %s subscribers has been undone.', $subscribers_undeleted, 'post-notif' ) ), number_format_i18n( $subscribers_undeleted ) ); ?></strong>
			</p>
		</div>
<?php elseif ( $subscribers_resent_confirmation ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( esc_html( _n( 'Confirmation email re-sent to subscriber.', 'Confirmation emails re-sent to %s subscribers.', $subscribers_resent_confirmation, 'post-notif' ) ), number_format_i18n( $subscribers_resent_confirmation ) ); ?></strong>
			</p>
		</div>
<?php endif; ?>
      	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>           
		<div id="post-body-content">
        	<form id="list_subscribers" method="post" action="<?php echo esc_url( $form_action ); ?>">
        		<?php wp_nonce_field( 'manage_subscribers', 'post-notif-manage_subscribers' ); ?>
<?php $view_subs_pg_list_table->display() ?>
			</form>        
		</div>
	</div>
