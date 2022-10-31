<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class SelectField extends ChoiceField {

	use Traits\BulkOperationLists;
	use Traits\InputSelect;

	/**
	 *	@inheritdoc
	 */
	protected function get_wrapper_attributes( $wrapper_attr, $is_quickedit = true ) {
		$wrapper_attr['data-ajax'] = isset( $this->acf_field['ajax'] )
			? $this->acf_field['ajax']
			: '0';
		$wrapper_attr['data-multiple'] = isset( $this->acf_field['multiple'] )
			? $this->acf_field['multiple']
			: '0';
		return $wrapper_attr;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {

		return $this->render_select_input( $input_atts, $this->acf_field, $is_quickedit );
	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return ! $this->acf_field['multiple'];
	}
}
