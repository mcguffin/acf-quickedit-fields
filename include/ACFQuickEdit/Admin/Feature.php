<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

/**
 *	Class
 */
abstract class Feature extends Core\Singleton {

	/**
	 * @var ACFQuickEdit\Core\Core
	 */
	protected $core;

	/**
	 * @var ACFQuickEdit\Admin\Admin
	 */
	protected $admin;

	/**
	 * @var ACFQuickEdit\Core\Core
	 */
	protected $fields = [];

	/**
	 * @var null|array
	 */
	private static $available_field_groups = null;

	/**
	 *	Constructor
	 */
	protected function __construct() {

	//	$this->core = Core\Core::instance();
		$this->admin = Admin::instance();

		if ( wp_doing_ajax() ) {
			add_action( 'admin_init', [ $this, 'init_fields' ] );
		} else {
			add_action( 'current_screen', [ $this, 'init_fields' ] );
		}
		parent::__construct();
	}

	/**
	 *	@return string
	 */
	abstract function get_type();

	/**
	 *	@return string
	 */
	abstract function get_fieldgroup_option();


	/**
	 *	@return bool
	 */
	final public function is_active() {
		return count( $this->fields ) > 0;
	}

	/**
	 *	@param string $key field Key
	 *	@param array $field_object ACF Field
	 */
	protected function add_field( $key, $field_object ) {
		$this->fields[ $key ] = $field_object;
	}

	/**
	 *	@param string $type
	 *	@return bool
	 */
	public function supports( $type ) {
		$types = Fields\Field::get_types();
		return isset( $types[ $type ] ) && $types[ $type ][ $this->get_type() ];
	}


	/**
	 *	@param	array	$field_group	ACF Field Group
	 *	@return	array
	 */
	protected function acf_get_fields( $field_group ) {
		$return_fields = [];
		if ( $acf_fields = acf_get_fields( $field_group ) ) {
			foreach ( $acf_fields as $field ) {
				if ( $field['type'] === 'group' ) {
					$return_fields = array_merge( $return_fields, $field['sub_fields'] );
				} else {
					$return_fields[] = $field;
				}
			}

		}
		return $return_fields;

	}

	/**
	 *	@return boolean
	 *	@action admin_init
	 */
	public function init_fields() {
		// action admin_init
		$current_view = CurrentView::instance();

		if ( ! in_array( $current_view->get_object_kind(), ['post','term','user'] ) ) {
			return false;
		}

		$fields_query = [];
		$fields_query[ $this->get_fieldgroup_option() ] = true;

		$current_fields = $current_view->get_fields( $fields_query );

		foreach ( $current_fields as $field ) {

			if ( ! $this->supports( $field[ 'type' ] ) ) {
				continue;
			}
			$field_object = Fields\Field::getFieldObject( $field );
			$this->add_field( $field_object->get_meta_key(), $field_object, false );
		}

		return $this->is_active();

	}



}
