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

		// early return if conditions not met
		if ( ! function_exists('acf') || ! class_exists('acf') || version_compare( acf()->version, '5.6', '<' ) ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				add_action( 'admin_notices', array( $this, 'print_no_acf_notice' ) );
			}
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 5 );

		// init everything
		add_action( 'admin_init' , array( $this, 'admin_init' ) );

		// enqueue assets
		add_action( 'load-edit.php' , array( $this, 'enqueue_edit_assets' ) );
		add_action( 'load-edit-tags.php' , array( $this, 'enqueue_edit_assets' ) );
		add_action( 'load-users.php' , array( $this, 'enqueue_edit_assets' ) );
		add_action( 'load-post.php' , array( $this, 'enqueue_post_assets' ) );

		// retrieving data
		add_action( 'wp_ajax_get_acf_post_meta' , array( $this, 'ajax_get_acf_post_meta' ) );

	}

	/**
	 * @action admin_notices
	 */
	public function print_no_acf_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php
				printf(
					_x( 'The <strong>ACF QuickEdit Fields</strong> plugin requires <a href="%1$s">ACF version 5.6 or later</a>. You can disable and uninstall it on the <a href="%2$s">plugins page</a>.',
						'1: ACF Pro URL, 2: plugins page url',
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
	 * @action admin_init
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
	 * @action 'load-edit.php'
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
	 *	@action admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {

		$core = Core\Core::instance();

		// register assets
		wp_register_style('acf-datepicker', acf_get_dir('assets/inc/datepicker/jquery-ui.min.css') );

		// timepicker. Contains some usefull parsing mathods even for dates.
		wp_register_script('acf-timepicker', acf_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.js'), array('jquery-ui-datepicker') );
		wp_register_style('acf-timepicker', acf_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.css') );


		if ( version_compare( acf()->version,'5.7','lt' ) ) {
			$script_src = 'js/legacy/5.6/acf-quickedit.min.js';
		} else {
			$script_src = 'js/acf-quickedit.min.js';
		}
		wp_register_script( 'acf-quickedit', plugins_url( $script_src, ACF_QUICK_EDIT_FILE ), array( 'acf-input' ), $core->get_version(), true );

		wp_register_style( 'acf-quickedit', plugins_url( 'css/acf-quickedit.css', ACF_QUICK_EDIT_FILE ) );

		$this->enqueue_edit_assets();
	}

	/**
	 * @action 'load-post.php'
	 */
	public function enqueue_post_assets() {
		global $typenow;
		if ( 'acf-field-group' === $typenow ) {
			wp_enqueue_script( 'acf-qef-field-group', plugins_url( 'js/acf-qef-field-group.min.js', ACF_QUICK_EDIT_FILE ), array( 'acf-field-group' ) );
			wp_enqueue_style( 'acf-qef-field-group', plugins_url( 'css/acf-qef-field-group.css', ACF_QUICK_EDIT_FILE ), array( 'acf-field-group' ) );
		}
	}

	/**
	 * @action 'wp_ajax_get_acf_post_meta'
	 */
	public function ajax_get_acf_post_meta() {

		header('Content-Type: application/json');

		if ( isset( $_REQUEST['object_id'] , $_REQUEST['acf_field_keys'] ) ) {

			$result = array();
			$result_denied = array( 'success' => false, 'message' => __( 'Insufficient Permission', 'acf-quick-edit-fields') );

			$object_ids = (array) $_REQUEST['object_id'];

			$is_multiple = count( $object_ids ) > 1;

			$field_keys = array_unique( $_REQUEST['acf_field_keys'] );

			foreach ( $object_ids as $object_id ) {

				// permission check
				if ( is_numeric( $object_id ) && ! current_user_can( 'edit_post', $object_id ) ) {
					// posts
					$result = $result_denied;
					break;

				} else if ( ! is_numeric( $object_id ) && preg_match('/^([\w\d-_]+)_(\d+)$/', $object_id, $matches ) ) {
					// terms
					list( $obj_id, $taxonomy, $term_id ) = $matches;
					if ( $taxonomy === 'user' || ! taxonomy_exists( $taxonomy ) || ! current_user_can( 'edit_term', $term_id ) ) {
						$result = $result_denied;
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

				foreach ( $field_keys as $key ) {

					$field = get_field_object( $key , $object_id );

					if ( $field_object = Fields\Field::getFieldObject( $field ) ) {
						if ( $is_multiple ) {
							if ( ! isset( $result[ $key ] ) ) {
								$result[ $key ] = array();
							}
							$result[ $key ][] = $field_object->get_value( $object_id, false );
						} else {
							$result[ $key ] = $field_object->get_value( $object_id, false );
						}
					}
				}
			}

			if ( $is_multiple ) {
				foreach ( $result as $key => $values ) {

					$values = $this->unique_values( $values );

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
