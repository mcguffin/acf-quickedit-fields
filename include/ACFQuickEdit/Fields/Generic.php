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
	 *	@inheritdoc
	 */
	public function update( $value, $post_id ) {
		/**
		 *	Update value
		 *
		 *	@param mixed $value
		 *	@param string/int $object_id
		 *	@param array $acf_field
		 */
		do_action( 'acf_qef_update_' . $this->acf_field['type'], $value, $post_id, $this->acf_field );
	}
}
