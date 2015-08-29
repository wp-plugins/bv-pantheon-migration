=== Pantheon Migration ===
Contributors: akshatc, blogvault, atjuchgmailcom
Tags: pantheon, migration
Requires at least: 1.5
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The easiest way to migrate your site to Pantheon

== Description ==

Pantheon is a website management platform for WordPress. The Pantheon Migration Plugin allows you to easily migrate your WordPress site to the platform, from any other web host.Just provide your Pantheon SFTP credentials, and the plugin will take care of the rest!

Don’t waste time manually migrating sites, focus on your projects and we’ll make it easy to get up and running on Pantheon.

== Installation ==

= There are two methods = 

1. Upload `bv-pantheon-migration` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Once the plugin is activated, click on Pantheon Migration in the left side navigation

Get the info below from your Pantheon account

Enter the required information

`Email:` (this can be any email address and will receive status updates on your migration)
`Destination URL:` (this will be your pantheon address you are migrating to, example: http://dev-sitename.pantheon.io)
`SFTP Server Address:` (this is where you will copy and paste the host address under Connection Information from your Pantheon account)
`SFTP Username:` (this is where you will copy and paste your SFTP username under Connection Information from your Pantheon account)
`SFTP Password:` (this will be the password you use to login to your Pantheon account)

Click the `Migrate` button and you will be redirected to the migration landing page. The plugin will automatically verify your SFTP credentials and let you know if there are any issues.

After the migration is complete there will be a button you can click to see the results of your migration and automatically redirected to your Pantheon site URL.

== Frequently Asked Questions ==

= 1) What information will the plugin ask for? =

You will have to provide the plugin your email address, destination url, SFTP host name, SFTP username, and SFTP password from your Pantheon account. Please read the [Installation section](https://wordpress.org/plugins/bv-pantheon-migration/installation/) for more information on where to find this.

= 2) Is Multisite supported with this plugin = 

Not yet, Pantheon is currently working on testing the support of Multisite on our platform but it's still too soon. We will update this section when it's available.

= 3) How long does it take to migrate a website? = 

This can range anywhere from 30 minutes to several hours depending on how big the size of the website is.

= 4) Are their any known incompatiblities? = 

Right now WordPress.com does not work migrating a site to Pantheon using this plugin. We are currently testing out hosting companies and reporting any inconsistancies we find.

= 5) What happens if I run into an error after the migration is complete? = 

We are always wanting to assist and help out in any way that we can. If you encounter any type of issue please use the support section of our plugin. [Click here](https://wordpress.org/support/plugin/bv-pantheon-migration/) to file an issue. `This section is monitored daily.`

= 6) Do I need to leave the window open while the migration is processing? = 

No, that's the beauty of this plugin. It runs on a SAAS based technology and a secure web address that runs everything in the background. Once you start the migration you can close the window at any time and come back to it later while it's still running, no need to wait for hours. You will also receive an email once the migration has completed.

= 7) I do not have a Pantheon account, can I still use this plugin? = 

No, but we would love to have you signup for a free account on our website to try it out! [Sign up here](https://pantheon.io/)

== Screenshots ==

1. Adding information to the Pantheon Migration plugin

== Changelog ==
= 1.17 =
* Add support for repair table so that the backup plugin itself can be used to repair tables without needing PHPMyAdmin access
* Making the plugin to be available network wide.
* Adding support for 401 Auth checks on the source or destination

= 1.16 =
* Improving the Base64 Decode functionality so that it is extensible for any parameter in the future and backups can be completed for any site
* Separating out callbacks gettablecreate and getrowscount to make the backups more modular
* The plugin will now automatically ping the server once a day. This will ensure that we know if we are not doing the backup of a site where the plugin is activated.
* Use SHA1 for authentication instead of MD5

= 1.15 =
* First release of Pantheon Plugin