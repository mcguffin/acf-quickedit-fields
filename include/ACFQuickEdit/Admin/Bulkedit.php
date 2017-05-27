<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Bulkedit extends EditFeature {

	public function get_type() {
		return 'bulkedit';
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	function render_acf_settings( $field ) {
		$post = get_post($field['ID']);
		if ( $post ) {
			$parent = get_post( $post->post_parent );

			if ( $parent->post_type == 'acf-field-group' ) {
				// show column: todo: allow sortable
				// add to bulk edit
				acf_render_field_setting( $field, array(
					'label'			=> __('Allow Bulk Edit','acf-quick-edit-fields'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'allow_bulkedit',
					'message'		=> __("Allow editing this field in Bulk edit mode", 'acf-quick-edit-fields')
				));
			}
		}
	}

	public function is_enabled_for_field( $field ) {

		return isset($field['allow_quickedit']) && $field['allow_quickedit'];

	}



	public function init_fields() {

		parent::init_fields();

		if ( $this->is_active() ) {
			add_action( 'bulk_edit_custom_box', array( $this , 'display_bulk_edit' ), 10, 2 );
		}
	}


	/**
	 *	@action bulk_edit_custom_box
	 */
	function display_bulk_edit( $wp_column_slug, $post_type ) {
		if ( $this->did_render ) {
			return;
		}

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );
		foreach ( $this->field_groups as $field_group ) {
			printf( '<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">', $post_type );
			printf( '<legend>%s</legend>', $field_group['title'] );

			foreach ( $field_group['fields'] as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $column, $post_type, 'bulk' );
			}
			echo '</fieldset>';
		}

		$this->did_render = true;
	}

}