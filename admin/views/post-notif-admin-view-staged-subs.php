<?php
/**
 * Staged Subscribers (accessible via Post Notif >> Staged Subscribers on admin menu sidebar).
 *
 * This markup generates the Staged Subscribers (List Table) page.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.4
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/views
 */
?>
	<div class="wrap">
	<?php if ( $staged_subscribers_created ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( _n( 'Staged subscriber created.', '%s staged subscribers created.', $staged_subscribers_created, 'post-notif' ), number_format_i18n( $staged_subscribers_created ) ); ?></strong>
			</p>
		</div>
	<?php elseif ( $staged_subscribers_deleted ) : ?>
		<div class="updated fade">
			<p>
				<strong><?php echo sprintf( _n( 'Staged subscriber deleted.', '%s staged subscribers deleted.', $staged_subscribers_deleted, 'post-notif' ), number_format_i18n( $staged_subscribers_deleted ) ); ?></strong>
			</p>
		</div>
    <?php endif; ?>
    	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>           
		<div id="post-body-content">
			<form id="list_staged_subscribers" method="post" action="<?php echo esc_url( $form_action ); ?>">
        		<?php wp_nonce_field( 'staged_subscribers', 'post-notif-staged_subscribers' ); ?>
<?php $view_staged_subs_pg_list_table->display() ?>
			</form>        
		</div>
	</div>
