<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class FileField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$output = '';
		/*
		$value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id );
		//*/

		if ( ! is_null($value) && ! empty($value) && ( $file = get_post($value) ) ) {
			$output .= sprintf( __('<a href="%s" class="acf-qed-icon" title="%s">%s</a>','acf-quick-edit-fields'),
				get_edit_post_link( $value ) ,
				$file->post_title,
				wp_get_attachment_image( $value, array(80,80), true ) );
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
		$output .= sprintf( '<button class="button-secondary select-media">%s</button>', __('Select File', 'acf-quick-edit-fields') );
		$output .= sprintf( '<button class="button-secondary remove-media">%s</button>', __('Remove File', 'acf-quick-edit-fields') );
		return $output;
	}


}
