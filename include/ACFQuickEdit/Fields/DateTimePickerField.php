<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class DateTimePickerField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$val = get_field( $this->acf_field['key'], $object_id, false );
		return acf_format_date( $val, $this->acf_field['display_format'] );

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$formats = acf_split_date_time($this->acf_field['display_format']);
		$wrap_atts = array(
			'class'				=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'data-date_format'	=> acf_convert_date_to_js($formats['date']),
			'data-time_format'	=> acf_convert_time_to_js($formats['time']),
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
	public function get_value( $post_id ) {
		return acf_get_metadata( $post_id, $this->acf_field['name'] );
	}




}