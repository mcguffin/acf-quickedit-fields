<?php

namespace ACFQuickEdit\Core;

use ACFQuickEdit\Compat;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Core extends Singleton {

	private $post_field_prefix = 'acf_qed_';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_action( 'plugins_loaded' , array( $this , 'load_textdomain' ) );
		add_action( 'plugins_loaded' , array( $this , 'init_compat' ), 0 );
		add_action( 'init' , array( $this , 'init' ) );
	}

	/**
	 *	Load frontend styles and scripts
	 *
	 *	@action wp_enqueue_scripts
	 */
	function wp_enqueue_style() {
	}

	/**
	 *	Load text domain
	 * 
	 *  @action plugins_loaded
	 */
	public function init_compat() {
		if ( class_exists( 'Polylang' ) ) {
			Compat\Polylang::instance();
		}
	}

	/**
	 *	Load text domain
	 * 
	 *  @action plugins_loaded
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'acf-quick-edit-fields' , false, ACFQUICKEDIT_DIRECTORY . '/languages/' );
	}

	/**
	 *	Init hook.
	 * 
	 *  @action init
	 */
	function init() {
	}

	public function prefix( $str ) {
		return $this->post_field_prefix . $str;

	}

}
