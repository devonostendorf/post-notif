<?php

/**
 * Subscription preferences (pseudo) page.
 *
 * This markup generates the subscription preferences (pseudo) page.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/public/views
 */
 
echo esc_html( $sub_prefs_greeting );
echo '<br />';
echo '<br />';

$post_notif_options_arr = get_option( 'post_notif_settings' );
$available_categories = array_key_exists( 'available_categories', $post_notif_options_arr ) ? $post_notif_options_arr['available_categories'] : '-1';

if ( '-1' != $available_categories ) {

	// Categories ARE available for subscribers to choose from
	
	echo esc_html( $sub_pref_selection_instrs );         
	echo '<form action="' . esc_attr( get_admin_url() ) . 'admin-post.php" method="post">';
	echo '<input type="hidden" name="action" value="sub-prefs-form" />';
	echo '<input type="hidden" name="hdnConfCd" value="' . esc_attr( $authcode ) . '" />';
	echo '<input type="hidden" name="hdnEmailAddr" value="' . esc_attr( $email_addr ) . '" />';
	
	// Is subscriber currently subscribed to all (available) categories?
	$all_selected = in_array( 0, $category_selected_arr );
	echo '<input type="checkbox" id="id_chkCatID_0" name="chkCatID_0" value=0 ' . ( ( true == $all_selected ) ? 'checked' : '' ) . '>&nbsp;' . __( 'All', 'post-notif' ) . '</input><br />';

	echo '<ul>';
	
	$all_cats_available = array_key_exists( 0, $available_categories );
	
	if ( $all_cats_available ) {
		
		// All categories (defined in the system) are available for subscribers
		//	to choose from
		
		$args = array(
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => 0
		);
		$system_categories = get_categories( $args );

		foreach( $system_categories as $category ) { 
			if ( $all_selected ) {
	
				// "All" categories is selected, default everything else to
				//	selected and grayed out
				echo '&nbsp;&nbsp;<input type="checkbox" class="cats" id="id_chkCatID_' . esc_attr( $category->cat_ID ) . '" name="chkCatID_' . esc_attr( $category->cat_ID ) . '" value="' . esc_attr( $category->cat_ID ) . '" checked disabled>&nbsp;' . $category->name . '</input><br />';
			}
			else {
				echo '&nbsp;&nbsp;<input type="checkbox" class="cats" id="id_chkCatID_' . esc_attr( $category->cat_ID ) . '" name="chkCatID_' . esc_attr( $category->cat_ID ) . '" value="' . esc_attr( $category->cat_ID ) . '" ' . ( in_array( $category->cat_ID, $category_selected_arr ) ? 'checked' : '' ) . '>&nbsp;' . $category->name. '</input><br />';
			}
		} 

	}
	else {

		// Only a subset of the categories defined in the system are available
		//	for subscribers to choose from

		foreach( $available_categories as $category_id => $category_val ) { 
			$system_category = get_category( $category_id );

			if ( $all_selected ) {
	
				// "All" categories is selected, default everything else to selected and grayed out
				echo '&nbsp;&nbsp;<input type="checkbox" class="cats" id="id_chkCatID_' . esc_attr( $category_id ) . '" name="chkCatID_' . esc_attr( $category_id ) . '" value="' . esc_attr( $category_id ) . '" checked disabled>&nbsp;' . $system_category->name . '</input><br />';
			}
			else {
				echo '&nbsp;&nbsp;<input type="checkbox" class="cats" id="id_chkCatID_' . esc_attr( $category_id ) . '" name="chkCatID_' . esc_attr( $category_id ) . '" value="' . esc_attr( $category_id ) . '" ' . ( in_array( $category_id, $category_selected_arr ) ? 'checked' : '' ) . '>&nbsp;' . $system_category->name . '</input><br />';
			}
		} 
	}

	echo '</ul>';
    
	echo '<input type="submit" value="' . esc_html__( 'Update', 'post-notif' ) . '" />';
	echo '</form>';
	echo '<br />';
}

// Generate generic subscriber URL base
$subscriber_url_template = Post_Notif_Misc::generate_subscriber_url_base();

// Tailor unsubscribe URL for current subscriber
$subscriber_url = $subscriber_url_template . '?email_addr=' . esc_attr( $email_addr ) . '&authcode=' . esc_attr( $authcode );
$prefs_url = str_replace( 'ACTION_PLACEHOLDER', 'unsubscribe', $subscriber_url );
echo '<a href="' . $prefs_url . '">' . $unsub_link_label . '</a>';
$this->get_sidebar_minus_post_notif_recent_posts_widgets(); 
?>
