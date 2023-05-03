<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class CheckboxField extends ChoiceField {

	use Traits\BulkOperationLists;
	use Traits\InputCheckbox;
	use Traits\ColumnLists;
	use Traits\Filter;

	/**
	 *	@inheritdoc
	 */
	public function render_filter( $index, $selected = '' ) {

		return $this->render_filter_dropdown(
			$index,
			$selected,
			true,
			$this->acf_field['choices']
		);
	}

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		return $this->render_list_column(
			$object_id,
			true
		);

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return $this->render_checkbox_input( $input_atts, $this->acf_field, $is_quickedit );
	}

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		return [
			'union'        => __( 'Merge', 'acf-quickedit-fields' ),
			'difference'   => __( 'Remove', 'acf-quickedit-fields' ),
			'intersection' => __( 'Overlap', 'acf-quickedit-fields' ),
		];
	}
}
