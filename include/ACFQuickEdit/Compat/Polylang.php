<?php
/**
 *	@package ACFQuickEdit\Compat
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ACFQuickEdit\Compat;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


use ACFQuickEdit\Core;


class Polylang extends Core\Singleton implements Core\ComponentInterface {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_filter( 'acf_quick_edit_post_ajax_actions', array( $this, 'post_ajax_action' ) );
		add_filter( 'acf_quick_edit_term_ajax_actions', array( $this, 'term_ajax_action' ) );
		add_filter( 'acf_quick_edit_post_id_request_param', array( $this, 'post_id_request_params' ) );
	}


	/**
	 *	@filter acf_quick_edit_post_ajax_actions
	 */
	public function post_ajax_action( $actions ) {
		$actions[] = 'pll_update_post_rows';
		return $actions;
	}

	/**
	 *	@filter acf_quick_edit_term_ajax_actions
	 */
	public function term_ajax_action( $actions ) {
		$actions[] = 'pll_update_term_rows';
		return $actions;
	}

	/**
	 *	@filter acf_quick_edit_post_id_request_param
	 */
	public function post_id_request_params( $params ) {
		$params[] = 'post_id';
		return $params;
	}

	/**
	 *	@inheritdoc
	 */
	 public function activate(){

	 }

	 /**
	  *	@inheritdoc
	  */
	 public function deactivate(){

	 }

	 /**
	  *	@inheritdoc
	  */
	 public static function uninstall() {
		 // remove content and settings
	 }

	/**
 	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {
	}

}
