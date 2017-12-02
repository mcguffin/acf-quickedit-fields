<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class DatePickerField extends DateTimePickerField {


	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$wrap_atts = array(
			'class'				=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'data-date_format'	=> acf_convert_date_to_js($this->acf_field['display_format']),
			'data-first_day'	=> $this->acf_field['first_day'],
		);
		$display_input_atts	= array(
			'type'	=> 'text',
		);
		$input_atts += array(
			'type'	=> 'hidden',
		);

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

	/**
	 *	@inheritdoc
	 */
	public function get_value( $post_id, $format_value = true ) {
		$value = acf_get_metadata( $post_id, $this->acf_field['name'] );
		return acf_format_date( $value, 'Ymd' );
	}

}
