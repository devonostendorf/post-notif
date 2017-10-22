=== Post Notif ===
Contributors: DevonOstendorf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6M98ZTPSAKPGU
Tags: post, notif, notification, email, subscribe
Requires at least: 4.1.1
Tested up to: 4.8
Requires PHP: 5.6
Stable tag: 1.1.5
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
	* Email sent to subscriber after subscription is confirmed subject and body
	* @@signature variable (for optional use in emails)
	* Many additional email template variables (for optional use in emails)
	* Send post notifications automatically when post is published 
		
* Configurable page settings:
	* Subscription confirmed page title and page greeting 	
	* Subscription preferences selection instructions	
	* Current subscription preferences page title and page greeting	
	* Subscription preferences updated page title and page greeting	
	* Unsubscribe link label
	* Unsubscribe confirmation page title
	* Unsubscribe confirmation page greeting
	
* Configurable widget settings:
	* Title label
	* Call to action text, font, font size, and font color
	* Submit button label
	* Require first name?
	* First name field size, font, font size, and font color
	* Email address field size, font, font size, and font color
	* First name and email placeholder text, font, font size, and font color
	* Error and status message font, font size, and font color
	
* Configurable widget messages:
	* Blank first name error
	* Blank and invalid email address errors
	* Widget processing message 	
	* Already subscribed info message	
	* Successful subscription request message	
		
* Configurable category settings:
	* Make all or a subset of system categories available to subscribers or turn off categories entirely
	
* Configurable post notification send:
	* Auto send (when post is published)
	* Send now
	* Schedule send (via WordPress cron)
	* Test send
	
* Admin and editor tools:
	* View subscribers (and the post categories they've subscribed to)
	* Manage post notifications sent (check the status of, pause, or cancel, post notification processes [both running and scheduled])
		
* Admin-only tools:
	* Import subscribers (see FAQ for details on how import process works)
	* Manage subscribers:
		* Export
		* Force confirm subscriber
		* Delete
		* Resend confirmation email
		
* Multisite capable

* Translations for the following languages:
	* Czech (cs_CZ) via language pack
	* Dutch (nl_NL) via language pack
	* German (de_DE and de_DE_formal) included in plugin package
	* Spanish (es_ES) included in plugin package

= Benefits: =

* No file server configuration required:
	* All configuration of email subjects and contents done via the plugin's settings page
	* All configuration of user preferences "pages" done via the plugin's settings page
	
* Dynamically-created pseudo-pages used for user preference configuration, avoiding issues with themes that automatically add pages to menus

* For Multisite networks, once the plugin is installed in the network's plugin directory, individual site activation and configuration of the plugin can be handled by individual site admins without any access to the file server.

== Installation ==

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

* If you are using permalink settings OTHER THAN the "Plain" (Settings >> Permalinks >> Common Settings >> Plain [radio button]), everything should work fine (the `.htaccess` file's regular expressions will handle the plugin's URLs)  
	1. Configure options (Settings >> Post Notif)  
	2. Add and configure widget (Appearance >> Widgets)  
  
* If you ARE using the "Plain" permalink settings (Settings >> Permalinks >> Common Settings >> Plain [radio button]) and don't mind changing your URL formatting, select another option under "Common Settings" and Save Changes.  If WordPress tells you that it is unable to automatically update your `.htaccess` file, due to permissions issues, manually make the changes it prescribes to your `.htaccess` file.  The Post Notif plugin should work fine now.  
	1. Configure options (Settings >> Post Notif)  
	2. Add and configure widget (Appearance >> Widgets)  
  
* If you are deliberately using the plain permalink settings and want to keep it that way, you'll need to manually modify (or, if it does not exist, create) your `.htaccess` file in your site's root directory (where `wp-config.php` also resides), so that it contains:  
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

= What's the Test Send functionality all about? =
This allows you to test send (to one email address or a list of comma-delimited email addresses) the post notification for the current post, so you can see the email subject and body, complete with variables substituted, as it will appear to your subscribers when you send it for real.  Please note that personalized variables (e.g., @@firstname, @@confurl, @@prefsurl, and @@unsubscribeurl) will not be resolved in the test send email.

= What does the Post Notif option ("Auto" or "Manual"), in the core Publish meta box, on the Edit Post page, do? =

This option defaults to whatever you've set it to on the Settings page (Settings >> Post Notif >> "Send post notifications when post published"), though the setting is ALWAYS defaulted to "Manual" for any post which has been published previously (to avoid inadvertent automatic re-sending of post notifications if you hit "Update" after something like a typo correction).  Regardless of what value defaults in, for a post, you can always override it, as desired.  If "Auto" is selected, post notifications will be sent immediately after the post is published, whether it is published by you manually or scheduled to be published by the system at a later date/time.  If "Manual" is selected, options will appear in the Post Notif meta box, corresponding to the post's current status.  If it has been published already, both "Send Now" and "Schedule" will be available.  If the post is scheduled to be published at a future date/time, only "Schedule" will be available.

= I've scheduled a post notification for a specific date/time, which has passed; why hasn't the post notification been sent? =

As WordPress Cron is not a true UNIX-style Cron daemon, some activity (even just a public page view from an unknown user) needs to happen SOMEWHERE on the blog, AFTER the schedule datetime, for the post notification process to be triggered.  You may also want to consider configuring your Post Notifications to be sent automatically upon post publish (see above).

= Where did the View Post Notifs Sent page go? =

It has been replaced with the Manage Post Notifs Sent page, which allows admins and editors to check the status of, pause, or cancel, post notification processes (both running and scheduled).

= How do I cancel a scheduled (future) notification? =

This functionality has been moved from the Post Notif metabox (on the Edit Post page) to the Manage Post Notifs Sent page; go there, find the scheduled notification and click on the "Cancel" action immediately below the Post ID.

= Why aren't all my notifications being sent out? =

If your web host throttles the number of emails you can send during a period of time, this plugin cannot override that limit.

= I have 10,000 subscribers - is this a good plugin for me to use to notify them? =

Post Notif is probably not a good fit for your needs (at least at this point in time).  Look into using a plugin that works in conjunction with a service like AWeber or MailChimp.

= Why isn't Post Notif translated into my language? =

It is because no one who speaks your language has translated this plugin yet.  If you'd like to do so, you'll find the current post-notif.pot file in the post-notif/languages directory.  [Please contact me](https://devonostendorf.com) with any translation files you create; I will help you get set up as a Project Translation Editor (PTE) with the WordPress.org [Polyglots team](https://make.wordpress.org/polyglots/) to create a language pack for Post Notif - thanks much!

= Why create another post notification plugin? =

The Post Notif plugin came to life after I used the Post Notification plugin for several years.  Though Post Notification has not been actively maintained for some time now, until WordPress 4.x it worked well enough.  Post Notif aims to take a considerable amount of the functionality I liked from the Post Notification plugin while improving on some of the implementation and configuration challenges I faced while using the plugin (particularly in a multisite environment).

= What does the Resend Confirmation functionality actually do? =

Resending confirmation will both generate a new authcode AND set the CONFIRMED value to 0 for each subscriber it is applied to.  This functionality was implemented to make it easy for a site admin to re-send a confirmation email to a subscriber who has indicated they never received the initial confirmation email.  However, it WILL allow you to resend a confirmation email to a confirmed subscriber (which will effectively unconfirm them and require them to re-confirm), so you probably want to think long and hard about doing this for anyone who has already confirmed, unless they've specifically asked you to do so (because they want their authcode reset, for instance).

= Why can't I see the Post Notif admin menu? =

It is likely clashing with another plugin's menu.  By default, Post Notif is set with a menu position value of 3.389.  If you do not see the Post Notif admin menu, go to the Admin Menu Settings section (under Settings >> Post Notif) and try new values in "Position in menu" (like 3.390) until you CAN see the menu.  For further reference see the [explanation of the position parameter](https://codex.wordpress.org/Function_Reference/add_menu_page).

= Why don't you provide a way for me to import subscribers directly from another plugin? =

In an effort to avoid an arms race of sorts, I've elected not to try to keep up on what various other plugins' database table structures are.  Consequently, the subscriber import functionality assumes you are able to export your existing subscribers and get them into comma-separated value (CSV) format.  That is, each row of data contains one or more fields, separated by commas, and terminated with a newline/carriage return.  If you are not familiar with CSV files, you can generate them fairly easily from Excel or an equivalent spreadsheet application (Excel allows you to save a file as CSV).

= Where do I import subscribers? =

Go to the Import Subscribers page (Post Notif >> Import Subscribers) and choose one of the two methods available there: "Import from file" or "Import directly".  If you choose to import directly, please be sure to copy-and-paste from a file you've saved somewhere as, once you press the Import button, the data in the field will not be retained when you return to the Import Subscribers page.

= What is the row format required for importing subscribers? =

At a minimum, each row requires a valid email address for the subscriber-to-be, like so:

jill@jill.com<br />
bill@bill.com<br />
gene@gene.com<br />

Optionally, you can include a first name (available for use in personalizing your email templates):

jill@jill.com,Jill<br />
bill@bill.com,Bill<br />
gene@gene.com<br />

Or, if your previous plugin tracked which categories each subscriber chose to be notified of posts for, you can provide one or more category IDs as well:

jill@jill.com,Jill,2,3<br />
bill@bill.com,Bill,1<br />
gene@gene.com,,2<br />

Please note that, as you can see from this last example, if you want to specify a category, you MUST provide a first name (even if it is blank/empty).

If you omit a first name for a row it will be defaulted to "[Unknown]" and when it is used in email templates it will be replaced with "there" (e.g. instead of "Hi Jill" or "Hi Bill", a subscriber with an unknown first name will be addressed as "Hi there").

If you omit categories for a row (as in the first two sets of examples above), the subscriber will be assigned all categories active in your system.

Also, do not worry if you have a trailing comma at the end of a row (as you may end up with via saving as CSV from Excel) - this will work fine too:

jill@jill.com,Jill,2,3<br />
bill@bill.com,Bill,1,<br />
gene@gene.com,,2,<br />

= What is the complete process for importing subscribers? =

Because I don't expect people to have 100% clean data the first time through, the subscriber creation process has two phases.  First, subscriber import, which loads each subscriber (and their categories) into staging tables in the database and validates each field against certain rules.  If the row is found valid (or has only permissible warnings), the import phase also stages it for subscriber creation.  If errors are found in a row during import, the staged row indicates those errors and cannot proceed to the second phase.  The second phase is subscriber creation, which takes cleanly staged subscriber rows and creates actual subscriber rows (which will receive post notifications going forward).  The only error that may occur during the subscriber creation phase is if a staged subscriber with an email address matching an existing subscriber (a duplicate) is encountered.

Once you've supplied your subscriber data (in the row format described above) and pressed the Import button, and the initial import phase completes, you are re-routed to the Staged Subscribers page.  Here you'll see the current status of each row you've attempted to load.

Please note that the Staged Subscribers page (and the database tables containing what you see on it) retain ALL rows you attempt to load, from ALL batches (you can think of a batch as those rows loaded each time you press the Import button) until you delete some or all of the rows (see below for how you do this).  There is no functional requirement to retain anything in the Staged Subscribers page once subscribers have been successfully created during the second phase of the process; it is strictly for the admin's analysis purposes, so rows can be deleted whenever you want to, or not at all.

If a row passed all the validation rules, you will see it with a Status of "Staged" (assuming the "Skip staging of clean rows?" checkbox is NOT selected - see 'What does the "Skip staging of clean rows?" checkbox do?' below for a bit more explanation of this).

Any row that failed one or more of the validation rules will fall into one of the following classifications:

1. The row has a blank email address: An error message is shown, indicating that the email address is missing - this row will not make it past this initial import step

2. The row has an invalid (badly formed) email address: An error message is shown, indicating that the email address is invalid - this row will not make it past this initial import step

3. The row has an email address that is too long (more than 100 characters): A warning message is shown, explaining that the email address has been truncated - this row IS eligible to continue through to subscriber creation

4. The row has a first name that is too long (more than 50 characters): A warning message is shown, explaining that the first name has been truncated - this row IS eligible to continue through to subscriber creation

5. The row has one or more category values that are non-numeric: An error message is shown, indicating which category(s) are non-numeric - this row will not make it past this initial import step

6. The row has one or more categories that cannot be found in the system: An error message is shown, indicating which category(s) are invalid - this row will not make it past this initial import step

On the Staged Subscribers page there are two actions available:

1. Delete (both single and bulk), which does exactly what you'd think it does - deletes selected staged subscribers (and their categories) from the staging tables (this has no bearing on the "real" subscriber tables)

2. Create (both single, for rows with no problems or with only truncation warnings [that still passed formatting checks] and bulk [though you can select rows ineligible for single creation, they get filtered out during processing]), which attempts to create the subscriber and their categories.  Since at this point they've passed the previously mentioned validation checks, the only thing that prevents creation at this point is if there is already a subscriber with the same email address, in which case, when the Staged Subscriber page refreshes, you'll see a status of "Duplicate email address".  All successfully created subscribers will show "Created".

Please note that selecting the checkbox to the left of the First Name header selects all rows.  Similarly, unchecking that checkbox clears the checkbox on each row.

= What does the "Skip staging of clean rows?" checkbox on the Import Subscribers page do? =

Selecting the "Skip staging of clean rows?" checkbox will do precisely that, effectively applying the Staged Subscriber "Create" functionality to each valid row in the import set.

= Why'd you make the subscriber import process so complicated?! =

I chose this two-phase approach as an attempt to satisfy both those in a hurry to simply import a bunch of email addresses they'd accumulated while using another plugin and for those that want to explicitly select what gets created during the creation phase, following review of the results of the validation process performed during the import phase.

= How do I export subscribers? =

Go to the Manage Subscribers page (Post Notif >> Manage Subscribers), select the subscribers you wish to export, select the Export option from the Bulk Actions dropdown, and press the Apply button.  You will be prompted for a file name and location to save the export file.  Subscribers are exported in the same CSV format as used by the import subscriber process (email address, first name, and category[s] subscribed to).  Here's an example:

jill@jill.com,Jill,2,3<br />
bill@bill.com,Bill<br />
gene@gene.com,[Unknown],2<br />

Note that Bill is an unconfirmed subscriber so he has no categories selected.

= How do I turn off categories? =

Go to the Settings page (Settings >> Post Notif):

1. Uncheck every category under "Categories available to subscribers" (in Category Settings section)

2. You probably also want to remove references to the "update prefs" URL (by default it is defined as "If you'd like to change the categories you're subscribed to, click here:@@prefsurl") from:
	* Email sent after subscription is confirmed body
	* Post notification email body
	
3. You probably also want to blank out:
	* Subscription preferences selection instructions
	* Current subscription preferences page greeting
	* Subscription preferences updated page greeting
	
4. Press the Save Changes button

You can later turn category functionality back on by doing the reverse of these steps.

= What variables are available for use in the Post Notif Settings configuration and where can they be used? =

1. @@blogname
	* Description: Your site's title (as defined in Settings >> General >> Site Title)
	
	* Can be used in:
		* Post notification email subject
		* Post notification email body
		* Subscription confirmation email subject
		* Subscription confirmation email body
		* Email sent after subscription is confirmed subject
		* Email sent after subscription is confirmed body
		* Subscription confirmed page title
		* Subscription confirmed page greeting
		* Subscription preferences selection instructions
		* Unsubscribe link label
		* Current subscription preferences page title
		* Current subscription preferences page greeting
		* Subscription preferences updated page title
		* Subscription preferences updated page greeting
		* Unsubscribe confirmation page title
		* Unsubscribe confirmation page greeting

2. @@posttitle
	* Description: Post's title
	
	* Can be used in:
		* Post notification email subject
		* Post notification email body

3. @@permalinkurl
	* Description: Post's permalink URL
	
	* Can be used in:
		* Post notification email body
	
4. @@permalink
	* Description: Post's permalink
	
	* Can be used in:
		* Post notification email body
	
5. @@postexcerptauto
	* Description: Post's first 55 words (standard WordPress core auto excerpt)
	
	* Can be used in:
		* Post notification email body
	
6. @@postexcerptmanual
	* Description: Post's manual [excerpt](https://codex.wordpress.org/Excerpt)
	
	* Can be used in:
		* Post notification email body
	
7. @@postteaser
	* Description: Post's content up to <code><!--more--></code>
	
	* Can be used in:
		* Post notification email body
	
8. @@featuredimage
	* Description: Post's featured image (post thumbnail)
	
	* Can be used in:
		* Post notification email body
	
9. @@signature
	* Description: Optional signature string [as defined in Settings >> Post Notif >> Email Settings >> @@signature])

	* Can be used in:	
		* Post notification email body
		* Subscription confirmation email body
		* Email sent after subscription is confirmed body
		
10. @@firstname
	* Description: Subscriber's first name or blank (if not defined)
	
	* Can be used in:	
		* Post notification email body
		* Subscription confirmation email body
		* Email sent after subscription is confirmed body
	
11. @@confurl
	* Description: Subscriber's unique confirmation URL
	
	* Can be used in:	
		* Subscription confirmation email body
	
12. @@prefsurl
	* Description: Subscriber's unique subscription preferences URL
	
	* Can be used in:	
		* Post notification email body
		* Email sent after subscription is confirmed body
	
13. @@unsubscribeurl
	* Description: Subscriber's unique [one-click] unsubscribe URL
	
	* Can be used in:	
		* Post notification email body
		* Email sent after subscription is confirmed body
	
14. @@postauthor
	* Description: Post's author's name
	
	* Can be used in:	
		* Post notification email subject
		* Post notification email body

15. @@postexcerpt
	* Description: Post's excerpt content
	* NOTE: This has been deprecated; use @@postexcerptmanual instead!
	
	* Can be used in:	
		* Post notification email body

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

= 1.1.5 =
Release Date: September 13, 2017

* FIXED: Fixed code to prevent duplicate notifs from being sent when a post has multiple categories
* FIXED: Fixed issue where subscriber import from file failed on "invalid" categories
* FIXED: Fixed situation where email addrs containing "+" wouldn't work with personalized URLs
* NEW: Added additional configurable settings to widget admin
* REMOVED: Removed language files for Czech and Dutch as both now have language packs maintained on WordPress.org

= 1.1.4 =
Release Date: May 14, 2017

* NEW: Added Czech translation (cs_CZ) by Jirka Licek
* CHANGED: Replaced generic gear dashicon with email dashicon for Post Notif top level menu

= 1.1.3 =
Release Date: April 30, 2017

* NEW: Added ability to auto-send post notifications when post is published
* CHANGED: Added ability to schedule post notifications for posts scheduled to be published
* FIXED: Restricted categories for subscriber import to set of PN-available categories
* FIXED: Added process to handle subscriber creation failure

= 1.1.2 =
Release Date: November 4, 2016

* NEW: Added Dutch (nl_NL) translation by frankmaNL
* FIXED: Updated custom table create statement to adhere to WordPress 4.6 dbDelta() KEY format

= 1.1.1 =
Release Date: October 28, 2016

* FIXED: Fixed pre-PHP 5.5 fatal errors with empty(trim()) calls
* CHANGED: Added corrected German translations (de_DE and de_DE_formal) by Ruediger Walter

= 1.1.0 =
Release Date: October 25, 2016

* NEW: Added additional, more specific, post excerpt vars for post notif email template
* NEW: Added @@permalinkurl, @@featuredimage vars for use in post notif email template
* FIXED: Fixed issue where subscriber import directly from textarea generates file error
* NEW: Added progress indicators to post notification send
* NEW: Added scheduling for post notification send process
* NEW: Added (force-)confirm subscriber (single and bulk) action to "Manage Subscribers" page
* NEW: Added post notification test send process
* FIXED: Fixed code to prevent PHP notice in Import Subscribers functionality
* CHANGED: Made all datetimes stored in DB UTC but display them on pages in local timezone
* FIXED: Fixed code to prevent invalid, selectable page, displaying in Post Notif admin menu in multisite environment
* FIXED: Fixed code which was preventing display of data on View Post Notifs Sent page in multisite environment

= 1.0.9 =
Release Date: May 23, 2016

* NEW: Added German translations (de_DE and de_DE_formal) by Ruediger Walter
* FIXED: Added missing 1.0.8 strings to Spanish translation

= 1.0.8 =
Release Date: April 30, 2016

* FIXED: Fixed handling of custom permalinks
* NEW: Added new variables for use in Post Notif settings, plus full explanation of each
* FIXED: Fixed issue with translation check generating errors during core update process

= 1.0.7 =
Release Date: March 19, 2016

* FIXED: Fixed translation-related bug and revised translation nag functionality
* NEW: Added minified JavaScript files

= 1.0.6 =
Release Date: February 21, 2016

* NEW: Added Spanish (es_ES) translation by [Enrique Maza](http://sevalepensar.com)
* NEW: Added translation nag functionality based on [Clorith's example](http://www.clorith.net/blog/encourage-community-translations-wordpress-org-plugins/)

= 1.0.5 =
Release Date: December 29, 2015

* NEW: Added configurable AJAX message to inform subscriber that widget is processing
* NEW: Added functionality to options page so admin can define set of categories from which subscriber can choose
* NEW: Added undo (subscriber) delete functionality to "Manage Subscribers" page
* CHANGED: Modified dynamic HTML throughout the code, sanitizing it with "esc()" functions and added missing nonces

= 1.0.4 =
Release Date: July 14, 2015

* CHANGED: Reworked plugin options and custom table update handling
* NEW: Added import subscriber functionality
* NEW: Added export subscriber functionality
* NEW: Added functionality to optionally send email after subscription is confirmed

= 1.0.3 =
Release Date: June 5, 2015

* CHANGED: Made the Post Notif admin menu position configurable to avoid clashes with other plugins that were resulting in "invisible" Post Notif admin menu
* FIXED: Fixed issue with Post Notif custom URLs causing "Page not found" errors for installs using (non-default) permalink settings with no trailing "/" 

= 1.0.2 =
Release Date: April 26, 2015

* CHANGED: Confirmed compatibility with WordPress 4.2 (though Post Notif 1.0.1 is compatible as well)
* FIXED: Fixed issue with View Post Notifs Sent page showing nothing for installs with table prefix other than "wp"
* NEW: Added widget-related messages as configurable settings (Settings >> Post Notif)
* NEW: Added widget input field placeholders as configurable settings (Appearance >> Widgets >> Post Notif)
* NEW: Added "Resend Confirmation" as both a single and bulk action on "Manage Subscribers" (formerly "Delete Subscribers") page
* CHANGED: Updated screenshots to accurately reflect current functionality

= 1.0.1 =
Release Date: April 13, 2015

* CHANGED: Updated installation steps in README.txt for clarity
* CHANGED: Added code to prevent attributes (category, author, and post date/time) and functionality (add comment) from appearing on subscriber preferences pages

= 1.0.0 =
Release Date: April 8, 2015

* Initial release

== Upgrade Notice ==

= 1.1.5 =
Prevented duplicate notifs when a post has multiple categories.  Fixed sub import from file failure on "invalid" cats.  Fixed issue w/email addrs containing "+" breaking personalized URLs.  Added new config settings to widget admin.  Removed Czech and Dutch lang files (moved to language packs).

= 1.1.4 =
Added Czech translation.  Added email dashicon for Post Notif top level menu.

= 1.1.3 =
Added ability to auto-send post notifications when post is published.  Added ability to schedule post notifications for posts scheduled to be published.  Fixed subscriber import category bug.  Fixed subscriber creation failure issue.

= 1.1.2 =
Added Dutch translation.  Updated custom table create statement to adhere to WordPress 4.6 dbDelta() KEY format.

= 1.1.1 =
Fixed pre-PHP 5.5 fatal errors with empty(trim()) calls.  Added corrected German translations.

= 1.1.0 =
NOTE: Post Notif admin menu item sequence has changed.  @@postexcerpt variable has been deprecated (use @@postexcerptmanual)!  Many new features and bug fixes are included in this release.  Full details are in the Changelog (https://wordpress.org/plugins/post-notif/changelog/) under 1.1.0.

= 1.0.9 =
Added German translations.  Added missing 1.0.8 strings to Spanish translation.

= 1.0.8 =
Fixed handling of custom permalinks.  Added new variables for use in Post Notif settings, plus full explanation of each.  Fixed issue with translation check generating errors during core update process.

= 1.0.7 =
Fixed translation-related bug and revised translation nag functionality.  Added minified JavaScript files.

= 1.0.6 =
Added Spanish translation.  Added (dismissible) translation nag screen to solicit translation help from all admins whose site uses a language for which Post Notif does not yet have an official translation.

= 1.0.5 =
Added configurable "processing" AJAX message to widget.  Added ability to define set of categories available to subscribers.  Added undo (subscriber) delete functionality to "Manage Subscribers" page.  Sanitized dynamic HTML via "esc()" functions.  Added missing nonces.

= 1.0.4 =
Fixed issue with plugin options and custom table updates requiring manual plugin deactivate/re-activate

= 1.0.3 =
Fixed issues with "invisible" Post Notif admin menu (due to clashes with other plugins) and with Post Notif custom URLs causing "Page not found" errors for installs using (non-default) permalink settings with no trailing "/" 

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
* [Enrique Maza](http://sevalepensar.com) for the Spanish (es_ES) translation
* David Cox for the Spanish translation revisions
* Ruediger Walter for the German (de_DE and de_DE_formal) translations
* Wolfgang for the German translation revisions
* frankmaNL for the Dutch (nl_NL) translation
* Jirka Licek for the Czech (cs_CZ) translation