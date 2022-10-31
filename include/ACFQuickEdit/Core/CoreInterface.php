<?php
/**
 *	@package ACFQuickEdit\Core
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ACFQuickEdit\Core;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


interface CoreInterface {

	/**
	 *	@return string current Plugin version
	 */
	public function version();

	/**
	 *	Return locations where to look for assets and map them to URLs.
	 *
	 *	@return array array(
	 * 		'absolute_path'	=> 'absolute_url',
	 * )
	 */
	public function get_asset_roots();

}
