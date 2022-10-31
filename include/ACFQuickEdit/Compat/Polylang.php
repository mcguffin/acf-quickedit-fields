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


class Polylang extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		// current action: plugins_loaded
		add_filter( 'acf_quick_edit_post_ajax_actions', [ $this, 'post_ajax_action' ] );
		add_filter( 'acf_quick_edit_term_ajax_actions', [ $this, 'term_ajax_action' ] );
		add_filter( 'acf_quick_edit_post_id_request_param', [ $this, 'post_id_request_params' ] );

		if ( defined( 'WPSEO_VERSION' ) ) {
			add_action( 'wp_ajax_pll_update_post_rows', [ $this, 'handle_wp_seo_columns' ] );
			add_action( 'wp_ajax_pll_update_term_rows', [ $this, 'handle_wp_seo_columns' ] );
		}

	}

	/**
	 *	@action wp_ajax_pll_update_post_rows
	 *	@action wp_ajax_pll_update_term_rows
	 */
	public function handle_wp_seo_columns() {
		// tested with WPSEO_VERSION = 11.9
		try {
			// link column(s)
			if ( \WPSEO_Options::get( 'enable_text_link_counter' ) ) {
				$link_cols = new \WPSEO_Link_Columns( new \WPSEO_Meta_Storage() );
				$link_cols->set_count_objects();
				$link_cols->register_init_hooks();
			}
		} catch( \Excetion $err ) {
			// Rare use case. error log should sustain
			error_log( sprintf( 'ACF QuickEdit Fields is having trouble with Yoast SEO v%s', WPSEO_VERSION ) );
			error_log( $err->getMessage() );
			error_log( $err->getTraceAsString() );
		}

		try {
			// score + readability
			$meta_cols = new \WPSEO_Meta_Columns();
			$meta_cols->setup_hooks();
		} catch( \Excetion $err ) {
			// Rare use case. error log should sustain
			error_log( sprintf( 'ACF QuickEdit Fields is having trouble with Yoast SEO v%s', WPSEO_VERSION ) );
			error_log( $err->getMessage() );
			error_log( $err->getTraceAsString() );
		}
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
