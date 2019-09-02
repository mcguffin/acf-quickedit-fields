<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class GroupField extends Field {

	private $sub_fields = array();


	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) { }

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) { }


	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		$sanitized_value = array();

		foreach ( (array) $value as $field_key => $value ) {

			if ( ! ( $field = get_field_object( $field_key ) ) ) {
				continue;
			}

			if ( $field_object = Field::getFieldObject( $field ) ) {
				$sanitized_value[$field_key] = $field_object->sanitize_value( $value );
			}

		}
		return $sanitized_value;
	}

	/**
	 *	Update field value
	 *
	 *	@param int $post_id
	 *	@param bool $is_quickedit
	 *
	 *	@return null
	 */
	public function maybe_update( $post_id, $is_quickedit ) {

		if ( isset( $this->parent ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['acf'] ) ) {
			return;
		}

		$param_name = $this->acf_field['key'];

		if ( isset ( $_REQUEST['acf'][ $param_name ] ) ) {
			$value = $this->sanitize_value( $_REQUEST['acf'][ $param_name ] );
		} else {
			$value = null;
		}

		if ( ! is_array( $value ) ) {
			return;
		}

		// remove unchanged from input
		$value = array_filter( $value, array( $this, 'filter_do_not_change') );

		// validate field values
		if ( ! acf_validate_value( $value, $this->acf_field, sprintf( 'acf[%s]', $param_name ) ) ) {
			return;
		}


		$this->update( $value, $post_id );
	}

	/**
	 *	array_filter callback
	 */
	private function filter_do_not_change( $val ) {
		return $val !== $this->dont_change_value;
	}

}
