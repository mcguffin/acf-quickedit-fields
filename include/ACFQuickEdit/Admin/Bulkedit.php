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
	public function get_fieldgroup_option() {
		return 'allow_bulkedit';
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

		$column = str_replace(' qef-thumbnail','', $wp_column_slug );
		foreach ( $this->fieldsets as $field_group_key => $fields ) {
			$field_group = acf_get_field_group( $field_group_key );
			// we need a div here because WP is prepending tags input to the fieldset:last in the editor
			echo '<!-- BEGIN ACF Quick Edit Fields - Bulk -->' . "\n";
			echo '<div>' . "\n";
			printf( '<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">', $post_type );
			printf( '<legend>%s</legend>', $field_group['title'] );
			echo '<div class="qed-fields">';

			foreach ( $fields as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $post_type, 'bulk' );
			}
			echo '</div>';
			echo '</fieldset>';
			echo '</div>' . "\n";
			echo '<!-- END ACF Quick Edit Fields - Bulk -->';
		}

		$this->did_render = true;
	}

}
