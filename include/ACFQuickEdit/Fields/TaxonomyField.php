<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TaxonomyField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$value = get_field( $this->acf_field['key'], $object_id );
		$output = '';
		if ( $value ) {
			$term_names = array();
			foreach ( (array) $value as $i => $term ) {
				if ( $this->acf_field['return_format'] === 'id' ) {
					$term = get_term($term, $this->acf_field['taxonomy']);
				}
				$term_names[] = $term->name;
			}
			$output .= implode( ', ', $term_names );
		} else {
			$output .= __('(No value)', 'acf-quick-edit-fields');
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