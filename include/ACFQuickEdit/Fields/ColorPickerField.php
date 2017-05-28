<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ColorPickerField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$value = get_field( $this->acf_field['key'], $object_id );
		if ( $value ) {
			$output = '<div class="color-indicator" style="border-radius:2px;border:1px solid #d2d2d2;width:26px;height:20px;background-color:'.$value.'"></div>';
		} else {
			$output = __('(No value)', 'acf-quick-edit-fields');
		}
		return $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += array(
			'class'	=> 'wp-color-picker acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'	=> 'text', 
		);

		return parent::render_input( $input_atts );// '<input '. acf_esc_attr( $input_atts ) .' />';

	}


}