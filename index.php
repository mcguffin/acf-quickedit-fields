<?php

/*
Plugin Name: ACF QuickEdit Fields
Plugin URI: https://github.com/mcguffin/acf-quickedit-fields
Description: Show Advanced Custom Fields in post list table. Edit field values in Quick Edit and / or Bulk edit.
Author: Jörn Lund
Version: 3.2.8
Author URI: https://github.com/mcguffin
License: GPL3
Requires WP: 4.8
Requires PHP: 5.6
Text Domain: acf-quickedit-fields
Domain Path: /languages/
*/

/*  Copyright 2019 Jörn Lund

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
Plugin was generated with Jörn Lund's WP Skelton
https://github.com/mcguffin/wp-skeleton
*/


namespace ACFQuickEdit;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


if ( is_admin() || wp_doing_ajax() ) {

	require_once __DIR__ . DIRECTORY_SEPARATOR . 'include/autoload.php';

	Core\Core::instance( __FILE__ );

	Admin\Admin::instance();
}
