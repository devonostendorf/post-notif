<?php

/**
 * Add title to widget.
 *
 * This markup generates the public-facing widget's title.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.3.0
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes/partials
 */

if ( ! empty( $instance['title'] ) ) {
	echo $args['before_title'] . $title . $args['after_title'];
}
