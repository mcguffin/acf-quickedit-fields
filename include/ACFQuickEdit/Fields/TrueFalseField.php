<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TrueFalseField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		echo get_field( $this->acf_field['key'], $object_id ) ? __('Yes') : __('No');

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$field_key = $this->acf_field['key'];
		$prefix_column = $this->core->prefix( $field_key );
		$output = '';
		$output .= sprintf( '<ul class="acf-radio-list" data-acf-field-key="%s">', $field_key );
		$output .= sprintf( '<li><label for="%s-yes">', $prefix_column );
		$output .= sprintf( '<input id="%s-yes" type="radio" value="1" class="acf-quick-edit" data-acf-field-key="%s" name="%s" />', 
								$prefix_column, 
								$field_key, 
								$input_atts['name'] 
							);
		$output .= __('Yes');
		$output .= '</label></li>';

		$output .= sprintf( '<li><label for="%s-no">', $prefix_column );
		$output .= sprintf( '<input id="%s-no" type="radio" value="0" class="acf-quick-edit" data-acf-field-key="%s" name="%s" />', 
								$prefix_column, 
								$field_key, 
								$input_atts['name'] 
							);
		$output .= __('No');
		$output .= '</label></li>';
		$output .= '</ul>';

		return $output;
	}


}