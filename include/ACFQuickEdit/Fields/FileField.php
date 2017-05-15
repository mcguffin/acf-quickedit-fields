<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class FileField extends Field {
	
	public static $quickedit = false;

	public static $bulkedit = false;
	
	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$output = '';
		$value = acf_get_value( $object_id, $this->acf_field );
		if ( ! is_null($value) && ! empty($value) ) {
			$file = get_post($value);
			$output .= sprintf( __('Edit: <a href="%s">%s</a>','acf-quick-edit-fields') , get_edit_post_link( $value ) , $file->post_title );
		}
		return $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column ) {
		return false;
	}


}