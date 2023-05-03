<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RelationshipField extends Field {

	use Traits\ColumnLists;
	use Traits\Filter;

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		return $this->render_list_column(
			$object_id,
			true,
			[ $this, 'render_list_column_item_value_post' ]
		);
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return '';
	}
}
