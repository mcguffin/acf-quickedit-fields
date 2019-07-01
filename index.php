<?php

/*
Plugin Name: ACF QuickEdit Fields
Plugin URI: https://github.com/mcguffin/acf-quick-edit-fields
Description: Show Advanced Custom Fields in post list table. Edit field values in Quick Edit and / or Bulk edit.
Author: Jörn Lund
Version: 2.4.18
Github Repository: mcguffin/acf-quick-edit-fields
GitHub Plugin URI: mcguffin/acf-quick-edit-fields
Release Asset: false
Author URI: https://github.com/mcguffin/
License: GPL3
Text Domain: acf-quick-edit-fields
Domain Path: /languages/
*/

namespace ACFQuickEdit;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

define( 'ACF_QUICK_EDIT_FILE', __FILE__ );
define( 'ACF_QUICK_EDIT_DIRECTORY', plugin_dir_path(__FILE__) );

require_once ACF_QUICK_EDIT_DIRECTORY . 'include/autoload.php';

if ( version_compare( phpversion(), '5.6', '<' ) ) {
	require_once ACF_QUICK_EDIT_DIRECTORY . 'include/legacy-php.php';
}

if ( is_admin() ) {

	// don't WP-Update actual repos!
	if ( ! file_exists( ACF_QUICK_EDIT_DIRECTORY . '/.git/' ) ) {

		// Not a git. Check if https://github.com/afragen/github-updater is active
		$active_plugins = get_option('active_plugins');
		if ( $sitewide_plugins = get_site_option('active_sitewide_plugins') ) {
			$active_plugins = array_merge( $active_plugins, array_keys( $sitewide_plugins ) );
		}

		if ( ! in_array( 'github-updater/github-updater.php', $active_plugins ) ) {
			// not github updater. Init our own...
			AutoUpdate\AutoUpdateGithub::instance()->init( __FILE__ );
		}
	}

	Core\Core::instance();

	Admin\Admin::instance();

}
