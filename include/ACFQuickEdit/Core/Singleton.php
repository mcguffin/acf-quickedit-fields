<?php

namespace ACFQuickEdit\Core;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class Singleton {

	/**
	 * Array containing derived class instances
	 */
	private static $instances = array();

	/**
	 * Getting a singleton.
	 *
	 * @return object single instance of Core
	 */
	public static function instance() {
		$class = get_called_class();
		if ( ! isset( self::$instances[ $class ] ) )
			self::$instances[ $class ] = new $class();
		return self::$instances[ $class ];
	}

	/**
	 *	Prevent Instantinating
	 */
	private function __clone() { }
	private function __wakeup() { }

	/**
	 *	Protected constructor
	 */
	protected function __construct() {
	}
}
