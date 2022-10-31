<?php

namespace ACFQuickEdit\Fields\Traits;

trait InputCheckbox {

	/**
	 *	Render Input element
	 *
	 *	@param array $input_attr
	 *	@param bool $is_quickedit
	 *
	 *	@return string
	 */
	public function render_checkbox_input( $input_atts, $acf_field, $is_quickedit = true ) {
		$output = '';

		$acf_field = wp_parse_args( $acf_field, [
			'choices' => [],
			'toggle' => 0,
			'allow_custom' => 0,
		]);

		// populate $_POST if nothing is selected
		// in bulk this breaks the 'do-not-change' checkbox
		if ( $is_quickedit ) {
			$output .= sprintf( '<input %s />', acf_esc_attr( [
				'type' => 'hidden',
				'name' => $input_atts['name'],
			] ) );
		}

		$output .= sprintf( '<ul class="acf-checkbox-list" data-acf-field-key="%s">', sanitize_key( $acf_field['key'] ) );

		if ( $acf_field['toggle'] ) {
			$output .= sprintf( '<li><label><input %s/>%s</label></li>', acf_esc_attr( [
				'class' => 'acf-checkbox-toggle',
				'type'  => 'checkbox',
			] ), esc_html__( ' Toggle All', 'acf-quickedit-fields' ) );
		}


		$input_atts		+= [
			'class' => 'acf-quick-edit',
			'id'    => $this->core->prefix( $acf_field['key'] ),
		];
		$acf_field['value']	= acf_get_array( $acf_field['value'], false );

		$field_name = $input_atts['name'] . '[]';

		foreach ( $acf_field['choices'] as $value => $label ) {
			$atts = [
				'data-acf-field-key' => $acf_field['key'],
				'type'               => 'checkbox',
				'value'              => $value,
				'name'               => $field_name,
				'id'                 => $this->core->prefix( $acf_field['key'] . '-'.$value ),
			];
			if ( ! $is_quickedit ) {
				$atts['disabled'] = 'disabled';
			}

			if ( in_array( $value, $acf_field['value'] ) ) {
				$atts['checked'] = 'checked';
			}
			$output .= sprintf( '<li><label><input %s/>%s</label></li>', acf_esc_attr( $atts ), acf_esc_html( $label ) );
		}

		$output .= '</ul>';

		if ( $acf_field['allow_custom'] ) {
			$output .= '<button class="button button-seconday add-choice">' . __('Add Choice','acf-quickedit-fields') . '</button>';
			$output .= sprintf( '<script type="text/html" id="tmpl-acf-qef-custom-choice-%s">', $acf_field['key'] );

			$id = $this->core->prefix( $acf_field['key'] . '-other' );

			$output .= '<li><label>';
			$output .= sprintf( '<input %s />', acf_esc_attr( [
				'type'    => 'checkbox',
				'class'   => 'acf-quick-edit custom',
				'checked' => 'checked',
			] ) );
			$output .= sprintf( '<input %s />', acf_esc_attr( [
				'type'               => 'text',
				'class'              => 'acf-quick-edit',
				'data-acf-field-key' => $acf_field['key'],
				'name'               => $field_name,
			] ) );

			$output .= '</label></li>';

			$output .= '</script>';
			$output .= sprintf( '<script type="text/html" id="tmpl-acf-qef-custom-choice-value-%s">', $acf_field['key'] );
			$output .= sprintf(
				'<li><label><input data-acf-field-key="%1$s" type="checkbox" value="{{ data.value }}" name="%2$s" id="%3$s-{{ data.value.toLowerCase() }}">{{ data.value }}</label></li>',
				$acf_field['key'],
				$field_name,
				$this->core->prefix( $acf_field['key'] )
			);
			$output .= '</script>';
		}

		return $output;
	}
}
