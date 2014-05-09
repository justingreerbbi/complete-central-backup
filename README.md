# Complete Central Backup

<hr>

## Description

<br>

Lets face it! 
Losing your data is always breath taking. Its like losing your wallet.

Complete Central Backup allows for backups to be made on the fly. There are some backup plugins out right now but sometimes they just get too clunky and are hard to follow. We designed this plugin for the normal user with a clean and simple dashboard to go along with WordPress 3.8.

NOTE: If you are upgrading from 1.0.5 to 2.x.x, please download you backups manually before upgrading. BACKUPS WILL BE LOST IF YOU SIMPLY UPGRADE WITHOUT DOING SO.

Included Features:

* ONE CLICK to create a backup of your database or file system.
* Schedule weekly, monthly or daily backups of your database or entire WordPress install.
* Enable or disable backup notifications when a scheduled backup is ran.
* Ability to upgrade the plugin and or WordPress without losing any backup data.
* Download your backups for safe keeping.
* New component structure to support future features.

All support matters are recommended to be place at our <a href="http://blackbirdi.com/2012/10/25/complete-central-backup-wordpress-plugin/">blog</a>. This way you will get a response sooner as this plugin has limited support.

## Installation

1. Upload the `complete-central-backup` directory to the `/wp-content/plugins/` directory or use the built in plugin installer.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You can find the plugin dashboard by visiting "backup" tab

## Changelog

= 2.1.0 =
* Imporoved SQL writer using buffer.
* Added progressive backup check before actual backup is performed.
* Added functionality to check for PHP MEMORY_LIMIT. Recommended MEMORY_LIMIT is now 64M
* Fixed remove functionality. Was not deleting actaul backup file.

= 2.0.26 =
* Fixed extra slash when downloading a backup. This was causing strict servers to prsent a 404.

= 2.0.25 =
* Error notice added if ZipArchive is not installed
* Minor UI changes

= 2.0.24 =
* Moved error reporting to sidebar and removed old option from "General Options"
* Minor UI enhancements

= 2.0.23 =
* Finished error component foundation and it now allows for errors to be reported to our analytics server
* Minor UI changes

= 2.0.22 =
* Added error component to plugin for reporting fatal errors to help development
* Add some minor upgrade notices to remind how important it is to make backups of backup before upgrading versions

= 2.0.21 =
* Removed AJAX backup request and moved to a more traditional style
* Added JS error analytics option to help track and monitor errors that may be happening that we are not able to track otherwise.
* Added new status "In Progress" for larger backups.
* Added memory management snippet to try to bypass poor hosting provider settings.
* Added HTML5 attributes to the request form.
* Added new setting "Error Analytics". This allows the plugin to send async calls to our development server for better support.
* Fixed double slash in files being included. This may have been causing errors on strict servers.

= 2.0.2 =
* UI cleanup
* Moved some internal code around for future additions

= 2.0.1 =
* Endless loading issue patched
* Minor change to e-mail notification
* Added sidebar in page layout for contacting support
* Added browser warning (plugin uses heave JS)
* Fixed notification warning causing some bad juju with default admin email
* Fixed other none bugs

= 2.0.0 =
* Project completely rebuilt from the ground up.
* Scheduling feature rebuilt to support daily, weekly, monthly backups using wp_cron
* Restore feature removed for the time being. 
* Security patches for with data vulnerability.
* Added all new backup features.
* Move where backups are stored. This prevents backups from being removed when updating plugin or WordPress.
* Started developer platform for the plugin to allow for extensions to be added.

= 1.0.5 =
* This update was a security update which prevents direct access to raw backup data.

= 1.0.4 =
* Added Support for sites that WordPress is in a sub directory.
* Added features to make future compatibility automatic
* Change bloginfo("admin_email") in wp_mail() to fix bug in mailing system - Notifications now work via email when the scheduled backup is turned off and or a backup has been complete.
* Started integration of encryption for database's with sensitive information.

= 1.0.3 =
* Styled logout button
* Cleaned un-needed files for class directory
* Added Weekly Scheduling Feature
* Patched naming for backup listings

= 1.0.2 =
* Init Public build