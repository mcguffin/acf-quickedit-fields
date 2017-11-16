<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PasswordField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$output = '';
		/*
		$value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id );
		//*/
		if ( $value ) {
			$output .= '<code>********</code>';
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += array(
			'class'			=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'		=> 'password',
			'autocomplete'	=> 'false',
			'readonly'		=> 'readonly',
			'onfocus'		=> 'this.removeAttribute(\'readonly\');',
			'onblur'		=> 'this.setAttribute(\'readonly\',\'readonly\');',
		);
		return '<input '. acf_esc_attr( $input_atts ) .' />';

	}


}
