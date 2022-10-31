<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Generic extends Field {

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		/**
		 *	Field is sortable
		 *
		 *	@param bool $sortable
		 *	@param array $acf_field
		 */
		return apply_filters('acf_qef_sortable_'.$this->acf_field['type'], false, $this->acf_field );
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		/**
		 *	Quick/Bulk Edit form element
		 *
		 *	@param string $element_html
		 *	@param array $input_atts Attributes for input element
		 *	@param bool $is_quickedit
		 *	@param array $acf_field
		 */
		return apply_filters( 'acf_qef_input_html_' . $this->acf_field['type'], '', $input_atts, $is_quickedit, $this->acf_field );
	}

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		/**
		 *	Column HTML Content
		 *
		 *	@param string $column_html
		 *	@param string/int $object_id
		 *	@param array $acf_field
		 */
		return apply_filters( 'acf_qef_column_html_' . $this->acf_field['type'], '', $object_id, $this->acf_field );
	}

	/**
	 *	@inheritdoc
	 */
	public function render_filter( $index, $selected = '' ) {
		return apply_filters( 'acf_qef_filter_html_' . $this->acf_field['type'], '', $object_id, $this->acf_field );
	}

	/**
	 *	@inheritdoc
	 */
	public function get_value( $object_id, $format_value = true ) {

		/**
		 *	Value to be loaded into editor
		 *
		 *	@param mixed $value
		 *	@param string/int $object_id
		 *	@param bool $format_value
		 *	@param array $acf_field
		 */
		return apply_filters( 'acf_qef_get_value_' . $this->acf_field['type'], parent::get_value( $object_id, $format_value ), $object_id, $format_value, $this->acf_field );
	}


	/**
	 *	@param array $wrapper_attr Field input attributes
	 *	@return array
	 */
	protected function get_wrapper_attributes( $wrapper_attr ) {
		return apply_filters(
			'acf_qef_wrapper_attributes_' . $this->acf_field['type'],
			$wrapper_attr,
			$this->acf_field
		);
	}


	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		return apply_filters(
			'acf_qef_bulk_operations_' . $this->acf_field['type'],
			[],
			$this->acf_field
		);
	}

	/**
	 *	Perform a bulk operation
	 *
	 *	@param string $operation
	 *	@param mixed $new_value
	 *	@return mixed
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {
		$old_value = $this->get_value( $object_id, $format_value );
		/**
		 *	Value being stored
		 *
		 *	@param mixed $new_value
		 *	@param mixed $old_value
		 *	@param string/int $object_id
		 *	@param array $acf_field
		 */
		return apply_filters(
			'acf_qef_bulk_operation_' . $this->acf_field['type'] . '_' . $operation,
			$new_value,
			$old_value,
			$object_id,
			$this->acf_field
		);
	}


	/**
	 *	@inheritdoc
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $operation ) {
		return apply_filters(
			'acf_qef_validate_bulk_operation_value_' . $this->acf_field['type'] . '_' . $operation,
			$valid,
			$new_value,
			$this->acf_field
		);
	}

}
