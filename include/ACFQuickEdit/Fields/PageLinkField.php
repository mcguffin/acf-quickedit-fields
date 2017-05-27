<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PageLinkField extends Field {

	public static $quickedit = false;

	public static $bulkedit = false;
	


	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$value	= get_field( $this->acf_field['key'], $object_id );

		if ( is_null( $value ) ) {
			return __('(No value)', 'acf-quick-edit-fields');
		}

		if ( is_string( $value ) ) {
			return sprintf( '<a href="%s">%s</a>', $value, $value );
		}

		$output	= '';
		$output .= '<ol>';
		foreach ( $value as $link ) {
			$output .= sprintf( '<li><a href="%s">%s</a></li>', esc_attr($link), $link );
		}
		$output .= '</ol>';
		return $output;
	}


	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		return false;
	}


}