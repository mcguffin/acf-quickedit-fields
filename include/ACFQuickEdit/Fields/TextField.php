<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class TextField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function is_sortable() {
		return true;
	}

}
