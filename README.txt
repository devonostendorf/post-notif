=== Post Notif ===
Contributors: devonostendorf
Donate link: https://devonostendorf.com
Tags: post, notif, notification, email, subscribe
Requires at least: 4.1.1
Tested up to: 4.2
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Notify subscribers when you publish new posts.

== Description ==

Post Notif is an easy way to keep your readers notified when you've posted something new.

Simply tailor the subscription widget labels, the email subjects and bodies, and the subscription preferences pages - all via the common WordPress online admin menu area (no tinkering with files required :) ).

= Included features: =

* Configurable email settings:
	* Plugin email sender name and email address
	* Subscription confirmation email subject and body
	* Post notification email subject and body
	* @@signature variable (for optional use in subscription confirmation and/or post notification emails)
		
* Configurable page settings:
	* Subscription confirmed page title and page greeting 	
	* Subscription preferences selection instructions	
	* Current subscription preferences page title and page greeting	
	* Subscription preferences updated page title and page greeting	
	* Unsubscribe link label
	* Unsubscribe confirmation page title
	* Unsubscribe confirmation page greeting
	
* Configurable widget messages:
	* Blank and invalid email address errors 	
	* Already subscribed info message	
	* Successful subscription request message	
		
* Configurable widget labels/input field placeholders:
	* Title
	* Call to action
	* Submit button label
	* First name and email placeholders
	
* Admin and editor tools:
	* View subscribers (and the post categories they've subscribed to)
	* View post notifications sent
		
* Admin-only tools:
	* Manage subscribers (either individually or in bulk):
		* Delete
		* Resend confirmation email
		
* Multisite capable

= Benefits: =

* No file server configuration required:
	* All configuration of email subjects and contents done via the plugin's settings page
	* All configuration of user preferences "pages" done via the plugin's settings page
	
* Dynamically-created pseudo-pages used for user preference configuration, avoiding issues with themes that automatically add pages to menus

* For Multisite networks, once the plugin is installed in the network's plugin directory, individual site activation and configuration of the plugin can be handled by individual site admins without any access to the file server.

== Installation ==

**If upgrading to v1.0.2 from a previous version of the plugin**  
Be sure you deactivate the plugin and then re-activate it IMMEDIATELY after installing the update!

= Download and Install =    
1. Go to "Plugins > Add New" in your Dashboard and search for: Post Notif
2. Click the "Install Now" button for the Post Notif plugin
3. See the appropriate section below ("Single Site - Activate and Configure" or "Multisite - Activate and Configure") **BEFORE** activating

**OR**  

1. Download `post-notif.[version_number].zip` from the WordPress.org plugin directory
2. Extract `post-notif.[version_number].zip` into a directory on your computer  
3. Upload the `post-notif` directory to your plugins directory (typically `../wp-content/plugins`)  
4. See the appropriate section below ("Single Site - Activate and Configure" or "Multisite - Activate and Configure") **BEFORE** activating

= Single Site - Activate and Configure =  
1. Activate the plugin in the Plugins admin menu (Plugins >> Installed Plugins)  
2. Choose the one bullet item below that best represents your configuration and perform the steps specified for it:

* If you are using permalink settings OTHER THAN the "Default" (Settings >> Permalinks >> Common Settings >> Default [radio button]), everything should work fine (the `.htaccess` file's regular expressions will handle the plugin's URLs)  
	1. Configure options (Settings >> Post Notif)  
	2. Add and configure widget (Appearance >> Widgets)  
  
* If you ARE using the "Default" permalink settings (Settings >> Permalinks >> Common Settings >> Default [radio button]) and don't mind changing your URL formatting, select another option under "Common Settings" and Save Changes.  If WordPress tells you that it is unable to automatically update your `.htaccess` file, due to permissions issues, manually make the changes it prescribes to your `.htaccess` file.  The Post Notif plugin should work fine now.  
	1. Configure options (Settings >> Post Notif)  
	2. Add and configure widget (Appearance >> Widgets)  
  
* If you are deliberately using the default permalink settings and want to keep it that way, you'll need to manually modify (or, if it does not exist, create) your `.htaccess` file in your site's root directory (where `wp-config.php` also resides), so that it contains:  
	1. Add the following to `.htaccess`:
	
		`# BEGIN WordPress`  	
		`<IfModule mod_rewrite.c>`  
		`RewriteEngine On`  
		`RewriteBase /`  
		`RewriteRule ^index\.php$ - [L]`  
		`RewriteCond %{REQUEST_FILENAME} !-f`  
		`RewriteCond %{REQUEST_FILENAME} !-d`  
		`RewriteRule . /index.php [L]`  
		`</IfModule>` 

		`# END WordPress`  

		* **NOTE:** If your blog resides in a subdirectory (e.g. "http://blogname.com/blogdir"), you'll want to use the following for `.htaccess` (instead of what's shown above), replacing 'blogdir' (on lines 4 and 8) with the literal subdirectory name:
    	
			`# BEGIN WordPress`  	
			`<IfModule mod_rewrite.c>`  
			`RewriteEngine On`  
			`RewriteBase /blogdir/`  
			`RewriteRule ^index\.php$ - [L]`  
			`RewriteCond %{REQUEST_FILENAME} !-f`  
			`RewriteCond %{REQUEST_FILENAME} !-d`  
			`RewriteRule . /blogdir/index.php [L]`  
			`</IfModule>` 

			`# END WordPress`  
	2. Configure options (Settings >> Post Notif)  
	3. Add and configure widget (Appearance >> Widgets)  

= Multisite - Activate and Configure =  
1. If your `.htaccess` file is similar to the recommended default (https://codex.wordpress.org/htaccess), everything should work fine (the `.htaccess` file's regular expressions will handle the plugin's URLs)  
2. Be sure you ONLY activate it for one or more individual sites - DO NOT "network activate" it (there is not yet a way for plugin authors to disable this option which is why it is even available for this plugin)  
3. Configure options (Settings >> Post Notif)  
4. Add and configure widget (Appearance >> Widgets)  

== Frequently Asked Questions ==

= Why is such a recent version of WordPress listed as the minimum version required? =

I strongly believe admins need to keep their sites updated with the current WordPress core.  This is particularly important for security reasons but also because it provides admins and their users with new-and-improved functionality.  If you cannot (or will not) upgrade, this plugin will probably work fine if you're running WordPress 3.5 or higher.  But seriously, you really should upgrade!

= How do I get this thing to work? =

Follow the steps on the Installation page and then review the Screenshots (which show the plugin "in action" from both the administrator's and a subscriber's perspective).  If people have more specific questions, I will update this page accordingly.

= Why aren't emails being sent from the email address I specified in the Post Notif settings? =

The email sender email address (under Email Settings in the settings) must be an email address already configured to send email from your domain.

= Why isn't there an option to schedule notifications? =

Since WordPress Cron is not a true UNIX-style Cron daemon, I believe it is disingenuous to imply that a notification will definitely be sent out according to a schedule that relies on it.  Since WP Cron's effectiveness is highly dependent on a site's traffic, allowing authors to manually trigger notification, once they've published a post, is the functionality I've chosen to provide.

= Why isn't Post Notif translated into my language? =

It is because no one who speaks your language has translated this plugin yet.  If you'd like to do so, you'll find the current post-notif.pot file in the post-notif/languages directory.  [Please contact me](https://devonostendorf.com) with any translation files you create - thanks much!

= Why create another post notification plugin? =

The Post Notif plugin came to life after I used the Post Notification plugin for several years.  Though Post Notification has not been actively maintained for some time now, until WordPress 4.x it worked well enough.  Post Notif aims to take a considerable amount of the functionality I liked from the Post Notification plugin while improving on some of the implementation and configuration challenges I faced while using the plugin (particularly in a multisite environment).

= What does the Resend Confirmation functionality actually do? =

Resending confirmation will both generate a new authcode AND set the CONFIRMED value to 0 for each subscriber it is applied to.  This functionality was implemented to make it easy for a site admin to re-send a confirmation email to a subscriber who has indicated they never received the initial confirmation email.  However, it WILL allow you to resend a confirmation email to a confirmed subscriber (which will effectively unconfirm them and require them to re-confirm), so you probably want to think long and hard about doing this for anyone who has already confirmed, unless they've specifically asked you to do so (because they want their authcode reset, for instance).

== Screenshots ==

1. Activating the plugin
2. Overriding the default settings with your own custom values
3. Adding the widget to a sidebar and overriding the defaults with your own custom values
4. Sending a post notification
5. Viewing post notifications sent
6. Viewing current subscribers
7. Manage subscribers
8. User subscribing to post notification via the widget
9.	User receives subscription confirmation email
10. User has confirmed subscription request
11. User has chosen to update their subscription preferences
12. User receives post notification email
13. User has decided to unsubscribe (via either link in post notification email or unsubscribe link at the bottom of subscription preferences page)

== Changelog ==

= 1.0.2 =
* Confirmed compatibility with WordPress 4.2 (though Post Notif 1.0.1 is compatible as well)
* Fixed issue with View Post Notifs Sent page showing nothing for installs with table prefix other than "wp"
* Added widget-related messages as configurable settings (Settings >> Post Notif)
* Added widget input field placeholders as configurable settings (Appearance >> Widgets >> Post Notif)
* Added "Resend Confirmation" as both a single and bulk action on "Manage Subscribers" (formerly "Delete Subscribers") page
* Updated screenshots to accurately reflect current functionality

= 1.0.1 =
* Updated installation steps in README.txt for clarity
* Added code to prevent attributes (category, author, and post date/time) and functionality (add comment) from appearing on subscriber preferences pages

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.2 =
Fixed issue with View Post Notifs Sent page showing nothing for site installs using a table prefix other than "wp_"

= 1.0.1 =
Fixed issue with some themes erroneously displaying extra attributes (category, author, and post date/time) and functionality (add comment) on subscriber preferences pages 

== Thanks ==

Special thanks to:

* [morty](https://profiles.wordpress.org/morty/) and all who worked on the Post Notification plugin, which inspired the creation of this plugin
* [Tom McFarlin](http://tommcfarlin.com) for creating the [WordPress Plugin](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/ "WordPress Plugin Boilerplate") and [Widget](https://github.com/tommcfarlin/WordPress-Widget-Boilerplate/ "WordPress Widget Boilerplate") Boilerplates
* [Matt Van Andel](http://www.mattvanandel.com) for the [Custom List Table Example plugin](https://wordpress.org/plugins/custom-list-table-example/)
* All of the people courageous enough to risk sounding stupid by asking the same questions I had about how things work in WordPress!