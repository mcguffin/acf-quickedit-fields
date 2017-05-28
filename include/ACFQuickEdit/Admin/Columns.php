<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Columns extends Feature {

	public function get_type() {
		return 'column';
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
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

	public function init_fields() {
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
				$display_hook	= 'manage_posts_custom_column';
			} else if ( 'page' == $post_type ) {
				$cols_hook		= 'manage_pages_columns';
				$display_hook	= 'manage_pages_custom_column';
			} else if ( 'attachment' == $post_type ) {
				$cols_hook		= 'manage_media_columns';
				$display_hook	= 'manage_media_custom_column';
			} else {
				$cols_hook		= "manage_{$post_type}_posts_columns";
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
			}
		} else if ( 'taxonomy' == $content_type ) {

			$taxonomy		= $_REQUEST['taxonomy'];
			$cols_hook		= "manage_edit-{$taxonomy}_columns";
			$display_hook	= "manage_{$taxonomy}_custom_column";

			if ( $is_active ) {
				$displays_filters[] = array(
					'cb'		=> array( $this, 'display_term_field_column' ),
					'priority'	=> 10,
					'args'		=> 3,
				);
			}

		} else if ( 'user' == $content_type ) {
			$cols_hook		= "manage_users_columns";
			$display_hook	= "manage_users_custom_column";

			if ( $is_active ) {
				$displays_filters[] = array(
					'cb'		=> array( $this, 'display_user_field_column' ),
					'priority'	=> 10,
					'args'		=> 3,
				);
			}
		}

		if ( $is_active ) {
			$cols_filters[] = array(
				'cb'		=> array( $this, 'add_field_columns' ),
				'priority'	=> null,
				'args'		=> null,
			);
		} else {
			$cols_filters[] = array(
				'cb'		=> array( $this, 'add_ghost_column' ),
				'priority'	=> null,
				'args'		=> null,
			);
			$displays_filters[] = array(
				'cb'		=> '__return_empty_string',
				'priority'	=> null,
				'args'		=> null,
			);
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

		wp_register_style( 'acf-qef-thumbnail-col', plugins_url( 'css/thumbnail-col.css', ACFQUICKEDIT_FILE ) );

		$this->styles[] = 'acf-qef-thumbnail-col';

	}

	public function is_enabled_for_field( $field ) {

		return isset($field['show_column']) && $field['show_column'];

	}

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

	private function _mul_100( $val ) {
		return intval( $val ) * 100;
	}

	/**
	 * @private
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
	 * @action manage_posts_custom_column
	 * @action manage_media_custom_column
	 * @action manage_{$post_type}_posts_custom_column
	 */
	public function display_post_field_column( $wp_column_slug , $object_id ) {
		echo $this->display_field_column( $wp_column_slug , $object_id );
	}

	/**
	 * @action manage_edit-{$taxonomy}_custom_column
	 */
	public function display_term_field_column( $content, $wp_column_slug , $object_id ) {

		$object = get_term( $object_id );

		if ( $object ) {

			return $this->display_field_column( $wp_column_slug , sprintf( '%s_%s', $object->taxonomy, $object_id ) );

		}
	}

	/**
	 * @action manage_user_custom_column
	 */
	public function display_user_field_column( $content, $wp_column_slug , $object_id ) {
		
		return $this->display_field_column( $wp_column_slug , sprintf( 'user_%s', $object_id ) );

	}

	public function display_field_column( $wp_column_slug , $object_id ) {

		$args = func_get_args();

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );

		if ( isset( $this->fields[$column] ) ) {
			$field_object = $this->fields[$column];
			return $field_object->render_column( $object_id );
		}
		return '';
	}

}