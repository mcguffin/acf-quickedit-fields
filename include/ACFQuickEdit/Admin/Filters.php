<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Filters extends Feature {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		parent::__construct();
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

			// terms
			add_action( 'parse_term_query', [ $this, 'parse_term_query' ] );

			$content_type = $current_view->get_object_type();

			add_filter( "handle_bulk_actions-edit-{$content_type}", [ $this, 'meta_query_redirect' ], 10, 3 );

		} else if ( 'user' == $content_kind ) {

			add_action( 'restrict_manage_users', [ $this, 'render_filters' ], 10, 2 );

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
	 *	@action manage_posts_extra_tablenav
	 *	@action manage_users_extra_tablenav
	 *	@action manage_terms_extra_tablenav
	 */
	public function render_filters( $post_type, $which ) {

		if ( 'top' !== $which ) {
			return;
		}

		$index = 0;
		// meta_query[relation]=AND&meta_query[0][key]=select&meta_query[0][value]=one
		?>
		<input type="hidden" name="meta_query[relation]" value="AND" />
		<?php
		foreach ( $this->fields as $name => $field ) {
			if ( isset( $_REQUEST['meta_query'] ) && isset( $_REQUEST['meta_query'][$index] ) && isset( $_REQUEST['meta_query'][$index]['value'] ) ) {
				$selected = wp_unslash( $_REQUEST['meta_query'][ $index ]['value'] );
			} else {
				$selected = '';
			}
			echo $field->render_filter( $index++, $selected );
		}
		?>
		<?php
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
			<div class="alignleft actions">
				<input type="hidden" name="delete_tags[]" value="-1" />
				<?php

				$this->render_filters( '', 'top' );

				printf(
					'<button type="submit" name="action" id="term-query-submit" class="button" value="filter">%s</button>',
					esc_html__( 'Filter', 'acf-quickedit-fields' )
				);

				?>
			</div>
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
