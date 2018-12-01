<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class SelectField extends ChoiceField {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$output = '';
		$input_atts += array(
			'class' => 'acf-quick-edit widefat',
			'id' => $this->core->prefix( $this->acf_field['key'] ),
		);

		if ( $this->acf_field['multiple'] ) {
			$input_atts['multiple'] = 'multiple';
			$input_atts['name']	.= '[]';
			if ( $this->acf_field['ui'] ) {
				$input_atts['class'] .= ' ui';
			}
		}

		$output .= sprintf( '<select %s>', acf_esc_attr( $input_atts ) );

		if ( ! $this->acf_field['multiple'] && $this->acf_field['allow_null'] ) {
			$output .= sprintf('<option value="">%s</option>', __( '— Select —', 'acf-quick-edit-fields' ) );
		}

		foreach($this->acf_field['choices'] as $name => $label) {
			$output .= sprintf('<option value="%s">%s</option>', esc_attr( $name ), acf_esc_html( $label ) );
		}

		$output .= '</select>';

		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return ! $this->acf_field['multiple'];
	}

}
