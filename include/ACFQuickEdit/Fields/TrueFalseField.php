<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TrueFalseField extends Field {

	use Traits\InputRadio;

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$choices = $this->get_choices();
		$value = get_field( $this->acf_field['key'], $object_id, false );

		if ( is_string( $value ) ) {
			return $choices[ $value ];
		}

		return esc_html__('(No value)', 'acf-quickedit-fields');
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return $this->render_radio_input( $input_atts, [ 'choices' => $this->get_choices() ] + $this->acf_field, $is_quickedit );
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return 'unsigned';
	}

	/**
	 *	@param int $index
	 */
	public function render_filter( $index, $selected = '' ) {

		$ui = isset( $this->acf_field['ui'] ) && $this->acf_field['ui'];

		$choices = $this->get_choices();

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
					/* translators: acf field label, neutral */
					__( '— Select: %s —', 'acf-quickedit-fields' ),
					$this->acf_field['label']
				)
			)
		) . PHP_EOL;

		foreach ( $choices as $value => $label ) {
			$out .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $value ),
				$selected === "{$value}"
					? 'selected'
					: '',
				esc_html( $label )
			) . PHP_EOL;
		}
		$out .= '</select>' . PHP_EOL;

		return $out;
	}

	/**
	 *	@return array
	 */
	private function get_choices() {
		$choices = [
			'1' => __( 'Yes', 'acf' ),
			'0' => __( 'No', 'acf' ),
		];
		if ( isset( $this->acf_field['ui'] ) && $this->acf_field['ui'] ) {
			if ( isset( $this->acf_field['ui_on_text'] ) && $this->acf_field['ui_on_text'] ) {
				$choices['1'] = $this->acf_field['ui_on_text'];
			}
			if ( isset( $this->acf_field['ui_off_text'] ) && $this->acf_field['ui_off_text'] ) {
				$choices['0'] = $this->acf_field['ui_off_text'];
			}
		}
		return $choices;
	}

	/**
	 *	@param mixed $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		return boolval( $value );
	}
}
