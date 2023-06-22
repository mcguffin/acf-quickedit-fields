<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TaxonomyField extends Field {

	use Traits\BulkOperationLists;
	use Traits\ColumnLists;
	use Traits\Filter;
	use Traits\InputCheckbox;
	use Traits\InputRadio;
	use Traits\InputSelect;

	/**
	 *	@param WP_Term $term
	 *	@param array $choices
	 *	@param int $depth
	 */
	private function term_choice( $term, &$choices, $depth = 0, $prop = 'term_id' ) {
		$choices[ $term->$prop ] = str_repeat( '&nbsp;', $depth * 3 ) . $term->name;
	}

	/**
	 *	@param WP_Term $term
	 *	@param array $choices
	 *	@param int $depth
	 */
	private function term_hierarchy_choice( $term, &$choices, $depth = 0, $prop = 'term_id' ) {

		$this->term_choice( $term, $choices, $depth, $prop );

		$terms = get_terms([
			'taxonomy'   => $term->taxonomy,
			'parent'     => $term->term_id,
			'hide_empty' => false,
		]);

		foreach ( $terms as $term ) {
			$this->term_hierarchy_choice( $term, $choices, $depth + 1, $prop );
		}
	}

	/**
	 *	@inheritdoc
	 */
	public function render_filter( $index, $selected = '' ) {

		$terms = get_terms([
			'taxonomy' => $this->acf_field['taxonomy'],
			'hide_empty' => false,
			'parent' => 0,
		]);
		$choices = [];
		$is_hierarchical = is_taxonomy_hierarchical( $this->acf_field['taxonomy'] );

		$term_prop = $this->acf_field['load_terms']
			? 'slug'
			: 'term_id';

		foreach ( $terms as $term ) {
			if ( $is_hierarchical ) {
				$this->term_hierarchy_choice( $term, $choices, 0, $term_prop );
			} else {
				$this->term_choice( $term, $choices, 0, $term_prop );
			}
		}

		if ( $this->acf_field['load_terms'] ) {
			return $this->render_term_filter_dropdown(
				$selected,
				$choices
			);
		}

		return $this->render_filter_dropdown(
			$index,
			$selected,
			in_array( $this->acf_field['field_type'], [ 'multi_select', 'checkbox' ] ),
			$choices
		);
	}

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		return $this->render_list_column(
			$object_id,
			in_array( $this->acf_field['field_type'], [ 'multi_select', 'checkbox' ] ),
			[ $this, 'render_list_column_item_value_term' ]
		);
	}

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
