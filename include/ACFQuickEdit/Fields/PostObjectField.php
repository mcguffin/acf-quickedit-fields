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
		$value = $this->get_value( $object_id );
		//*/

		if ( ! $value ) {
			return '';
		}

		$output	= '';
		$output .= '<ol>';
		foreach ( (array) $value as $post_id ) {
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
		if ( current_user_can( 'edit_post', $post->ID ) ) {
			return sprintf('<a href="%s">%s</a>', get_edit_post_link( $post->ID ), $post->post_title );
		} else if ( ( $pto = get_post_type_object( $post->post_type ) ) && $pto->public ) {
			return sprintf('<a href="%s">%s</a>', get_permalink( $post->ID ), $post->post_title );
		} else {
			return $post->post_title;
		}

	}
}
