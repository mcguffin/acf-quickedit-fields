<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TextField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		if ( $value = $this->get_value( $object_id ) ) {
			return sprintf( '<pre>%s</pre>', esc_html( $value ) );
		}

		return '';

	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}

}
