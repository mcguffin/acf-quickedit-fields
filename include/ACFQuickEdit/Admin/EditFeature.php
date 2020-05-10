<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class EditFeature extends Feature {


	protected $fieldsets = [];

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		$is_active = parent::init_fields();

		if ( ! $is_active ) {
			return;
		}

		$current_view = CurrentView::instance();
		$object_kind = $current_view->get_object_kind();


		if ( $object_kind === 'user' ) {
			// no QE on user screen
			return;
		} else if ( $object_kind == 'term' ) {
			// cb
			$action = 'edit_term';
			$callback = [ $this, 'save_acf_term_meta' ];
			$count_args = 3;

			// add js deps
			$this->admin->js->add_dep( 'inline-edit-tax' );
		} else if ( $object_kind == 'post' ) {

			// cb
			$action = 'save_post';
			$callback = [ $this, 'save_acf_post_meta' ];
			$count_args = 1;

			// add js deps
			$this->admin->js->add_dep( 'inline-edit-post' );
		}


		// register quick/bulk save actions
		if ( ! has_action( $action, $callback ) ) {

			wp_enqueue_media();

			add_action( $action, $callback, 10, $count_args );

		}


		foreach ( $this->fields as $field ) {
			$acf_field = $field->get_acf_field();
			$fieldgroup = $current_view->get_group_of_field( $acf_field );
			if ( is_null( $fieldgroup ) ) {
				continue;
			}
			if ( ! isset( $this->fieldsets[ $fieldgroup['key'] ] ) ) {
				$this->fieldsets[ $fieldgroup['key'] ] = [];
			}

			$this->fieldsets[ $fieldgroup['key'] ][] = $field;

			// deps should be property of field type!
			if ( $acf_field['type'] === 'date_picker' || $acf_field['type'] === 'time_picker' || $acf_field['type'] === 'date_time_picker' ) {
				$this->admin->js->add_dep( 'jquery-ui-datepicker' );
				$this->admin->js->add_dep( 'acf-timepicker' );

				$this->admin->css->add_dep( 'acf-datepicker' );
				$this->admin->css->add_dep( 'acf-timepicker' );
			}
			if ( $acf_field['type'] === 'link' ) {
				$this->admin->js->add_dep( 'wplink' );
				$this->admin->css->add_dep( 'editor-buttons' );
			}
			if ( $acf_field['type'] === 'color_picker' ) {
				$this->admin->js->add_dep('wp-color-picker');
				$this->admin->css->add_dep('wp-color-picker');
			}

		}

	}



	/**
	 *	@param string $key field Key
	 *	@param array $field_object ACF Field
	 */
	protected function add_field( $key, $field_object ) {

		parent::add_field( $key, $field_object );

		if ( ! $field_parent = $field_object->get_parent() ) {
			return;
		}

		$parent_key = $field_parent->get_acf_field()['key'];

		if ( ! isset( $this->fields[$parent_key] ) ) {
			$this->add_field( $parent_key, $field_parent );
		}
	}



	/**
	 *	@param int $term_id
	 *	@param int $tt_id
	 *	@param string $taxonomy
	 *	@action save_term
	 */
	public function save_acf_term_meta( $term_id, $tt_id, $taxonomy ) {

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		$object_id = sprintf( '%s_%s', $taxonomy, $term_id );

		// avoid infinite loop
		remove_action( 'edit_term', [ $this, 'save_acf_term_meta' ], 10 );

		$ret = acf_save_post( $object_id, $this->get_save_data() );

		add_action( 'edit_term', [ $this, 'save_acf_term_meta' ], 10, 3 );

		return $ret;

	}



	/**
	 *	@param int $post_id
	 *	@action save_post
	 */
	public function save_acf_post_meta( $post_id ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// avoid infinite loop
		remove_action( 'save_post', [ $this, 'save_acf_post_meta' ], 10 );

		$ret = acf_save_post( $post_id, $this->get_save_data() );

		add_action( 'save_post', [ $this, 'save_acf_post_meta' ], 10, 1 );

		return $ret;
	}

	/**
	 *	Request data to be saved.
	 *	Will be passed to acf_save_post() which falls back to $_POST['acf']
	 *
	 *	@return null|array
	 */
	abstract protected function get_save_data();


}
