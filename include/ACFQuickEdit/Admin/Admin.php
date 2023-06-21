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

	/**
	 *	@var Core\Core
	 */
	private $core;

	/**
	 *	@var Asset\Asset admin css
	 */
	private $css;

	/**
	 *	@var Asset\Asset admin js (editor)
	 */
	private $js;

	/**
	 *	@var Asset\Asset admin js (columns only)
	 */
	private $js_columns;

	/**
	 *	@var Columns
	 */
	private $columns = null;

	/**
	 *	@var Quickedit
	 */
	private $quickedit = null;

	/**
	 *	@var Bulkedit
	 */
	private $bulkedit = null;

	/**
	 *	@var Filters
	 */
	private $filters = null;

	/**
	 *	@var Ajax\AjaxHandler
	 */
	private $ajax_handler = null;

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		$this->core = Core\Core::instance();

		$this->js = Asset\Asset::get('js/acf-quickedit.js');

		$this->js_columns = Asset\Asset::get('js/acf-columns.js');

		$this->css = Asset\Asset::get('css/acf-quickedit.css');

		add_action( 'after_setup_theme', [ $this , 'setup' ] );

		// init field group admin
		add_action( 'acf/field_group/admin_head', [ $this, 'field_group_admin_head' ] );

	}

	/**
	 *	@param string $what
	 */
	public function __get( $what ) {
		switch( $what ) {
			case 'js':
			case 'css':
				return $this->$what;
		}
	}

	/**
	 *	@action acf/field_group/admin_head
	 */
	public function field_group_admin_head() {
		if ( version_compare( acf()->version, '6.0.0', '>=' ) ) {
			FieldGroup::instance();
		} else {
			LegacyFieldGroup::instance();
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
				add_action( 'admin_notices', [ $this, 'print_no_acf_notice' ] );
			}
			return;
		}

		$this->columns		= Columns::instance();
		$this->quickedit	= Quickedit::instance();
		$this->bulkedit		= Bulkedit::instance();
		$this->filters		= Filters::instance();
		$this->ajax_handler = new Ajax\AjaxHandler( 'get_acf_post_meta', [
			'public'			=> false,
			'use_nonce'			=> true,
			'capability'		=> false, // apply_filters( 'acf_qef_capability', 'edit_posts' ),
			'callback'			=> [ $this, 'ajax_get_acf_post_meta' ],
		]);

		add_action( 'load-edit.php', [ $this , 'enqueue_edit_assets' ] );
		add_action( 'load-edit-tags.php', [ $this , 'enqueue_edit_assets' ] );
		add_action( 'load-users.php', [ $this, 'enqueue_columns_assets' ] );
		add_action( 'acf/field_group/admin_enqueue_scripts', [ $this, 'enqueue_fieldgroup_assets' ] );

	}

	/**
	 * @action 'wp_ajax_get_acf_post_meta'
	 */
	public function ajax_get_acf_post_meta( $params ) {

		$success = false;
		$message = '';
		$data = null;

		if ( isset( $params['object_id'] , $params['acf_field_keys'] ) ) {

			$object_ids = (array) $params['object_id'];

			$field_keys = array_unique( (array) $params['acf_field_keys'] );

			$object_ids = array_filter( $object_ids, [ $this, 'can_edit_object' ] );

			$success = true;

			$data = [];

			foreach ( $object_ids as $object_id ) {

				foreach ( $field_keys as $key ) {

					// ACF-Field must exists
					if ( ! ( $field = get_field_object( $key , $object_id ) ) ) {
						continue;
					}

					if ( $field_object = Fields\Field::getFieldObject( $field ) ) {
						$value = $field_object->get_value( $object_id, false );
						if ( ! isset( $data[ $key ] ) ) {
							// first iteration - always set value
							$val = $field_object->get_value( $object_id, false );
							$data[ $key ] = $field_object->sanitize_value( $val, 'ajax' );
						} else {
							// multiple iterations - no value if values aren't equal
							if ( $data[ $key ] != $value ) {
								$data[ $key ] = '';
							}
						}
					}
				}
			}

		}
		return [
			'success'	=> $success,
			'message'	=> $message,
			'data'		=> $data,
		];
	}

	/**
	 *	Current user can edit
	 *
	 *	@param string $object_id ACF Object ID
	 *	@return boolean
	 */
	private function can_edit_object( $object_id ) {
		if ( is_numeric( $object_id ) ) {
			return current_user_can( 'edit_post', $object_id );
		}
		if ( preg_match('/^([\w\d\-_]+)_(\d+)$/', $object_id, $matches ) ) {
			list( $obj_id, $type, $term_id ) = $matches;
			if ( $type === 'user' ) {
				return current_user_can('edit_users');
			}
			if ( taxonomy_exists( $type ) ) {
				return current_user_can( 'edit_term', $term_id );
			}
		}
		return false;
	}

	/**
	 *	@action admin_notices
	 */
	public function print_no_acf_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php
				printf(
					wp_kses(
						/* Translators: 1: ACF Pro URL, 2: plugins page url */
						__( 'The <strong>ACF QuickEdit Fields</strong> plugin requires <a href="%1$s" target="_blank" rel="noopener noreferrer">ACF version 5.6 or later</a>. You can disable and uninstall it on the <a href="%2$s">plugins page</a>.',
							'acf-quickedit-fields'
						),
						[
							'strong' => [],
							'a'	=> [ 'href' => [], 'target' => [], 'rel' => '' ]
						]
					),
					esc_url( 'https://www.advancedcustomfields.com/' ),
					esc_url( admin_url('plugins.php' ) )

				);
			?></p>
		</div>
		<?php
	}

	/**
	 *	Enqueue options Assets
	 *	@action admin_print_scripts
	 */
	public function enqueue_columns_assets() {
		$this->css->enqueue();
		$this->js_columns->footer()->enqueue();
	}

	/**
	 *	Enqueue options Assets
	 *	@action admin_print_scripts
	 */
	public function enqueue_edit_assets() {

		$bulk = Bulkedit::instance();
		$acf_version = acf()->version;
		wp_enqueue_media();
		acf_enqueue_scripts();

		// register assets
		wp_register_style( 'acf-datepicker', acf_get_url( 'assets/inc/datepicker/jquery-ui.min.css' ), [], $acf_version );

		// timepicker. Contains some usefull parsing mathods even for dates.
		wp_register_script('acf-timepicker', acf_get_url( 'assets/inc/timepicker/jquery-ui-timepicker-addon.min.js' ), [ 'jquery-ui-datepicker' ], $acf_version );
		wp_register_style('acf-timepicker', acf_get_url( 'assets/inc/timepicker/jquery-ui-timepicker-addon.min.css' ), [], $acf_version );

		$this->css->enqueue();

		$this->js
			->footer()
			->add_dep( 'acf-input' )
			->add_dep( 'wp-backbone' )
			->localize( [
				/* Script Localization */
				'options'	=> [
					'request'	=> $this->ajax_handler->request,
					'do_not_change_value'	=> $bulk->get_dont_change_value(),
				],
			], 'acf_qef' )
			->enqueue();
		// 3rd party integration backwards compatibility
		wp_add_inline_script( $this->js->handle, 'window.acf_quickedit = window.acf_qef;', 'after' );
	}

	/**
	 * @action acf/field_group/admin_enqueue_scripts
	 */
	public function enqueue_fieldgroup_assets() {

		if ( version_compare( acf()->version, '6.0.0', '>=' ) ) {
			Asset\Asset::get( 'css/acf-qef-field-group.css' )
				->deps( 'acf-field-group' )
				->enqueue();
		} else {
			LegacyFieldGroup::instance();
			Asset\Asset::get( 'js/acf-qef-field-group-legacy.js' )
				->deps( 'acf-field-group' )
				->enqueue();

			Asset\Asset::get( 'css/acf-qef-field-group-legacy.css' )
				->deps( 'acf-field-group' )
				->enqueue();
		}

	}

	/**
	 *	@param array $values
	 *	@return array
	 */
	private function unique_values( $values ) {
		$ret = [];
		foreach ( $values as $i => $value ) {
			if ( ! in_array( $value, $ret ) ) {
				$ret[] = $value;
			}
		}
		return $ret;
	}
}
