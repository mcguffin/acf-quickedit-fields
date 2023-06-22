=== ACF Quick Edit Fields ===
Contributors: podpirate
Donate link: https://www.msf.org/donate
Tags: acf, quickedit, columns, bulk edit
Requires at least: 4.7
Tested up to: 6.2
Requires PHP: 7.2
Stable tag: 3.2.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable Columns, Filters, Quick Edit and Bulk Edit for ACF Fields in WordPress List Tables

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

 - *Column weight* gives you an option to take control of the column order. The higher the weight, the more to the right the column will be. Present columns will have defaults weights of multiples of 100 starting with zero for the checkbox column. For example to place an image column between the checkbox (column weight = 0) and title (column weight = 100) choose a value between 0 and 100.

 - *Filter* add a filter to the posts users or terms list.

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

Presumbly I won't. However, there are some [plugin filters and actions](https://github.com/mcguffin/acf-quickedit-fields/wiki/Plugin-Filters) that might come in handy, if you decide to write an implementation by yourself.

I even wrote a [tutorial page](https://github.com/mcguffin/acf-quickedit-fields/wiki/Tutorial:-Custom-Field-Integration) on how to write our own field type integration.

= I'm having trouble. =

Please use the issues section in the [GitHub-Repository](https://github.com/mcguffin/acf-quickedit-fields/issues). A well described issue that can be reproduced quickly is more likely to be fixed quickly.

I will most likely not maintain the support forum on wordpress.org. Anyway, other users might have an answer for you, so it's worth a shot.

= I'm having trouble with WooCommerce. =

Welcome to the world of commerce.

If you are located in the EU, you can [hire me](https://flyingletters.net) for the usual market price of an IT guy in central europe.

Outside the EU you can try to fix it yourself or find someone who does. I will likely accept well crafted and tested pull request in the [GitHub-Repository](https://github.com/mcguffin/acf-quickedit-fields).

= I'd like to suggest a feature. Where should I post it? =

Please post an issue in the [GitHub-Repository](https://github.com/mcguffin/acf-quickedit-fields/issues)


== Screenshots ==
1. Field group admin
2. Column view and posts filter
3. QuickEdit
4. Bulk editor with bulk operations

== Upgrade Notice ==

Version 3.2.4 contains a security fix. Registered users who are able to edit posts were able to see arbitrary ACF handled user metadata using an ajax-request. Upgrading is strongly encouraged!

== Changelog ==

= 3.2.7 =
 - Taxonomy column: terms link to filtered view instead of term editor
 - Fix: values not loaded on CPT for users not having `edit_posts` capability
 - Fix: ... a few more PHP 8.2 deprecation warnings
 - Fix: Closing QuickEdit on ESC caused JS error
 - Fix: Remove dumb capability check in taxonomy field column
 - Fix: term filter dropdowns were not selected

= 3.2.7 =
 - Fix: Taxonomy field filter not working
 - Fix: PHP warning on early registered fields

= 3.2.6 =
 - Regression: Single post objects not loading
 - Fix: another PHP 8.2 deprecations

= 3.2.5 =
 - Fix: PHP 8.2 deprecations

= 3.2.4 =
 - Feature: Support post category location rule (not just taxonomy)
 - Fix information disclosure vulnerability: ACF-handled user metadata was disclosed via ajax request to registered users having the edit_posts capability.
 - Fix: fix checkbox and radio taxonomy field
 - Fix: post object value (single) not loaded into ui

= 3.2.3 =
 - Lower memory usage

= 3.2.2 =
 - Feature: Support Taxonomy filter
 - Fix: Multiple selection taxonomy field reporting empty terms

= 3.2.1 =
 - Fix: PHP warning in Admin/CurrentView
 - Fix: select not being selected in QuickEdit

= 3.2.0 =
 - Feature: Column filter
 - Feature: Bulk operations
 - UI Improvement: Display non-multiple values
 - UI Improvement: Show UI labels in column view for True/False Fields
 - UI Improvement: Fix column with in posts list tables
 - Hooks API: introduce `acf_qef_sanitize_value_{$field_type}` filter
 - Hooks API: introduce `acf_qef_wrapper_attributes_{$field_type}` filter
 - Hooks API: introduce `acf_qef_bulk_operations_{$field_type}` filter
 - Hooks API: introduce `acf_qef_bulk_operation_{$field_type}_{$operation}` filter
 - Hooks API: introduce `acf_qef_validate_bulk_operation_value_{$field_type}_{$operation}` filter
 - Bugfix: Radio and checkbox fields did not show custom values
 - Bugfix: sanitize data behind `acf_quick_edit_fields_types`

= 3.1.14 =
 - Fix Field group settings for ACF 6
 - Introduce Legacy mode for ACF < 6.0.0

= 3.1.13 =
 - Fix PHP 8.1 Deprecation notices
 - Fix Taxonomy Field display

= 3.1.12 =
 - Fix: Different Gallery field return formats

= 3.1.11 =
 - Fix: PHP Fatal in posts list

= 3.1.10 =
 - Fix: Nested groups not saving

= 3.1.9 =
 - Fix: Typo which caused a fatal in PHP 8

= 3.1.8 =
 - Improvement: Hide WordPress Taxonomy UI from quick/bulk if ACF Taxonomy is present
 - Fix: PHP warning in taxonomy field
 - Fix: PHP warning in link field

= 3.1.7 =
 - Don't sanitize text fields in ajax output
 - Fix: Syntax error in user field (PHP < 7.3)
 - Fix: PHP warning if post date column is not present

= 3.1.6 =
 - Support ACF RGBA color picker

= 3.1.5 =
 - Added Basic support for User field in quick and bulk

= 3.1.4 =
 - Introduce filter `acf_qef_capability`
 - Fix: User columns not being displayed

= 3.1.3 =
 - Feature: Make user fields sortable (by user ID)
 - Feature: Support Toggle option for checkbox field
 - Fix: PHP Warning __wakeup
 - Fix: Grouped fields not shown

= 3.1.2 =
 - Fix: QuickEdit Taxonomy checkboxes looking weird
 - Fix: BulkEdit grouped fields didn't pass validation
 - Tested with WP 5.6 / jQuery 3

= 3.1.1 =
 - Fix: Group subfields sometimes not displaying

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
