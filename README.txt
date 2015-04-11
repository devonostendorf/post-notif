=== Post Notif ===
Contributors: devonostendorf
Donate link: https://devonostendorf.com
Tags: post, notif, notification, email, subscribe
Requires at least: 4.1.1
Tested up to: 4.1.1
Stable tag: 1.0.0
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
		
* Configurable widget labels:
	* Title
	* Call to action
	* Submit button label
	
* Admin and editor tools:
	* View subscribers (and the post categories they've subscribed to)
	* View post notifications sent
		
* Admin-only tools:
	* Delete subscribers (either individually or in bulk)
		
* Multisite capable

= Benefits: =

* No file server configuration required:
	* All configuration of email subjects and contents done via the plugin's settings page
	* All configuration of user preferences "pages" done via the plugin's settings page
	
* Dynamically-created pseudo-pages used for user preference configuration, avoiding issues with themes that automatically add pages to menus

* For Multisite networks, once the plugin is installed in the network's plugin directory, individual site activation and configuration of the plugin can be handled by individual site admins without any access to the file server.

== Installation ==

= Multisite =  
1. Extract `post-notif.zip` into a directory on your computer  
2. Upload the `post-notif` directory to your plugins directory (typically `../wp-content/plugins`)  
3. If your `.htaccess` file is similar to the recommended default (https://codex.wordpress.org/htaccess), everything should work fine (the `.htaccess` file's regular expressions will handle the plugin's URLs)  
4. Be sure you ONLY activate it for one or more individual sites - DO NOT "network activate" it (there is not yet a way for plugin authors to disable this option which is why it is even available for this plugin)  
5. Configure options (Settings >> Post Notif)  
6. Add and configure widget (Appearance >> Widgets)  

= Single Site =  
1. Extract `post-notif.zip` into a directory on your computer  
2. Upload the `post-notif` directory to your plugins directory (typically `../wp-content/plugins`)  
3. Activate the plugin on the WordPress Plugin Dashboard  
4. Choose the one bullet item below that best represents your configuration and perform the steps specified for it:

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

== Frequently Asked Questions ==

= Why is WordPress 4.1.1 listed as the minimum version required? =

I strongly believe admins need to keep their sites updated with the current WordPress core.  This is particularly important for security reasons but also because it admins and their users with new-and-improved functionality.  If you cannot (or will not) upgrade, this plugin will probably work fine if you're running WordPress 3.5 or higher.  But seriously, you really should upgrade!

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

== Screenshots ==

**NOTE:** #1 - 7 depict admin functionality while #8 - 13 show subscriber screenshots: 

1. Activating the plugin
2. Overriding the default settings with your own custom values
3. Adding the widget to a sidebar and overriding the defaults with your own custom values
4. Sending a post notification
5. Viewing post notifications sent
6. Viewing current subscribers
7. Delete subscribers (these two never confirmed their requests)
8. User subscribing to post notification via the widget
9.	User receives subscription confirmation email
10. User has confirmed subscription request
11. User has chosen to update their subscription preferences
12. User receives post notification email
13. User has decided to unsubscribe (via either link in post notification email or unsubscribe link at the bottom of subscription preferences page)

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==
N/A

== Thanks ==

Special thanks to:

* [morty](https://profiles.wordpress.org/morty/) and all who worked on the Post Notification plugin, which inspired the creation of this plugin
* [Tom McFarlin](http://tommcfarlin.com) for creating the [WordPress Plugin](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/ "WordPress Plugin Boilerplate") and [Widget](https://github.com/tommcfarlin/WordPress-Widget-Boilerplate/ "WordPress Widget Boilerplate") Boilerplates
* [Matt Van Andel](http://www.mattvanandel.com) for the [Custom List Table Example plugin](https://wordpress.org/plugins/custom-list-table-example/)
* All of the people courageous enough to risk sounding stupid by asking the same questions I had about how things work in WordPress!