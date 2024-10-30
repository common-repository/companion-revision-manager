=== Companion Revision Manager - Revision Control ===
Contributors: Papin
Donate link: https://www.paypal.me/dakel/
Tags: revision, manager, control, post, page, companion, disable, enable, restore, version, backup, speed, clean, database
Requires at least: 3.5.0
Tested up to: 6.5
Stable tag: 1.6.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lightweight plugin that allows full control over post revisions.

== Description ==

= What are revisions? =
"The WordPress revisions system stores a record of each saved draft or published update. The revision system allows you to see what changes were made in each revision by dragging a slider (or using the Next/Previous buttons). The display indicates what has changed in each revision - what was added, what remained unchanged, and what was removed. Lines added or removed are highlighted, and individual character changes get additional highlighting."
- [WordPress](https://wordpress.org/support/article/revisions/)

= Speed up your website =
Revisions can be nice to have, but having a lot of revisions may slow down your website. So disabling them can help keep your website fast.
With Companion Revision Manager you can take back control over revisions! 
This plugin tells you how many revisions are currently stored and allows you to delete them all at once, and if you want to save less revisions or none at all, we give you an option to change the maximum number of disable them all together.

= Plugin features =
1. Delete all existing revisions
1. Turn off revisions completely
1. Set a maximum number of revisions that can be stored


= Want to know more about revisions? =
[Visite the WordPress codex for more info](https://wordpress.org/support/article/revisions/)

== Installation ==

= Manual install =
1. Download Companion Revision Manager.
1. Upload the 'Companion Revision Manager' directory to your '/wp-content/plugins/' directory.
1. Activate Companion Revision Manager from your Plugins page.

= Via WordPress =
1. Search for 'Companion Revision Manager'.
1. Click install.
1. Activate.

= Settings =
Settings can be found trough Tools > Revision Manager

== Frequently Asked Questions ==

= Where can I find the settings? =

You can find the settings under Tools > Revision Manager

= What are revisions? =

The WordPress revisions system stores a record of each saved draft or published update. The revision system allows you to see what changes were made in each revision by dragging a slider (or using the Next/Previous buttons). The display indicates what has changed in each revision - what was added, what remained unchanged, and what was removed. Lines added or removed are highlighted, and individual character changes get additional highlighting.
[Read more here](https://wordpress.org/support/article/revisions/)

== Screenshots ==

1. Setting page

== Changelog ==

= 1.6.2 (November 8, 2019) =
* Tweak: crm_database_creation() query is no longer running on every page load, just on activation or updating of the plugin

= 1.6.1 (Later that same day) =
* Fix where sometimes succes messages wouldn't show up.

= 1.6.0 (March 1, 2019) =
* New: You can disable revisions completely or limit the number of revision stored

= 1.5.2 (February 28, 2019) =
* "Thank you for using Companion Revision Manager" message now only shows on pages of this plugin

= 1.5.1 (February 27, 2019) =
* Security improvements

= 1.5 (February 22, 2019) =
* Security improvements
* Support for WordPress 5.1

= 1.4.2 (February 4, 2019) =
* Fix error: headers already sent

= 1.4.1 (February 2, 2019) =
* Fix error: Undefined index: page

= 1.4 (January 16, 2019) =
* Security update

= 1.3 (December 18, 2018) =
* You no longer need to reload the page to see changed settings.

= 1.2 (February 9, 2018) = 
* Fix: Issue where sometimes plugin wouldn't work.
* Fix: Issue where sometimes plugin would break an entire website.

= 1.1 (December 8, 2017) =
* New: Disable post revisions

= 1.0 (August 8, 2017) =
* Initital release