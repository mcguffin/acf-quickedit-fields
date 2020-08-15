=== ACF Quick Edit Fields ===
Contributors: podpirate
Donate link: https://www.msf.org/donate
Tags: acf, quickedit, columns, bulk edit
Requires at least: 4.7
Tested up to: 5.5
Requires PHP: 5.6
Stable tag: 3.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable Columns, Quick Edit and Bulk Edit for ACF Fields in WordPress List Tables

== Description ==

WordPress plugin which adds Quick Edit functionality to Advanced Custom Fields Plugin ([Pro](http://www.advancedcustomfields.com/pro/) and [Free 5+](https://wordpress.org/plugins/advanced-custom-fields/).

= Features =
 - Show ACF field values in List views
   [Supported ACF Fields](https://github.com/mcguffin/acf-quickedit-fields/wiki/Feature-Support-Matrix#supported-acf-fields).
 - Supports Post, Term and User list tables
 - Scalar Columns (Like Text, Number, ...) can be made sortable
 - Edit ACF Field values in Quick edit and Bulk edit

= Usage =

#### In the Fieldgroup editor:

**Column View:**

 - *Show Column* will sho a column in the WP List Table.

 - *Sortable Column* will make the column sortable. This only works with primitive Field types like Text, Number, Range and so on.

 - *Column weight* gives you an option to take control of the column order. The higher the weight, the more to the right the column will be. Present columns will have defaults weights of multiples of 100 starting with zero for the checkbox column. For example to place an image column between the checkbox (column weight = 0) and title (clumn weight = 100) choose a value between 0 and 100.

**Editing**

Toggle *QuickEdit* and *Bulk Edit* to enable Editing in the List table view.

**Location Rules**

The plugin follows ACFs location rule logic as far as possible. If you have a field group that is only applies to posts in a specific category or with a certain post starus, then columns and quick edit will only show up, if you have filtered the posts by that category ar post status.

[Read more on the WikiPage](https://github.com/mcguffin/acf-quickedit-fields/wiki/Feature-Support-Matrix#acf-location-rules)

**Conditional Logic**

Conditional logic is not supported.

= Development =
Please head over to the source code [on Github](https://github.com/mcguffin/acf-quickedit-fields).

== Installation ==

Just follow the [Automatic Plugin Installation](https://wordpress.org/support/article/managing-plugins/#automatic-plugin-installation) procedere.

== Frequently asked questions ==

= When will you support the Non-ACF Field XYZ? =

Presumbly I won't – I'm sorry. However, there are some [plugin filters and actions](https://github.com/mcguffin/acf-quickedit-fields/wiki/Plugin-Filters) that might come in handy, if you decide to write an implementation by yourself.

I even wrote a [tutorial page](https://github.com/mcguffin/acf-quickedit-fields/wiki/Tutorial:-Custom-Field-Integration) on how to write our own field type integration.

= I found a bug. Where should I post it? =

Please use the issues section in the [GitHub-Repository](https://github.com/mcguffin/acf-quickedit-fields/issues).

I will most likely not maintain the forum support forum on wordpress.org. Anyway, other users might have an answer for you, so it's worth a shot.

= I'd like to suggest a feature. Where should I post it? =

Please post an issue in the [GitHub-Repository](https://github.com/mcguffin/acf-quickedit-fields/issues)


== Screenshots ==
1. Field Group Admin
2. Some Columns and QuickEdit
3. Bulk Editor

== Upgrade Notice ==

On the whole upgrading is always a good idea.

== Changelog ==

= 3.1.0 =
 - Feature: Ajax load terms in Taxonomy Field
 - Fix: PHP Warning on Upgrade
 - Fix: avoid infinite loops when something hooks into acf/save_post and saves the post
 - Dependencies: Remove legacy PHP support

= 3.0.8 =
 - Fix: PHP Warning on Upgrade
 - Security hardening

= 3.0.7 =
 - Fix: Location rules at taxonomy edit screen

= 3.0.6 =
 - Fix: Location rules too restrictive

= 3.0.5 =
 - Feature: Support Post Object Bulk and Quick Edit (thanks to [@m0n0mind](https://github.com/m0n0mind))
 - Fix: Column for Post object with multiple values not displaying
 - Fix: ACF Field Location rules applied incorrectly
 - Fix: JS Error with link fields

= 3.0.4 =
 - Fix: Quick/Bulk Edit not showing when list table filter is used

= 3.0.3 =
 - Fix: QuickEdit sometimes caused a JS Error

= 3.0.2 =
 - Fix: Broken 3rd party field integration

= 3.0.1 =
 - Fix: Some Fields not saved
 - Fix: Checkbox not displayed properly
 - Fix: file/image field without value not displayed properly in editor
 - Fix: JS Error in post editor
 - Fix: Bulk edit overriding values
 - User List: only enqueue necessary assets

= 3.0.0 =
 - Release at wordpress.org
 - Feature: Added support for Link and User Field
 - UI: Improvements in Column view,
 - Fix: column issue with [Polylang](https://wordpress.org/plugins/polylang) and [Wordpress SEO](http://wordpress.org/plugins/wordpress-seo)
 - Fix: Datepicker saved wrong value
 - Fix: Checkbox in group didn't save
