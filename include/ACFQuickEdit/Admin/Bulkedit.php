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
	 *	Field value will leave fields unchanged
	 */
	private $dont_change_value = '___do_not_change';

	/**
	 *	Get value for do-not-change chackbox
	 *
	 *	@return string
	 */
	public function get_dont_change_value() {
		return $this->dont_change_value;
	}

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

			add_action( 'bulk_edit_custom_box', [ $this , 'display_bulk_edit' ], 200, 2 );

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
			printf(
				"<!-- BEGIN ACF Quick Edit Fields - Bulk <%s> -->\n",
				sanitize_key( $field_group_key )
			);
			echo '<div>' . "\n";
			printf(
				'<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">',
				sanitize_key( $post_type )
			);
			printf( '<legend>%s</legend>', esc_html( $field_group['title'] ) );
			echo '<div class="qed-fields">';

			foreach ( $fields as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $post_type, 'bulk' );
			}

			echo '</div>';
			echo '</fieldset>';
			echo '</div>' . "\n";
			printf(
				"<!-- END ACF Quick Edit Fields - Bulk {$field_group_key} -->\n",
				sanitize_key( $field_group_key )
			);
		}

		$this->did_render = true;
	}

	/**
	 *	@inheritdoc
	 */
	protected function get_save_data() {
		// remove do-not-change values from $_GET['acf']
		$data = null;
		if ( isset( $_GET['acf'] ) && is_array( $_GET['acf'] ) ) {
			$data = wp_unslash( $_GET['acf'] );
			$this->strip_dont_change( $data );
		}
		return $data;
	}

	/**
	 *	array_walk callback - recursive remove do-not-change values
	 *	@param mixed $data
	 */
	private function strip_dont_change( &$data ) {
		if ( is_array( $data ) ) {
			$data = array_filter( $data, [ $this, 'filter_do_not_change' ] );
			array_walk( $data, [ $this, 'strip_dont_change' ] );
			$data = array_filter( $data, [ $this, 'filter_ampty_array' ] );
		}
	}

	/**
	 *	array_filter callback - returns false if $el is do-not-change value
	 */
	private function filter_do_not_change( $el ) {
		return $el !== $this->get_dont_change_value();
	}

	/**
	 *	array_filter callback - returns false for empty arrays
	 */
	private function filter_ampty_array( $el ) {
		return ! is_array( $el ) || ( count( $el ) > 0 );
	}
}
