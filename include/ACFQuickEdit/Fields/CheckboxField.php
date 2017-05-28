<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class CheckboxField extends ChoiceField {


	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$output = '';
		$output .= sprintf( '<ul class="acf-checkbox-list" data-acf-field-key="%s">', $this->acf_field['key'] );

		$input_atts		+= array(
			'class'	=> 'acf-quick-edit',
			'id'	=> $this->core->prefix( $this->acf_field['key'] ),
		);
		$this->acf_field['value']	= acf_get_array( $this->acf_field['value'], false );

		$field_name = sprintf( 'acf[%s][]', $this->acf_field['key'] );

		if ( ! $is_quickedit ) {
			$output .= sprintf( '<li><label><input %s/>%s</label></li>', acf_esc_attr( array(
				'data-acf-field-key'	=> $this->acf_field['key'],
				'type'					=> 'checkbox',
				'value'					=> $this->dont_change_value,
				'name'					=> $field_name,
				'id'					=> $this->core->prefix( $this->acf_field['key'] . '-'.$this->dont_change_value ),
				'checked'				=> 'checked',
				'data-is-do-not-change'	=> 'true',
			) ), __( '— No Change —', 'acf-quick-edit-fields' ) );
		}

		foreach ( $this->acf_field['choices'] as $value => $label ) {
			$atts = array(
				'data-acf-field-key'	=> $this->acf_field['key'],
				'type'					=> 'checkbox',
				'value'					=> $value,
				'name'					=> $field_name,
				'id'					=> $this->core->prefix( $this->acf_field['key'] . '-'.$value ),
			);
			if ( ! $is_quickedit ) {
				$atts['disabled'] = 'disabled';
			}

			if ( in_array( $value, $this->acf_field['value'] ) ) {
				$atts['checked'] = 'checked';
			}
			$output .= sprintf( '<li><label><input %s/>%s</label></li>', acf_esc_attr( $atts ), $label );
		}
		$output .= '</ul>';

		return $output;
	}

	
	/**
	 *	@inheritdoc
	 */
	public function update( $post_id, $is_quickedit = true ) {

		$param_name = $this->acf_field['key'];

		if ( isset( $_REQUEST['acf'][ $param_name ] ) ) {
			$value = $_REQUEST['acf'][ $param_name ];
		} else {
			$value = null;
		}

		if ( in_array( $this->dont_change_value, (array) $value ) ) {
			return;
		}

		update_field( $this->acf_field['key'], $value, $post_id );

	}


}