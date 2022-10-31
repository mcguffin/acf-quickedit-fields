<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class CheckboxField extends ChoiceField {

	use Traits\BulkOperationLists;
	use Traits\InputCheckbox;

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
