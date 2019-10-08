ACF QuickEdit Fields
====================

This is the official github repository of the [ACF QuickEdit Fields](https://wordpress.org/plugins/acf-quickedit-fields) plugin.

WordPress plugin adding Quick Edit functionality to Advanced Custom Fields Plugin ([Pro](http://www.advancedcustomfields.com/pro/) and [Free 5+](https://wordpress.org/plugins/advanced-custom-fields/).

Features
--------
 - Show ACF field values in List views  
   [Supported ACF Fields](https://github.com/mcguffin/acf-quickedit-fields/wiki/Feature-Support-Matrix#supported-acf-fields).
 - Supports Post, Term and User list tables
 - Scalar Columns (Like Text, Number, ...) can be made sortable
 - Edit ACF Field values in Quick edit and Bulk edit

Compatibility
-------------
 - Requires WordPress 4.7+
 - Requires ACF 5.7+ (Free and Pro)
 - Requires at least PHP 5.6+


Installation
------------

#### In WP Admin
Just follow the [Automatic Plugin Installation](https://wordpress.org/support/article/managing-plugins/#automatic-plugin-installation) procedere.

#### WP-CLI
```shell
wp plugin install --activate acf-quickedit-fields
```

#### Using Composer
```shell
composer require mcguffin/acf-quickedit-fields
```

### Development
```shell
git clone git@github.com:mcguffin/acf-quickedit-fields.git
cd acf-quickedit-fields
npm install
npm run dev
```

## Migrating from Github to WPORG
Please note that the plugin slug has chaged from `acf-quick-edit-fields` to `acf-quickedit-fields` (missing a dash in the middle). You can safely delete the old version 2.x and re-install the plugin on plugins page.

Development
-----------
npm scripts:
 - `npm run dev`: Watch css and js soure dirs
 - `npm run test`: load some test fields
 - `npm run dev-test`: load some test fields and watch css and js soure dirs
 - `npm run dashicons`: Generate dashicons scss variables from source
 - `npm run i18n`: generate `.pot` file
 - `npm run rollback`: remove last commit (local and remote  – use with caution!)


Documentation
-------------

 - [Quick Start](https://github.com/mcguffin/acf-quickedit-fields/wiki)
 - [Filters and Actions](https://github.com/mcguffin/acf-quickedit-fields/wiki/Plugin-Filters)
 - [Known Issues](https://github.com/mcguffin/acf-quickedit-fields/wiki/Known-Issues) that can't be fixed
 - [How to Support more Field types](https://github.com/mcguffin/acf-quickedit-fields/wiki/Tutorial:-Custom-Field-Integration)
