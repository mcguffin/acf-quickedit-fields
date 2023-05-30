<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Filters extends Feature {

	/**
	 *	Field value will leave fields unchanged
	 */
	private $no_value = '___no_value';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		add_filter( 'acf_qef_meta_query_request', [ $this, 'transform_meta_query' ] );
		parent::__construct();
	}

	/**
	 *	@inheritdoc
	 */
	public function load_field( $field ) {
		return wp_parse_args( $field, [
			'show_column_filter'	=> false,
		]);
	}

	/**
	 *	@filter acf_qef_meta_query_request
	 */
	public function transform_meta_query( $meta_query ) {
		return array_map( function( $statement ) {
			if (
				! is_array( $statement )
				|| ! isset( $statement['value'] )
				|| $statement['value'] !== $this->no_value
			) {
				return $statement;
			}
			return [
				'relation' => 'OR',
				[
					'key' => $statement['key'],
					'compare' => 'NOT EXISTS',
				],
				[
					'key' => $statement['key'],
					'compare' => '=',
					'value' => '',
				],
			];
		}, $meta_query );
	}

	/**
	 *	Get value for do-not-change chackbox
	 *
	 *	@return string
	 */
	public function get_none_value() {
		return $this->no_value;
	}

	/**
	 *	@inheritdoc
	 */
	public function get_type() {
		return 'filter';
	}

	/**
	 *	@inheritdoc
	 */
	public function get_fieldgroup_option() {
		return 'show_column_filter';
	}

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {
		if ( ! parent::init_fields() ) {
			return;
		}

		$current_view = CurrentView::instance();
		$content_kind = $current_view->get_object_kind();

		if ( 'post' == $content_kind ) {

			add_action( 'restrict_manage_posts', [ $this, 'render_filters' ], 10, 2 );
			add_action( 'pre_get_posts', [ $this, 'parse_query' ] );

		} else if ( 'term' == $content_kind ) {

			add_action( 'admin_footer', [ $this, 'render_terms_filter_form' ], 10 );
			add_action( 'parse_term_query', [ $this, 'parse_term_query' ] );

			$content_type = $current_view->get_object_type();

			add_filter( "handle_bulk_actions-edit-{$content_type}", [ $this, 'meta_query_redirect' ], 10, 3 );

		} else if ( 'user' == $content_kind ) {

			add_action( 'manage_users_extra_tablenav', [ $this, 'render_filter_form' ], 10, 1 );
			add_filter( 'pre_get_users', [ $this, 'pre_get_users' ] );
		}
	}

	/**
	 *	Term Filters com as a post request. We put the meta query in $_GET vars here.
	 *
	 *	@filter handle_bulk_actions-{$screen}
	 */
	public function meta_query_redirect( $location, $action, $tags ) {
		if ( 'filter' === $action ) {
			$location = add_query_arg( 'meta_query',$this->get_meta_query(), $location );
		}
		return $location;
	}

	/**
	 *	@action restrict_manage_users
	 *	@action restrict_manage_posts
	 *	@action manage_terms_extra_tablenav
	 */
	public function render_filters( $post_type, $which = null ) {

		if ( ( ! is_null( $which ) && 'top' !== $which ) || ( is_null( $which ) && 'top' !== $post_type ) ) {
			return;
		}
		?>
		<div class="alignleft actions acf-qef-filter-form">
			<?php

			$index = 0;

			?>
			<input type="hidden" name="meta_query[relation]" value="AND" />
			<?php
			foreach ( $this->fields as $name => $field ) {

				if ( isset( $_REQUEST['meta_query'] ) && isset( $_REQUEST['meta_query'][$index] ) && isset( $_REQUEST['meta_query'][$index]['value'] ) ) {
					$selected = wp_unslash( $_REQUEST['meta_query'][ $index ]['value'] );
				} else if ( 'taxonomy' === $field->acf_field['type'] && $field->acf_field['load_terms'] && isset( $_REQUEST[ $field->acf_field['taxonomy'] ] ) ) {
					$selected = $_REQUEST[ $field->acf_field['taxonomy'] ];
				} else {
					$selected = '';
				}
				echo $field->render_filter( $index++, $selected );
			}
			?>
		</div>
		<?php
	}

	/**
	 *	@action manage_users_extra_tablenav
	 */
	public function render_filter_form($which) {
		if ( 'top' === $which ) {
			$this->render_filters( '', 'top' );
			printf(
				'<button type="submit" name="action" id="term-query-submit" class="button" value="filter">%s</button>',
				esc_html__( 'Filter', 'acf-quickedit-fields' )
			);
		}
	}

	/**
	 *	Terms list table is lacking a restict_manage_terms / manage_terms_extra_tablenav hook.
	 *	Clumsy JS solution required
	 *  @see https://core.trac.wordpress.org/ticket/56931
	 *
	 *	@action manage_terms_extra_tablenav
	 */
	public function render_terms_filter_form() {

		?>
		<!-- BEGIN: ACF QuickEdit Fields -->
		<template id="acf-qef-terms-filter-form">
			<input type="hidden" name="delete_tags[]" value="-1" />
			<?php $this->render_filter_form( 'top' ); ?>
		</template>
		<script>
		(function($){
			$( $('#acf-qef-terms-filter-form').html() )
				.insertBefore( '.tablenav.top .tablenav-pages' )
		})(jQuery)
		</script>
		<!-- END: ACF QuickEdit Fields -->
		<?php
	}
}
