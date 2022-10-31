<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TextareaField extends TextField {

	use Traits\BulkOperationText;

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += [
			'class'	=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
		];

		return '<textarea '. acf_esc_attr( $input_atts ) .'></textarea>';

	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return false;
	}

	/**
	 *	@param mixed $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		if ( 'ajax' === $context ) {
			return $value;
		}
		return sanitize_textarea_field( $value );
	}
}
