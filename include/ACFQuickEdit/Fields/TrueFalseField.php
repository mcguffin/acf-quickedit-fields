<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TrueFalseField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$ui = isset( $this->acf_field['ui'] ) && $this->acf_field['ui'];
		$yes = $ui && isset( $this->acf_field['ui_on_text'] ) && $this->acf_field['ui_on_text']
			? acf_esc_html( $this->acf_field['ui_on_text'] )
			: __( 'Yes', 'acf' );
		$no = $ui && isset( $this->acf_field['ui_off_text'] ) && $this->acf_field['ui_off_text']
			? acf_esc_html( $this->acf_field['ui_off_text'] )
			: __( 'No', 'acf' );
		return $this->get_value( $object_id ) ? $yes : $no;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$field_key = $this->acf_field['key'];

		$ui = isset( $this->acf_field['ui'] ) && $this->acf_field['ui'];

		$prefix_column = $this->core->prefix( $field_key );
		$output = '';
		$output .= sprintf( '<ul class="acf-radio-list" data-acf-field-key="%s">', $field_key );
		$output .= sprintf( '<li><label for="%s-yes">', $prefix_column );
		$output .= sprintf( '<input id="%s-yes" type="radio" value="1" class="acf-quick-edit" data-acf-field-key="%s" name="%s" />',
								$prefix_column,
								$field_key,
								$input_atts['name']
							);

		$output .= $ui && isset( $this->acf_field['ui_on_text'] ) && $this->acf_field['ui_on_text']
			? acf_esc_html( $this->acf_field['ui_on_text'] )
			: __( 'Yes', 'acf' );

		$output .= '</label></li>';

		$output .= sprintf( '<li><label for="%s-no">', $prefix_column );
		$output .= sprintf( '<input id="%s-no" type="radio" value="0" class="acf-quick-edit" data-acf-field-key="%s" name="%s" />',
								$prefix_column,
								$field_key,
								$input_atts['name']
							);

		$output .= $ui && isset( $this->acf_field['ui_off_text'] ) && $this->acf_field['ui_off_text']
			? acf_esc_html( $this->acf_field['ui_off_text'] )
			: __( 'No', 'acf' );

		$output .= '</label></li>';
		$output .= '</ul>';

		return $output;
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

		$choices = [
			'1' => $ui && isset( $this->acf_field['ui_on_text'] ) && $this->acf_field['ui_on_text']
				? acf_esc_html( $this->acf_field['ui_on_text'] )
				: __( 'Yes', 'acf' ),
			'0' => $ui && isset( $this->acf_field['ui_off_text'] ) && $this->acf_field['ui_off_text']
				? acf_esc_html( $this->acf_field['ui_off_text'] )
				: __( 'No', 'acf' ),
		];

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
	 *	@param mixed $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		return boolval( $value );
	}


}
