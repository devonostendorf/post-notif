=== Post Notif ===
Contributors: DevonOstendorf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6M98ZTPSAKPGU
Tags: post, notif, notification, email, subscribe
Requires at least: 4.1.1
Tested up to: 4.2
Stable tag: 1.0.4
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
	* Import subscribers (see FAQ for details on how import process works)
	* Manage subscribers:
		* Export
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

= Why aren't all my notifications being sent out? =

If your web host throttles the number of emails you can send during a period of time, this plugin cannot override that limit.

= I have 10,000 subscribers - is this a good plugin for me to use to notify them? =

Post Notif is probably not a good fit for your needs (at least at this point in time).  Look into using a plugin that works in conjunction with a service like AWeber or MailChimp.

= Why isn't Post Notif translated into my language? =

It is because no one who speaks your language has translated this plugin yet.  If you'd like to do so, you'll find the current post-notif.pot file in the post-notif/languages directory.  [Please contact me](https://devonostendorf.com) with any translation files you create - thanks much!

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

= 1.0.4 =
* Reworked plugin options and custom table update handling
* Added import subscriber functionality
* Added export subscriber functionality
* Added functionality to optionally send email after subscription is confirmed

= 1.0.3 =
* Made the Post Notif admin menu position configurable to avoid clashes with other plugins that were resulting in "invisible" Post Notif admin menu
* Fixed issue with Post Notif custom URLs causing "Page not found" errors for installs using (non-default) permalink settings with no trailing "/" 

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