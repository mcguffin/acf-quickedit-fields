<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class CheckboxField extends Field {

	public static $quickedit = true;

	public static $bulkedit = true;

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$field_value = get_field( $this->acf_field['key'], $object_id );

		$values = array();

		foreach ( (array) $field_value as $value )
			$values[] = isset( $this->acf_field['choices'][ $value ] ) 
							? $this->acf_field['choices'][ $value ] 
							: $value;
		
		$output = implode( __(', ', 'acf-quick-edit-fields' ) , $values );

		if ( empty( $output ) )
			$output = __('(No value)', 'acf-quick-edit-fields');

		return $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column ) {
		?><ul class="acf-checkbox-list" data-acf-field-key="<?php echo $this->acf_field['key'] ?>"><?php
		$input_atts		+= array(
			'class'	=> 'acf-quick-edit',
			'id'	=> $this->core->prefix( $column ),
		);
		$this->acf_field['value']	= acf_get_array( $this->acf_field['value'], false );
		foreach ( $this->acf_field['choices'] as $value => $label ) {
			$atts = array(
				'data-acf-field-key'	=> $this->acf_field['key'],
				'type'					=> 'checkbox',
				'value'					=> $value,
				'name'					=> $this->core->prefix( $column . '[]' ),
				'id'					=> $this->core->prefix( $column . '-'.$value ),
			);

			if ( in_array( $value, $this->acf_field['value'] ) ) {
				$atts['checked'] = 'checked';
			}
			echo '<li><label><input ' . acf_esc_attr( $atts ) . '/>' . $label . '</label></li>';
		}
		?></ul><?php

	}


}