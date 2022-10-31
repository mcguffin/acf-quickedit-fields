<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class EmailField extends Field {

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {
		return parent::render_input( [ 'type' => 'email' ], $is_quickedit );
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
		return sanitize_email( $value );
	}
}
