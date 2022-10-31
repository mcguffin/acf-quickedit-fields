<?php

namespace ACFQuickEdit\Fields\Traits;

trait BulkOperationLists {

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		if ( $this->acf_field['multiple'] ) {
			return [
				'union'        => __( 'Merge', 'acf-quickedit-fields' ),
				'difference'   => __( 'Remove', 'acf-quickedit-fields' ),
				'intersection' => __( 'Overlap', 'acf-quickedit-fields' ),
			];
		}
		return [];
	}

	/**
	 *	@inheritdoc
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {

		$old_value = (array) $this->get_value( $object_id, false );

		if ( 'union' === $operation ) {
			$value = array_unique( array_merge( $old_value, $new_value ) );
		} else if ( 'difference' === $operation ) {
			$value = array_diff( (array) $old_value, (array) $new_value );
		} else if ( 'intersection' === $operation ) {
			$value = array_intersect( (array) $old_value, (array) $new_value );
		} else {
			$value = $new_value;
		}

		if ( ! count( $value ) ) {
			$value = null;
		}

		return array_values($value);
	}

	/**
	 *	@inheritdoc
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $operation ) {
		return is_array($new_value);
	}
}
