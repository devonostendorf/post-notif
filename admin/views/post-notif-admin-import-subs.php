<?php
/**
 * Import Subscribers (accessible via Post Notif >> Import Subscribers on admin menu sidebar).
 *
 * This markup generates the Import Subscribers page.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.4
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/admin/views
 */
?>
	<div class="wrap">
      <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>           
		<div id="post-body-content">
        <form action="<?php echo esc_attr( get_admin_url() ); ?>admin-post.php" method="post">
        		<input type="hidden" name="action" value="import-subs-form" />
        		<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="tarSubscriberData"><?php _e( 'Subscriber data:', 'post-notif' ); ?></label>
						</th>
						<td>
							<textarea name="tarSubscriberData" rows="10" cols="73" id="id_tarSubscriberData"></textarea>
            			<p class="description">
            				<?php _e( 'For best results, limit your imports to batches of less than 100 rows.' ); ?>
            			</p>
 						</td>
					</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Skip staging of clean rows?', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="checkbox" id="id_chkSkipStaging" name="chkSkipStaging" CHECKED>
            			<br />
            		</td>
            	</tr>
            </table>
            <?php submit_button( 'Import' ) ?>
        </form>        
      </div>
   </div>
