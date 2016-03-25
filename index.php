<?php

/*
Plugin Name: ACF QuickEdit Fields
Plugin URI: http://wordpress.org/
Description: Show Advanced Custom Fields in post list table. Edit field values in Quick Edit and / or Bulk edit.
Author: Jörn Lund
Version: 1.0.1
Author URI: 
License: GPL3
*/


if ( ! defined( 'ABSPATH' ) )
	die('Nope.');


if ( is_admin() ) {
	require_once __DIR__.'/include/class-acftoquickedit.php';
}
