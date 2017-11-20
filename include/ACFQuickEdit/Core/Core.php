<?php

namespace ACFQuickEdit\Core;

use ACFQuickEdit\Compat;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Core extends Plugin {

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
	 *	@return string
	 */
	public function get_version() {
		$version = null;
		if ( ! $version = get_option('acf_quickedit_version') ) {
			if ( function_exists('get_plugin_data') ) {
				$plugin_data = get_plugin_data( ACF_QUICK_EDIT_FILE );
				$version = $plugin_data['Version'];
			}
		}
		return $version;
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
		load_plugin_textdomain( 'acf-quick-edit-fields' , false, ACF_QUICK_EDIT_DIRECTORY . '/languages/' );
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
