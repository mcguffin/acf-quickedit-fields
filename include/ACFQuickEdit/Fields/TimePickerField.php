<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TimePickerField extends DateTimePickerField {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$wrap_atts = [
			'class'				=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'data-time_format'	=> acf_convert_time_to_js($this->acf_field['display_format']),
		];
		$display_input_atts	= [
			'type'	=> 'text',
		];
		$input_atts += [
			'type'	=> 'hidden',
		];
		$output = '';

		$output .= '<span '. acf_esc_attr( $wrap_atts ) .'>';
		$output .= '<input '. acf_esc_attr( $input_atts ) .' />';
		$output .= '<input '. acf_esc_attr( $display_input_atts ) .' />';
		$output .= '</span>';
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}
}
