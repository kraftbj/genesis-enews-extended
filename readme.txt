=== Genesis eNews Extended ===
Contributors: kraftbj, coffeaweb
Donate link: http://www.brandonkraft.com/donate/
Tags: genesis, genesiswp, mailchimp, aweber, studiopress, newsletter, subscribe
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.3.0

Creates a new widget to easily add mailing lists integration to a Genesis website. Works with FeedBurner, MailChimp, AWeber, FeedBlitz and more.

== Description ==

Creates a new widget to easily add mailing lists to a Genesis website. Recommended plugin to replace the Genesis eNews Widget being depreciated in Genesis 1.9. The widget allows the site administrator to set the either the Feedburner ID or form action, e-mail form field and hidden fields to mimic the subscribe form of other mailing list services.

== Installation ==

1. Upload contents of the directory to /wp-content/plugins/ (or use the automatic installer)
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In Appearance->Widgets, add Genesis eNews Extended widget to any sidebar.
1. Using the mailing list contact form code provided by your vendor, add the form action URL, the form field ID for the e-mail field and any hidden fields (not all services use them) into the widget options. See some tips for this on the [plugin's install page](http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/install).
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

= 1.3.0 =
* Adds option for HTML5-enhanced forms.
* Adds error checking for including http://feeds.feedburner.com in the Feedburner ID field.
* Minor code improvements

= 1.2.0 =
* Adds text space after form. Perfect for a link to a Privacy Statement!
* Code cleanup. Thanks to [Kim Parsell](http://profiles.wordpress.org/kpdesign/) for reporting.
* Adds labels to form elements to make it compatible with screen readers for the visually impaired.
* Adds Serbian Translation. Props to Diana S.
* Listing of issues [resolved in this version](https://github.com/kraftbj/genesis-enews-extended/issues?milestone=11&state=closed).

= 1.1.2 =
* Improved French translation. Props to [Paul de Wouters](http://paulwp.com/).
* Improved German translation. Props to [David Decker!](http://deckerweb.de/).
* Enables mail service validation of name fields by clearing default text onsubmit.

= 1.1.1 =
* Fixed bug with incorrect escaping function used in first and last name fields.
* Fixed bug with some translations not working.

= 1.1.0 =
* Allows for more HTML tags to be used in Text to Show field. Dropped genesis_allowed_tags in favor or wp_kses_post. Props to [John Levandowski](http://wpselect.com/).
* eNews Extended now compatible with Catalyst theme (Catalyst not officially supported).
* Fuzzy translations added for a wide number of langauges based on Genesis 1.8.2 translations of the original eNews Widget.

= 1.0.X =
* Adds ability to edit "First Name" and "Last Name" displayed on front-end.
* Security update and other code cleanup.
* Version numbering now using semver.org rationale.

= 0.2.0 =
* Various code enhancements to improve performance and adhere better to WP standards. Props to [Gary Jones](http://garyjones.co.uk/)
* Adds Spanish translation. Props to [Ryan Sullivan](http://www.wpsitecare.com/)
* Adds Italian translation. Props to [Marco Galasso](http://neatandplain.com/)
* Adds Slovak translation. Props to [Branco Radenovich](http://webhostinggeeks.com/user-reviews/)

= 0.1.6 =
* Makes available first and last name fields.

= 0.1.5 =
* Adds option to open confirmation window in same tab.

= 0.1.4 =
* Adds l18n support for other languages, props to [David Decker!](http://deckerweb.de/)
* Adds German translation , props to [David Decker!](http://deckerweb.de/)

= 0.1.3 =
* Adds Feedburner support in anticipation of Genesis 1.9
* Security and translation updates

= 0.1.2 =
* Modifies class name to work with more StudioPress themes.

= 0.1.1 =
* Adds "Hidden Fields" widget setting to make widget compatible with more mailing services.

= 0.1 =
* Inital release.

== Thanks ==

A special thanks to all who have contributed to Genesis eNews Extended.

= Financial Donations =
* Joe Taylor
* Dorian Speed
* Paul Meyers
* Joel Runyon
* [Maybe You?](http://www.brandonkraft.com/donate/)

= Code Contributions =
* Gary Jones (many, many times over)
* John Levandowski
* David Decker
* Kim Parsell
* Erick Nelson

= Translations =
* David Decker (German)
* Branco Radenovich (Slovak)
* Marco Galasso (Italian)
* Ryan Sullivan (Spanish)
* Paul de Wouters (French)
* Diane S (Serbian)

= StudioPress =
* Special thanks to Brian Gardner and the team at StudioPress who wrote the original code that provided the foundation for the plugin.

If you're not listed and think you should be, please drop me a note. Any omission is, in no way, intentional.

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

= 0.2.0 =
Code enhancements and adds Spanish, Italian, and Slovak translations.

= 1.0.0 =
Enable changes to first name and last name text displayed on site.

= 1.0.1 =
Verify plugin is active after update.

= 1.0.2 =
Verify plugin is active after update.

= 1.1.0 =
Allows images in the "Text to Show" area.

= 1.1.1 =
Fixed bug in onclick clearing of first and last name fields and translations that were not loading.

= 1.1.2 =
Fixed bug that prevents a mail service's validation to work correctly. Improved translations.

= 1.2.0 =
* Adds text space after form. Perfect for a link to a Privacy Statement! Add screen reader compatability.

= 1.3.0 =
* Enables Genesis 2.0 HTML5 support and other improvements.