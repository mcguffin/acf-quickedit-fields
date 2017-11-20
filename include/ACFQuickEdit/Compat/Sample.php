<?php

namespace ACFQuickEdit\Compat;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


use ACFQuickEdit\Core;


class Sample extends Core\PluginComponent {

	protected function __construct() {
	}

	/**
	 *	@inheritdoc
	 */
	 public function activate(){

	 }

	 /**
	  *	@inheritdoc
	  */
	 public function deactivate(){

	 }

	 /**
	  *	@inheritdoc
	  */
	 public function uninstall() {
		 // remove content and settings
	 }

	/**
 	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {
	}

}
