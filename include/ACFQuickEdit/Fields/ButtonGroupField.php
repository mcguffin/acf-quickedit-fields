<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ButtonGroupField extends RadioField {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {

		$output = '';

		if ( $this->acf_field['allow_null'] ) {
			$output .= sprintf( '<input %s />', acf_esc_attr( [
				'type'					=> 'hidden',
				'name'					=> $input_atts['name'],
			] ) );
		}

		$output .= parent::render_input( $input_atts, $is_quickedit );

		return $output;
	}
}
