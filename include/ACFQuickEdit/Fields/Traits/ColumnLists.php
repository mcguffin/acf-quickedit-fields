<?php

namespace ACFQuickEdit\Fields\Traits;

trait ColumnLists {

	/**
	 *	@param int $object_id
	 */
	protected function render_list_column( $object_id, $is_multiple = false, $callback = null ) {

		if ( ! is_callable( $callback ) ) {
			$callback = [ $this, 'render_list_column_item_value' ];
		}

		$value = $this->get_value( $object_id, false );
		if ( is_object( $value ) && isset( $value->id ) ) {
			$value = $value->id;
		}
		$value = (array) $value;
		$value = array_filter( $value );

		$output = '';

		if ( $is_multiple && is_array( $value ) && count( $value ) > 0 ) {

			$output .= '<ul>'.PHP_EOL;
			foreach ( $value as $val ) {
				$output .= sprintf(
					'<li>%s</li>'.PHP_EOL,
					call_user_func( $callback, $val )
				);
			}
			$output .= '</ul>'.PHP_EOL;

		} else if ( ! empty( $value ) ) {

			foreach ( $value as $val ) {
				$output .= call_user_func( $callback, $val );
			}

		} else {
			// $output .= '<p>';
			$output .= $this->__no_value();
			// $output .= '</p>';
		}
		return $output;
	}

	/**
	 *	@param mixed $value
	 */
	protected function render_list_column_item_value( $value ) {
		return $value;
	}

	/**
	 *	@param int $value User ID
	 */
	protected function render_list_column_item_value_post( $value ) {

		$post = get_post( $value );

		if ( ! is_a( $post, '\WP_Post' ) ) {
			/* translators: Post ID */
			return sprintf( esc_html__( '(Post %s not found)', 'acf-quickedit-fields' ), $value );
		}

		$link_tpl = '<a href="%s">%s</a>';
		$post_title = $post->post_title;

		if ( empty( trim( $post_title ) ) ) {
			$post_title = esc_html__( '(no title)', 'acf-quickedit-fields' );
		}

		if ( current_user_can( 'edit_post', $post->ID ) ) {
			return sprintf(
				$link_tpl,
				get_edit_post_link( $post->ID ),
				esc_html( $post_title )
			);
		} else if ( ( $pto = get_post_type_object( $post->post_type ) ) && $pto->public ) {
			return sprintf(
				$link_tpl,
				get_permalink( $post->ID ),
				esc_html( $post_title )
			);
		}
		return $post_title;
	}

	/**
	 *	@param int $value User ID
	 */
	protected function render_list_column_item_value_user( $value ) {
		$can_edit = current_user_can( 'edit_users' );
		$output = '';
		if ( $userdata = get_userdata( $value ) ) {
			if ( $can_edit ) {
				$link = get_edit_user_link( $value );
			} else {
				$link = get_author_posts_url( $value );
			}
			$output .= sprintf( '<a href="%s">%s</a>'.PHP_EOL, esc_url( $link ), esc_html( $userdata->display_name ) );
		} else {
			return esc_html__( '(User not found)', 'acf-quickedit-fields' );
		}
		return $output;
	}

	/**
	 *	@param int $value User ID
	 */
	protected function render_list_column_item_value_term( $value ) {

		$term_obj = get_term( $value, $this->acf_field['taxonomy'] );

		$is_term = is_a( $term_obj, '\WP_Term' );

		if ( ! $is_term ) {
			/* translators: Term ID */
			return sprintf( esc_html__( '(Term ID %d not found)', 'acf-quickedit-fields' ), $term );
		} else if ( trim( $term_obj->name ) !== '' ) {
			$label =  $term_obj->name;
		} else if ( trim( $term_obj->slug ) !== '' ) {
			$label =  $term_obj->slug;
		} else {
			$label =  $term_obj->id;
		}

		$link = add_query_arg( $term_obj->taxonomy, $term_obj->slug );
		foreach ( array_keys( $_GET ) as $param ) {
			if ( $term_obj->taxonomy !== $param && taxonomy_exists( $param ) ) {
				$link = remove_query_arg( $param, $link );
			}
		}
		if ( is_wp_error( $link ) ) {
			$link = null;
		}

		if ( ! is_null( $link ) ) {
			return sprintf(
				'<a href="%s">%s</a>'.PHP_EOL,
				esc_url( $link ),
				esc_html( $label )
			);
		}
		return esc_html( $label ) ;
	}
}
