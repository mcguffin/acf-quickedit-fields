<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Quickedit extends EditFeature {

	private $did_render = false;

	public function get_type() {
		return 'quickedit';
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	public function render_acf_settings( $field ) {
		// add to quick edit
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow QuickEdit','acf-quick-edit-fields'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'allow_quickedit',
			'message'		=> __("Allow editing this field in QuickEdit mode", 'acf-quick-edit-fields')
		));
	}

	public function init_fields() {

		parent::init_fields();

		if ( $this->is_active() ) {

			add_action( 'quick_edit_custom_box',  array( $this, 'display_quick_edit' ), 10, 2 );

		}
	}

	public function is_enabled_for_field( $field ) {

		return isset( $field['allow_quickedit'] ) && $field['allow_quickedit'];

	}

	/**
	 *	@action quick_edit_custom_box
	 */
	function display_quick_edit( $wp_column_slug, $post_type ) {

		if ( $this->did_render ) {
			return;
		}

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );
		foreach ( $this->field_groups as $field_group ) {
			printf( '<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">', $post_type );
			printf( '<legend>%s</legend>', $field_group['title'] );
			printf( '<input type="hidden" name="nonce" value="%s" />', wp_create_nonce( 'acf_nonce' ) );

			foreach ( $field_group['fields'] as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $post_type, 'quick' );
			}
			echo '</fieldset>';
		}

		$this->did_render = true;
	}



}