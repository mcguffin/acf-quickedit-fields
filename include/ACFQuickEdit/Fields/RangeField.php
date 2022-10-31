<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RangeField extends NumberField {

	use Traits\BulkOperationNone;
	/**
	 *	@inheritdoc
	 */
	protected $wrapper_class = 'acf-input-wrap acf-range-wrap';

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {

		$output = '';

		if ( $this->acf_field['prepend'] ) {
			$output .= sprintf( '<span class="prepend">%s</span>', esc_html( $this->acf_field['prepend'] ) );
		}

		$output .= parent::render_input( [ 'type' => 'range' ], $is_quickedit );

		$len = max( 3, strlen( (string) $this->acf_field['max'] ) );
		$output .= '<input '. acf_esc_attr( [
				'type'					=> 'number',
				'id'					=> $this->acf_field['key'] . '-alt',
				'data-acf-field-key'	=> $this->acf_field['key'],
				'step'					=> $this->acf_field['step'],
				'style'					=> sprintf(
					'min-width: %1$sem;max-width:%1$sem',
					1.8 + $len * 0.7
				)
			] ) .' />';
		if ( $this->acf_field['append'] ) {
			$output .= sprintf( '<span class="append">%s</span>', esc_html( $this->acf_field['append'] ) );
		}

		return $output;
	}
}
