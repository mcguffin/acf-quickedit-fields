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
	 *	Key for bulk operations
	 */
	private $bulk_operation_key = '___bulk_op';


	/**
	 *	Current bulk operations
	 */
	private $_bulk_operations = [];

	/**
	 *	@inheritdoc
	 */
	public function load_field( $field ) {
		return wp_parse_args( $field, [
			'allow_bulkedit'		=> false,
		]);
	}

	/**
	 *	@filter acf/validate_value
	 */
	public function validate_value( $valid, $value, $field, $input ) {
		if ( $operation = $this->get_bulk_operation( $field['key'] ) ) {
			$valid = Fields\Field::getFieldObject( $field )->validate_bulk_operation_value( $valid, $value, $operation );
		}

		return $valid;
	}

	/**
	 *	Get value for do-not-change chackbox
	 *
	 *	@return string
	 */
	public function get_dont_change_value() {
		return $this->dont_change_value;
	}

	/**
	 *	Get key for bulk operation
	 *
	 *	@return string
	 */
	public function get_bulk_operation_key() {
		return $this->bulk_operation_key;
	}

	/**
	 *	@inheritdoc
	 */
	protected function is_saving() {
		return ( isset( $_GET['action'] ) && $_GET['action'] === 'edit')
			|| ( isset( $_GET['action2'] ) && $_GET['action2'] === 'edit');
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

		add_filter( 'acf/validate_value', [ $this, 'validate_value'], 10, 4 );

		parent::init_fields();

		if ( $this->is_active() ) {

			add_action( 'bulk_edit_custom_box', [ $this , 'display_bulk_edit' ], 200, 2 );

		}

	}

	/**
	 *	@return boolean
	 */
	public function is_bulk_operation( $field_key ) {
		return isset( $_REQUEST['acf'] )
			&& is_array( $_REQUEST['acf'] )
			&& isset( $_REQUEST['acf'][ $this->get_bulk_operation_key() ] )
			&& is_array( $_REQUEST['acf'][ $this->get_bulk_operation_key() ] )
			&& isset( $_REQUEST['acf'][ $this->get_bulk_operation_key() ][ $field_key] )
			&& ! empty( $_REQUEST['acf'][ $this->get_bulk_operation_key() ][ $field_key] );
	}

	/**
	 *	@return boolean
	 */
	public function get_bulk_operation( $field_key ) {
		return $this->is_bulk_operation( $field_key )
		 	? $_REQUEST['acf'][ $this->get_bulk_operation_key() ][ $field_key]
			: false;
	}

	/**
	 *	@action bulk_edit_custom_box
	 */
	public function display_bulk_edit( $wp_column_slug, $post_type ) {
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
				"<!-- END ACF Quick Edit Fields - Bulk %s -->\n",
				sanitize_key( $field_group_key )
			);
		}

		$this->did_render = true;
	}

	/**
	 *	@inheritdoc
	 */
	protected function get_save_data( $post_id ) {
		// remove do-not-change values from $_GET['acf']
		$data = null;
		if ( isset( $_GET['acf'] ) && is_array( $_GET['acf'] ) ) {

			$data = wp_unslash( $_GET['acf'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( isset( $data[ $this->get_bulk_operation_key() ] ) ) {
				$this->_bulk_operations = array_filter( $data[ $this->get_bulk_operation_key() ] );
				unset( $data[ $this->get_bulk_operation_key() ] );
			}

			$this->process_data( $data, null, $post_id );

		}
		return $data;
	}

	/**
	 *	array_walk callback - recursive remove do-not-change values
	 *	@param mixed $data
	 */
	private function process_data( &$data, $key = null, $post_id = null ) {
		if ( is_array( $data ) ) {
			//
			$data = array_filter( $data, [ $this, 'filter_commands' ] );
			array_walk( $data, [ $this, 'process_data' ], $post_id );

			$data = array_filter( $data, [ $this, 'filter_ampty_array' ] );
		}

		$op = $this->get_bulk_operation( $key );

		if ( false !== $op ) {
			$field = Fields\Field::getFieldObject( $key );
			$data = $field->do_bulk_operation( $op, $data, $post_id );
		}
	}

	/**
	 *	array_filter callback - returns false if $el is do-not-change value
	 */
	private function filter_commands( $el ) {
		return $el !== $this->get_dont_change_value();
	}

	/**
	 *	array_filter callback - returns false for empty arrays
	 */
	private function filter_ampty_array( $el ) {
		return ! is_array( $el ) || ( count( $el ) > 0 );
	}
}
