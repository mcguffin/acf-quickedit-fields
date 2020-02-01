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

/**
 *	Ajax Handler Class
 *
 *	Usage
 *	-----
 *
 *	// FILE: ajax-action.php
 *	<?php
 *
 *	use ACFQuickEdit\Ajax;
 *	use ACFQuickEdit\Assets;
 *
 *	$my_ajax_handler = new Ajax\AjaxHandlder( 'ajax-action-name', [
 *		'callback'	=> function( $request_data ) {
 *			// $request_data = $_REQUEST
 *			$result = [
 *				// input sanitaion happens in the callback
 *				'some_result' => sanitize_text_field( $request_data[ 'some_param' ] ),
 *				'some_other_result'	=> sanitize_text_field( $request_data[ 'some_other_param' ] ),
 *			];
 *			// will send result result as json
 *			return $result;
 *		},
 *		'capability'	=> 'manage_options', // false to allow for everybody
 *		'public'		=> false,	// true for public access
 *		'use_nonce'		=> true,	// enable csrf
 *	] );
 *
 *	$ajax_js = Asset\Asset::get( 'js/send-ajax-request.js' )
 *		->localize( [
 *			// already holds action and nonce
 *			'request'	=> $my_ajax_handler->request
 *		], 'ajax_options' )
 *		->enqueue();
 *
 *
 *	// FILE: js/send-ajax-request.js
 *
 *	var data = _.extend( {}, ajax_options, {
 *	 	'some_param' : 'some_value',
 *	 	'some_other_param' : 'some_other_value',
 *	} );
 *	$.ajax({
 *		url:ajaxurl,
 *		data: data,
 *		success: function(response) {
 *			// response: return value of ajax callback
 *			console.log( response )
 *		},
 *	})
 */
class AjaxHandler {

	/** @var array ajax action name */
	private $_action		= null;

	/** @var array constructor args */
	private $options		= null;

	/** @var string current nonce */
	private $_nonce			= null;

	/** @var string request var name of nonce */
	private $_nonce_param	= '_ajax_nonce';

	/**
	 *	@param	string	$action
	 *	@param	array	$args
	 */
	public function __construct( $action, $args ) {

		$this->_action	= $action;

		$defaults = [
			'public'		=> false,
			'use_nonce'		=> true,
			'capability'	=> 'manage_options',
			'callback'		=> null,
			'sanitize_callback'	=> null,
		];

		$this->options = (object) wp_parse_args( $args, $defaults );

		if ( $this->public ) {
			$this->options->capability	= false;
			add_action( "wp_ajax_nopriv_{$this->action}", [ $this, 'ajax_callback' ] );
		}

		add_action( "wp_ajax_{$this->action}", [ $this, 'ajax_callback' ] );
	}

	/**
	 *	@param string $prop
	 *	@return mixed
	 */
	public function __get( $prop ) {
		if ( $prop === 'nonce' ) {
			return $this->get_nonce();
		} else if ( $prop === 'request' ) {
			$req = [
				'action'	=> $this->_action,
			];
			$req[ $this->_nonce_param ] = $this->get_nonce();
			return $req;
		} else if ( $prop === 'action' ) {
			return $this->_action;
		} else if ( isset( $this->options->$prop ) ) {
			return $this->options->$prop;
		}
	}

	/**
	 *	@return string
	 */
	private function get_nonce() {
		if ( is_null( $this->_nonce ) ) {
			$this->_nonce = wp_create_nonce( '_nonce_' . $this->action );
		}
		return $this->_nonce;
	}

	/**
	 *	@return bool
	 */
	private function verify_nonce() {
		return check_ajax_referer( '_nonce_' . $this->action, null, false );
	}

	/**
	 *	Execute Ajax Callback
	 *
	 *	@return null
	 */
	public function ajax_callback() {

		$response = [ 'success' => false ];

		if ( $this->use_nonce && ! $this->verify_nonce() ) {

			// check nonce
			$response['message'] = __( 'Nonce invalid', 'acf-quickedit-fields' );

		} else if ( $this->capability !== false && ! current_user_can( $this->capability ) ) {

			// check capability
			$response['message'] = __( 'Insufficient Permission', 'acf-quickedit-fields' );

		} else if ( is_callable( $this->callback ) ) {

			$params = $_REQUEST;

			if ( is_callable( $this->sanitize_callback ) ) {
				$params = call_user_func( $this->sanitize_callback, $params );
			} else {
				$params = $this->sanitize_default( $params );
			}

			if ( $result = call_user_func( $this->callback, $params ) ) {
				$response = $result;
			};
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );

		exit();
	}

	/**
	 *	Sanitize params
	 *
	 *	@param mixed
	 *	@return int|string|array|null
	 */
	private function sanitize_default( $params ) {
		if ( is_array( $params ) ) {
			foreach ( $params as $key => $value ) {
				$sanitized = $this->sanitize_default( $value );
				if ( is_null( $sanitized ) ) {
					unset( $params[$key] );
				} else {
					$params[$key] = $sanitized;
				}
			}
		} else if ( is_numeric( $params ) ) {
			$params = intval( $params );
		} else if ( is_string( $params ) ) {
			$params = sanitize_text_field( $params );
		} else {
			$params = null;
		}
		return $params;
	}

	/**
	 *	Remove ajax action
	 */
	public function __destruct( ) {
		if ( $this->public ) {
			remove_action( "wp_ajax_nopriv_{$this->action}", [ $this, 'ajax_callback' ] );
		}
		remove_action( "wp_ajax_{$this->action}", [ $this, 'ajax_callback' ] );
	}

}
