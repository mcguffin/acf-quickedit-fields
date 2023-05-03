<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ColorPickerField extends Field {

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		$value = $this->get_value( $object_id );

		$indicator_class = 'acf-qef-color-indicator';

		if ( ! $value ) {
			$indicator_class .= ' no-value';
			$value = 'rgba(255,255,255,0)';
		}
		if ( is_array( $value ) ) {
			$value = sprintf(
				'rgba(%d,%d,%d,%f)',
				$value['red'],
				$value['green'],
				$value['blue'],
				$value['alpha']
			);
		}

		return sprintf(
			'<div class="%s" style="background-color:%s" data-bg-color="%s"></div>',
			sanitize_html_class( $indicator_class ),
			esc_attr( $value ),esc_attr( $value )
		);
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += [
			'class'	=> 'wp-color-picker acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'	=> 'text',
		];
		if ( isset( $this->acf_field['enable_opacity'] ) && $this->acf_field['enable_opacity'] ) {
			$input_atts['data-alpha-enabled'] = true;
		}


		return parent::render_input( $input_atts );// '<input '. acf_esc_attr( $input_atts ) .' />';
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}
}
