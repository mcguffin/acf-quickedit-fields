<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class ChoiceField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$output				= '';
		$is_multiple 		= ( isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple'] ) || $this->acf_field['type'] === 'checkbox';
		$is_return_array	= 'array' === $this->acf_field['return_format'];

		$field_value = $this->get_value( $object_id );

		if ( '' === $field_value ) {
			$field_value = [];
		}
		if ( is_string( $field_value ) || ( $is_return_array && isset( $field_value['value'] ) ) ) {
			$field_value = [ $field_value ];
		}

		$values = [];

		if ( empty( $field_value ) ) {
			$field_value = [];
		}
		if ( ! is_array( $field_value ) ) {
			$field_value = [ $field_value ];
		}

		foreach ( $field_value as $value ) {

			if ( $is_return_array ) {
				$values[] = sprintf('%s =&gt; %s', $value['value'], $value['label'] );
			} else {
				if ( isset( $this->acf_field['choices'][ $value ] ) ) {
					$value = $this->acf_field['choices'][ $value ];
				}
				$values[] = $value;
			}
		}

		if ( empty( $values ) ) {
			$output .= '<p>';
			$output .= __('(No value)', 'acf-quickedit-fields');
			$output .= '</p>';
		} else {
			if ( $is_multiple ) {
				$output .= sprintf( '<ol class="acf-qef-value-list" data-count-values="%d">', count( $values ) );
				foreach ( $values as $val ) {
					$output .= sprintf( '<li>%s</li>', acf_esc_html( $val ) ); //implode( __(', ', 'acf-quickedit-fields' ) , $values );
				}
				$output .= '</ol>';
			} else {
				foreach ( $values as $val ) {
					$output .= sprintf( '<div class="qef-choice">%s</div>', esc_html( $val ) );
				}
			}
		}
		return $output;

	}

	/**
	 *	@param int $index
	 */
	public function render_filter( $index, $selected = '' ) {

		$is_multiple = ( isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple'] ) || $this->acf_field['type'] === 'checkbox';

		if ( $is_multiple ) {
			$selected = trim( $selected, '"' );
		}

		$out = '';
		$out .= sprintf( '<input type="hidden" name="meta_query[%d][key]" value="%s" />', $index, esc_attr($this->acf_field['name']) ) . PHP_EOL;
		$out .= sprintf( '<select name="meta_query[%d][value]">', $index ) . PHP_EOL;
		$out .= sprintf(
			'<option value="" %s>%s</option>',
			$selected === ''
				? 'selected'
				: '',
			esc_html(
				sprintf(
					/* translators: acf field label */
					__( '— %s —', 'acf-quickedit-fields' ),
					$this->acf_field['label']
				)
			)
		) . PHP_EOL;

		foreach ( $this->acf_field['choices'] as $value => $label ) {

			$out .= sprintf(
				'<option value="%s" %s>%s</option>',
				$is_multiple
					? esc_attr(sprintf('"%s"', $value ))
					: esc_attr( $value ),
				$selected === $value
					? 'selected'
					: '',
				esc_html( $label )
			) . PHP_EOL;
		}
		$out .= '</select>' . PHP_EOL;

		if ( $is_multiple ) {
			$out .= sprintf(
				'<input type="hidden" name="meta_query[%d][compare]" value="LIKE" />',
				$index
			) . PHP_EOL;
		}

		return $out;
	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		if ( is_array( $value ) ) {
			return $this->sanitize_strings_array( array_values( $value ), $context );
		} else {
			return sanitize_text_field( $value );
		}
	}


}
