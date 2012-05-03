=== Genesis eNews Extended ===
Contributors: kraftbj
Tags: genesis
Requires at least: 3.0
Tested up to: 3.4-beta1
Stable tag: 0.1.2

Creates a new widget to replace the Genesis eNews Widget to allow easier use of non-Feedburner mailing lists.

== Description ==

Creates a new widget to replace the Genesis eNews Widget to allow easier use of non-Feedburner mailing lists. The widget allows the site administrator to set the form action, e-mail form field and hidden fields to mimic the subscribe form of other mailing list services.

== Installation ==

1. Upload contents of the directory to /wp-content/plugins/ (or use the automatic installer)
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In Appearance->Widgerts, add Genesis eNews Extended widget to any sidebar.
1. Using the mailing list contact form code provided by your vendor, add the form action URL, the form field ID for the e-mail field and any hidden fields (not all services use them) into the widget options.
1. Verify it works!

Note: If you are using the first and last name fields, you will need to edit your styles.css. The fields use the ID subbox1 and subbox2 as some themes provide e-mail icons in the form field (which wouldn't make sense in every box).

== Frequently Asked Questions ==

= What services work with this plugin? =

I've only tested this with MailChimp, but should work with most mailing list services. If you have tested this with other services, please [contact me](http://brandonkraft.com/contact/)

== Changelog ==

= 0.1 =
* Inital release.

= 0.1.1 =
* Adds "Hidden Fields" widget setting to make widget compatible with more mailing services.

= 0.1.2 =
* Modifies class name to work with more StudioPress themes.

= 0.2 =
* Adds first/last name fields (CSS ID subbox1 and subbox2)

== Upgrade Notice ==

= 0.1 =
Initial stable release. Please update from alpha now.

= 0.1.1 =
* Adds "Hidden Fields" widget setting to make widget compatible with more mailing services. Upgrade if you want to use AWeber or other services that require one or more hidden fields.

= 0.1.2 =
* Expands widget's usefulness to more StudioPress themes (Balance, etc).

= 0.2 =
* Adds first and last name fields.