=== BV Pantheon Migration ===
Name: BV Pantheon Migration
Contributors: BV Pantheon Migration
Tags: pantheon, migration
Donate link: http://blogvault.net
Requires at least: 1.5
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The easiest way to migrate your site to Pantheon

== Description ==

The easiest way to migrate your site to Pantheon. The blogVault Account can be created by visiting the website, https://blogvault.net. This account credentials, can then be used to sign-in to the plugin.

== Changelog ==
= 1.16 =
* Improving the Base64 Decode functionality so that it is extensible for any parameter in the future and backups can be completed for any site
* Separating out callbacks gettablecreate and getrowscount to make the backups more modular
* The plugin will now automatically ping the server once a day. This will ensure that we know if we are not doing the backup of a site where the plugin is activated.
* Use SHA1 for authentication instead of MD5

= 1.15 =
* First release of Pantheon Plugin
