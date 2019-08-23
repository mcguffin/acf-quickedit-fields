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


interface ComponentInterface {

	/**
	 *	Called on Plugin activation
	 *
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	public function activate();

	/**
	 *	Called on Plugin upgrade
	 *	@param	string	$new_version
	 *	@param	string	$old_version
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	public function upgrade( $new_version, $old_version );

	/**
	 *	Called on Plugin deactivation
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	public function deactivate();

	/**
	 *	Called on Plugin uninstall
	 *	@param	string	$new_version
	 *	@param	string	$old_version
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	public static function uninstall();

}
