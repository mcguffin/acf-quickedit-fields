<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class EditFeature extends Feature {

	


	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		$core = Core\Core::instance();

		add_filter( 'acf_quick_edit_render_group', '__return_false' );

		$field_groups = $this->get_available_field_groups();

		if ( is_null( $field_groups ) ) {
			return;
		}

		$content_type = $this->get_current_content_type();

		if ( $content_type === 'user' ) {
			return;
		}


		if ( $content_type == 'taxonomy' ) {
			$this->admin->js->add_dep( 'inline-edit-tax' );
		} else if ( $content_type == 'post' ) {
			$this->admin->js->add_dep( 'inline-edit-post' );
		}

		wp_enqueue_media();

		foreach ( $field_groups as $field_group ) {

			$fields = $this->acf_get_fields( $field_group );

			if ( ! $fields ) {
				continue;
			}

			foreach ( $fields as $field ) {

				if ( ! $this->supports( $field[ 'type' ] ) ) {
					continue;
				}

				$field_object = Fields\Field::getFieldObject( $field );

				// register column display
				if ( $this->is_enabled_for_field( $field ) ) {

					$this->add_field( $field['name'], $field_object, true );

					if ( ! isset( $this->field_groups[ $field_group['ID'] ] ) ) {
						$this->field_groups[ $field_group['ID'] ] = $field_group;
					}

					$this->field_groups[ $field_group['ID'] ]['fields'][ $field['key'] ] = $field_object;

					// deps should be property of field type!
					if ( $field['type'] === 'date_picker' || $field['type'] === 'time_picker' || $field['type'] === 'date_time_picker' ) {
						$this->admin->js->add_dep( 'jquery-ui-datepicker' );
						$this->admin->js->add_dep( 'acf-timepicker' );

						$this->admin->css->add_dep( 'acf-datepicker' );
						$this->admin->css->add_dep( 'acf-timepicker' );
					}
					if ( $field['type'] === 'link' ) {
						$this->admin->js->add_dep( 'wplink' );
						$this->admin->css->add_dep( 'editor-buttons' );
					}
					if ( $field['type'] === 'color_picker' ) {
						$this->admin->js->add_dep('wp-color-picker');
						$this->admin->css->add_dep('wp-color-picker');
					}
				}
			}
		}



		// bind save actions
		if ( $content_type == 'post' ) {

			$action = 'save_post';
			$callback = array( $this, 'quickedit_save_acf_post_meta' );
			$count_args = 1;

		} else if ( $content_type = 'taxonomy' ) {

			$action = 'edit_term';
			$callback = array( $this, 'quickedit_save_acf_term_meta' );
			$count_args = 3;
		}


		// register quick/bulk save actions
		if ( $this->is_active() && ! has_action( $action, $callback ) ) {

			add_action( $action, $callback, 10, $count_args );

		}
	}



	/**
	 *	@param int $term_id
	 *	@param int $tt_id
	 *	@param string $taxonomy
	 *	@action save_term
	 */
	public function quickedit_save_acf_term_meta( $term_id, $tt_id, $taxonomy ) {

		$object_id = sprintf( '%s_%s', $taxonomy, $term_id );

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		return $this->quickedit_save_acf_meta( $object_id, true );
	}



	/**
	 *	@param int $post_id
	 *	@action save_post
	 */
	public function quickedit_save_acf_post_meta( $post_id ) {
		if ( 'acf-field' === get_post_type( $post_id ) ) {
			return;
		}
		$is_quickedit = is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		return $this->quickedit_save_acf_meta( $post_id, $is_quickedit );
	}

	/**
	 *	@param int $post_id
	 *	@param bool $is_quickedit
	 */
	private function quickedit_save_acf_meta( $post_id, $is_quickedit = true ) {

		foreach ( $this->fields as $field_name => $field_object ) {

			if ( ( $this instanceof Quickedit && $is_quickedit ) || ($this instanceof Bulkedit && ! $is_quickedit ) ) {

				$field_object->maybe_update( $post_id, $is_quickedit );

			}

		}
	}


}
