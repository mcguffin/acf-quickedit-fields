<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Generic extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) { }

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) { }

	/**
	 *	@inheritdoc
	 */
	public function update( $post_id ) {
	}
}
