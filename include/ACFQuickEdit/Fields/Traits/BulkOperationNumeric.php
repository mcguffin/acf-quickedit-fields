<?php

namespace ACFQuickEdit\Fields\Traits;

trait BulkOperationNumeric {

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		return [
			'add'      => __('+','acf-quickedit-fields'),
			'subtract' => __('&minus;','acf-quickedit-fields'),
			'multiply' => __('&times;','acf-quickedit-fields'),
			'divide'   => __('&divide;','acf-quickedit-fields'),
		];
	}

	/**
	 *	@inheritdoc
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {

		$old_value = $this->get_value( $object_id, false );
		$new_value = floatval( $new_value );

		if ( 'add' === $operation ) {
			$value = $old_value + $new_value;
		} else if ( 'subtract' === $operation ) {
			$value = $old_value - $new_value;
		} else if ( 'multiply' === $operation ) {
			$value = $old_value * $new_value;
		} else if ( 'divide' === $operation ) {
			if ( $new_value !== 0 ) {
				// Division by zero
				$value = $old_value / $new_value;
			} else {
				$value = $old_value;
			}
		} else {
			$value = $new_value;
		}

		return $this->sanitize_value( $value );
	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		// check min/max/step
		if ( $this->acf_field['min'] ) {
			$value = max( $this->acf_field['min'], $value );
		}
		if ( $this->acf_field['max'] ) {
			$value = min( $this->acf_field['max'], $value );
		}
		if ( $this->acf_field['step'] ) {
			$value = round( $value / $this->acf_field['step'] ) * $this->acf_field['step'];
		}
		return $value;
	}

	/**
	 *	@inheritdoc
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $operation ) {
		if ( 'add' === $operation ) {
		} else if ( 'subtract' === $operation ) {
		} else if ( 'multiply' === $operation ) {
		} else if ( 'divide' === $operation ) {
			// everybody loves division by zero
			return floatval( $new_value ) !== 0;
		}
		return $valid;
	}
}
