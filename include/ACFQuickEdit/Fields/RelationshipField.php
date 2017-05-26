<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RelationshipField extends Field {

	public static $quickedit = false;

	public static $bulkedit = false;
	
	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$field_value = get_field( $this->acf_field['key'], $object_id );
		$output = '';
		if ( is_a( $field_value, 'WP_Post' ) ) {
			$output .= $this->get_post_object_link( $field_value->ID );
		} else if ( is_array( $field_value ) ) {
			$links = array();
			foreach ( $field_value as $field_value_post ) {
				$field_value_post_id = 0;
				if ( is_a( $field_value_post, 'WP_Post' ) ) {
					$field_value_post_id = $field_value_post->ID;
				} else if ( is_int( $field_value_post ) ) {
					$field_value_post_id = $field_value_post;
				}
				if ( $field_value_post_id && $link = $this->get_post_object_link( $field_value_post_id ) ) {
					$links[] = $link;
				}
			}
			if ( count( $links > 1 ) ) {
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
	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		return false;
	}


}