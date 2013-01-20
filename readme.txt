=== Genesis eNews Extended ===
Contributors: kraftbj, coffeaweb
Donate link: http://www.brandonkraft.com/donate/
Tags: genesis
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.1.6.1

Creates a new widget to easily add mailing lists integration to a Genesis website. Works with FeedBurner, MailChimp, AWeber, FeedBlitz and more.

== Description ==

Creates a new widget to easily add mailing lists to a Genesis website. Recommended plugin to replace the Genesis eNews Widget being depreciated in Genesis 1.9. The widget allows the site administrator to set the either the Feedburner ID or form action, e-mail form field and hidden fields to mimic the subscribe form of other mailing list services.

== Installation ==

1. Upload contents of the directory to /wp-content/plugins/ (or use the automatic installer)
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In Appearance->Widgets, add Genesis eNews Extended widget to any sidebar.
1. Using the mailing list contact form code provided by your vendor, add the form action URL, the form field ID for the e-mail field and any hidden fields (not all services use them) into the widget options.
1. If using name fields, read the [plugin's tutorial website](http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/tutorials).
1. Verify it works!

== Frequently Asked Questions ==

= What services work with this plugin? =

Feedburner, MailChimp, Aweber, FeedBlitz and Constant Contact are confirmed to work, but it should work with almost all services. If you have tested this with other services, please [contact me](http://brandonkraft.com/contact/)

= The "Opens in Same Tab" option doesn't work with FeedBurner. What's wrong? =

That is intentional. You don't want to knock people off your site and leave them on FeedBurner's.

= I'm using Minimum 2.0. Where's the submit button? =

That is a feature of the theme. If you want the button back, remove ".enews #subbutton," on line 1236 of style.css.

= The first and last name fields look funky. =

Read more on the [plugin's tutorial website](http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/tutorials).

= I need help! Where I can get it? =

"Official" tutorials will be maintained on the [plugin's website](http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/).

Questions can be asked at the [WordPress.org Support Forum](http://wordpress.org/support/plugin/genesis-enews-extended) for this plugin.

== Screenshots ==
1. Example of the plugin in use on a site running Streamline 2.0.
2. Widget setting screen.

== Changelog ==

= 0.1.6.1 =
* Updates German translation (props to David Decker)
* Adds Spanish translation (props to Ryan Sullivan)
* Adds Italian translation (props to Marco Galasso)

= 0.1.6 =
* Makes available first and last name fields.

= 0.1.5 =
* Adds option to open confirmation window in same tab.

= 0.1.4 =
* Adds l18n support for other languages (thanks David Decker!)
* Adds German translation (thanks David Decker!)

= 0.1.3 =
* Adds Feedburner support in anticipation of Genesis 1.9
* Security and translation updates

= 0.1.2 =
* Modifies class name to work with more StudioPress themes.

= 0.1.1 =
* Adds "Hidden Fields" widget setting to make widget compatible with more mailing services.

= 0.1 =
* Inital release.

== Upgrade Notice ==

= 0.1 =
Initial stable release. Please update from alpha now.

= 0.1.1 =
Adds "Hidden Fields" widget setting to make widget compatible with more mailing services. Upgrade if you want to use AWeber or other services that require one or more hidden fields.

= 0.1.2 =
Expands widget's usefulness to more StudioPress themes (Balance, etc).

= 0.1.3 =
Security update and adds Feedburner support natively. 

= 0.1.4 =
Adds translation support and adds German translation.

= 0.1.5 =
Adds option to open confirmation screen in same tab.

= 0.1.6 =
Adds first and last name fields. Check instructions before usage.

= 0.1.6.1 =
Adds Spanish and updates German translation in addition to the first and last name fields from 0.1.6.
