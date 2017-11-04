<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Columns extends Feature {

	/**
	 *	@return string
	 */
	public function get_type() {
		return 'column';
	}

	/**
	 *	@inheritdoc
	 */
	function render_acf_settings( $field ) {
		// show column: todo: allow sortable
		acf_render_field_setting( $field, array(
			'label'			=> __('Show Column','acf-quick-edit-fields'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'show_column',
			'message'		=> __("Show a column in the posts list table", 'acf-quick-edit-fields'),
			'width'			=> 50,
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Column Weight','acf-quick-edit-fields'),
			'instructions'	=> __('Columns with a higher weight will be pushed to the right. The leftmost WordPress column has a weight of <em>0</em>, the next one <em>100</em> and so on. Leave empty to place a column to the rightmost position.','acf-quick-edit-fields'),
			'type'			=> 'number',
			'name'			=> 'show_column_weight',
			'message'		=> __("Column Weight", 'acf-quick-edit-fields'),
			'default_value'	=> '1000',
			'min'			=> '-10000',
			'max'			=> '10000',
			'step'			=> '1',
			'placeholder'	=> '',
			'width'			=> '50',
		));
	}

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		$is_sortable = false;

		$field_groups = $this->get_available_field_groups();

		if ( is_null( $field_groups ) ) {
			return;
		}

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group );

			if ( ! $fields ) {
				continue;
			}

			foreach ( $fields as $field ) {

				if ( ! $this->supports( $field[ 'type' ] ) ) {
					continue;
				}

				$field_object = Fields\Field::getFieldObject( $field );
				$is_sortable |= $this->get_field_sortable( $field_object ) !== false;

				// register column display
				if ( isset($field['show_column']) && $field['show_column'] ) {
					$this->add_field( $field['name'], $field_object );
				}
			}
		}

		$cols_filters = array();
		$displays_filters = array();
		$is_active = $this->is_active();

		$content_type = $this->get_current_content_type();
		if ( 'post' == $content_type ) {
			$post_type = $this->get_current_post_type();
			if ( 'post' == $post_type ) {
				$cols_hook		= 'manage_posts_columns';
				$sortable_hook	= 'manage_edit-post_sortable_columns';
				$display_hook	= 'manage_posts_custom_column';
			} else if ( 'page' == $post_type ) {
				$cols_hook		= 'manage_pages_columns';
				$sortable_hook	= 'manage_edit-page_sortable_columns';
				$display_hook	= 'manage_pages_custom_column';
			} else if ( 'attachment' == $post_type ) {
				$cols_hook		= 'manage_media_columns';
				$sortable_hook	= 'manage_upload_sortable_columns';
				$display_hook	= 'manage_media_custom_column';
			} else {
				$cols_hook		= "manage_{$post_type}_posts_columns";
				$sortable_hook	= "manage_edit-{$post_type}_sortable_columns";
				$display_hook	= "manage_{$post_type}_posts_custom_column";
			}

			if ( $is_active ) {
				$cols_filters[] = array(
					'cb'		=> array( $this, 'move_date_to_end' ),
					'priority'	=> 11,
					'args'		=> null,
				);
				$displays_filters[] = array(
					'cb'		=> array( $this, 'display_post_field_column' ),
					'priority'	=> 10,
					'args'		=> 2,
				);
			} else {
				$cols_filters[] = array(
					'cb'		=> array( $this, 'add_ghost_column' ),
					'priority'	=> null,
					'args'		=> null,
				);
			}
			if ( $is_sortable ) {
				// post query vars
				add_filter( 'query_vars', array( $this, 'sortable_posts_query_vars' ) );
			}
		} else if ( 'taxonomy' == $content_type ) {

			$taxonomy		= $_REQUEST['taxonomy'];
			$cols_hook		= "manage_edit-{$taxonomy}_columns";
			$sortable_hook	= "manage_edit-{$taxonomy}_sortable_columns";
			$display_hook	= "manage_{$taxonomy}_custom_column";

			if ( $is_active ) {
				$displays_filters[] = array(
					'cb'		=> array( $this, 'display_term_field_column' ),
					'priority'	=> 10,
					'args'		=> 3,
				);
			}
			if ( $is_sortable ) {
				add_action( 'parse_term_query', array( $this, 'sortable_terms_query_vars' ) );
			}

		} else if ( 'user' == $content_type ) {
			$cols_hook		= "manage_users_columns";
			$sortable_hook	= "manage_users_sortable_columns";
			$display_hook	= "manage_users_custom_column";

			if ( $is_active ) {
				$displays_filters[] = array(
					'cb'		=> array( $this, 'display_user_field_column' ),
					'priority'	=> 10,
					'args'		=> 3,
				);
			}
			if ( $is_sortable ) {
				add_filter( 'users_list_table_query_args', array( $this, 'sortable_users_query_vars' ) );
			}
		}

		if ( $is_active ) {
			$cols_filters[] = array(
				'cb'		=> array( $this, 'add_field_columns' ),
				'priority'	=> null,
				'args'		=> null,
			);
		}
		if ( $is_sortable ) {
			add_filter( $sortable_hook, array( $this, 'add_sortable_columns' ) );
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
	}

	/**
	 *	@inheritdoc
	 */
	public function is_enabled_for_field( $field ) {

		return isset($field['show_column']) && $field['show_column'];

	}

	/**
	 *	@filter manage_posts_columns
	 *	@filter manage_pages_columns
	 *	@filter manage_media_columns
	 *	@filter manage_{$post_type}_posts_columns
	 */
	public function add_ghost_column( $columns ) {
		$columns['_acf_qed_ghost'] = '';
		return $columns;
	}

	/**
	 *	@filter manage_posts_columns
	 *	@filter manage_pages_columns
	 *	@filter manage_media_columns
	 *	@filter manage_{$post_type}_posts_columns
	 */
	public function move_date_to_end($defaults) {
	    $date = $defaults['date'];
	    unset($defaults['date']);
	    $defaults['date'] = $date;
	    return $defaults;
	}


	/**
	 * @filter manage_posts_columns
	 * @filter manage_media_columns
	 * @filter manage_{$post_type}_posts_columns
	 */
	public function add_field_columns( $columns ) {

		$this->_wp_column_weights = array_map( array( $this, '_mul_100' ) , array_flip( array_keys( $columns ) ) );

		foreach ( $this->fields as $field_slug => $field_object ) {
			$field = $field_object->get_acf_field();
			if ( in_array( $field['type'], array('image','gallery','file'))) {
				$field_slug .= '-qef-thumbnail';
			}
			$columns[ $field_slug ] = $field['label'];
		}
		uksort($columns, array( $this, '_sort_columns_by_weight' ));

		return $columns;
	}

	/**
	 *	@param ACFQuickEditFields\Fields\Field	$field_object
	 *	@return bool|string
	 */
	private function get_field_sortable( $field_object ) {
		$acf_field	= $field_object->get_acf_field();
		$field_name	= $acf_field['name'];

		/**
		 * Filters whether and how a column is sortable
		 * Return boolean or meta_type like `numeric`, `decimal(10,2)`, `datetime`, ...
		 *
		 * @since 2.1.1
		 *
		 * @param bool|string	$sortable
		 */
		return apply_filters( "acf_quick_edit_sortable_column_{$field_name}", $field_object->is_sortable() )
	}

	/**
	 * @filter manage_posts_sortable_columns
	 * @filter manage_media_sortable_columns
	 * @filter manage_{$post_type}_posts_sortable_columns
	 * @filter
	 */
	public function add_sortable_columns( $columns ) {

		foreach ( $this->fields as $field_slug => $field_object ) {

			if ( $sortable = $this->get_field_sortable( $field_object ) ) {

				$order = isset( $_GET['order'] ) ? strtolower($_GET['order']) === 'asc' : false;

				// Wouldn't we all wish for a new filter filter...?
				if ( isset( $_GET['meta_key'] ) ) {
					$_SERVER['REQUEST_URI'] = remove_query_arg( array('meta_key','meta_type'), $_SERVER['REQUEST_URI'] );
				}

				if ( $sortable === true ) {
					$columns[ $field_slug ] = array( $field_slug . '&meta_key=' . $field_slug, $order );
				} else {
					$columns[ $field_slug ] = array( $field_slug . '&meta_type=' . $sortable . '&meta_key=' . $field_slug, $order );
				}
			}
		}
		return $columns;
	}

	/**
	 *	@filter query_vars
	 */
	public function sortable_posts_query_vars( $query_vars ) {
		$query_vars[] = 'meta_key';
		$query_vars[] = 'meta_type';
		return $query_vars;
	}

	/**
	 * @action users_list_table_query_args
	 */
	public function sortable_users_query_vars( $query_vars ) {
		if ( isset( $_GET['meta_key'] ) ) {
			$query_vars['meta_key'] = $_GET['meta_key'];
		}
		if ( isset( $_GET['meta_type'] ) ) {
			$query_vars['meta_type'] = $_GET['meta_type'];
		}
		return $query_vars;
	}

	/**
	 * @action parse_term_query
	 */
	public function sortable_terms_query_vars( $term_query ) {

		if ( isset( $_GET['meta_key'] ) ) {
			$term_query->query_vars['meta_key'] = $_GET['meta_key'];
		}
		if ( isset( $_GET['meta_type'] ) ) {
			$term_query->query_vars['meta_type'] = $_GET['meta_type'];
		}
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

		$column_slug = str_replace('-qef-thumbnail','',$column_slug);

		if ( isset( $this->_wp_column_weights[ $column_slug ] ) ) {
			return intval( $this->_wp_column_weights[ $column_slug ] );
		}

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
		echo $this->filter_field_column( '', $wp_column_slug , $object_id );
	}

	/**
	 *	@param string $content
	 *	@param string $wp_column_slug
	 *	@param string $object_id
	 * @action manage_edit-{$taxonomy}_custom_column
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
	 *	@action manage_user_custom_column
	 */
	public function display_user_field_column( $content, $wp_column_slug , $object_id ) {

		return $this->filter_field_column( $content, $wp_column_slug , sprintf( 'user_%s', $object_id ) );

	}

	/**
	 *
	 *	@param string $wp_column_slug
	 *	@param string $object_id
	 *	@return string
	 */
	public function filter_field_column( $content, $wp_column_slug , $object_id ) {

		$args = func_get_args();

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );

		if ( isset( $this->fields[$column] ) ) {
			$field_object = $this->fields[$column];
			return $field_object->render_column( $object_id );
		}
		return $content;
	}

}
