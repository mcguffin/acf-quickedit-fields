<?php
/**
 *	@package ACFQuickEdit\Admin
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ACFQuickEdit\Admin;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use ACFQuickEdit\Ajax;
use ACFQuickEdit\Asset;
use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

class Admin extends Core\Singleton {

	private $core;

	private $css;

	private $js;

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		$this->core = Core\Core::instance();

		$this->js = Asset\Asset::get('js/acf-quickedit.js');

		$this->css = Asset\Asset::get('css/acf-quickedit.css');

		add_action( 'after_setup_theme', array( $this , 'setup' ) );

		// init field group admin
		add_action( 'acf/field_group/admin_head', [ 'ACFQuickEdit\Admin\FieldGroup', 'instance' ] );

	}

	public function __get( $what ) {
		switch( $what ) {
			case 'js':
			case 'css':
				return $this->$what;
		}
	}

	/**
	 *	Setup plugin
	 *
	 *	@action after_setup_theme
	 */
	public function setup() {

		// early return if conditions not met
		if ( ! function_exists('acf') || ! class_exists('acf') || version_compare( acf()->version, '5.7', '<' ) ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				add_action( 'admin_notices', array( $this, 'print_no_acf_notice' ) );
			}
			return;
		}

		$this->columns		= Columns::instance();
		$this->quickedit	= Quickedit::instance();
		$this->bulkedit		= Bulkedit::instance();
		$this->ajax_handler = new Ajax\AjaxHandler( 'get_acf_post_meta', array(
			'public'		=> false,
			'use_nonce'		=> true,
			'capability'	=> 'edit_posts',
			'callback'		=> array( $this, 'ajax_get_acf_post_meta' ),
		));

		//
		add_action( 'load-edit.php', 'acf_enqueue_scripts' );
		add_action( 'load-edit-tags.php', 'acf_enqueue_scripts' );
		add_action( 'load-users.php', 'acf_enqueue_scripts' );
		add_action( 'acf/field_group/admin_enqueue_scripts', array( $this, 'enqueue_fieldgroup_assets' ) );


		add_action( 'admin_init', array( $this , 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this , 'enqueue_assets' ) );
	}



	/**
	 * @action 'wp_ajax_get_acf_post_meta'
	 */
	public function ajax_get_acf_post_meta( $params ) {

//		header('Content-Type: application/json');
		$success = false;
		$message = '';
		$data = null;

		if ( isset( $params['object_id'] , $params['acf_field_keys'] ) ) {

			$object_ids = (array) $params['object_id'];

			$is_multiple = count( $object_ids ) > 1;

			$field_keys = array_unique( (array) $params['acf_field_keys'] );

			foreach ( $object_ids as $object_id ) {

				// permission check
				if ( is_numeric( $object_id ) && ! current_user_can( 'edit_post', $object_id ) ) {
					// posts
					$data = null;
					$message = __( 'Insufficient Permission', 'acf-quick-edit-fields' );
					break;

				} else if ( ! is_numeric( $object_id ) && preg_match('/^([\w\d-_]+)_(\d+)$/', $object_id, $matches ) ) {
					// terms
					list( $obj_id, $taxonomy, $term_id ) = $matches;
					if ( $taxonomy === 'user' || ! taxonomy_exists( $taxonomy ) || ! current_user_can( 'edit_term', $term_id ) ) {
						$data = null;
						$message = __( 'Insufficient Permission', 'acf-quick-edit-fields' );
						break;
					}
				}

				if ( is_numeric( $object_id ) ) {
					if ( ! current_user_can( 'edit_post', $object_id ) ) {
						continue;
					}
				} else {
					$term_id_num = preg_replace( '([^\d])', '', $object_id );
					if ( ! current_user_can( 'edit_term', $term_id_num ) ) {
						continue;
					}
				}

				$success = true;
				$data = array();

				foreach ( $field_keys as $key ) {

					$field = get_field_object( $key , $object_id );

					if ( $field_object = Fields\Field::getFieldObject( $field ) ) {
						if ( $is_multiple ) {
							if ( ! isset( $data[ $key ] ) ) {
								$data[ $key ] = array();
							}
							$data[ $key ][] = $field_object->get_value( $object_id, false );
						} else {
							$data[ $key ] = $field_object->get_value( $object_id, false );
						}
					}
				}
			}

			if ( $is_multiple ) {
				foreach ( $data as $key => $values ) {

					$values = $this->unique_values( $values );

					if ( 1 === count( $values ) ) {
						$data[ $key ] = $values[0];
					} else {
						$data[ $key ] = null;
					}
				}
			}
		}
		return array(
			'success'				=> $success,
			'message'				=> $message,
			'data'					=> $data,
		);
	}


	/**
	 *	@action admin_notices
	 */
	public function print_no_acf_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php
				printf(
					/* Translators: 1: ACF Pro URL, 2: plugins page url */
					__( 'The <strong>ACF QuickEdit Fields</strong> plugin requires <a href="%1$s">ACF version 5.6 or later</a>. You can disable and uninstall it on the <a href="%2$s">plugins page</a>.',
						'acf-quick-edit-fields'
					),
					'http://www.advancedcustomfields.com/',
					admin_url('plugins.php' )

				);
			?></p>
		</div>
		<?php
	}

	/**
	 *	Admin init
	 *	@action admin_init
	 */
	public function admin_init() {

		$this->columns->init_fields();
		$this->quickedit->init_fields();
		$this->bulkedit->init_fields();

	}

	/**
	 *	Enqueue options Assets
	 *	@action admin_print_scripts
	 */
	public function enqueue_assets() {

		// register assets
		wp_register_style('acf-datepicker', acf_get_url('assets/inc/datepicker/jquery-ui.min.css') );

		// timepicker. Contains some usefull parsing mathods even for dates.
		wp_register_script('acf-timepicker', acf_get_url('assets/inc/timepicker/jquery-ui-timepicker-addon.min.js'), array('jquery-ui-datepicker') );
		wp_register_style('acf-timepicker', acf_get_url('assets/inc/timepicker/jquery-ui-timepicker-addon.min.css') );



		$this->css->enqueue();

		$this->js
			->footer()
			->add_dep( 'acf-input' )
			->localize( array(
				/* Script Localization */
				'options'	=> array(
					'request'	=> $this->ajax_handler->request
				),
			), 'acf_qef' )
			->enqueue();
		acf_enqueue_scripts();

	}

	/**
	 * @action 'load-post.php'
	 */
	public function enqueue_fieldgroup_assets() {

		Asset\Asset::get( 'js/acf-qef-field-group.js' )
			->deps( 'acf-field-group' )
			->enqueue();

		Asset\Asset::get( 'css/acf-qef-field-group.css' )
			->deps( 'acf-field-group' )
			->enqueue();

	}


	/**
	 *	@param array $values
	 *	@return array
	 */
	private function unique_values( $values ) {
		$ret = array();
		foreach ( $values as $i => $value ) {
			if ( ! in_array( $value, $ret ) ) {
				$ret[] = $value;
			}
		}
		return $ret;
	}


}
