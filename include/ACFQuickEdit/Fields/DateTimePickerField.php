<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class DateTimePickerField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$value = $this->get_value( $object_id, false );

		if ( is_null( $value ) ) {
			return __('(No value)', 'acf-quick-edit-fields');
		}

		return acf_format_date( $value, $this->acf_field['display_format'] );

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
	public function get_value( $post_id, $format_value = true ) {
		//*
		return acf_get_metadata( $post_id, $this->acf_field['name'] );
		$value = acf_format_date($value,$this->acf_field['display_format']);

		return $value;
		return acf_get_metadata( $post_id, $this->acf_field['name'] );
		/*/
		return get_field( $this->acf_field['name'], $post_id );
		//*/
	}


	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return 'datetime';
	}


}
