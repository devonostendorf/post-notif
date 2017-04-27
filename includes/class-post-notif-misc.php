<?php

/**
 * A collection of miscellaneous functions that need to be accessible to 
 *	multiple classes.
 *
 * @link		https://devonostendorf.com/projects/#post-notif
 * @since		1.0.2
 *
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
 */

/**
 * A collection of miscellaneous functions.
 *
 * Defines functions to handle:
 *		1) Authcode generation
 *		2) Post excerpt generation
 *		3) Subscriber base URL generation
 *		4) Local timezone offset from UTC calculation
 *		5) Subscription confirmation email send
 *		6) UTC to local timezone datetime conversion
 *
 * @since		1.0.2
 * @package		Post_Notif
 * @subpackage	Post_Notif/includes
 * @author		Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Misc {
	
 	/**
	 * Generate a new authcode.
	 *
	 * @since	1.0.2
	 * @return	string	Newly-generated authcode
	 */
	public static function generate_authcode() {

		// Generate authcode
			
		// NOTE: Thanks to the Kohana team and their Text::random() function (system/classes/Kohana/Text.php)
		//		for inspiration behind the authcode generation code.
		/*
			Per http://kohanaframework.org/license:
				
			Copyright © 2007–2015 Kohana Team. All rights reserved.

			Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

			Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
			Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
			Neither the name of the Kohana nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

			THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.			
		*/
			
		$authcode = '';
		$authcode_length = 32;
		$authcode_pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$authcode_pool = str_split( $authcode_pool, 1 );

		// Largest possible key
		$max_authcode_length = count( $authcode_pool ) - 1;

		for ( $i = 0; $i < $authcode_length; $i++ ) {
	
			// Select a random character to add to the string
			$authcode .= $authcode_pool[mt_rand( 0, $max_authcode_length )];
		}

		if ( ctype_alpha( $authcode ) ) {
		
			// String contains ONLY letters
				
			// Add a random numeric digit
			$authcode[mt_rand( 0, $authcode_length - 1 )] = chr( mt_rand( 48, 57 ) );
		}
		elseif ( ctype_digit( $authcode ) ) {
		
			// String contains ONLY numeric digits

			// Add a random letter
			$authcode[mt_rand( 0, $authcode_length - 1 )] = chr( mt_rand( 65, 90 ) );
		}
		/*
			End - (modified) Kohana code
		*/
		return $authcode;
	}
	
 	/**
	 * Generate (or retrieve) post excerpt of type specified.
	 *
	 * @since	1.1.0
	 * @param	int				$post_id	Post ID to retrieve.
	 * @param	string			$excerpt_type	Type of excerpt ('auto', 'manual, or 'teaser') to produce.
	 * @return	string	The excerpt, retrieved or generated for the specified post.
	 */
	public static function generate_excerpt( $post_id, $excerpt_type ) {
	
		$post_attribs = get_post( $post_id ); 
		$post_content = $post_attribs->post_content;
		
		switch ( $excerpt_type ) {			
			case 'auto':
				
				// NOTE: Thanks to Withers Davis (http://uplifted.net/programming/wordpress-get-the-excerpt-automatically-using-the-post-id-outside-of-the-loop/)
				//		for a clever algorithm
				
				// Standard WordPress core auto excerpts consist of a post's first 55 words
				$word_count_in_excerpt = 55;
				
				$post_content_stripped = strip_tags( strip_shortcodes( $post_content ) );
				
				// Populate word array with first 55 words and the rest of the content as the 56th "word"
				$word_arr = explode( ' ', $post_content_stripped, $word_count_in_excerpt + 1 );
				if ( count( $word_arr ) > $word_count_in_excerpt ) {
						
					// Clear anything beyond 55 words from the array
					array_pop( $word_arr );
				
					// Add ellipsis as a new element on the end of the array to indicate content has been truncated
					array_push( $word_arr, '...');
				}	
				
				// Recreate excerpt string from array				
				return implode( ' ', $word_arr );
				
			case 'manual':
				return strip_tags( strip_shortcodes( $post_attribs->post_excerpt ) );
				
			case 'teaser':
				$more_found_loc = strpos( $post_content, '<!--more-->' );
				if ( false !== $more_found_loc ) {
					
					// "More" tag found, return content preceding it
					return strip_tags( strip_shortcodes( substr( $post_content, 0, $more_found_loc ) ) );
				}
		}
		
		// No excerpt found or bad type passed
		return '';
		
	}

	/**
	 * Generate generic subscriber URL base.
	 *
	 * @since	1.0.8
	 * @return	string	Newly-generated URL base
	 */
	public static function generate_subscriber_url_base() {
		
		// Build subscriber URL base starting with the site's URL
		$url = get_site_url();

		if ( array_key_exists( 'custom_permalink_with_category_concat', get_option( 'post_notif_settings' ) ) ) {
			
			// Admin has indicated in Post Notif settings that they are using %category% concatenated with something
			//		in their custom permalinks			
			$category_base = get_option( 'category_base', '' );
			if ( empty( $category_base ) ) {
				
				// NO category base defined
				$url .= '/category';
			}
			else {
				
				// Category base IS defined
				$url .= '/' . $category_base;
			}
		}

		// Add boilerplate placeholder
		$url .= '/post_notif/ACTION_PLACEHOLDER';

		// Determine whether trailing "/" is required
		$permalink_structure = get_option( 'permalink_structure', '' );
		
		if ( empty( $permalink_structure ) || ( '/' == ( substr( $permalink_structure, -1 ) ) ) ) {
					
			// Include trailing "/", in URLs, based on blog's current permalink structure
			$url .= '/';
		}
		
		return $url;

	}

 	/**
	 * Calculate local timezone's offset from UTC.
	 *
	 * @since	1.1.0
	 * @return	int	The offset, in seconds, between UTC timezone and blog's local timezone.
	 */
	public static function offset_from_UTC() {

		$local_timezone = get_option( 'timezone_string', 'UTC' );

		if ( false == trim( $local_timezone ) ) {
			$local_timezone = 'UTC';
		}
		
		// This needs to be today's date to properly account for Daylight Saving Time
		$today = new DateTime( date( 'Y-m-d' ), new DateTimeZone( $local_timezone ) );
		
		return $today->getOffset();
		
	}
	
 	/**
	 * Send failed subscriber creation email to admin.
	 *
	 * @since	1.1.3
	 * @param 	array	$subscriber_arr	A single subscriber's data (first name, email addr).
	 */
	public static function send_admin_failed_subscriber_creation_email( $subscriber_arr ) {

		// This is based on Post Notif settings
    		
		// Compose email
		    
		$post_notif_options_arr = get_option( 'post_notif_settings' );
    
		$email_subject = __( 'Post Notif: FAILURE to create subscriber', 'post-notif' );

		$email_body = __( 'The following person attempted to subscribe but subscriber creation failed.', 'post-notif' );
		$email_body .= '  ' . __( 'You should add them directly (via the Import Subscribers process and then "resend" their confirmation email [from the Manage Subscribers page]), but also be sure to check your database permissions too, to determine why failure occurred in the first place!', 'post-notif' ) . '<br /><br />';
		$email_body .= __( 'First Name: ', 'post-notif' ) . $subscriber_arr['first_name'] . '<br />';
		$email_body .= __( 'Email Address: ', 'post-notif' ) . $subscriber_arr['email_addr'];
				
		// Send email to admin
    		
		// Set sender name and email address
		$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
			. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
   		
		// Specify HTML-formatted email
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
   
		//	Physically send email
   		$mail_sent = wp_mail( $post_notif_options_arr['eml_sender_eml_addr'], $email_subject, $email_body, $headers );   
			  
	}
	
 	/**
	 * Send subscription confirmation email to a subscriber.
	 *
	 * @since	1.0.2
	 * @param 	array	$subscriber_arr	A single subscriber's data (first name, email addr, authcode).
	 */
	public static function send_confirmation_email( $subscriber_arr ) {

		// This is based on Post Notif settings, with placeholders replaced by user-specific/admin-specified data
    		
		// Compose confirmation email
		    
		$post_notif_options_arr = get_option( 'post_notif_settings' );
    
		// Replace variables in both the post notif email subject and body
   
		$conf_email_subject = $post_notif_options_arr['sub_conf_eml_subj'];
		$conf_email_subject = str_replace( '@@blogname', get_bloginfo( 'name' ), $conf_email_subject );
    		
		// Tell PHP mail() to convert both double and single quotes from their respective HTML entities to their applicable characters
		$conf_email_subject = html_entity_decode( $conf_email_subject, ENT_QUOTES, 'UTF-8' );

		$conf_email_body = $post_notif_options_arr['sub_conf_eml_body'];
		$conf_email_body = str_replace( '@@firstname', ( '[Unknown]' != $subscriber_arr['first_name'] ) ? $subscriber_arr['first_name'] : '', $conf_email_body );
		$conf_email_body = str_replace( '@@blogname', get_bloginfo( 'name' ), $conf_email_body );

		// Generate generic subscriber URL base
		$subscriber_url_template = Post_Notif_Misc::generate_subscriber_url_base();

		// Tailor confirm link for current subscriber
		$subscriber_url = $subscriber_url_template . '?email_addr=' . $subscriber_arr['email_addr'] . '&authcode=' . $subscriber_arr['authcode'];
		$conf_url = str_replace( 'ACTION_PLACEHOLDER', 'confirm', $subscriber_url );
		$conf_email_body = str_replace( '@@confurl', '<a href="' . $conf_url . '">' . $conf_url . '</a>', $conf_email_body );

		$conf_email_body = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $conf_email_body );
       
		// Send confirmation email to new subscriber
    		
		// Set sender name and email address
		$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
			. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
   		
		// Specify HTML-formatted email
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
   
		//	Physically send email
   		$mail_sent = wp_mail( $subscriber_arr['email_addr'], $conf_email_subject, $conf_email_body, $headers );   
			  
	}

 	/**
	 * Convert UTC datetime to local timezone.
	 *
	 * @since	1.1.0
	 * @param	string			$utc_datetime	Datetime in UTC timezone
	 * @return	string	The date, converted to blog's local timezone and formatted to 'October 20, 2016 @ 8:49 AM' format.
	 */
	public static function UTC_to_local_datetime( $utc_datetime ) {
		
		$local_datetime = new DateTime( $utc_datetime );
		
		$local_timezone = get_option( 'timezone_string', 'UTC' );
		
		if ( false == trim( $local_timezone ) ) {
			$local_timezone = 'UTC';
		}
		
		$local_datetime->setTimezone( new DateTimeZone( $local_timezone ) );
		
		return $local_datetime->format('F j, Y @ g:i:s A') . "\n";	
		
	}
	
}
