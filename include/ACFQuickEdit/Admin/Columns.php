<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Columns extends Feature {

	private $_prev_request_uri = false;

	private $_wp_column_weights = [];

	/**
	 *	@inheritdoc
	 */
	public function get_type() {
		return 'column';
	}

	/**
	 *	@inheritdoc
	 */
	public function get_fieldgroup_option() {
		return 'show_column';
	}

	/**
	 *	@inheritdoc
	 */
	public function load_field( $field ) {
		return wp_parse_args( $field, [
			'show_column'			=> false,
			'show_column_weight'	=> 1000,
			'show_column_sortable'	=> false,
		]);
	}

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		$is_active = parent::init_fields();

		$current_view = CurrentView::instance();
		$is_sortable = count( $current_view->get_fields( [ 'show_column_sortable' => 1 ] ) );

		$cols_filters = [];
		$displays_filters = [];

		$content_kind = $current_view->get_object_kind();
		$content_type = $current_view->get_object_type();

		if ( 'post' == $content_kind ) {
			if ( 'post' == $content_type ) {
				$cols_hook		= 'manage_posts_columns';
				$sortable_hook	= 'manage_edit-post_sortable_columns';
				$display_hook	= 'manage_posts_custom_column';
			} else if ( 'page' == $content_type ) {
				$cols_hook		= 'manage_pages_columns';
				$sortable_hook	= 'manage_edit-page_sortable_columns';
				$display_hook	= 'manage_pages_custom_column';
			} else if ( 'attachment' == $content_type ) {
				$cols_hook		= 'manage_media_columns';
				$sortable_hook	= 'manage_upload_sortable_columns';
				$display_hook	= 'manage_media_custom_column';
			} else {
				$cols_hook		= "manage_{$content_type}_posts_columns";
				$sortable_hook	= "manage_edit-{$content_type}_sortable_columns";
				$display_hook	= "manage_{$content_type}_posts_custom_column";
			}

			if ( $is_active ) {
				$cols_filters[] = [
					'cb'		=> [ $this, 'move_date_to_end' ],
					'priority'	=> 11,
					'args'		=> null,
				];
				$displays_filters[] = [
					'cb'		=> [ $this, 'display_post_field_column' ],
					'priority'	=> 10,
					'args'		=> 2,
				];
			} else {
				// we need at least one column for quick/bulk edit
				$cols_filters[] = [
					'cb'		=> [ $this, 'add_ghost_column' ],
					'priority'	=> null,
					'args'		=> null,
				];
			}
			if ( $is_sortable ) {
				// posts
				$this->init_meta_query();
			}
		} else if ( 'term' == $content_kind ) {

			$cols_hook		= "manage_edit-{$content_type}_columns";
			$sortable_hook	= "manage_edit-{$content_type}_sortable_columns";
			$display_hook	= "manage_{$content_type}_custom_column";

			if ( $is_active ) {
				$displays_filters[] = [
					'cb'		=> [ $this, 'display_term_field_column' ],
					'priority'	=> 10,
					'args'		=> 3,
				];
			} else {
				// we need at least one column for quick/bulk edit
				$cols_filters[] = [
					'cb'		=> [ $this, 'add_ghost_column' ],
					'priority'	=> null,
					'args'		=> null,
				];
			}
			if ( $is_sortable ) {
				// terms
				$this->init_meta_query();
			}

		} else if ( 'user' == $content_kind ) {

			$cols_hook		= "manage_users_columns";
			$sortable_hook	= "manage_users_sortable_columns";
			$display_hook	= "manage_users_custom_column";

			if ( $is_active ) {
				$displays_filters[] = [
					'cb'		=> [ $this, 'display_user_field_column' ],
					'priority'	=> 10,
					'args'		=> 3,
				];
			}
			if ( $is_sortable ) {
				$this->init_meta_query();
			}
		}

		if ( $is_active ) {
			$cols_filters[] = [
				'cb'		=> [ $this, 'add_field_columns' ],
				'priority'	=> 1000, // we hook in so late because we have to sort the columns when all of them are present
				'args'		=> null,
			];
			if ( $is_sortable ) {
				add_filter( $sortable_hook, [ $this, 'add_sortable_columns' ] );
			}
			add_filter('admin_body_class', [ $this, 'add_admin_body_class' ] );
		}

		foreach ( $cols_filters as $filter ) {
			if ( ! is_null( $filter['args'] ) ) {
				add_filter( $cols_hook, $filter['cb'], $filter['priority'], $filter['args'] );
			} else if ( ! is_null( $filter['priority'] ) ) {
				add_filter( $cols_hook, $filter['cb'], $filter['priority'] );
			} else {
				add_filter( $cols_hook, $filter['cb'] );
			}
		}

		foreach ( $displays_filters as $filter ) {
			if ( ! is_null( $filter['args'] ) ) {
				add_filter( $display_hook, $filter['cb'], $filter['priority'], $filter['args'] );
			} else if ( ! is_null( $filter['priority'] ) ) {
				add_filter( $display_hook, $filter['cb'], $filter['priority'] );
			} else {
				add_filter( $display_hook, $filter['cb'] );
			}
		}

		return true;
	}

	/**
	 *	@filter admin_body_class
	 */
	public function add_admin_body_class( $classes ) {
		$classes .= ' has-acf-qef-columns';
		return $classes;
	}

	/**
	 *	@filter manage_posts_columns
	 *	@filter manage_pages_columns
	 *	@filter manage_media_columns
	 *	@filter manage_{$post_type}_posts_columns
	 */
	public function add_ghost_column( $columns ) {
		$columns['_acf_qef_ghost'] = '';
		return $columns;
	}

	/**
	 *	@filter manage_posts_columns
	 *	@filter manage_pages_columns
	 *	@filter manage_media_columns
	 *	@filter manage_{$post_type}_posts_columns
	 */
	public function move_date_to_end($defaults) {
		if ( isset( $defaults['date'] ) ) {
			$date = $defaults['date'];
			unset($defaults['date']);
			$defaults['date'] = $date;
		}
		return $defaults;
	}

	/**
	 * @filter manage_posts_columns
	 * @filter manage_media_columns
	 * @filter manage_{$post_type}_posts_columns
	 */
	public function add_field_columns( $columns ) {

		$this->_wp_column_weights = array_map( [ $this, '_mul_100' ], array_flip( array_keys( $columns ) ) );

		foreach ( $this->fields as $field_slug => $field_object ) {
			$field = $field_object->get_acf_field();
			$field_slug .= '--qef-type-' . $field['type'] . '--';
			$columns[ $field_slug ] = $field['label'];
		}
		uksort( $columns, [ $this, '_sort_columns_by_weight' ] );

		return $columns;
	}

	/**
	 *	Whether a field supports sorting
	 *
	 *	@param ACFQuickEditFields\Fields\Field	$field_object
	 *	@return bool|string
	 */
	private function get_field_sortable( $field_object ) {

		return $field_object->is_sortable();

		/**
		 * Filters whether and how a column is sortable
		 * Return boolean or meta_type like `numeric`, `decimal(10,2)`, `datetime`, ...
		 *
		 * @since 2.1.1
		 *
		 * @param bool|string	$sortable
		 */
		return apply_filters( "acf_quick_edit_sortable_column_{$field_name}", $field_object->is_sortable() );
	}

	/**
	 *	Whether a field is configured to be sorted
	 *
	 *	@param ACFQuickEditFields\Fields\Field	$field_object
	 *	@return bool|string
	 */
	 private function get_field_sorted( $field_object ) {
 		$acf_field	= $field_object->get_acf_field();
 		$field_name	= $acf_field['name'];
		$sorted = boolval( $acf_field['show_column_sortable'] ) ? $field_object->is_sortable() : false;

 		/**
 		 * Filters whether and how a column is sortable
 		 * Return boolean or meta_type like `numeric`, `decimal(10,2)`, `datetime`, ...
 		 *
 		 * @since 2.1.1
 		 *
 		 * @param bool|string	$sortable
 		 */
 		return apply_filters( "acf_quick_edit_sortable_column_{$field_name}", $sorted );
 	}

	/**
	 * @filter manage_posts_sortable_columns
	 * @filter manage_media_sortable_columns
	 * @filter manage_{$post_type}_posts_sortable_columns
	 * @filter
	 */
	public function add_sortable_columns( $columns ) {

		foreach ( $this->fields as $field_slug => $field_object ) {

			if ( $sortable = $this->get_field_sorted( $field_object ) ) {

				// will affect css class
				$column_key = $field_slug . '--qef-type-' . $field_object->get_acf_field()['type'] . '--';

				$columns[ $column_key ] = $field_object->get_meta_key();

			}
		}
		return $columns;
	}

	/**
	 *	@inheritdoc
	 */
	public function parse_query( $query ) {
		parent::parse_query( $query );

		if ( $field = $this->get_sort_field( $query ) ) {
			add_filter( 'posts_clauses', [ $this, 'sort_posts_clauses' ], 10, 2 );
		}
	}

	/**
	 *	@filter posts_clauses
	 */
	public function sort_posts_clauses( $clauses, $wp_query ) {
		global $wpdb;

		$field = $this->get_sort_field( $wp_query );
		$sortable = $field->is_sortable();

		if ( is_string( $sortable ) ) {
			$mq = new \WP_Meta_Query([]);
			$cast = $mq->get_cast_for_type( $sortable );

			$clauses['orderby'] = " CAST(COALESCE(msort.meta_value,0) AS {$cast}) " . $wp_query->get('order');
		} else {
			$clauses['orderby'] = " COALESCE(msort.meta_value,0) " . $wp_query->get('order');
		}
		$clauses['join'] .= $wpdb->prepare(
			" LEFT JOIN {$wpdb->postmeta} AS msort ON {$wpdb->posts}.ID = msort.post_id AND msort.meta_key = %s",
			$field->get_meta_key()
		);
		remove_filter('posts_clauses', [ $this, 'sort_posts_clauses' ], 10 );
		return $clauses;

	}

	/**
	 *	@inheritdoc
	 */
	public function parse_term_query( $query ) {
		parent::parse_term_query( $query );
		if ( $field = $this->get_sort_field( $query ) ) {
			add_filter('terms_clauses', function( $clauses ) use ( $field ) {
				global $wpdb;

				$sortable = $field->is_sortable();

				if ( is_string( $sortable ) ) {
					$mq = new \WP_Meta_Query([]);
					$cast = $mq->get_cast_for_type( $sortable );

					$clauses['orderby'] = "ORDER BY CAST(COALESCE(msort.meta_value,0) AS {$cast}) " . $wp_query->query_vars['order'];
				} else {
					$clauses['orderby'] = "ORDER BY COALESCE(msort.meta_value,0) ";
				}
				$clauses['join'] .= $wpdb->prepare(
					" LEFT JOIN {$wpdb->termmeta} AS msort ON t.term_id = msort.term_id AND msort.meta_key = %s",
					$field->get_meta_key()
				);
				return $clauses;
			}, 10 );
		}
	}

	/**
	 *	@inheritdoc
	 */
	public function pre_get_users( $query ) {
		parent::pre_get_users( $query );
		if ( $field = $this->get_sort_field( $query ) ) {
			add_filter( 'users_pre_query', function( $null, $wp_query ) use ( $field ) {
				global $wpdb;

				$sortable = $field->is_sortable();
				if ( is_string( $sortable ) ) {
					$mq = new \WP_Meta_Query([]);
					$cast = $mq->get_cast_for_type( $sortable );

					$wp_query->query_orderby = "ORDER BY CAST(COALESCE(msort.meta_value,0) AS {$cast}) " . $wp_query->query_vars['order'];
				} else {
					$wp_query->query_orderby = "ORDER BY COALESCE(msort.meta_value,0) " . $wp_query->query_vars['order'];
				}

				$wp_query->query_from .= $wpdb->prepare(
					" LEFT JOIN {$wpdb->usermeta} AS msort ON {$wpdb->users}.ID = msort.user_id AND msort.meta_key = %s",
					$field->get_meta_key()
				);
				return $null;
			}, 10, 2 );
		}
	}

	/**
	 *	@param WP_Query|WP_Term_Query|WP_User_Query $query
	 *	@return boolean|Fields\Field
	 */
	private function get_sort_field( $query ) {
		if ( ! isset( $query->query_vars['orderby'] )
			|| !( $by = $query->query_vars['orderby'])
			|| is_array( $by )
			|| ! isset( $this->fields[ $by ] )
		) {
			return false;
		}

		return $this->fields[ $by ];
	}

	/**
	 *	@return array
	 */
	private function get_meta_query_args( $by ) {

		$sortable = $this->fields[ $by ]->is_sortable();
		if ( is_string( $sortable ) ) {
			$type_query = [ 'type' => strtoupper( $sortable ) ];
		} else {
			$type_query = [];
		}
		return [
			'relation'	=> 'OR',
			$by => [
				'key'		=> $by,
				'compare'	=> 'NOT EXISTS',
			] + $type_query,
			[
				'key'		=> $by,
				'compare'	=> 'EXISTS',
			] + $type_query,
		];
	}

	/**
	 *	@param number
	 *	@return number
	 */
	private function _mul_100( $val ) {
		return intval( $val ) * 100;
	}

	/**
	 *	Sort callback
	 *
	 *	@private
	 */
	private function _sort_columns_by_weight( $a_slug, $b_slug ) {
		$a = $b = 0;
		$a = $this->_get_column_weight( $a_slug );
		$b = $this->_get_column_weight( $b_slug );

		return $a - $b;
	}

	/**
	 * @private
	 */
	private function _get_column_weight( $column_slug ) {

		$column_slug = preg_replace('/--([\w-]+)--$/is', '', $column_slug );

		// wp column
		if ( isset( $this->_wp_column_weights[ $column_slug ] ) ) {
			return intval( $this->_wp_column_weights[ $column_slug ] );
		}

		// acf column
		if ( isset( $this->fields[ $column_slug ] ) ) {
			$field_object = $this->fields[ $column_slug ];
			$field = $field_object->get_acf_field();
			if ( isset( $field['show_column_weight'] ) ) {
				return intval( $field['show_column_weight'] );
			}
		}

		return max( $this->_wp_column_weights ) + 1;
	}

	/**
	 *	@param string $content
	 *	@param string $wp_column_slug
	 *	@action manage_posts_custom_column
	 *	@action manage_media_custom_column
	 *	@action manage_{$post_type}_posts_custom_column
	 */
	public function display_post_field_column( $wp_column_slug , $object_id ) {
		echo wp_kses_post( $this->filter_field_column( '', $wp_column_slug , $object_id ) );
	}

	/**
	 *	@param string $content
	 *	@param string $wp_column_slug
	 *	@param string $object_id
	 *	@filter manage_edit-{$taxonomy}_custom_column
	 */
	public function display_term_field_column( $content, $wp_column_slug , $object_id ) {

		$object = get_term( $object_id );

		if ( $object ) {
			return $this->filter_field_column( $content, $wp_column_slug , sprintf( '%s_%s', $object->taxonomy, $object_id ) );
		}

		return $content;
	}

	/**
	 *	@param string $content
	 *	@param string $wp_column_slug
	 *	@param string $object_id
	 *	@action manage_users_custom_column
	 */
	public function display_user_field_column( $content, $wp_column_slug , $object_id ) {

		return $this->filter_field_column( $content, $wp_column_slug , sprintf( 'user_%s', $object_id ) );

	}

	/**
	 *	@param string $wp_column_slug
	 *	@param string $object_id
	 *	@return string
	 */
	public function filter_field_column( $content, $wp_column_slug , $object_id ) {

		$args = func_get_args();

		$column = preg_replace('/--([\w-]+)--$/is', '', $wp_column_slug );

		if ( isset( $this->fields[$column] ) ) {
			$field_object = $this->fields[$column];
			return $field_object->render_column( $object_id );
		}
		return $content;
	}

}
