<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class NumberField extends Field {

	use Traits\BulkOperationNumeric;

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		$output = '';

		$value = $this->get_value( $object_id );

		if ( $value === "" ) {
			$output .= $this->__no_value();
		} else {
			$output .= number_format_i18n( floatval($value), strlen( substr( strrchr( $value, "." ), 1 ) ) ); //
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += [
			'class'	=> 'acf-quick-edit',
			'type'	=> 'number',
			'min'	=> $this->acf_field['min'],
			'max'	=> $this->acf_field['max'],
			'step'	=> $this->acf_field['step'],
		];

		return parent::render_input( $input_atts, $is_quickedit );
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return 'numeric';
	}
}
