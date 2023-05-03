<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class FileField extends Field {

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		$output = '';

		$value = $this->get_value( $object_id, false );

		if ( ! is_null($value) && ! empty($value) && ( $file = get_post($value) ) ) {
			$output .= sprintf( '<a href="%s" class="acf-qef-icon" title="%s">%s</a>',
				get_edit_post_link( $value ) ,
				esc_html( $file->post_title ),
				wp_get_attachment_image( $value, [ 80, 80 ], true ) );
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		if ( !empty( $this->acf_field['mime_types'] ) ) {
			$input_atts['data-mime_types'] = $this->acf_field['mime_types'];
		}

		$input_atts['type']			= 'hidden';
		$input_atts['data-library']	= $this->acf_field['library'];
		unset($input_atts['disabled']);

		$output = '';
		$output .= parent::render_input( $input_atts, $is_quickedit );
		$output .= '<span class="file-content"><span class="media-mime"></span><span class="media-title"></span></span>';
		$output .= sprintf( '<button class="button-secondary select-media">%s</button>', esc_html__('Select File', 'acf-quickedit-fields') );
		$output .= sprintf( '<button class="button-link remove-media dashicons dashicons-dismiss"><span class="screen-reader-text">%s</span></button>', esc_html__('Remove File', 'acf-quickedit-fields') );
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		return intval( $value );
	}
}
