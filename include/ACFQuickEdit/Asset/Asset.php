<?php
/**
 *	@package ACFQuickEdit\Asset
 *	@version 1.0.1
 *	2018-09-22
 */

namespace ACFQuickEdit\Asset;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use ACFQuickEdit\Core;

/**
 * Base Class
 */
class Asset {

	/**
	 *	@var string relative asset path
	 */
	private $asset;

	/**
	 *	@var string Absolute asset path
	 */
	private $path;

	/**
	 *	@var string Absolute asset url
	 */
	private $url;

	/**
	 *	@var array Dependencies
	 */
	private $deps = [];

	/**
	 *	@var array|boolean Localization
	 */
	private $in_footer = true;

	/**
	 *	@var string css|js
	 */
	private $type;

	/**
	 *	@var string
	 */
	private $handle;

	/**
	 *	@var string
	 */
	private $varname;

	/**
	 *	@var boolean
	 */
	private $localized;

	/**
	 *	@var array|boolean Localization
	 */
	private $l10n = false;

	/**
	 *	@var string css|js
	 */
	private $registered = false;

	/**
	 *	@var Core\Core
	 */
	private $core = null;

	/**
	 *	@param string Asset path relative to plugin directory
	 */
	static function get( $asset ) {
		return new self($asset);
	}

	/**
	 *	@param string Asset path relative to plugin directory
	 */
	public function __construct( $asset ) {

		$this->core = Core\Core::instance();

		$this->asset = preg_replace( '/^(\/+)/', '', $asset ); // unleadingslashit
		$this->type = strtolower( pathinfo( $this->asset, PATHINFO_EXTENSION ));
		$this->handle = $this->generate_handle();
		$this->varname = str_replace( '-', '_', $this->handle );
		$this->in_footer = $this->type === 'js';
		$this->locate();
	}

	/**
	 *	Generate script handle.
	 */
	private function generate_handle() {
		$asset = preg_replace( '/^(js|css)\//', '', $this->asset );
		$pi = pathinfo( $asset );
		$handle = str_replace( '/', '-', sprintf( '%s-%s', $pi['dirname'], $pi['filename'] ) );
		$handle = preg_replace( '/[^a-z0-9_]/','-',  $handle );
		$handle = preg_replace( '/^(-+)/','',  $handle );
		return $handle;
	}

	/**
	 *	Locate asset file
	 */
	private function locate() {
		// !!! must know plugin or theme !!!
		$check = $this->core->get_asset_roots();
		foreach ( $check as $root_path => $root_url ) {
			$root_path = untrailingslashit( $root_path );
			$root_url = untrailingslashit( $root_url );
			$path = $root_path . '/' . $this->asset;
			if ( file_exists( $path ) ) {
				$this->path = $path;
				$this->url = $root_url . '/' . $this->asset;
				return;
			}
		}
		throw new \Exception( sprintf( 'Couldn\'t locate %s', $this->asset ) );
	}

	/**
	 *	Set Dependencies
	 *
	 *	@param array $deps Dependencies
	 */
	public function deps( $deps = [] ) {
		$this->deps = (array) $deps;
		return $this;
	}

	/**
	 *	Add Dependency
	 *
	 *	@param Asset|array|string $dep Dependency slug(s) or Asset instance
	 */
	public function add_dep( $dep ) {
		if ( $dep instanceof self ) {
			$dep = $dep->handle;
		}
		if ( is_array( $dep ) ) {
			foreach ( $dep as $d ) {
				$this-add_dep($d);
			}
		} else {
			if ( ! in_array( $dep, $this->deps ) ) {
				$this->deps[] = $dep;
			}
		}
		return $this;
	}

	/**
	 *	Set Dependencies
	 *
	 *	@param boolean $in_footer Dependencies
	 */
	public function footer( $in_footer = true ) {
		$this->in_footer = $in_footer;
		return $this;
	}

	/**
	 *	Register asset
	 *	Wrapper for wp_register_[script|style]
	 */
	public function register( ) {
		if ( ! $this->registered ) {
			$fn = $this->type === 'js' ? 'wp_register_script' : 'wp_register_style';
			$args = [
				$this->handle,
				$this->url,
				$this->deps,
				$this->core->version()
			];
			if ( $this->in_footer ) {
				$args[] = $this->in_footer;
			}
			call_user_func_array(
				$fn,
				$args
			);
			$this->registered = true;

		}
		return $this->_localize();
	}

	/**
	 *	Enqueue asset
	 *	Wrapper for wp_enqueue_[script|style]
	 *
	 *	@param array $deps Dependencies
	 */
	public function enqueue() {

		$fn = $this->type === 'js' ? 'wp_enqueue_script' : 'wp_enqueue_style';

		if ( ! $this->registered ) {
			$this->register();
		}

		call_user_func( $fn, $this->handle );

		return $this;
	}

	/**
	 *	Localize
	 *	Wrapper for wp_localize_script
	 *
	 *	@param array $deps Dependencies
	 */
	public function localize( $l10n = [], $varname = null ) {
		if ( $this->type !== 'js' ) {
			throw new \Exception( 'Can\'t localize stylesheet' );
		}
		if ( ! is_null( $varname ) ) {
			$this->varname = $varname;
		}
		if ( is_array( $l10n ) ) {
			$this->l10n = $l10n;
		}
		return $this->_localize();
	}

	/**
	 *	Maybe call wp_localize_script
	 */
	private function _localize( ) {
		if ( $this->registered && ! $this->localized && is_array( $this->l10n ) ) {

			wp_localize_script( $this->handle, $this->varname, $this->l10n );

			$this->localized = true;

		}
		return $this;
	}

	/**
	 *	magic getter
	 */
	public function __get( $var ) {
		switch ( $var ) {
			case 'asset':
			case 'handle':
			case 'in_footer':
			case 'path':
			case 'url':
			case 'varname':
				return $this->$var;
			case 'deps':
				return array_values( $this->$var );
		}
	}
}
