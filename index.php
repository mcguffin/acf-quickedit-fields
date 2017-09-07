<?php

/*
Plugin Name: ACF QuickEdit Fields
Plugin URI: https://github.com/mcguffin/acf-quick-edit-fields
Description: Show Advanced Custom Fields in post list table. Edit field values in Quick Edit and / or Bulk edit.
Author: Jörn Lund
Version: 2.0.7
Author URI: https://github.com/mcguffin/
License: GPL3
*/

namespace ACFQuickEdit;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

define( 'ACFQUICKEDIT_FILE', __FILE__ );
define( 'ACFQUICKEDIT_DIRECTORY', plugin_dir_path(__FILE__) );

require_once ACFQUICKEDIT_DIRECTORY . 'include/vendor/autoload.php';

if ( is_admin() ) {

	Core\Core::instance();

	Admin\Admin::instance();

}
