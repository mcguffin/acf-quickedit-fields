<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class UserField extends SelectField {

	use Traits\InputSelect;

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {

		if ( ! current_user_can( 'list_users' ) ) {
			return '';
		}

		$is_multiple = ( isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple'] ) || $this->acf_field['type'] === 'checkbox';
		$can_edit = current_user_can( 'edit_users' );

		// check permissions!
		$output = '';
		if ( $is_multiple ) {
			$output .= '<ul>'.PHP_EOL;
			$user_ids = (array) $this->get_value( $object_id, false );
			foreach ( $user_ids as $user_id ) {
				if ( $userdata = get_userdata( $user_id ) ) {
					if ( $can_edit ) {
						$link = get_edit_user_link( $user_id );
					} else {
						$link = get_author_posts_url( $user_id );
					}
					$output .= sprintf( '<li><a href="%s">%s</a></li>'.PHP_EOL, esc_url($link), esc_html( $userdata->display_name ) );
				}
			}
			$output .= '</ul>'.PHP_EOL;
		} else {
			$user_id = (array) $this->get_value( $object_id, false );
			if ( $userdata = get_userdata( $user_id ) ) {
				if ( $can_edit ) {
					$link = get_edit_user_link( $user_id );
				} else {
					$link = get_author_posts_url( $user_id );
				}
				$output .= sprintf( '<li><a href="%s">%s</a></li>'.PHP_EOL, esc_url($link), esc_html( $userdata->display_name ) );
			}

		}
		return $output;
	}


	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $is_quickedit = true ) {

		$get_users_args = [
			'fields' => [ 'ID', 'user_nicename' ],
		];

		if ( ! empty( $this->acf_field['role'] ) ) {
			$get_users_args['role__in'] = $this->acf_field['role'];
		}
		$users = get_users($get_users_args);

		return $this->render_select_input(
			[
				'data-query-nonce' => wp_create_nonce( 'acf/fields/user/query' . $this->acf_field['key'] ),
			] + $input_atts,
			[
				'ui' => 1,
				'ajax' => 1,
				'choices' => array_combine(
					array_map( function($user) { return $user->ID; }, $users ),
					array_map( function($user) { return $user->user_nicename; }, $users )
				),
			] + $this->acf_field,
			$is_quickedit
		);
	}

	/**
	 *	@inheritdoc
	 */
	protected function sanitize_ajax_result( $value ) {

		$value = intval( $value );

		// bail if post doesn't exist
		if ( ! $user = get_userdata( $value ) ) {
			return '';
		}

		return [
			'id'	=> $value,
			'text'	=> $user->user_nicename,
		];
	}


	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return 'unsigned';
	}



}
