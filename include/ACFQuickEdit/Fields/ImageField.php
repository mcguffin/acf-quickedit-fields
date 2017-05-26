<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class ImageField extends Field {

	public static $quickedit = false;

	public static $bulkedit = false;
	
	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$image_id = get_field( $this->acf_field['key'] );
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

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		return false;
	}


}