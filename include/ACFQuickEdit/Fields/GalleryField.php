<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class GalleryField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$output = '';
		/**
		 * Filter number of images to be displayed in Gallery Column
		 *
		 * @param int $max_images	Maximum Number of images
		 */
		if ( $max_images = apply_filters( 'acf_quick_edit_fields_gallery_col_max_images', 15 ) ) {
			/*
			$images = get_field( $this->acf_field['key'], $object_id );
			/*/
			$images = $this->get_value( $object_id );
			//*/
			if ( $images ) {
				$class = count($images) > 1 ? 'acf-qef-gallery-col' : 'acf-qef-image-col';
				$output .= sprintf( '<div class="%s">', $class );
				foreach ( array_values( $images ) as $i => $image) {
					if ( $i >= $max_images ) {
						break;
					}
					$output .= wp_get_attachment_image( $image['id'] , array(80, 80) );
				}
				$output .= '</div>';
			}
		}
		return $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return '';
	}


}
