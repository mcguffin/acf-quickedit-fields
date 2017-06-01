<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Admin extends Core\Singleton {

	protected $core;

	protected $columns;

	private $column_fields = array();	

	private $quickedit_fields = array();	

	private $quickedit_field_groups = array();	

	private $bulkedit_fields = array();	

	private $bulkedit_field_groups = array();	

	private $_wp_column_weights = array();	


	/**
	 *	Constructor
	 */
	protected function __construct() {
		$this->core			= Core\Core::instance();
		$this->columns		= Columns::instance();
		$this->quickedit	= Quickedit::instance();
		$this->bulkedit		= Bulkedit::instance();
		add_action( 'after_setup_theme' , array( $this , 'setup' ) );
	}

	/**
	 * Setup plugin
	 *
	 * @action plugins_loaded
	 */
	public function setup() {

		if ( class_exists( 'acf' ) && function_exists( 'acf_get_field_groups' ) ) {

			// init everything
			add_action( 'admin_init' , array( $this, 'admin_init' ) );

			// enqueue assets
			add_action( 'load-edit.php' , array( $this, 'enqueue_edit_assets' ) );
			add_action( 'load-edit-tags.php' , array( $this, 'enqueue_edit_assets' ) );
			add_action( 'load-users.php' , array( $this, 'enqueue_edit_assets' ) );
			add_action( 'load-post.php' , array( $this, 'enqueue_post_assets' ) );

			// retrieving data
			add_action( 'wp_ajax_get_acf_post_meta' , array( $this, 'ajax_get_acf_post_meta' ) );

		} else if ( class_exists( 'acf' ) && current_user_can( 'activate_plugins' ) ) {

			// say something about incompatibility
			add_action( 'admin_notices', array( $this, 'print_acf_free_notice' ) );

		}
	}

	/**
	 * @action admin_notices
	 */
	function print_acf_free_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php 
				printf( 
					_x( 'The ACF QuickEdit Fields plugin only provies support for <a href="%1$s">ACF Pro</a>. You can disable and uninstall it on the <a href="%2$s">plugins page</a>.', 
						'1: ACF Pro URL, 2: plugins page url',
						'acf-quick-edit-fields' 
					),
					'http://www.advancedcustomfields.com/pro/',
					admin_url('plugins.php' )
					
				); 
			?></p>
		</div>
		<?php
	}

	/**
	 * @action admin_init
	 */
	function admin_init() {

		$this->columns->init_acf_settings();
		$this->quickedit->init_acf_settings();
		$this->bulkedit->init_acf_settings();

		$this->columns->init_fields();
		$this->quickedit->init_fields();
		$this->bulkedit->init_fields();

	}


	/**
	 * @action 'load-edit.php'
	 */
	function enqueue_edit_assets() {

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
	 * @action 'load-post.php'
	 */
	function enqueue_post_assets() {
		global $typenow;
		if ( 'acf-field-group' === $typenow ) {
			wp_enqueue_script( 'acf-qef-field-group', plugins_url( 'js/acf-qef-field-group.min.js', ACFQUICKEDIT_FILE ), array( 'acf-field-group' ) );
		}
	}
	/**
	 * @action 'wp_ajax_get_acf_post_meta'
	 */
	function ajax_get_acf_post_meta() {

		header('Content-Type: application/json');

		if ( isset( $_REQUEST['post_id'] , $_REQUEST['acf_field_keys'] ) ) {

			$result = array();
			 
			$post_ids = (array) $_REQUEST['post_id'];

			$is_multiple = count( $post_ids ) > 1;

		//	$post_ids = array_filter( $post_ids,'intval');

			$field_keys = array_unique( $_REQUEST['acf_field_keys'] );

			foreach ( $post_ids as $post_id ) {

				if ( is_numeric( $post_id ) ) {
					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						continue;
					}
				} else {
					$term_id_num = preg_replace( '([^\d])', '', $post_id );
					if ( ! current_user_can( 'edit_term', $term_id_num ) ) {
						continue;
					}
				}

				foreach ( $field_keys as $key ) {

					$field = get_field_object( $key , $post_id );

					if ( $field_object = Fields\Field::getFieldObject( $field ) ) {
						if ( $is_multiple ) {
							if ( ! isset( $result[ $key ] ) ) {
								$result[ $key ] = array();
							}
							$result[ $key ][] = $field_object->get_value( $post_id );
						} else {
							$result[ $key ] = $field_object->get_value( $post_id );
						}
					}


/*
					switch ( $field_obj['type'] ) {
						case 'date_time_picker':
						case 'time_picker':
						case 'date_picker':
							$field_val	= acf_get_metadata( $post_id, $field_obj['name'] );
							break;
						default:
							$field_val	= get_field( $field_obj['key'], $post_id, false );
//							$field_val	= acf_get_metadata( $post_id, $field_obj['name'] );
							break;
					}
					if ( ! isset( $result[ $key ] ) || $result[ $key ] == $field_val ) {

						$result[ $key ]	= $field_object;

					} else {

						$result[ $key ] = '';

					}
*/
				}
			}
			if ( $is_multiple ) {
				foreach ( $result as $key => $values ) {
					$values = array_unique( $values );
					if ( 1 === count( $values ) ) {
						$result[ $key ] = $values[0];
					} else {
						$result[ $key ] = null;
					}
				}
			}
			echo json_encode( $result );

			exit();
		}
	}



}