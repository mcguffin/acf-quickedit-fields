ACF QuickEdit Fields
====================

WordPress plugin which extends the functionality of the Advanced Custom Fields Plugin ([Pro](http://www.advancedcustomfields.com/pro/) and [Free 5+](https://www.advancedcustomfields.com/resources/upgrade-guide-version-5/)).

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
 - Tested with WordPress up to 4.9.6


Installation
------------

### Production (Stand-Alone)
 - Head over to [releases](../../releases)
 - Download 'acf-quick-edit-fields.zip'
 - Upload and activate it like any other WordPress plugin
 - AutoUpdate will run as long as the plugin is active

### Production (using Github Updater – recommended for Multisite)
 - Install [Andy Fragen's GitHub Updater](https://github.com/afragen/github-updater) first.
 - In WP Admin go to Settings / GitHub Updater / Install Plugin. Enter `mcguffin/acf-quick-edit-fields` as a Plugin-URI.

### Development
 - cd into your plugin directory
 - $ `git clone git@github.com:mcguffin/acf-quick-edit-fields.git`
 - $ `cd acf-quick-edit-fields`
 - $ `npm install`
 - $ `gulp`


Documentation
-------------

 - [Quick Start](https://github.com/mcguffin/acf-quick-edit-fields/wiki)
 - [Filters and Actions](https://github.com/mcguffin/acf-quick-edit-fields/wiki/Plugin-Filters)
 - [Known Issues](https://github.com/mcguffin/acf-quick-edit-fields/wiki/Known-Issues) that can't be fixed
 - [How to Support more Fields](https://github.com/mcguffin/acf-quick-edit-fields/wiki/Tutorial:-Custom-Field-Integration)
