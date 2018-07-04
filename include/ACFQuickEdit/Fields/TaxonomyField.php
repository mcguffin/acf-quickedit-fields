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

		$field_clone['name'] = 'acf';

		if ( isset( $this->parent ) ) {
			$field_clone['name'] .= sprintf('[%s]', $field_clone['parent'] );
		}
		$field_clone['name'] .= sprintf('[%s]', $field_clone['key'] );

		if ( in_array( $field_clone['field_type'], array( 'checkbox', 'multi_select' ) ) ) {

			$field_clone['name'] .= '[]';

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

		if ( 'radio' === $field_clone['field_type'] || 'checkbox' === $field_clone['field_type'] ) {

			$output .= '<ul ' . acf_esc_attr( array(
				'class'	=> 'acf-checkbox-list acf-bl',
			) ) . '>';

			if ( 'radio' === $field_clone['field_type'] && $field_clone['allow_null'] ) {
				// add – No Value – option ...
				$output .= '<li>';
				$output .= '<label>';
				$output .= '<input ' . acf_esc_attr(array(
					'name'	=> $field_clone['name'],
					'value'	=> '',
					'type'	=> $field_clone['field_type']
				)) . ' />';
				$output .= sprintf('<span>%s</span>',__('– No Selection –','acf-quick-edit-fields'));
				$output .= '</label>';
				$output .= '</li>';
			}
			$output .= wp_list_categories($args);

			$output .= '</ul>';
		} else {

			$field_clone['type']		= 'select';
			$field_clone['multiple']	= 'multi_select' === $field_clone['field_type'];
			$field_clone['choices']		= array();
			$field_clone['ui']			= false;
			$field_clone['ajax']		= false;

			if ( $field_clone['allow_null'] ) {
				$field_clone['choices'][''] = __('– No Selection –','acf-quick-edit-fields');
			}

			$terms = acf_get_terms( array(
				'taxonomy'		=> $field_clone['taxonomy'],
				'hide_empty'	=> false
			) );

			foreach( $terms as $term ) {
				$term_title = '';

				// ancestors
				$ancestors = get_ancestors( $term->term_id, $field_clone['taxonomy'] );

				if( ! empty( $ancestors ) ) {

					$term_title .= str_repeat('- ', count($ancestors));

				}

				$term_title .= $term->name;

				$field_clone['choices'][ $term->term_id ] = $term_title;
			}
			ob_start();
			acf_render_field($field_clone);
			$output .= ob_get_clean();

		}
		return $output;
	}


}
