<?php

namespace ACFQuickEdit\Fields\Traits;

trait BulkOperationNone {

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		return [];
	}

	/**
	 *	@inheritdoc
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {
		return $new_value;
	}
}
