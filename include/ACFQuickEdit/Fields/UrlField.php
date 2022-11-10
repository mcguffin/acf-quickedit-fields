<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

use ACFQuickEdit\Admin;

class UrlField extends Field {

	use Traits\BulkOperationURL;

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {
		$output = '';
		$output .= '<span class="acf-url">';
		$output .= '<i class="acf-icon -globe small"></i>';
		$output .= parent::render_input( [ 'type' => 'url' ], $is_quickedit );
		$output .= '</span>';
		return $output;
	}
	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		if ( true === acf_validate_value( $value, $this->acf_field, null ) ) {
			return esc_url_raw( $value );
		}
		return '';
	}

	/**
	 *	Validate value for Bulk operation
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $input) {
		if ( Admin\Bulkedit::instance()->is_bulk_operation( $field['key'] ) ) {
			$valid = true;
		}
		return $valid;
	}
}
