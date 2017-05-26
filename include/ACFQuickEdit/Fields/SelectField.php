<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class SelectField extends Field {

	public static $quickedit = true;

	public static $bulkedit = true;
	
	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$field_value = get_field( $this->acf_field['key'], $object_id );

		$values = array();

		foreach ( (array) $field_value as $value ) {
			$values[] = isset( $this->acf_field['choices'][ $value ] ) 
							? $this->acf_field['choices'][ $value ] 
							: $value;
		}		

		$output = implode( __(', ', 'acf-quick-edit-fields' ) , $values );

		if ( empty( $output ) ) {
			$output = __('(No value)', 'acf-quick-edit-fields');
		}

		echo $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		$output = '';
		$input_atts += array(
			'class' => 'acf-quick-edit widefat',
			'id' => $this->core->prefix( $column ),
		);

		if ( $this->acf_field['multiple'] ) {
			$input_atts['multiple'] = 'multiple';
		}

		$output .= sprintf( '<select %s>', acf_esc_attr( $input_atts ) );

		if ( ! $is_quickedit ) {
			$output .= sprintf('<option value="%s" selected="selected">%s</option>', 
				$this->dont_change_value, 
				__( '— No Change —', 'acf-quick-edit-fields' )
			);
		}

		if ( $this->acf_field['allow_null'] ) {
			$output .= sprintf('<option value="">%s</option>', __( '— Select —', 'acf-quick-edit-fields' ) );
		}

		foreach($this->acf_field['choices'] as $name => $label) {
			$output .= sprintf('<option value="%s">%s</option>', esc_attr( $name ), $label );
		}

		$output .= '</select>';

		echo $output;
	}


}