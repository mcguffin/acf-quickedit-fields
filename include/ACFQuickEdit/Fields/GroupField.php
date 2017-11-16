<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class GroupField extends Field {

	private $sub_fields = array();


	/**
	 *	@inheritdoc
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) { }

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) { }

	/**
	 *	Update field value
	 *
	 *	@param int $post_id
	 *	@param bool $is_quickedit
	 *
	 *	@return null
	 */
	public function update( $post_id ) {

		if ( isset( $this->parent ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['acf'] ) ) {
			return;
		}

		$param_name = $this->acf_field['key'];

		if ( isset ( $_REQUEST['acf'][ $param_name ] ) ) {
			$value = $_REQUEST['acf'][ $param_name ];
		} else {
			$value = null;
		}
		if ( ! is_array( $value ) ) {
			return;
		}
		$value = array_filter( $value, array( $this, 'filter_do_not_change') );


		update_field( $this->acf_field['key'], $value, $post_id );
	}
	private function filter_do_not_change( $val ) {
		return $val !== $this->dont_change_value;
	}

}
