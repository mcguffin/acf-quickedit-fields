<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class OembedField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$value = get_field( $this->acf_field['key'], $object_id, false );
		return sprintf( '<a href="%s">%s</a>', $value, parse_url( $value, PHP_URL_HOST ) );
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return '';
	}


}