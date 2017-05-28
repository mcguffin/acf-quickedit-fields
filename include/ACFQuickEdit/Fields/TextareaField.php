<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TextareaField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		return sprintf( '<pre>%s</pre>', get_field( $this->acf_field['key'], $object_id, true ) );

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += array(
			'class'	=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'	=> 'text', 
		);

		return '<textarea '. acf_esc_attr( $input_atts ) .'></textarea>';

	}


}