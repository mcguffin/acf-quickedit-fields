<?php

namespace ACFQuickEdit\Compat;

use ACFQuickEdit\Core;


/**
 *	Maintain compatibility with polylang
 */
class Polylang extends Core\Singleton {

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

}
