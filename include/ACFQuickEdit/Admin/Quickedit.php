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
	public function get_fieldgroup_option() {
		return 'allow_quickedit';
	}

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {

		parent::init_fields();

		if ( $this->is_active() ) {

			add_action( 'quick_edit_custom_box',  [ $this, 'display_quick_edit' ], 10, 2 );

		}
	}

	/**
	 *	@action quick_edit_custom_box
	 */
	function display_quick_edit( $wp_column_slug, $post_type ) {

		if ( $this->did_render ) {
			return;
		}

		$column = str_replace(' qef-thumbnail','', $wp_column_slug );
		printf(
			'<input type="hidden" name="_wp_http_referer" value="%s" />',
			esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) )
		);
		foreach ( $this->fieldsets as $field_group_key => $fields ) {

			$field_group = acf_get_field_group( $field_group_key );

			printf(
				'<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">',
				sanitize_key( $post_type )
			);
			printf(
				'<legend>%s</legend>',
				esc_html( $field_group['title'] )
			);
			echo '<div class="qed-fields">';

			foreach ( $fields as $sub_field_object ) {

				$sub_field_object->render_quickedit_field( $post_type, 'quick' );
			}

			echo '</div>';
			echo '</fieldset>';
		}

		$this->did_render = true;
	}

	/**
	 *	@inheritdoc
	 */
	protected function get_save_data() {
		// fall back to $_POST['acf']
		return null;
	}


}
