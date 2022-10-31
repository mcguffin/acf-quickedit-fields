<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class DatePickerField extends DateTimePickerField {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$wrap_atts = [
			'class'				=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'data-date_format'	=> acf_convert_date_to_js($this->acf_field['display_format']),
			'data-first_day'	=> $this->acf_field['first_day'],
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
