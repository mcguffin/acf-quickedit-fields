<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PasswordField extends Field {

	public static $quickedit = true;

	public static $bulkedit = false;
	
	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$output = '';
		if ( $field_value = get_field( $this->acf_field['key'], $object_id ) ) {
			$output .= '<code>********</code>';
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		$input_atts += array(
			'class'	=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'	=> 'password', 
			'autocomplete'	=> 'false',
			'readonly'	=> 'readonly',
			'onfocus' => 'this.removeAttribute(\'readonly\');'
		);
		echo '<input '. acf_esc_attr( $input_atts ) .' />';

	}


}