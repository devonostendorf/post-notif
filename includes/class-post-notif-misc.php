<?php

/**
 * A collection of miscellaneous functions that need to be accessible to 
 *	multiple classes.
 *
 * @link			https://devonostendorf.com/projects/#post-notif
 * @since      1.0.2
 *
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 */

/**
 * A collection of miscellaneous functions.
 *
 * Defines functions to handle:
 *		1) Authcode generation
 *		2) Subscription confirmation email send
 *
 * @since      1.0.2
 * @package    Post_Notif
 * @subpackage Post_Notif/includes
 * @author     Devon Ostendorf <devon@devonostendorf.com>
 */
class Post_Notif_Misc {
	
 	/**
	 * Generate a new authcode.
	 *
	 * @since	1.0.2
	 *	@return	string	Newly-generated authcode
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
	 * Send subscription confirmation email to a subscriber.
	 *
	 * @since	1.0.2
	 * @param 	array	$subscriber_arr	A single subscriber's data (first name, email addr, authcode).
	 */
	public static function send_confirmation_email( $subscriber_arr ) {

		// This is based on Post Notif settings, with placeholders replaced by user-specific/admin-specified data
    		
		// Compose confirmation email
		// NOTE: It is IMPERATIVE that there is "/" at the end of this URL or "@" will get filtered out of URL, causing all sorts of problems!
    
		$post_notif_options_arr = get_option( 'post_notif_settings' );
    
		// Replace variables in both the post notif email subject and body
   
		$conf_email_subject = $post_notif_options_arr['sub_conf_eml_subj'];
		$conf_email_subject = str_replace( '@@blogname', get_bloginfo( 'name' ), $conf_email_subject );
    		
		// Tell PHP mail() to convert both double and single quotes from their respective HTML entities to their applicable characters
		$conf_email_subject = html_entity_decode( $conf_email_subject, ENT_QUOTES, 'UTF-8' );

		$conf_email_body = $post_notif_options_arr['sub_conf_eml_body'];
		$conf_email_body = str_replace( '@@firstname', ( $subscriber_arr['first_name'] != '' ) ? $subscriber_arr['first_name'] : __( 'there', 'post-notif' ), $conf_email_body );
		$conf_email_body = str_replace( '@@blogname', get_bloginfo( 'name' ), $conf_email_body );

		// NOTE: This is in place to minimize chance that, due to email client settings, subscribers
		//		will be unable to see and/or click the confirm URL link within their email

   	// Include or omit trailing "/", in URL, based on blog's current permalink settings
   	$permalink_structure = get_option( 'permalink_structure', '' );
   	if ( empty( $permalink_structure ) || ( ( substr( $permalink_structure, -1) ) == '/' ) ) {
			$conf_url = get_site_url() . '/post_notif/confirm/?email_addr=' . $subscriber_arr['email_addr'] . '&authcode=' . $subscriber_arr['authcode'];
		}
		else {
			$conf_url = get_site_url() . '/post_notif/confirm?email_addr=' . $subscriber_arr['email_addr'] . '&authcode=' . $subscriber_arr['authcode'];
		}
		$conf_email_body = str_replace( '@@confurl', '<a href="' . $conf_url . '">' . $conf_url . '</a>', $conf_email_body );

		$conf_email_body = str_replace( '@@signature', $post_notif_options_arr['@@signature'], $conf_email_body );
       
		// Send confirmation email to new subscriber
    		
		// Set sender name and email address
		$headers[] = 'From: ' . $post_notif_options_arr['eml_sender_name'] 
			. ' <' . $post_notif_options_arr['eml_sender_eml_addr'] . '>';
   		
		// Specify HTML-formatted email
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
   
		wp_mail( $subscriber_arr['email_addr'], $conf_email_subject, $conf_email_body, $headers );   
			  
	}

}
