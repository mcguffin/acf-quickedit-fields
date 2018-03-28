<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PostObjectField extends RelationshipField {

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return '';
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
			$post_title = __( '(no title)', 'acf-quick-edit-fields' );
		}
		if ( current_user_can( 'edit_post', $post->ID ) ) {
			return sprintf('<a href="%s">%s</a>', get_edit_post_link( $post->ID ), $post_title );
		} else if ( ( $pto = get_post_type_object( $post->post_type ) ) && $pto->public ) {
			return sprintf('<a href="%s">%s</a>', get_permalink( $post->ID ), $post_title );
		}
		return $post_title;

	}
}
