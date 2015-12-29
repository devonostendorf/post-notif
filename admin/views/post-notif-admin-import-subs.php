<?php
/**
 * Import Subscribers (accessible via Post Notif >> Import Subscribers on admin menu sidebar).
 *
 * This markup generates the Import Subscribers page.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.4
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/views
 */
?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>           
		<div id="post-body-content">
        	<form action="<?php echo esc_url( get_admin_url() ); ?>admin-post.php" method="post" enctype="multipart/form-data">
        		<?php wp_nonce_field( 'import_subscribers', 'post-notif-import_subscribers' ); ?>
        		<input type="hidden" name="action" value="import-subs-form" />
        		<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
							<label for="btnSubscriberFile"><?php esc_html_e( 'Import from file:', 'post-notif' ); ?></label>
						</th>
						<td>
            				<input type="file" name="btnSubscriberFile" id="id_btnSubscriberFile" value="" class="action"/>
            				<br />
            			</td>
            		</tr>            		       		
					<tr valign="top">
						<th scope="row">
							<label for="tarSubscriberData"><?php esc_html_e( 'Import directly:', 'post-notif' ); ?></label>
						</th>
						<td>
							<textarea name="tarSubscriberData" rows="10" cols="73" id="id_tarSubscriberData"></textarea>
							<p class="description">
            					<?php esc_html_e( "NOTES:", 'post-notif' ); ?>
            				</p>
            				<p class="description">
            					<?php esc_html_e( "1. Data entered into \"Import directly\" field is NOT preserved; do NOT type in data directly (copy-and-paste from a file you've saved somewhere).", 'post-notif' ); ?>
            				</p>
            				<p class="description">
            					<?php esc_html_e( '2. Data entered into "Import directly" field will only be processed if NO file is selected.', 'post-notif' ); ?>
            				</p>            			
            				<p class="description">
            					<?php esc_html_e( '3. See FAQ for row format (required and optional fields).', 'post-notif' ); ?>
            				</p>
            				<p class="description">
            					<?php esc_html_e( '4. For best results, limit your imports to batches of less than 100 rows.', 'post-notif' ); ?>
            				</p>
 						</td>
					</tr>
					<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Skip staging of clean rows?', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="checkbox" name="chkSkipStaging" id="id_chkSkipStaging" />
            				<br />
            			</td>
            		</tr>
            	</table>
            	<?php submit_button( __( 'Import', 'post-notif' ) ) ?>
            </form>
		</div>
	</div>
