<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PostObjectField extends RelationshipField {

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

		$output = '';

		// handle empty values
		$output .= acf_get_hidden_input( [
			'name'	=> $input_atts['name'],
		]);

		// handle multiple values
		if ( $this->acf_field['multiple'] ) {
			$input_atts['name'] .= '[]';
		}

		$output .= acf_get_select_input( $input_atts );

		return $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		$value = $this->get_value( $object_id, false );

		if ( ! $value ) {
			return '';
		}

		// return single value
		$value = (array) $value;

		if ( count( $value ) === 1 ) {
			$post = get_post( $value[0] );
			return $this->get_post_link( $post );
		}

		// display multiple posts as list
		$output	= '';
		$output .= '<ol>';
		foreach ( $value as $post_id ) {
			$post = get_post( $post_id );
			$output .= sprintf( '<li>%s</li>', $this->get_post_link( $post ) );
		}
		$output .= '</ol>';
		return $output;
	}

	/**
	 *	@param WP_Post $post
	 *	@return string
	 */
	private function get_post_link( $post ) {

		$post_title = $post->post_title;
		if ( empty( trim( $post_title ) ) ) {
			$post_title = esc_html__( '(no title)', 'acf-quickedit-fields' );
		}
		if ( current_user_can( 'edit_post', $post->ID ) ) {
			return sprintf('<a href="%s">%s</a>', get_edit_post_link( $post->ID ), esc_html( $post_title ) );
		} else if ( ( $pto = get_post_type_object( $post->post_type ) ) && $pto->public ) {
			return sprintf('<a href="%s">%s</a>', get_permalink( $post->ID ), esc_html($post_title) );
		}
		return $post_title;

	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		$sanitation_cb = $context === 'ajax' ? [ $this, 'sanitize_ajax_result' ] : 'intval';

		if ( is_array( $value ) ) {
			// strip out falsy values
			$value = array_map( $sanitation_cb, $value );
			// strip out falsy values
			$value = array_filter( $value );
			// reset array keys
			return array_values( $value );
		}

		return call_user_func( $sanitation_cb, $value );

	}

	/**
	 *	Format result data for select2
	 *
	 *	@param mixed $value
	 *	@return string|array If value present and post exists Empty string
	 */
	private function sanitize_ajax_result( $value ) {

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
