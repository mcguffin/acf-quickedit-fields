<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class ChoiceField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		if ( is_array( $value ) ) {
			return $this->sanitize_strings_array( array_values( $value ), $context );
		} else {
			return sanitize_text_field( $value );
		}
	}
}
