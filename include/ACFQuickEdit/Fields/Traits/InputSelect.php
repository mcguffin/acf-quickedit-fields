<?php

namespace ACFQuickEdit\Fields\Traits;

trait InputSelect {

	/**
	 *	Render Input element
	 *
	 *	@param array $input_attr
	 *	@param bool $is_quickedit
	 *
	 *	@return string
	 */
	public function render_select_input( $input_atts, $acf_field, $is_quickedit = true ) {
		$output = '';

		$acf_field = wp_parse_args( $acf_field, [
			'choices' => [],
			'other_choice' => 0,
		]);

		$input_atts += [
			'class' => 'acf-quick-edit widefat',
			'id' => $this->core->prefix( $acf_field['key'] ),
			'data-ui'			=> $acf_field['ui'],
			'data-ajax'			=> $acf_field['ajax'],
			'data-type'			=> $acf_field['type'],//'select',
			'data-multiple'		=> $acf_field['multiple'],
			'data-allow_null'	=> $acf_field['allow_null'],
		];

		$output .= acf_get_hidden_input( [
			'name'	=> $input_atts['name'],
		]);

		if ( $acf_field['multiple'] ) {
			$input_atts['multiple'] = 'multiple';
			$input_atts['name']	.= '[]';
			if ( $acf_field['ui'] ) {
				$input_atts['class'] .= ' ui';
			}
		}

		$output .= sprintf( '<select %s>', acf_esc_attr( $input_atts ) ) . PHP_EOL;

		if ( ! $acf_field['ajax'] ) {

			if ( ! $acf_field['multiple'] && $acf_field['allow_null'] ) {
				$output .= sprintf('<option value="">%s</option>', __( '— Select —', 'acf-quickedit-fields' ) ) . PHP_EOL;
			}

			foreach( $acf_field['choices'] as $name => $label) {
				$output .= sprintf('<option value="%s">%s</option>', esc_attr( $name ), acf_esc_html( $label ) ) . PHP_EOL;
			}
		}

		$output .= '</select>' . PHP_EOL;

		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		$sanitation_cb = $context === 'ajax'
			? [ $this, 'sanitize_ajax_result' ]
			: 'sanitize_text_field';

		if ( is_array( $value ) ) {
			$value = array_map( $sanitation_cb, $value );
			$value = array_filter( $value );
			return array_values( $value );
		}

		return parent::sanitize_value( $value, $context );
	}

	/**
	 *	Format result data for select2
	 *
	 *	@param mixed $value
	 *	@return string|array If value present and post exists Empty string
	 */
	protected function sanitize_ajax_result( $value ) {

		// bail if post doesn't exist
		if ( ! isset( $this->acf_field['choices'][ $value ] ) ) {
			return '';
		}

		return [
			'id'	=> $value,
			'text'	=> $this->acf_field['choices'][ $value ],
		];
	}
}
