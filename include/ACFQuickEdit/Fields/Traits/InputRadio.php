<?php

namespace ACFQuickEdit\Fields\Traits;

trait InputRadio {

	/**
	 *	Render Input element
	 *
	 *	@param array $input_attr
	 *	@param bool $is_quickedit
	 *
	 *	@return string
	 */
	public function render_radio_input( $input_atts, $acf_field, $is_quickedit = true ) {

		$output = '';

		$acf_field = wp_parse_args( $acf_field, [
			'choices' => [],
			'other_choice' => 0,
		]);

		$output .= sprintf( '<ul class="acf-radio-list%s" data-acf-field-key="%s">',
			isset( $acf_field['other_choice'] ) && $acf_field['other_choice'] ? ' other' : '',
			$acf_field['key']
		);

		foreach( $acf_field['choices'] as $name => $value) {

			$id = $this->core->prefix( $acf_field['key'] . '-' . $name );

			$output .= sprintf( '<li><label for="%s">', $id );
			$output .= sprintf( '<input %s />%s', acf_esc_attr( [
				'id'					=> $id,
				'type'					=> 'radio',
				'value'					=> $name,
				'class'					=> 'acf-quick-edit',
				'data-acf-field-key'	=> $acf_field['key'],
				'name'					=> $input_atts['name'],
			] ), acf_esc_html( $value ) );

			$output .= '</label></li>';

		}

		if ( isset( $acf_field['other_choice'] ) && $acf_field['other_choice'] ) {

			$id = $this->core->prefix( $acf_field['key'] . '-other' );

			$output .= sprintf( '<li><label for="%s">', $id );
			$output .= sprintf( '<input %s />', acf_esc_attr( [
				'id'					=> $id,
				'type'					=> 'radio',
				'value'					=> 'other',
				'class'					=> 'acf-quick-edit',
				'data-acf-field-key'	=> $acf_field['key'],
				'name'					=> $input_atts['name'],
			] ) );
			$output .= sprintf( '<input %s />', acf_esc_attr( [
				'type'					=> 'text',
				'class'					=> 'acf-quick-edit',
				'data-acf-field-key'	=> $acf_field['key'],
				'name'					=> $input_atts['name'],
				// 'style'					=> 'width:initial',
				'disabled'				=> 'disabled',
			] ) );

			$output .= '</label></li>';
		}

		$output .= '</ul>';

		return $output;
	}
}
