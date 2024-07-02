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
				$input_atts['data-nonce'] = wp_create_nonce( $acf_field['key'] );
				$input_atts['data-query-nonce'] = wp_create_nonce( $acf_field['key'] ); // backwards compatibility ACF < 6.3.1
			}
		}

		$output .= sprintf( '<select %s>', acf_esc_attr( $input_atts ) ) . PHP_EOL;

		if ( ! $acf_field['ajax'] ) {

			if ( ! $acf_field['multiple'] && $acf_field['allow_null'] ) {
				$output .= sprintf('<option value="">%s</option>', __( '— Select —', 'acf-quickedit-fields' ) ) . PHP_EOL;
			}

			$output .= $this->render_select_options( $acf_field['choices'], null, (boolean) $acf_field['multiple'] );

		}

		$output .= '</select>' . PHP_EOL;

		return $output;
	}

	/**
	 *	@param array $choices
	 *	@param string $out
	 *	@param string $selected
	 *	@param boolean $is_multiple
	 */
	protected function render_select_options( $choices, $selected, $is_multiple = false ) {
		$out = '';
		foreach ( $choices as $value => $label ) {
			if ( is_array( $label ) ) {
				$out .= sprintf( '<optgroup label="%s">', esc_attr( $value ) ) . PHP_EOL;
				$out .= $this->render_select_options( $label, $selected, $is_multiple ) . PHP_EOL;
				$out .= '</optgroup>' . PHP_EOL ;
			} else {
				$value = $is_multiple
					? serialize( trim( "{$value}" ) ) // prepare value for LIKE comparision in serialize array
					: "{$value}";

				$out .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					$selected === $value
						? 'selected'
						: '',
					esc_html( $label )
				) . PHP_EOL;
			}
		}
		return $out;
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
			if ( $this->acf_field['allow_custom'] ) {
				return [
					'id'   => sanitize_text_field( $value ),
					'text' => sanitize_text_field( $value ),
				];
			} else {
				return '';
			}
		}

		return [
			'id'   => $value,
			'text' => $this->acf_field['choices'][ $value ],
		];
	}
}
