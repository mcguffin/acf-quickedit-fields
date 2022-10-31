<?php

namespace ACFQuickEdit\Fields\Traits;

trait BulkOperationText {

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		return [
			'prefix' => __( 'Prepend', 'acf-quickedit-fields' ),
			'suffix' => __( 'Append', 'acf-quickedit-fields' ),
		];
	}

	/**
	 *	@inheritdoc
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {

		$old_value = $this->get_value( $object_id, false );

		if ( 'prefix' === $operation ) {
			$value = $new_value . $old_value;
		} else if ( 'suffix' === $operation ) {
			$value = $old_value . $new_value;
		} else {
			$value = $new_value;
		}

		return $this->sanitize_value( $value );
	}

	/**
	 *	@inheritdoc
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $operation ) {
		return is_string($new_value);
	}
}
