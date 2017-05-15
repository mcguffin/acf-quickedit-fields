<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class PageLinkField extends Field {

	public static $quickedit = false;

	public static $bulkedit = false;
	

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column ) {
		return false;
	}


}