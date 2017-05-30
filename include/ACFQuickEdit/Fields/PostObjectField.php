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
	 *	Render Column content
	 *
	 *	@param int|string $object_id
	 *	@return string
	 */
	public function render_column( $object_id ) {
		$value = get_field( $this->acf_field['key'], $object_id );

		if ( ! $value ) {
			return '';
		}
		$post = get_post( $value );
		if ( current_user_can( 'edit_post', $value ) ) {
			return sprintf('<a href="%s">%s</a>', get_edit_post_link( $value ), $post->post_title );
		}
		return sprintf('<a href="%s">%s</a>', get_permalink( $value ), $post->post_title );

	}

}
