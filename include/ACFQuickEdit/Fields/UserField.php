<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class UserField extends Field {

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
	public function sanitize_value( $value, $context = 'db' ) {

		return intval( $value );

	}

}
