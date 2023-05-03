<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ImageField extends FileField {

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		$output = '';

		$image_id = $this->get_value( $object_id );

		if ( $image_id ) {
			if ( is_array( $image_id ) ) {
				// Image field is an object
				$output .= wp_get_attachment_image( $image_id['id'], [ 80, 80 ] );
			} else if( is_numeric( $image_id ) ) {
				// Image field is an ID
				$output .= wp_get_attachment_image( $image_id, 'thumbnail' );
			} else {
				// Image field is a url
				$output .= '<img src="' . esc_url_raw( $image_id ) . '" width="80" height="80" />';
			};
		}
		return $output;
	}
}
