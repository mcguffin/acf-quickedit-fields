<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class EditFeature extends Feature {


	public function init_fields() {

		$field_groups = $this->get_available_field_groups();

		if ( is_null( $field_groups ) ) {
			return;
		}

		$content_type = $this->get_current_content_type();

		if ( $content_type === 'user' ) {
			return;
		}

		// register assets
		wp_register_style('acf-datepicker', acf_get_dir('assets/inc/datepicker/jquery-ui.min.css') );

		// timepicker. Contains some usefull parsing mathods even for dates.
		wp_register_script('acf-timepicker', acf_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.js'), array('jquery-ui-datepicker') );
		wp_register_style('acf-timepicker', acf_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.css') );


		wp_register_style( 'acf-quickedit', plugins_url( 'css/acf-quickedit.css', ACFQUICKEDIT_FILE ) );

		if ( $content_type == 'taxonomy' ) {
			wp_register_script( 'acf-quickedit', plugins_url( 'js/acf-quickedit.min.js', ACFQUICKEDIT_FILE ), array( 'inline-edit-tax', 'acf-input' ), null, true );
		} else if ( $content_type == 'post' ) {
			wp_register_script( 'acf-quickedit', plugins_url( 'js/acf-quickedit.min.js', ACFQUICKEDIT_FILE ), array( 'inline-edit-post', 'acf-input' ), null, true );
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
				if ( $this->is_enabled_for_field( $field ) ) {

					$this->add_field( $field['name'], $field_object );

					if ( ! isset( $this->field_groups[ $field_group['ID'] ] ) ) {
						$this->field_groups[ $field_group['ID'] ] = $field_group;
					}

					$this->field_groups[ $field_group['ID'] ]['fields'][ $field['key'] ] = $field_object;

					if ( $field['type'] === 'date_picker' || $field['type'] === 'time_picker' || $field['type'] === 'date_time_picker' ) {
						$this->scripts[]	=  'jquery-ui-datepicker';
						$this->scripts[]	=  'acf-timepicker';

						$this->styles[] 	=  'jquery-ui-datepicker';
						$this->styles[]		=  'acf-timepicker';
					}
					if ( $field['type'] === 'color_picker' ) {
						$this->scripts[]	=  'wp-color-picker';
						$this->styles[]		=  'wp-color-picker';
					}
					if ( $field['type'] === 'file' || $field['type'] === 'image' ) {
						wp_enqueue_media();
					}

				}
			}

		}
		$this->scripts[] = 'acf-quickedit';
		$this->styles[] = 'acf-quickedit';

		$this->scripts = array_unique( $this->scripts );
		$this->styles = array_unique( $this->styles );


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
	 *	@action save_term
	 */
	function quickedit_save_acf_term_meta( $term_id, $tt_id, $taxonomy ) {

		$object_id = sprintf( '%s_%s', $taxonomy, $term_id );

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		return $this->quickedit_save_acf_meta( $object_id, true );
	}



	/**
	 *	@action save_post
	 */
	function quickedit_save_acf_post_meta( $post_id ) {

		$is_quickedit = is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		return $this->quickedit_save_acf_meta( $post_id, $is_quickedit );
	}

	function quickedit_save_acf_meta( $post_id, $is_quickedit = true ) {

		foreach ( $this->fields as $field_name => $field_object ) {

			$field_object->update( $post_id, $is_quickedit );

		}
	}


}