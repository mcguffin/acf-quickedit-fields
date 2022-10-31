<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TaxonomyField extends Field {

	use Traits\BulkOperationLists;
	use Traits\InputCheckbox;
	use Traits\InputRadio;
	use Traits\InputSelect;

	/**
	 *	@inheritdoc
	 */
	protected function get_wrapper_attributes( $wrapper_attr, $is_quickedit = true ) {
		$wrapper_attr['data-ajax'] = isset( $this->acf_field['ajax'] )
			? $this->acf_field['ajax']
			: '0';
		return $wrapper_attr;
	}

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		if ( $this->acf_field['multiple'] || in_array( $this->acf_field['field_type'], [ 'multi_select', 'checkbox' ] ) ) {
			return [
				'union'        => __('Union','acf-quickedit-fields'),
				'difference'   => __('Difference','acf-quickedit-fields'),
				'intersection' => __('Intersection','acf-quickedit-fields'),
			];
		}
		return [];
	}

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$output = '';

		$value = $this->get_value( $object_id, false );

		if ( $value ) {
			$term_names = [];
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

			foreach ( $value as $i => $term ) {

				$term_obj = get_term( $term, $this->acf_field['taxonomy'] );

				// fix #63 ?
				if ( is_null( $term_obj ) ) {
					/* translators: Term ID */
					$term_names[] = sprintf( esc_html__( '(Term ID %d not found)', 'acf-quickedit-fields' ), $term );
				} else if ( trim( $term_obj->name ) !== '' ) {
					$term_names[] = $term_obj->name;
				} else if ( trim( $term_obj->slug ) !== '' ) {
					$term_names[] = $term_obj->slug;
				} else {
					$term_names[] = $term_obj->id;
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

		if ( ! taxonomy_exists($this->acf_field['taxonomy'] ) ) {
			return $output;
		}

		$this->acf_field['choices'] = [];

		if ( 'radio' === $this->acf_field['field_type'] ) {
			$output .= $this->render_radio_input(
				$input_atts,
				[
					'choices' => get_terms([
						'taxonomy'   => $this->acf_field['taxonomy'],
						'fields'     => 'id=>name',
						'hide_empty' => false,
					]),
				] + $this->acf_field,
				$is_quickedit
			);

		} else if ( 'checkbox' === $this->acf_field['field_type'] ) {

			$output .= $this->render_checkbox_input(
				$input_atts,
				[
					'choices' => get_terms([
						'taxonomy'   => $this->acf_field['taxonomy'],
						'fields'     => 'id=>name',
						'hide_empty' => false,
					]),
				] + $this->acf_field,
				$is_quickedit
			);

		} else if ( 'select' === $this->acf_field['field_type'] || 'multi_select' === $this->acf_field['field_type'] ) {

			$output .= $this->render_select_input(
				$input_atts,
				[
					'ui' => 1,
					'ajax' => 1,
					'multiple' => 'multi_select' === $this->acf_field['field_type'],
				] + $this->acf_field,
				$is_quickedit
			);

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

		if ( ! $value ) {
			return '';
		}

		$term = get_term( $value );

		// bail if term doesn't exist
		if ( ! $term || is_wp_error( $term ) ) {
			return '';
		}

		return [
			'id'	=> $term->term_id,
			'text'	=> esc_html( $term->name ),
		];
	}
}
