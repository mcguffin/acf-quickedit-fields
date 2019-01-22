<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class NumberField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		/*
		$value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id );
		//*/

		$output = '';

		if ( $value === "" ) {
			$output .= __('(No value)', 'acf-quick-edit-fields');
		} else {
			$output .= number_format_i18n( floatval($value), strlen( substr( strrchr( $value, "." ), 1 ) ) ); // 
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += array(
			'class'	=> 'acf-quick-edit',
			'type'	=> 'number',
			'min'	=> $this->acf_field['min'],
			'max'	=> $this->acf_field['max'],
			'step'	=> $this->acf_field['step'],
		);

		return parent::render_input( $input_atts, $is_quickedit );
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return 'numeric';
	}

}
