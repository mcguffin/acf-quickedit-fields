<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TimePickerField extends DateTimePickerField {


	public static $quickedit = true;

	public static $bulkedit = true;
	
	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column ) {
		$wrap_atts = array(
			'class'				=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'data-time_format'	=> acf_convert_time_to_js($this->acf_field['display_format']),
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
		echo $output;
	}


}