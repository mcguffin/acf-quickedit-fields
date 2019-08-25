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

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		$this->core			= Core\Core::instance();

		add_action( 'after_setup_theme' , array( $this , 'setup' ) );

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
		add_action( 'load-edit.php' , array( $this, 'enqueue_edit_assets' ) );
		add_action( 'load-edit-tags.php' , array( $this, 'enqueue_edit_assets' ) );
		add_action( 'load-users.php' , array( $this, 'enqueue_edit_assets' ) );
		add_action( 'load-post.php' , array( $this, 'enqueue_post_assets' ) );


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

		$this->columns->init_acf_settings();
		$this->quickedit->init_acf_settings();
		$this->bulkedit->init_acf_settings();

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



		Asset\Asset::get('css/acf-quickedit.css')->enqueue();

		Asset\Asset::get('js/acf-quickedit.js')
			->footer()
			->deps( array( 'acf-input' ) )
			->localize( array(
				/* Script Localization */
				'options'	=> array(
					'request'	=> $this->ajax_handler->request
				),
			), 'acf_qef' )
			->enqueue();

		$this->enqueue_edit_assets();

	}

	/**
	 * @action 'load-post.php'
	 */
	public function enqueue_post_assets() {
		global $typenow;
		if ( 'acf-field-group' === $typenow ) {
			Asset\Asset::get( 'js/acf-qef-field-group.js' )
				->deps( 'acf-field-group' )
				->enqueue();
			Asset\Asset::get( 'css/acf-qef-field-group.css' )
				->deps( 'acf-field-group' )
				->enqueue();
			// wp_enqueue_script( 'acf-qef-field-group', plugins_url( 'js/acf-qef-field-group.js', ACF_QUICK_EDIT_FILE ), array( 'acf-field-group' ) );
			// wp_enqueue_style( 'acf-qef-field-group', plugins_url( 'css/acf-qef-field-group.css', ACF_QUICK_EDIT_FILE ), array( 'acf-field-group' ) );
		}
	}

	/**
	 *	@action 'load-edit.php'
	 */
	public function enqueue_edit_assets() {

		acf_enqueue_scripts();

		// enqueue features assets
		$styles = array_unique( array_merge(
			$this->columns->get_styles(),
			$this->quickedit->get_styles(),
			$this->bulkedit->get_styles()
		) );

		$scripts = array_unique( array_merge(
			$this->columns->get_scripts(),
			$this->quickedit->get_scripts(),
			$this->bulkedit->get_scripts()
		) );

		foreach ( $styles as $style ) {
			wp_enqueue_style( $style );
		}
		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script );
		}

		// enqueue features assets

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
