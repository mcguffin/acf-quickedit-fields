<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class GroupField extends Field {

	private $sub_fields = [];

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) { }

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) { }

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		$sanitized_value = [];

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
}
