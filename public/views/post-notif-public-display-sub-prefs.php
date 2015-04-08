<?php

/**
 * Subscription preferences (pseudo) page.
 *
 * This markup generates the subscription preferences (pseudo) page.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.0
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/public/views
 */

echo $sub_prefs_greeting;
?>
	<br />
	<br />
<?php
echo $sub_pref_selection_instrs;         
echo '<form action="' . esc_attr( get_admin_url() ) . 'admin-post.php" method="post">';
echo '<input type="hidden" name="action" value="sub-prefs-form" />';
echo '<input type="hidden" name="hdnConfCd" value="' . esc_attr( $authcode ) . '" />';
echo '<input type="hidden" name="hdnEmailAddr" value="' . esc_attr( $email_addr ) . '" />';
$all_selected = in_array( 0, $category_selected_arr );
echo '<input type="checkbox" id="id_chkCatID_0" name="chkCatID_0" value=0 ' . ( ( $all_selected == true ) ? 'CHECKED' : '' ) . '>&nbsp;' . __( 'All', 'post-notif' ) . '</input><br />';

$args = array(
	'exclude' => 1,		// Omit Uncategorized
	'orderby' => 'name',
	'order' => 'ASC',
	'hide_empty' => 0
);

echo '<ul>';
$categories = get_categories( $args );
foreach( $categories as $category ) { 
	if ( $all_selected ) {
	
		// "All" categories is selected, default everything else to selected and grayed out
		echo '&nbsp;&nbsp;<input type="checkbox" class="cats" id="id_chkCatID_' . esc_attr( $category->cat_ID ) . '" name="chkCatID_' . esc_attr( $category->cat_ID ) . '" value="' . esc_attr( $category->cat_ID ) . '" CHECKED DISABLED>&nbsp;' . $category->name . '</input><br />';
	}
	else
	{
		echo '&nbsp;&nbsp;<input type="checkbox" class="cats" id="id_chkCatID_' . esc_attr( $category->cat_ID ) . '" name="chkCatID_' . esc_attr( $category->cat_ID ) . '" value="' . esc_attr( $category->cat_ID ) . '" ' . ( in_array($category->cat_ID, $category_selected_arr) ? 'CHECKED' : '' ) . '>&nbsp;' . $category->name. '</input><br />';
	}
} 
echo '</ul>';
    
echo '<input type="submit" value="' . __( 'Update', 'post-notif' ) . '" />';
echo '</form>';
echo '<br />';
echo '<a href="' . site_url() . '/post_notif/unsubscribe/?email_addr=' . esc_attr( $email_addr ) . '&authcode=' . esc_attr( $authcode ) . '">' . $unsub_link_label . '</a>';
$this->get_sidebar_minus_post_notif_recent_posts_widgets(); 
?>
