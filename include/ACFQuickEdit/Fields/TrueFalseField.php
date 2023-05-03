<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TrueFalseField extends Field {

	use Traits\InputRadio;
	use Traits\Filter;

	/**
	 *	@inheritdoc
	 */
	public function render_filter( $index, $selected = '' ) {

		return $this->render_filter_dropdown(
			$index,
			$selected,
			false,
			$this->get_choices()
		);
	}

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

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
