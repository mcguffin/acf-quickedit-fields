<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RadioField extends ChoiceField {

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
			$this->acf_field['choices']
		);
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {

		return $this->render_radio_input( $input_atts, $this->acf_field, $is_quickedit );

	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}
}
