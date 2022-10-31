<?php
/**
 *	@package ACFQuickEdit\Core
 *	@version 1.0.1
 *	2018-09-22
 */

namespace ACFQuickEdit\Core;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}
use ACFQuickEdit\Compat;

class Core extends Plugin implements CoreInterface {

	private $post_field_prefix = 'acf_qed_';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action( 'plugins_loaded', [ $this, 'init_compat' ], 0 );

		add_filter('safecss_filter_attr_allow_css', [ $this, 'allow_rgba_css_bg' ],10,2);

		$args = func_get_args();
		parent::__construct( ...$args );
	}

	/**
	 *	Allow rgba+hlsa background colors
	 *	Required by rgba color picker (since acf 5.10)
	 *	@see https://core.trac.wordpress.org/ticket/24157
	 *
	 *	@filter safecss_filter_attr_allow_css
	 */
	public function allow_rgba_css_bg( $allow, $css_test_string ) {
		if ( preg_match( '/^background-color:\s*(rgba?|hsla?)\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*[\d\.]+\s*\)$/', $css_test_string ) ) {
			return true;
		}
		return $allow;
	}

	/**
	 *	Prefix a string
	 *
	 *	@param string $str
	 *	@return string
	 */
	public function prefix( $str ) {
		return $this->post_field_prefix . $str;
	}

	/**
	 *	Load Compatibility classes
	 *
	 *  @action plugins_loaded
	 */
	public function init_compat() {
		if ( defined('POLYLANG_VERSION') && version_compare( POLYLANG_VERSION, '1.0.0', '>=' ) ) {
			Compat\Polylang::instance();
		}
	}
}
