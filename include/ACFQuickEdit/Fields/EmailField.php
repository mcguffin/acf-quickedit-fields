<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class EmailField extends Field {

	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {

		return parent::render_input( array( 'type' => 'email', ), $is_quickedit );

	}
	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}


}
