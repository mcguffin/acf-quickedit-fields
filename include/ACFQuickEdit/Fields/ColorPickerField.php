<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ColorPickerField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		/*
		$value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id );
		//*/
		$indicator_class = 'acf-qef-color-indicator';
		if ( ! $value ) {
			$indicator_class .= ' no-value';
			$value = 'rgba(255,255,255,0)';
		}

		return sprintf( '<div class="%s" style="background-color:%s"></div>', $indicator_class, $value );

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

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}

}
