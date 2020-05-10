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
		$value = $this->get_value( $object_id, false );

		//*/
		$output = '';
		if ( $value ) {
			$term_names = [];
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}
			foreach ( $value as $i => $term ) {
				if ( $this->acf_field['return_format'] === 'id' ) {
					$term = get_term( $term, $this->acf_field['taxonomy'] );
				}
				// fix #63 ?
				if ( trim( $term->name ) !== '' ) {
					$term_names[] = $term->name;
				} else if ( trim( $term->slug ) !== '' ) {
					$term_names[] = $term->slug;
				} else {
					$term_names[] = $term->id;
				}
			}
			$term_names = array_map( 'esc_html', $term_names );
			$output .= implode( ', ', $term_names );
		} else {
			$output .= esc_html__('(No value)', 'acf-quickedit-fields');
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		$output = '';

		acf_include('includes/walkers/class-acf-walker-taxonomy-field.php');

		$field_clone = $this->acf_field + [];

		$field_clone['value'] = [];

		$field_clone['name'] = 'acf';

		if ( isset( $this->parent ) ) {
			$field_clone['name'] .= sprintf('[%s]', $field_clone['parent'] );
		}
		$field_clone['name'] .= sprintf('[%s]', $field_clone['key'] );

		if ( in_array( $field_clone['field_type'], [ 'checkbox', 'multi_select' ] ) ) {

			$field_clone['name'] .= '[]';

		}

		$taxonomy_obj = get_taxonomy( $field_clone['taxonomy'] );

		$args = [
			'taxonomy'     		=> $field_clone['taxonomy'],
			'show_option_none'	=> sprintf( _x('No %s', 'No terms', 'acf'), strtolower($taxonomy_obj->labels->name) ),
			'hide_empty'   		=> false,
			'style'        		=> 'none',
			'walker'       		=> new \ACF_Taxonomy_Field_Walker( $field_clone ),
			'echo'				=> false,
		];

		if ( 'radio' === $field_clone['field_type'] || 'checkbox' === $field_clone['field_type'] ) {

			$output .= '<ul ' . acf_esc_attr( [
				'class'	=> 'acf-checkbox-list acf-bl',
			] ) . '>';

			if ( 'radio' === $field_clone['field_type'] && $field_clone['allow_null'] ) {
				// add – No Value – option ...
				$output .= '<li>';
				$output .= '<label>';
				$output .= '<input ' . acf_esc_attr( [
					'name'	=> $field_clone['name'],
					'value'	=> '',
					'type'	=> $field_clone['field_type']
				] ) . ' />';
				$output .= sprintf('<span>%s</span>', esc_html__('– No Selection –','acf-quickedit-fields'));
				$output .= '</label>';
				$output .= '</li>';
			}
			$output .= wp_list_categories( $args );

			$output .= '</ul>';
		} else {

//			$field_clone['type']		= 'select';
			$field_clone['multiple']	= 'multi_select' === $field_clone['field_type'];
			$field_clone['choices']		= [];
			$field_clone['ui']			= true;
			$field_clone['ajax']		= true;
			$field_clone['type']		= 'select';
			$field_clone['ajax_action']		= 'acf/fields/taxonomy/query';

			if ( $field_clone['allow_null'] ) {
				$field_clone['choices'][''] = __('– No Selection –','acf-quickedit-fields');
			}

			$terms = acf_get_terms( [
				'taxonomy'		=> $field_clone['taxonomy'],
				'hide_empty'	=> false
			] );

			foreach( $terms as $term ) {
				$term_title = '';

				// ancestors
				$ancestors = get_ancestors( $term->term_id, $field_clone['taxonomy'] );

				if( ! empty( $ancestors ) ) {

					$term_title .= str_repeat('- ', count($ancestors));

				}

				$term_title .= $term->name;

				$field_clone['choices'][ $term->term_id ] = esc_html( $term_title );
			}
			ob_start();
			acf_render_field($field_clone);
			$output .= ob_get_clean();

		}
		return $output;
	}

	/**
	 *	@param mixed $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		$sanitation_cb = $context === 'ajax' ? [ $this, 'sanitize_ajax_result' ] : 'intval';

		if ( is_array( $value ) ) {
			$value = array_map( $sanitation_cb, $value );
			$value = array_filter( $value );
			return array_values( $value );
		}
		return call_user_func( $sanitation_cb, $value );//sanitize_text_field($value);
	}

	/**
	 *	Format result data for select2
	 *
	 *	@param mixed $value
	 *	@return string|array If value present and post exists Empty string
	 */
	private function sanitize_ajax_result( $value ) {

		$value = intval( $value );

		$term = get_term( $value );

		// bail if term doesn't exist
		if ( ! $term ) {
			return '';
		}

		return [
			'id'	=> $term->term_id,
			'text'	=> esc_html( $term->name ),
		];
	}

}
