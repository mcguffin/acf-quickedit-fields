<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Bulkedit extends EditFeature {

	/**
	 *	@var bool
	 */
	private $did_render = false;


	/**
	 *	@inheritdoc
	 */
	public function get_type() {
		return 'bulkedit';
	}

	/**
	 *	@inheritdoc
	 */
	public function render_acf_settings( $field ) {

		// show column: todo: allow sortable
		// add to bulk edit
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Bulk Edit','acf-quick-edit-fields'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'allow_bulkedit',
			'ui'			=> 1,
			'message'		=> __("Allow editing this field in Bulk edit mode", 'acf-quick-edit-fields')
		));
	}

	/**
	 *	@inheritdoc
	 */
	public function is_enabled_for_field( $field ) {

		return isset($field['allow_bulkedit']) && $field['allow_bulkedit'];

	}


	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		parent::init_fields();

		if ( $this->is_active() ) {
			add_action( 'bulk_edit_custom_box', array( $this , 'display_bulk_edit' ), 200, 2 );
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
			// we need a div here because WP is prepending tags input to the fieldset:last in the editor
			echo '<!-- BEGIN ACF Quick Edit Fields - Bulk -->' . "\n";
			echo '<div>' . "\n";
			printf( '<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">', $post_type );
			printf( '<legend>%s</legend>', $field_group['title'] );

			foreach ( $field_group['fields'] as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $post_type, 'bulk' );
			}
			echo '</fieldset>';
			echo '</div>' . "\n";
			echo '<!-- END ACF Quick Edit Fields - Bulk -->';
		}

		$this->did_render = true;
	}

}
