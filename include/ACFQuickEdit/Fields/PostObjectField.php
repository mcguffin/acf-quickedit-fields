<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PostObjectField extends RelationshipField {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		ob_start();
		$field_type = acf_get_field_type($this->acf_field['type']);
		$field_type->render_field($this->acf_field);
		$html = ob_get_clean();

		return apply_filters( 'acf_qef_input_html_' . $this->acf_field['type'], $html, $input_atts, $is_quickedit, $this->acf_field );
	}

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		/*
		$value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id, false );
		//*/

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
	 *
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
		// 2DO - need to re-enable sanitization, we might do this in formatResult() though
		/*error_log('now sanitizing value ' . var_export($value, 1), 0);
		if ( is_array( $value ) ) {
			return array_map( 'intval', $value );
		}
		return intval( $value );*/
		return $value;
	}

	/**
	 *	Value to be loaded into editor, prepare data for select2
		*
		*	@param mixed $value
		*	@param string/int $object_id
		*	@param bool $format_value
		*	@param array $acf_field
		*/
	public function get_value( $object_id, $format_value = true ) {

		$value = parent::get_value( $object_id, $format_value );

		// prepare field values: get post title and set select2 data format 
		if( is_array($value) ) {
			$result = array_map( function( $id ) {
				return $this->formatResult($id);
			}, $value );
		} else {
			$result = $this->formatResult($value);
		}

		return apply_filters( 'acf_qef_get_value_' . $this->acf_field['type'], $result, $object_id, $format_value, $this->acf_field );
	}

	/**
	 *	Format result data for select2
		*
		*	@param mixed $value
		*	@return StdClass
		*/
	private function formatResult($value) {
		$result = new \StdClass();
		$result->id = $value;
		$result->text = get_the_title($value);

		return $result;
	}


}
