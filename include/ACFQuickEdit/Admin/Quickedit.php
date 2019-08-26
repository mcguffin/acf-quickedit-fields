<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Quickedit extends EditFeature {

	/**
	 *	@var bool
	 */
	private $did_render = false;

	/**
	 *	@inheritdoc
	 */
	public function get_type() {
		return 'quickedit';
	}

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		parent::init_fields();

		if ( $this->is_active() ) {

			add_action( 'quick_edit_custom_box',  array( $this, 'display_quick_edit' ), 10, 2 );

		}
	}

	/**
	 *	@inheritdoc
	 */
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

		$column = str_replace(' qef-thumbnail','', $wp_column_slug );
		foreach ( $this->field_groups as $field_group ) {
			printf( '<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">', $post_type );
			printf( '<legend>%s</legend>', $field_group['title'] );
			echo '<div class="qed-fields">';
			foreach ( $field_group['fields'] as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $post_type, 'quick' );
			}
			echo '</div>';
			echo '</fieldset>';
		}

		$this->did_render = true;
	}



}
