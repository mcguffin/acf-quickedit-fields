<?php

/*
Plugin Name: ACF QuickEdit Fields
Plugin URI: https://github.com/mcguffin/acf-quick-edit-fields
Description: Show Advanced Custom Fields in post list table. Edit field values in Quick Edit and / or Bulk edit.
Author: Jörn Lund
Version: 2.1.7
Github Repository: mcguffin/acf-quick-edit-fields
Author URI: https://github.com/mcguffin/
License: GPL3
Text Domain: acf-quick-edit-fields
Domain Path: /languages/
*/

namespace ACFQuickEdit;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

define( 'ACFQUICKEDIT_FILE', __FILE__ );
define( 'ACFQUICKEDIT_DIRECTORY', plugin_dir_path(__FILE__) );

require_once ACFQUICKEDIT_DIRECTORY . 'include/vendor/autoload.php';

if ( is_admin() ) {

	// don't WP-Update actual repos!
	if ( ! file_exists( ACFQUICKEDIT_DIRECTORY . '/.git/' ) ) {
		AutoUpdate\AutoUpdateGithub::instance();
	}

	Core\Core::instance();

	Admin\Admin::instance();

}
