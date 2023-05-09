<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PostObjectField extends SelectField {

	use Traits\BulkOperationLists;
	use Traits\ColumnLists;
	use Traits\InputSelect;

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		return $this->render_list_column(
			$object_id,
			isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple'],
			[ $this, 'render_list_column_item_value_post' ]
		);
	}

	/**
	 *	@inheritdoc
	 */
	protected function get_wrapper_attributes( $wrapper_attr, $is_quickedit = true ) {
		$wrapper_attr['data-ajax'] = '1';
		$wrapper_attr['data-multiple'] = isset( $this->acf_field['multiple'] )
			? $this->acf_field['multiple']
			: '0';
		return $wrapper_attr;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {

		$input_atts += [
			'data-ui'			=> '1',
			'data-ajax'			=> '1',
			'data-type'			=> 'post_object',
			'data-multiple'		=> $this->acf_field['multiple'],
			'data-allow_null'	=> $this->acf_field['allow_null'],
		];
		return $this->render_select_input(
			$input_atts,
			[
				'ui' => 1,
				'ajax' => 1,
			] + $this->acf_field,
			$is_quickedit
		);

	}

	/**
	 *	@return mixed Unsanitized value of acf field.
	 */
	public function get_value( $object_id, $format_value = true ) {

		$value = parent::get_value( $object_id, $format_value );

		if ( is_scalar( $value ) && ( $post = get_post($value ) ) ) {
			$value = ['id' => $value, 'text' => $post->post_title ];
		}

		return $value;
	}

	/**
	 *	@inheritdoc
	 */
	protected function sanitize_ajax_result( $value ) {

		$value = intval( $value );

		// bail if post doesn't exist
		if ( ! get_post( $value ) ) {
			return '';
		}

		return [
			'id'	=> $value,
			'text'	=> esc_html( get_the_title( $value ) ),
		];
	}
}
