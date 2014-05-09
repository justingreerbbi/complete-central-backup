=== Complete Central Backup ===
Contributors: Justin Greer
Donate link: http://blackbirdi.com/donate
Tags: backup,restore,database backup,help
Requires at least: 3.4.2
Tested up to: 3.9
Stable tag:t 2.1.0
License: GPLv2 or laer
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create a instant live backup of your whole site with just a click.

== Description ==

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

== Installation ==

1. Upload the `complete-central-backup` directory to the `/wp-content/plugins/` directory or use the built in plugin installer.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You can find the plugin dashboard by visiting "backup" tab

== Frequently Asked Questions ==

= What happened to the restore feature of the plugin? =
We have removed the restore feature for the time being. If you find yourself in need of a restore, you can use phpMyAdmin to do so. You are welcome to visit our blog and ask for help as well. For a small fee, an expert in WordPress recovery can be assigned to help.

== Where are my backup stored ? ==
Backups are store in a directory labeled "wpbu-backup" in the main "upload" directory of WordPress. This allows for backups to remain in tacked during plugin and CMS upgrades.v

== Backup is (x)MB and download is only 1KB ==
This is most likely due to server permission settings. We are currently working on a clean way to override and road blocks due to poor server permissions. Until then you can manually download any backups using a FTP program of your choice. Please refer to the FAQ above if you do not know where the backups are stored.

= "I lost everything!!! It is your fault!" =

Although we are happy to help, We (Blackbird Interactive and any developers) take no responsibility for actions done by the user using this plugin. Extreme caution should be taken whenever dealing with any database or files files.

== Upgrade Notice ==
<hr>
<i style="color:red;">Take a minute to upgrade, here's why:</i>
<li> Improved SQL writer. </li>
<li> Added progressive backup checks before doing backup. </li>
<li> Fixed remove feature. </li>

== Screenshots ==


== Changelog ==

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