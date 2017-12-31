<?php 

/**
 * Display plugin settings (accessible via Settings >> Post Notif on admin menu sidebar).
 *
 * This markup generates the plugin's settings page.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/admin/views
 */
?> 
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<div id="post-body-content">
			<form method="post" action="options.php">
				<?php settings_fields( 'post_notif_settings_group' ); ?>
				<?php $options = get_option( 'post_notif_settings' ); ?>                              
				<h2 class="title"><?php esc_html_e( 'Email Settings', 'post-notif' ); ?></h2>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="eml_sender_name"><?php esc_html_e( 'Email sender name:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[eml_sender_name]" id="eml_sender_name" value="<?php echo esc_attr( $options['eml_sender_name'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="eml_sender_eml_addr"><?php esc_html_e( 'Email sender email address:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[eml_sender_eml_addr]" id="eml_sender_eml_addr" value="<?php echo esc_attr( $options['eml_sender_eml_addr'] ); ?>">
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
					<tr valign="top">
						<th scope="row">
							<label for="sub_conf_eml_subj"><?php esc_html_e( 'Subscription confirmation email subject:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[sub_conf_eml_subj]" id="sub_conf_eml_subj" value="<?php echo esc_attr( $options['sub_conf_eml_subj'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sub_conf_eml_body"><?php esc_html_e( 'Subscription confirmation email body:', 'post-notif' ); ?></label>
						</th>
						<td>
							<textarea name="post_notif_settings[sub_conf_eml_body]" rows="10" cols="73" id="sub_conf_eml_body"><?php echo esc_html( $options['sub_conf_eml_body'] ); ?></textarea>
 						</td>
					</tr>
					<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'After subscription is confirmed:', 'post-notif' ); ?>
            			</th>
            			<td>
							<label for="send_eml_to_sub_after_conf">
            					<input type="checkbox" name="post_notif_settings[send_eml_to_sub_after_conf]" id="send_eml_to_sub_after_conf" value="1" <?php echo ( ( array_key_exists( 'send_eml_to_sub_after_conf', $options ) ) ? 'checked' : '' ); ?> />
            					<?php esc_html_e( 'Send email to subscriber?', 'post-notif' ); ?>
            				</label>
            				<br />
            			</td>
            		</tr>
					<tr valign="top">
						<th scope="row">
							<label for="eml_to_sub_after_conf_subj"><?php esc_html_e( 'Email sent after subscription is confirmed subject:', 'post-notif' ); ?></label>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[eml_to_sub_after_conf_subj]" id="eml_to_sub_after_conf_subj" value="<?php echo esc_attr( $options['eml_to_sub_after_conf_subj'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="eml_to_sub_after_conf_body"><?php esc_html_e( 'Email sent after subscription is confirmed body:', 'post-notif' ); ?></label>
						</th>
						<td>
							<textarea name="post_notif_settings[eml_to_sub_after_conf_body]" rows="10" cols="73" id="eml_to_sub_after_conf_body"><?php echo esc_html( $options['eml_to_sub_after_conf_body'] ); ?></textarea>
 						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="post_notif_eml_subj"><?php esc_html_e( 'Post notification email subject:', 'post-notif' ); ?>
						</th>
						<td>
							<input type="text" size="75" name="post_notif_settings[post_notif_eml_subj]" id="post_notif_eml_subj" value="<?php echo esc_attr( $options['post_notif_eml_subj'] ); ?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Post notification email body:', 'post-notif' ); ?>
						</th>
						<td>
							<textarea name="post_notif_settings[post_notif_eml_body]" rows="10" cols="73" id="post_notif_eml_body"><?php echo esc_html( $options['post_notif_eml_body'] ); ?></textarea>
							<br />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php esc_html_e( 'Send post notifications when post published:', 'post-notif' ); ?>
						</th>
						<td>
							<span class="block-element">
								<input type="radio" name="post_notif_settings[send_notif_on_publish]" id="send_notif_on_publish_yes" value="yes" <?php checked( $options['send_notif_on_publish'], 'yes' ); ?> >
								<label for="send_notif_on_publish_yes"><?php esc_html_e( 'Yes (Auto)', 'post-notif' ); ?></label>
								&nbsp;&nbsp;&nbsp;&nbsp;
							</span>
							<span class="block-element">
								<input type="radio" name="post_notif_settings[send_notif_on_publish]" id="send_notif_on_publish_no" value="no" <?php checked( $options['send_notif_on_publish'], 'no' ); ?> >
								<label for="send_notif_on_publish_no"><?php esc_html_e( 'No (Manual)', 'post-notif' ); ?></label>
							</span>
						</td>
					</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Enable batch send options:', 'post-notif' ); ?>
            			</th>
            			<td>
							<label for="enable_batch_send_options">
            					<input type="checkbox" name="post_notif_settings[enable_batch_send_options]" id="enable_batch_send_options" value="1" <?php echo ( ( array_key_exists( 'enable_batch_send_options', $options ) ) ? 'checked' : '' ); ?> />
            				</label>
            				<br />
            				<p class="description">
            					<?php esc_html_e( 'You may want to enable (and configure) this if your host throttles emails.', 'post-notif' ); ?>
            				</p>
            			</td>
            		</tr>					
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Batch size:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[batch_size]" id="batch_size" value="<?php echo esc_attr( $options['batch_size'] ); ?>" <?php if ( ! array_key_exists( 'enable_batch_send_options', $options ) ) echo ' readonly="readonly" '; ?> >
            				<br />
            				<p class="description">
            					<?php esc_html_e( 'Number of emails to be sent at a time (some hosts limit this in the name of spam prevention).', 'post-notif' ); ?>
            				</p>
            			</td>
            		</tr>
 					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Batch pause (in minutes):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[batch_pause]" id="batch_pause" value="<?php echo esc_attr( $options['batch_pause'] ); ?>" <?php if ( ! array_key_exists( 'enable_batch_send_options', $options ) ) echo ' readonly="readonly" '; ?> >
            				<br />
            				<p class="description">
            					<?php esc_html_e( 'How long to wait before sending next batch of emails (some hosts set a maximum number of emails that can be sent per hour, for example).', 'post-notif' ); ?>
            				</p>
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'If using "Custom Structure" permalink:', 'post-notif' ); ?>
            			</th>
            			<td>
							<label for="custom_permalink_with_category_concat">
            					<input type="checkbox" name="post_notif_settings[custom_permalink_with_category_concat]" id="custom_permalink_with_category_concat" value="1" <?php echo ( ( array_key_exists( 'custom_permalink_with_category_concat', $options ) ) ? 'checked' : '' ); ?> />
            					<?php esc_html_e( '%category% is concatenated with something', 'post-notif' ); ?>
            				</label>
            				<br />
            				<p class="description">
            					<?php esc_html_e( 'This is not common.', 'post-notif' ); ?>
            				</p>
            			</td>
            		</tr>					
				</table>
				<h2 class="title"><?php esc_html_e( 'Page Settings', 'post-notif' ); ?></h2>
				<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Subscription confirmed page title:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[sub_confirmed_page_title]" id="sub_confirmed_page_title" value="<?php echo esc_attr( $options['sub_confirmed_page_title'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Subscription confirmed page greeting:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[sub_confirmed_page_greeting]" id="sub_confirmed_page_greeting" value="<?php echo esc_attr( $options['sub_confirmed_page_greeting'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Subscription preferences selection instructions:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[sub_pref_selection_instrs]" id="sub_pref_selection_instrs" value="<?php echo esc_attr( $options['sub_pref_selection_instrs'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Current subscription preferences page title:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[curr_sub_prefs_page_title]" id="curr_sub_prefs_page_title" value="<?php echo esc_attr( $options['curr_sub_prefs_page_title'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Current subscription preferences page greeting:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[curr_sub_prefs_page_greeting]" id="curr_sub_prefs_page_greeting" value="<?php echo esc_attr( $options['curr_sub_prefs_page_greeting'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Subscription preferences updated page title:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[sub_prefs_updated_page_title]" id="sub_prefs_updated_page_title" value="<?php echo esc_attr( $options['sub_prefs_updated_page_title'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Subscription preferences updated page greeting:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[sub_prefs_updated_page_greeting]" id="sub_prefs_updated_page_greeting" value="<?php echo esc_attr( $options['sub_prefs_updated_page_greeting'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Unsubscribe link label:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[unsub_link_label]" id="unsub_link_label" value="<?php echo esc_attr( $options['unsub_link_label'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Unsubscribe confirmation page title:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[unsub_confirmation_page_title]" id="unsub_confirmation_page_title" value="<?php echo esc_attr( $options['unsub_confirmation_page_title'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Unsubscribe confirmation page greeting:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[unsub_confirmation_page_greeting]" id="unsub_confirmation_page_greeting" value="<?php echo esc_attr( $options['unsub_confirmation_page_greeting'] ); ?>">
            				<br />
            			</td>
            		</tr>
            	</table> 
            	<h2 class="title"><?php esc_html_e( 'Widget Messages', 'post-notif' ); ?></h2>
            	<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Error (required first name is blank):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_error_reqd_first_name_blank]" id="widget_error_reqd_first_name_blank" value="<?php echo esc_attr( $options['widget_error_reqd_first_name_blank'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Error (blank email address):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_error_email_addr_blank]" id="widget_error_email_addr_blank" value="<?php echo esc_attr( $options['widget_error_email_addr_blank'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Error (invalid email address):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_error_email_addr_invalid]" id="widget_error_email_addr_invalid" value="<?php echo esc_attr( $options['widget_error_email_addr_invalid'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (processing):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_info_message_processing]" id="widget_info_message_processing" value="<?php echo esc_attr( $options['widget_info_message_processing'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (already subscribed):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_info_message_already_subscribed]" id="widget_info_message_already_subscribed" value="<?php echo esc_attr( $options['widget_info_message_already_subscribed'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (successful subscription request):', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_success_message]" id="widget_success_message" value="<?php echo esc_attr( $options['widget_success_message'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (failed subscription request):', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[widget_failure_message]" id="widget_failure_message" value="<?php echo esc_attr( $options['widget_failure_message'] ); ?>">
            				<br />
            			</td>
            		</tr>
            	</table> 
            	<h2 class="title"><?php esc_html_e( 'Category Settings', 'post-notif' ); ?></h2>
            	<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Categories available to subscribers:', 'post-notif' ); ?>
            			</th>
            		</tr>
<?php

// If the available_categories array exists in options, assign it to variable
//	otherwise assign a placeholder array with a single element, with index of
//	-1, representing NO categories selected
$available_categories = array_key_exists( 'available_categories', $options ) ? $options['available_categories'] : array( '-1' => '1' );

// The presence of an element with index of 0 means ALL categories are selected
$all_selected = array_key_exists( 0, $available_categories );
?> 

            		<tr valign="top">            		
            			<td>
            				<input type="checkbox" name="post_notif_settings[available_categories][0]" id="available_categories[0]" value="1" <?php echo $all_selected  ? 'checked' : ''; ?> ><?php esc_html_e( 'All', 'post-notif' ); ?></input>
            			</td>
            		</tr>
<?php

// Retrieve all categories in the system
$args = array(
	'orderby' => 'name',
	'order' => 'ASC',
	'hide_empty' => 0
);

$categories = get_categories( $args );
foreach ( $categories as $category ) { 
?>
            		<tr valign="top">            		
            			<td>
<?php
	if ( $all_selected ) {
		echo '&nbsp;&nbsp;<input type="checkbox" class="cats" name="post_notif_settings[available_categories][' . $category->cat_ID . ']" id="available_categories[' . $category->cat_ID . ']" value="1" checked disabled>&nbsp;' . $category->name . '</input>';
	}
	else {
		echo '&nbsp;&nbsp;<input type="checkbox" class="cats" name="post_notif_settings[available_categories][' . $category->cat_ID . ']" id="available_categories[' . $category->cat_ID . ']" value="1"' . ( ( array_key_exists( $category->cat_ID, $available_categories ) ) ? ' checked' : ' ' ) . ' >' . $category->name . '</input>';
	}
?>            			
             			</td>
             		</tr>
<?php
} 
?>  	
				</table> 
				<h2 class="title"><?php esc_html_e( 'Subscriber Form Shortcode Settings', 'post-notif' ); ?></h2>
				<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Title:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_title]" id="shortcode_title" value="<?php echo esc_attr( $options['shortcode_title'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Call to Action:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_call_to_action]" id="shortcode_call_to_action" value="<?php echo esc_attr( $options['shortcode_call_to_action'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Button Label:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_button_label]" id="shortcode_button_label" value="<?php echo esc_attr( $options['shortcode_button_label'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'First Name field size:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_first_name_field_size]" id="shortcode_first_name_field_size" value="<?php echo esc_attr( $options['shortcode_first_name_field_size'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'First Name Placeholder:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_first_name_placeholder]" id="shortcode_first_name_placeholder" value="<?php echo esc_attr( $options['shortcode_first_name_placeholder'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Require First Name?', 'post-notif' ); ?>
            				</th>
            			<td>
							<label for="shortcode_require_first_name">
            					<input type="checkbox" name="post_notif_settings[shortcode_require_first_name]" id="shortcode_require_first_name" value="1" <?php echo ( ( array_key_exists( 'shortcode_require_first_name', $options ) ) ? 'checked' : '' ); ?> />
            				</label>
            			</td>
            		</tr>
            		<tr valign="top">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Email Address field size:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_email_addr_field_size]" id="shortcode_email_addr_field_size" value="<?php echo esc_attr( $options['shortcode_email_addr_field_size'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Email Address Placeholder:', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_email_addr_placeholder]" id="shortcode_email_addr_placeholder" value="<?php echo esc_attr( $options['shortcode_email_addr_placeholder'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Override theme CSS?', 'post-notif' ); ?>
            			</th>
            			<td>
							<label for="shortcode_override_theme_css">
            					<input class="pn-chk-override-theme-options" type="checkbox" name="post_notif_settings[shortcode_override_theme_css]" id="shortcode_override_theme_css" value="1" <?php echo ( ( array_key_exists( 'shortcode_override_theme_css', $options ) ) ? 'checked' : '' ); ?> />
            				</label>
            			</td>
            		</tr>	
            	</table>
				<table class="form-table" id="override_theme_css_option" <?php echo ( ( array_key_exists( 'shortcode_override_theme_css', $options ) ) ? '' : 'style="display: none"' ); ?> >					
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Stylesheet filename:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="25" name="post_notif_settings[shortcode_stylesheet_filename]" id="shortcode_stylesheet_filename" value="<?php echo esc_attr( $options['shortcode_stylesheet_filename'] ); ?>" >
            				<br />
            				<p class="description">
            					<?php esc_html_e( 'This file must be located in the ../post-notif/public/css directory. If this field is populated, all override fields below will be ignored.', 'post-notif' ); ?>
            				</p>
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Call to Action font family:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="25" name="post_notif_settings[shortcode_call_to_action_font_family]" id="shortcode_call_to_action_font_family" value="<?php echo esc_attr( $options['shortcode_call_to_action_font_family'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Call to Action font size [include unit of measure]:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_call_to_action_font_size]" id="shortcode_call_to_action_font_size" value="<?php echo esc_attr( $options['shortcode_call_to_action_font_size'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<label for="shortcode_call_to_action_font_color"><?php esc_html_e( 'Call to Action font color:', 'post-notif' ); ?></label>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_call_to_action_font_color]" id="shortcode_call_to_action_font_color" value="<?php echo esc_attr( $options['shortcode_call_to_action_font_color'] ); ?>" class="color-picker" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Placeholder font family:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="25" name="post_notif_settings[shortcode_placeholder_font_family]" id="shortcode_placeholder_font_family" value="<?php echo esc_attr( $options['shortcode_placeholder_font_family'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Placeholder font size [include unit of measure]:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_placeholder_font_size]" id="shortcode_placeholder_font_size" value="<?php echo esc_attr( $options['shortcode_placeholder_font_size'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<label for="shortcode_placeholder_font_color"><?php esc_html_e( 'Placeholder font color:', 'post-notif' ); ?></label>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_placeholder_font_color]" id="shortcode_placeholder_font_color" value="<?php echo esc_attr( $options['shortcode_placeholder_font_color'] ); ?>" class="color-picker" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Input fields font family:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="25" name="post_notif_settings[shortcode_input_fields_font_family]" id="shortcode_input_fields_font_family" value="<?php echo esc_attr( $options['shortcode_input_fields_font_family'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Input fields font size [include unit of measure]:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_input_fields_font_size]" id="shortcode_input_fields_font_size" value="<?php echo esc_attr( $options['shortcode_input_fields_font_size'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<label for="shortcode_input_fields_font_color"><?php esc_html_e( 'Input fields font color:', 'post-notif' ); ?></label>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_input_fields_font_color]" id="shortcode_input_fields_font_color" value="<?php echo esc_attr( $options['shortcode_input_fields_font_color'] ); ?>" class="color-picker" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Error message font family:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="25" name="post_notif_settings[shortcode_error_font_family]" id="shortcode_error_font_family" value="<?php echo esc_attr( $options['shortcode_error_font_family'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Error message font size [include unit of measure]:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_error_font_size]" id="shortcode_error_font_size" value="<?php echo esc_attr( $options['shortcode_error_font_size'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<label for="shortcode_error_font_color"><?php esc_html_e( 'Error message font color:', 'post-notif' ); ?></label>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_error_font_color]" id="shortcode_error_font_color" value="<?php echo esc_attr( $options['shortcode_error_font_color'] ); ?>" class="color-picker" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Success message font family:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="25" name="post_notif_settings[shortcode_message_font_family]" id="shortcode_message_font_family" value="<?php echo esc_attr( $options['shortcode_message_font_family'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<?php esc_html_e( 'Success message font size [include unit of measure]:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_message_font_size]" id="shortcode_message_font_size" value="<?php echo esc_attr( $options['shortcode_message_font_size'] ); ?>" >
            			</td>
            		</tr>
					<tr valign="top">
            			<th scope="row" style="padding-left: 50px;">
            				<label for="shortcode_message_font_color"><?php esc_html_e( 'Success message font color:', 'post-notif' ); ?></label>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[shortcode_message_font_color]" id="shortcode_message_font_color" value="<?php echo esc_attr( $options['shortcode_message_font_color'] ); ?>" class="color-picker" >
            			</td>
            		</tr>					
				</table> 
            	<h2 class="title"><?php esc_html_e( 'Subscriber Form Shortcode Messages', 'post-notif' ); ?></h2>
            	<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Error (required first name is blank):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_error_reqd_first_name_blank]" id="shortcode_error_reqd_first_name_blank" value="<?php echo esc_attr( $options['shortcode_error_reqd_first_name_blank'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Error (blank email address):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_error_email_addr_blank]" id="shortcode_error_email_addr_blank" value="<?php echo esc_attr( $options['shortcode_error_email_addr_blank'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Error (invalid email address):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_error_email_addr_invalid]" id="shortcode_error_email_addr_invalid" value="<?php echo esc_attr( $options['shortcode_error_email_addr_invalid'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (processing):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_info_message_processing]" id="shortcode_info_message_processing" value="<?php echo esc_attr( $options['shortcode_info_message_processing'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (already subscribed):', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_info_message_already_subscribed]" id="shortcode_info_message_already_subscribed" value="<?php echo esc_attr( $options['shortcode_info_message_already_subscribed'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (successful subscription request):', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_success_message]" id="shortcode_success_message" value="<?php echo esc_attr( $options['shortcode_success_message'] ); ?>">
            				<br />
            			</td>
            		</tr>
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Message (failed subscription request):', 'post-notif' ); ?>
            				</th>
            			<td>
            				<input type="text" size="75" name="post_notif_settings[shortcode_failure_message]" id="shortcode_failure_message" value="<?php echo esc_attr( $options['shortcode_failure_message'] ); ?>">
            				<br />
            			</td>
            		</tr>
            	</table>
				<h2 class="title"><?php esc_html_e( 'Admin Menu Settings', 'post-notif' ); ?></h2>
				<table class="form-table">
            		<tr valign="top">
            			<th scope="row">
            				<?php esc_html_e( 'Position in menu:', 'post-notif' ); ?>
            			</th>
            			<td>
            				<input type="text" size="5" name="post_notif_settings[admin_menu_position]" id="admin_menu_position" value="<?php echo esc_attr( $options['admin_menu_position'] ); ?>">
            				<br />
            				<p class="description">
            					<?php esc_html_e( 'If you cannot see the Post Notif admin menu, change this to another number of the form "3.xyz".', 'post-notif' ); ?>
            				</p>
            			</td>
            		</tr>
            	</table>
            	<?php submit_button() ?>
            </form>
        </div> <!-- end post-body-content -->
    </div>
