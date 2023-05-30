<?php

namespace ACFQuickEdit\Fields\Traits;

use ACFQuickEdit\Admin;

trait Filter {

	/**
	 *	@param int $index
	 *	@param string $selected
	 *	@param boolean $is_multiple
	 *	@param array $choices
	 *	@return string
	 */
	protected function render_filter_dropdown( $index, $selected = '', $is_multiple = false, $choices = [] ) {


		$out = '';

		if ( ! count( $choices ) ) {
			return $out;
		}

		$none_value = Admin\Filters::instance()->get_none_value();

		$out .= sprintf( '<input type="hidden" name="meta_query[%d][key]" value="%s" />', $index, $this->get_meta_key() ) . PHP_EOL;
		if ( $is_multiple ) {
			$selected = trim( $selected, '"' );
			$out .= sprintf(
				'<input type="hidden" name="meta_query[%d][compare]" value="LIKE" />',
				$index
			) . PHP_EOL;
		}
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

		$out .= sprintf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $none_value ),
			$selected === $none_value
				? 'selected'
				: '',
			$this->__no_value()
		) . PHP_EOL;

		foreach ( $choices as $value => $label ) {

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
		$out .= '</select>' . PHP_EOL;

		return $out;
	}

	/**
	 *	@param int $index
	 *	@param string $selected
	 *	@param boolean $is_multiple
	 *	@param array $choices
	 *	@return string
	 */
	protected function render_term_filter_dropdown( $selected = '', $choices = [] ) {

		$out = '';

		if ( ! count( $choices ) ) {
			return $out;
		}

		$none_value = Admin\Filters::instance()->get_none_value();

		$out .= sprintf( '<select name="%s">', $this->acf_field['taxonomy'] ) . PHP_EOL;
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

		$out .= sprintf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $none_value ),
			$selected === $none_value
				? 'selected'
				: '',
			$this->__no_value()
		) . PHP_EOL;

		foreach ( $choices as $value => $label ) {

			$out .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $value ),
				$selected === $value
					? 'selected'
					: '',
				esc_html( $label )
			) . PHP_EOL;
		}
		$out .= '</select>' . PHP_EOL;

		return $out;
	}
}
