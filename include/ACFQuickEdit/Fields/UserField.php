<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class UserField extends SelectField {

	use Traits\BulkOperationLists;
	use Traits\InputSelect;
	use Traits\ColumnLists;

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {

		if ( ! current_user_can( 'list_users' ) ) {
			return '';
		}
		return $this->render_list_column(
			$object_id,
			isset( $this->acf_field['multiple'] ) && $this->acf_field['multiple'],
			[ $this, 'render_list_column_item_value_user' ]
		);
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
