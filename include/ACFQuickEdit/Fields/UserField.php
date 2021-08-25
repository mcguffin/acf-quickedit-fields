<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class UserField extends SelectField {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		if ( ! current_user_can( 'list_users' ) ) {
			return '';
		}
		// check permissions!
		if ( ( $user_id = $this->get_value( $object_id, false ) ) && ( $userdata = get_userdata( $user_id ) ) ) {

			if ( current_user_can( 'edit_users' ) ) {
				$link = get_edit_user_link( $user_id );
			} else {
				$link = get_author_posts_url( $user_id );
			}
			return sprintf( '<a href="%s">%s</a>', esc_url($link), esc_html( $userdata->display_name ) );
		}

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
		$this->acf_field['ui'] = true;
		$this->acf_field['choices'] = array_combine(
			array_map( function($user) { return $user->ID; }, $users ),
			array_map( function($user) { return $user->user_nicename; }, $users ),
		);
		return parent::render_input( $input_atts, $is_quickedit );

	}

	/**
	 *	@inheritdoc
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		if ( is_array( $value ) ) {
			return array_map( 'intval', $value );
		}
		return intval( $value );

	}

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return 'unsigned';
	}

}
