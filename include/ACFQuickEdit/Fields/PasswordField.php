<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PasswordField extends Field {

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		$output = '';

		$value = $this->get_value( $object_id );

		if ( $value ) {
			$output .= '<code>********</code>';
		} else {
			$output .= $this->__no_value();
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += [
			'class'			=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'		=> 'password',
			'autocomplete'	=> 'false',
			'readonly'		=> 'readonly',
			'onfocus'		=> 'this.removeAttribute(\'readonly\');',
			'onblur'		=> 'this.setAttribute(\'readonly\',\'readonly\');',
		];
		return '<input '. acf_esc_attr( $input_atts ) .' />';

	}
}
