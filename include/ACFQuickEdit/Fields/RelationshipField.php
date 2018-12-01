<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RelationshipField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		/*
		$field_value = get_field( $this->acf_field['key'], $object_id );
		/*/
		$value = $this->get_value( $object_id );
		//*/

		$output = '';
		if ( is_a( $value, 'WP_Post' ) ) {
			$output .= $this->get_post_object_link( $value->ID );
		} else if ( is_array( $value ) ) {
			$links = array();
			foreach ( $value as $post ) {
				$post_id = 0;
				if ( is_a( $post, 'WP_Post' ) ) {
					$post_id = $post->ID;
				} else if ( is_int( $post ) ) {
					$post_id = $post;
				}
				if ( $post_id && $link = $this->get_post_object_link( $post_id ) ) {
					$links[] = $link;
				}
			}
			if ( count( $links ) > 1 ) {
				$output .= '<ol>';
				foreach ( $links as $link ) {
					$output .= sprintf( '<li>%s</li>', $link );
				}
				$output .= '</ol>';
			} else {
				$output .= implode( '<br />', $links );
			}
		}
		return $output;
	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {
		return '';
	}

	/**
	 *	@param int $post_id
	 *	@return string HTML
	 */
	private function get_post_object_link( $post_id ) {
		$result = '';
		$title = get_the_title( $post_id );

		if ( current_user_can( 'edit_post', $post_id ) ) {
			$result .= sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post_id ), $title );
		} else if ( current_user_can( 'read_post', $post_id ) ) {
			$result .= sprintf( '<a href="%s">%s</a>', get_permalink( $post_id ), $title );
		} else {
			$result .= $title;
		}

		if ( 'attachment' !== get_post_type( $post_id ) && 'private' === get_post_status( $post_id ) ) {
			$result .= ' &mdash; ' . __('Private', 'acf-quick-edit-fields' );
		}
		return $result;
	}


}
