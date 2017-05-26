<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PostObjectField extends RelationshipField {

	public static $quickedit = false;

	public static $bulkedit = false;
	
	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		return false;
	}



}