<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class UrlField extends Field {

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

		if ( true === acf_validate_value( $value, $this->get_acf_field(), null ) ) {
			return esc_url_raw( $value );
		}
		return '';
	}
}
