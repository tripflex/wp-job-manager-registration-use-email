=== WP Job Manager Registration Use Email ===
Contributors: tripflex, fris
Donate link: http://gittip.com/tripflex
Tags: registration, email, user, jobify, job manager
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 1.1.2

Use email address as username when a new user registers with WP Job Manager. Compatible with Jobify.

== Description ==

This plugin will use the email address as the username whenever someone registers through WP Job Manager.  This will also work with any other plugins or themes that use the `job_manager_create_account_data` filter.

The Jobify theme has been confirmed and tested to work correctly with this plugin.

* note: * using email as username is disabled by default, you must enable it under the WP Job Manager settings page.  There is a link to the page when you activate this plugin.

= Features =
* Use email as username
* Set display_name and nickname as username that was going to be used
* Replace "Username" with "Username or Email" on login forms

= Requires =
* [WP Job Manager by Mike Jolley](http://mikejolley.com/projects/wp-job-manager/)

= Recommended =
* [Jobify Wordpress Theme by Astoundify](http://themeforest.net/item/jobify-job-board-wordpress-theme/5247604?ref=tripflex)

= Documentation =

Documentation will be maintained on the [GitHub Wiki here](https://github.com/tripflex/wp-job-manager-registration-use-email/wiki).

= Contributing and reporting bugs =

You can contribute code and localizations to this plugin via GitHub: [https://github.com/tripflex/wp-job-manager-registration-use-email](https://github.com/tripflex/wp-job-manager-registration-use-email)

= Support =

If you spot a bug, you can of course log it on [Github](https://github.com/tripflex/wp-job-manager-registration-use-email/issues)

Or contact me at myles@smyl.es

== Screenshots ==

1. Enable from WP Job Manager settings "Job Submission" tab

== Installation ==

Install directly from Wordpress installation.  Go to Plugins and then search for "WP Job Manager Registration Use Email"

* Manual Installation *
Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

* Usage *
1. Install and Activate
2. Go to "Job Listings > Settings > Job Submission" and select checkbox.

== Frequently Asked Questions ==

== Changelog ==
= 1.1.2 =
*May 5, 2014*
Fix static notice issue, change add_option to update_option

= 1.1.1 =
* May 5, 2014
BUG FIX: User role dropdown being removed on settings page

= 1.1.0 =
* May 3, 2014
New Feature: Change "Username" to "Username or Email" on login forms
Update to semantic versioning
Added GitHub and Translation links to plugin page

= 1.0 =
* April 19, 2014
First Release
