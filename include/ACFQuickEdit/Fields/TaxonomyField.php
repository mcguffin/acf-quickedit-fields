<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TaxonomyField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		/*
		$value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id );
		//*/
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
		$output = '';

		acf_include('includes/walkers/class-acf-walker-taxonomy-field.php');
		$field_clone = $this->acf_field + array();
		$field_clone['value'] = array();
		if ( in_array( $field_clone['field_type'], array( 'radio', 'select' ) ) ) {
			// single
			$field_clone['field_type'] = 'radio';
			$field_clone['name'] = sprintf( 'acf[%s]', $field_clone['key'] );
		} else {
			$field_clone['field_type'] = 'checkbox';
			$field_clone['name'] = sprintf( 'acf[%s][]', $field_clone['key'] );
		}

		$taxonomy_obj = get_taxonomy( $field_clone['taxonomy'] );

		$args = array(
			'taxonomy'     		=> $field_clone['taxonomy'],
			'show_option_none'	=> sprintf( _x('No %s', 'No terms', 'acf'), strtolower($taxonomy_obj->labels->name) ),
			'hide_empty'   		=> false,
			'style'        		=> 'none',
			'walker'       		=> new \ACF_Taxonomy_Field_Walker( $field_clone ),
			'echo'				=> false,
		);
		$output .= '<ul class="acf-checkbox-list acf-bl">';
		// if allow null and radio add –No Value– option
		$output .= wp_list_categories($args);
		$output .= '</ul>';
		return $output;

		$input_atts += array(
			'class' => 'acf-quick-edit widefat',
			'id' => $this->core->prefix( $this->acf_field['key'] ),
		);

		return '';
	}


}
