<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RangeField extends NumberField {


	/**
	 *	@inheritdoc
	 */
	protected $wrapper_class = 'acf-range-wrap';

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {

		$output = '';

		if ( $this->acf_field['prepend'] ) {
			$output .= sprintf( '<span class="prepend">%s</span>', $this->acf_field['prepend'] );
		}

		$output .= parent::render_input( array( 'type' => 'range', ), $is_quickedit );
		$len = strlen( (string) $this->acf_field['max'] );
		$output .= '<input '. acf_esc_attr( array(
				'type'					=> 'number',
				'id'					=> $this->acf_field['key'] . '-alt',
				'data-acf-field-key'	=> $this->acf_field['key'],
				'step'					=> $this->acf_field['step'],
				'style'					=> 'width: ' . (1.8 + $len*0.7) . 'em;'
			) ) .' />';
		if ( $this->acf_field['append'] ) {
			$output .= sprintf( '<span class="append">%s</span>', $this->acf_field['append'] );
		}

		return $output;
	}

}
