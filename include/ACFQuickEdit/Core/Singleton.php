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


abstract class Singleton {

	/**
	 * Array containing derived class instances
	 */
	private static $instances = [];

	/**
	 * Getting a singleton.
	 *
	 * @return object single instance of Core
	 */
	public static function instance() {

		$class = get_called_class();

		if ( ! isset( self::$instances[ $class ] ) ) {
			$args = func_get_args();
			self::$instances[ $class ] = new $class( ...$args );
		}

		return self::$instances[ $class ];
	}

	/**
	 *	Prevent Instantinating
	 */
	private function __clone() { }

	/**
	 *	Protected constructor
	 */
	protected function __construct() {
	}

	/**
	 *	Array filter: Append instance to array.
	 *	Use it for filters
	 */
	public function append_this( $arr ) {
		$arr[] = $this;
		return $arr;
	}
}
