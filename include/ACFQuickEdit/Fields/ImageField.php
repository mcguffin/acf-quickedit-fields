<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ImageField extends FileField {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		/*
		$image_id = get_field( $this->acf_field['key'], $object_id );
		/*/
		$image_id = $this->get_value( $object_id );
		//*/
//		$image_id = get_field( $this->acf_field['key'], $object_id );

		$output = '';

		if ( $image_id ) {
			if ( is_array( $image_id ) ) {
				// Image field is an object
				$output .= wp_get_attachment_image( $image_id['id'] , array(80,80) );
			} else if( is_numeric( $image_id ) ) {
				// Image field is an ID
				$output .= wp_get_attachment_image( $image_id , array(80,80) );
			} else {
				// Image field is a url
				$output .= '<img src="' . $image_id . '" width="80" height="80" />';
			};
		}
		return $output;
	}

}
