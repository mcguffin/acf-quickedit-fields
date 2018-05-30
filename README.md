ACF QuickEdit Fields
====================

WordPress plugin which extends the functionality of the Advanced Custom Fields Plugin (Pro, Version 5+).  
http://www.advancedcustomfields.com/pro/

Features
--------
 - Show ACF field values in List views  
   [Supported ACF Fields](https://github.com/mcguffin/acf-quick-edit-fields/wiki/Supported-ACF-Fields).
 - Supports Post, Term and User list tables
 - Scalar Columns (Like Text, Number, ...) can be made sortable
 - Edit ACF Field values in Quick edit and Bulk edit

Compatibility
-------------
 - Requires ACF Free or Pro 5.6+ (ACF Free 4.x won’t work). ACF Free 5.6+ is available via early access. Follow [this guide](https://www.advancedcustomfields.com/resources/upgrade-guide-version-5/)
 - Requires at least PHP 5.3+
 - Requires at least WP 4.7+
 - Tested with WordPress up to 4.9.4

Docs
----

A note on sortable Columns: Sorting works over a meta query. As a result, items
with no ACF-Value will disappear from the list. To adjust or suppress sortability
for a specific field use `acf_quick_edit_sortable_column_{$field_name}` filter.


Installation
------------

 - Download `acf-quick-edit-fields.zip` from the [releases](../../releases/latest) tab.
 - Install it like a regular WordPress plugin.
 - As long as the plugin is active it will check for Updates here on GitHub.


Developing
----------

Clone this repository into the `wp-content/plugins` directory.

    git clone git@github.com:mcguffin/acf-quick-edit-fields.git

Install npm and run gulp

    cd acf-quick-edit-fields
    npm install
    gulp


ToDo / Known Issues:
-----
 - [ ] Bug in Sortable Columns: Rows are not shown if the ACF value is not set (aka. postmeta not present).
 - [ ] Bug: Password field always saves empty value
 - [ ] Radio/Checkbox with Other: other value(s) not shown after save (need to reload)
 - [ ] Support Taxonomy QE / BE
