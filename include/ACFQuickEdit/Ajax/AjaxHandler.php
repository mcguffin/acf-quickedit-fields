<?php
/**
 *	@package ACFQuickEdit\Ajax
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ACFQuickEdit\Ajax;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use ACFQuickEdit\Core;

class AjaxHandler {

	private $_action	= null;

	private $options	= null;

	private $_nonce		= null;

	/**
	 *	@param	string	$action
	 *	@param	array	$args
	 */
	public function __construct( $action, $args ) {

		$this->_action	= $action;

		$defaults = array(
			'public'		=> false,
			'use_nonce'		=> true,
			'capability'	=> 'manage_options',
			'callback'		=> null,
		);

		$this->options = (object) wp_parse_args( $args, $defaults );

		if ( $this->public ) {
			$this->options->capability	= false;
			add_action( "wp_ajax_nopriv_{$this->action}", array( $this, 'ajax_callback' ) );
		}

		add_action( "wp_ajax_{$this->action}", array( $this, 'ajax_callback' ) );
	}

	public function __get( $prop ) {
		if ( $prop === 'nonce' ) {
			return $this->get_nonce();
		} else if ( $prop === 'action' ) {
			return $this->_action;
		} else if ( isset( $this->options->$prop ) ) {
			return $this->options->$prop;
		}
	}

	private function get_nonce() {
		if ( is_null( $this->_nonce ) ) {
			$this->_nonce = wp_create_nonce( '_nonce_' . $this->action );
		}
		return $this->_nonce;
	}

	private function verify_nonce( $nonce ) {
		return wp_verify_nonce( $nonce, '_nonce_' . $this->action );
	}

	public function ajax_callback() {
		$params = wp_parse_args( $_POST, array(
			'nonce'	=> false,
		));

		// check nonce
		if ( $this->use_nonce && ( ! $params['nonce'] || ! $this->verify_nonce($_POST['nonce']) ) ) {
			return false;
		}
		// check capability
		if ( $this->capability !== false && ! current_user_can( $this->capability ) ) {
			return false;
		}

		$response = array( 'success' => false );

		if ( is_callable( $this->callback ) ) {
			if ( $result = call_user_func( $this->callback, $params ) ) {
				$response = $result;
			};
		}

		echo json_encode( $response );

		exit();
	}

	public function __destruct( ) {
		if ( $this->public ) {
			remove_action( "wp_ajax_nopriv_{$this->action}", array( $this, 'ajax_callback' ) );
		}
		remove_action( "wp_ajax_{$this->action}", array( $this, 'ajax_callback' ) );
	}

}
