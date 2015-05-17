<?php 

/**
 * Display plugin settings (accessible via Settings >> Post Notif on admin menu sidebar).
 *
 * This markup generates the plugin's settings page.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/admin/views
 */
?> 
	<div class="wrap">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<div id="post-body-content">
			<form method="post" action="options.php">
				<?php settings_fields( 'post_notif_settings_group' ); ?>
				<?php $options = get_option( 'post_notif_settings' ); ?>                              
				<h3 class="title"><?php _e( 'Email Settings', 'post-notif' ); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="eml_sender_name"><?php _e( 'Email sender name:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[eml_sender_name]" id="eml_sender_name" value="<?php echo esc_attr( $options['eml_sender_name'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="eml_sender_eml_addr"><?php _e( 'Email sender email address:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[eml_sender_eml_addr]" id="eml_sender_eml_addr" value="<?php echo esc_attr( $options['eml_sender_eml_addr'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sub_conf_eml_subj"><?php _e( 'Subscription confirmation email subject:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[sub_conf_eml_subj]" id="sub_conf_eml_subj" value="<?php echo esc_attr( $options['sub_conf_eml_subj'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sub_conf_eml_body"><?php _e( 'Subscription confirmation email body:', 'post-notif' ); ?></label>
						</th>
						<td>
							<textarea name="post_notif_settings[sub_conf_eml_body]" rows="10" cols="73" id="sub_conf_eml_body"><?php echo $options['sub_conf_eml_body']; ?></textarea>
 						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="post_notif_eml_subj"><?php _e( 'Post notification email subject:', 'post-notif' ); ?>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[post_notif_eml_subj]" id="post_notif_eml_subj" value="<?php echo esc_attr( $options['post_notif_eml_subj'] ); ?>">
						</td>
					</tr>
               <tr valign="top">
               	<th scope="row">
               		<?php _e( 'Post notification email body:', 'post-notif' ); ?>
               	</th>
               	<td>
               		<textarea name="post_notif_settings[post_notif_eml_body]" rows="10" cols="73" id="post_notif_eml_body"><?php echo $options['post_notif_eml_body']; ?></textarea>
               		<br />
               	</td>
               </tr>
               <tr valign="top">
               	<th scope="row">
               		<?php echo '@@signature:'; ?>
               	</th>
               	<td>
               		<input type="text" size="75" name="post_notif_settings[@@signature]" id="@@signature" value="<?php echo esc_attr( $options['@@signature'] ); ?>">
               		<br />
               	</td>
               </tr>
            </table>
            <h3 class="title"><?php _e( 'Page Settings', 'post-notif' ); ?></h3>
            <table class="form-table">
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Subscription confirmed page title:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[sub_confirmed_page_title]" id="sub_confirmed_page_title" value="<?php echo esc_attr( $options['sub_confirmed_page_title'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Subscription confirmed page greeting:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[sub_confirmed_page_greeting]" id="sub_confirmed_page_greeting" value="<?php echo esc_attr( $options['sub_confirmed_page_greeting'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Subscription preferences selection instructions:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[sub_pref_selection_instrs]" id="sub_pref_selection_instrs" value="<?php echo esc_attr( $options['sub_pref_selection_instrs'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Current subscription preferences page title:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[curr_sub_prefs_page_title]" id="curr_sub_prefs_page_title" value="<?php echo esc_attr( $options['curr_sub_prefs_page_title'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Current subscription preferences page greeting:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[curr_sub_prefs_page_greeting]" id="curr_sub_prefs_page_greeting" value="<?php echo esc_attr( $options['curr_sub_prefs_page_greeting'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Subscription preferences updated page title:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[sub_prefs_updated_page_title]" id="sub_prefs_updated_page_title" value="<?php echo esc_attr( $options['sub_prefs_updated_page_title'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Subscription preferences updated page greeting:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[sub_prefs_updated_page_greeting]" id="sub_prefs_updated_page_greeting" value="<?php echo esc_attr( $options['sub_prefs_updated_page_greeting'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Unsubscribe link label:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[unsub_link_label]" id="unsub_link_label" value="<?php echo esc_attr( $options['unsub_link_label'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Unsubscribe confirmation page title:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[unsub_confirmation_page_title]" id="unsub_confirmation_page_title" value="<?php echo esc_attr( $options['unsub_confirmation_page_title'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Unsubscribe confirmation page greeting:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[unsub_confirmation_page_greeting]" id="unsub_confirmation_page_greeting" value="<?php echo esc_attr( $options['unsub_confirmation_page_greeting'] ); ?>">
            			<br />
            		</td>
            	</tr>
            </table> 
            <h3 class="title"><?php _e( 'Widget Messages', 'post-notif' ); ?></h3>
            <table class="form-table">
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Error (blank email address):', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[widget_error_email_addr_blank]" id="widget_error_email_addr_blank" value="<?php echo esc_attr( $options['widget_error_email_addr_blank'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Error (invalid email address):', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[widget_error_email_addr_invalid]" id="widget_error_email_addr_invalid" value="<?php echo esc_attr( $options['widget_error_email_addr_invalid'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Message (already subscribed):', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[widget_info_message_already_subscribed]" id="widget_info_message_already_subscribed" value="<?php echo esc_attr( $options['widget_info_message_already_subscribed'] ); ?>">
            			<br />
            		</td>
            	</tr>
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Message (successful subscription request):', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="75" name="post_notif_settings[widget_success_message]" id="widget_success_message" value="<?php echo esc_attr( $options['widget_success_message'] ); ?>">
            			<br />
            		</td>
            	</tr>
            </table> 
            <h3 class="title"><?php _e( 'Admin Menu Settings', 'post-notif' ); ?></h3>
            <table class="form-table">
            	<tr valign="top">
            		<th scope="row">
            			<?php _e( 'Position in menu:', 'post-notif' ); ?>
            		</th>
            		<td>
            			<input type="text" size="5" name="post_notif_settings[admin_menu_position]" id="admin_menu_position" value="<?php echo esc_attr( $options['admin_menu_position'] ); ?>">
            			<br />
            			<p class="description">
            				<?php _e( 'If you cannot see the Post Notif admin menu, change this to another number of the form "3.xyz".', 'post-notif' ); ?>
            			</p>
            		</td>
            	</tr>
            </table> 
            <?php submit_button() ?>
         </form>
      </div> <!-- end post-body-content -->
   </div>
