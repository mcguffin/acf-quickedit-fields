<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class SelectField extends ChoiceField {

	use Traits\BulkOperationLists;
	use Traits\InputSelect;
	use Traits\ColumnLists;
	use Traits\Filter;

	/**
	 *	@inheritdoc
	 */
	public function render_filter( $index, $selected = '' ) {

		return $this->render_filter_dropdown(
			$index,
			$selected,
			isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple'],
			$this->acf_field['choices']
		);
	}

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {
		return $this->render_list_column(
			$object_id,
			isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple']
		);
	}

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
