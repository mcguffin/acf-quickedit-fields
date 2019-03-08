<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class Feature extends Core\Singleton {

	/**
	 * @var array styles to enqueue
	 */
	protected $styles = array();

	/**
	 * @var array scripts to enqueue
	 */
	protected $scripts = array();

	/**
	 * @var ACFQuickEdit\Core\Core
	 */
	protected $core;

	/**
	 * @var ACFQuickEdit\Core\Core
	 */
	protected $fields = array();

	/**
	 * @var null|array
	 */
	private static $available_field_groups = null;

	/**
	 *	Constructor
	 */
	protected function __construct() {
		$this->core = Core\Core::instance();
		parent::__construct();
	}

	/**
	 *	@return string
	 */
	abstract function get_type();

	/**
	 *	@return array
	 */
	final public function get_styles() {
		return $this->styles;
	}

	/**
	 *	@return array
	 */
	final public function get_scripts() {
		return $this->scripts;
	}

	/**
	 *	@return bool
	 */
	public function is_active() {
		return count( $this->fields ) > 0;
	}

	/**
	 *	@param string $key field Key
	 *	@param array $field_object ACF Field
	 */
	protected function add_field( $key, $field_object, $add_parent = false ){
		$this->fields[ $key ] = $field_object;
		// add parent
		if ( ! $add_parent ) {
			return;
		}
		if ( ! $field_parent = $field_object->get_parent() ) {
			return;
		}
		$parent_key = $field_parent->get_acf_field()['key'];
		if ( ! isset( $this->fields[$parent_key] ) ) {
			$this->add_field( $parent_key, $field_parent );
		}
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
	 *	Initialize
	 */
	public function init_acf_settings() {
		$types = Fields\Field::get_types();

		foreach ( $types as $type => $supports ) {
			if ( $supports[ $this->get_type() ] ) {
				add_action( "acf/render_field_settings/type={$type}" , array( $this , 'render_acf_settings' ) );
			}
		}
	}
	/**
	 *	@param	array	$field_group	ACF Field Group
	 *	@return	array
	 */
	protected function acf_get_fields( $field_group ) {
		$return_fields = array();
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
	 *	@action 'acf/render_field_settings/type={$type}'
	 */
	abstract function render_acf_settings( $field );

	/**
	 *	@return null
	 *	@action admin_init
	 */
	abstract function init_fields();

	/**
	 *	@return boolean
	 */
	abstract function is_enabled_for_field( $field );


	/**
	 * @return null|string	post | taxonomy | user
	 */
	protected function get_current_content_type() {
		global $typenow, $pagenow;
		if ( in_array( $pagenow, array( 'upload.php', 'edit.php' ) ) ) {
			return 'post';
		} else if ( $pagenow == 'edit-tags.php' && isset( $_REQUEST['taxonomy'] ) && taxonomy_exists( $_REQUEST['taxonomy'] ) ) {
			return 'taxonomy';
		} else if ( $pagenow == 'users.php' ) {
			return 'user';
		} else if ( defined( 'DOING_AJAX' ) && DOING_AJAX  ) {
			if ( in_array( $_REQUEST['action'], apply_filters( 'acf_quick_edit_post_ajax_actions', array( 'inline-save' ) ) ) ) {
				return 'post';
			} else if ( in_array( $_REQUEST['action'], apply_filters( 'acf_quick_edit_term_ajax_actions', array( 'inline-save-tax', 'add-tag' ) ) ) ) {
				return 'taxonomy';
			}
		}
	}


	/**
	 * @return null|string	post type
	 */
	protected function get_current_post_type() {
		global $typenow, $pagenow;

		if ( $pagenow === 'upload.php' ) {
			return 'attachment';

		} else if ( $pagenow === 'edit.php' ) {
			return isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : ( ! empty( $typenow ) ? $typenow : 'post' );

		} else if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			if ( in_array( $_REQUEST['action'], apply_filters( 'acf_quick_edit_post_ajax_actions', array( 'inline-save' ) ) ) ) {
				return $_REQUEST['post_type'];
			}

		}
	}


	/**
	 * @return array acf field gruops
	 */
	protected function get_available_field_groups() {

		global $typenow, $pagenow;

		if ( is_null( self::$available_field_groups ) ) {

			$content_type = $this->get_current_content_type();

			if ( is_null( $content_type ) ) {
				return null;
			}

			// gather conditions for field parts
			$multiple_conditions = array();

			if ( $content_type === 'post' ) {
				$conditions = array( 'post_type' => $this->get_current_post_type() );

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					foreach ( apply_filters( 'acf_quick_edit_post_id_request_param', array( 'post_ID' ) ) as $request_param ) {
						if ( isset( $_REQUEST[$request_param] ) ) {
							$conditions['post_id'] = absint( $_REQUEST[$request_param] );
						}
					}

				} else {
					add_filter( 'acf/location/rule_match/post_category', array( $this, 'match_post_taxonomy' ), 11, 3 );
					add_filter( 'acf/location/rule_match/post_taxonomy', array( $this, 'match_post_taxonomy' ), 11, 3 );
					add_filter( 'acf/location/rule_match/post_format', array( $this, 'match_post_format' ), 11, 3 );
					add_filter( 'acf/location/rule_match/post_status', array( $this, 'match_post_status' ), 11, 3 );
				}

				$multiple_conditions = array( $conditions );

			} else if ( $content_type === 'taxonomy' ) {

				$conditions = array( 'taxonomy' => $_REQUEST['taxonomy'] );

				$multiple_conditions = array( $conditions );

			} else if ( $content_type === 'user' ) {
				$multiple_conditions = array(
					array(
						'user_role' => isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : 'all',
					),
					array(
						'user_form' => 'register',
					),
					array(
						'user_form' => 'edit',
					),
					array(
						'user_form' => 'all',
					),
				);

			} else {
				return;
			}

			/**
			 * Getting the Field Groups to be displayed in posts list table
			 *
			 * @param array $conditions	Field group conditions being passed to `acf_get_field_groups()`
			 */
			$field_groups = array();

			foreach ( $multiple_conditions as $conditions ) {
				$add_groups = acf_get_field_groups( apply_filters( 'acf_quick_edit_fields_group_filter', $conditions ) );
				$field_groups = array_merge( $field_groups, $add_groups );
			}
			/*
			self::$available_field_groups = acf_get_field_groups( apply_filters( 'acf_quick_edit_fields_group_filter', $conditions ) );
			/*/
			self::$available_field_groups = array_unique( $field_groups, SORT_REGULAR );//array_unique( $field_groups );
			//*/
		}

		return self::$available_field_groups;
	}

	/**
	 * @filter 'acf/location/rule_match/post_taxonomy'
	 */
	function match_post_taxonomy( $match, $rule, $options ) {

		if ( isset( $_REQUEST['category_name'] ) ) {

			// WP categories

			return $rule['operator'] == '==' && $rule['value'] == sprintf('category:%s', $_REQUEST['category_name'] );

		} else {

			// Any other taxonomy

			foreach ( $_REQUEST as $key => $value ) {

				if ( taxonomy_exists( $key ) && strpos( $rule['value'], $key ) === 0 ) {
					return $rule['operator'] == '==' && $rule['value'] == sprintf('%s:%s', $key, $value );
				}

			}

		}
		return $match;
	}

	/**
	 * @filter 'acf/location/rule_match/post_format'
	 */
	function match_post_format( $match, $rule, $options ) {

		if ( isset( $_REQUEST['post_format'] ) ) {

			return $rule['operator'] == '==' && $rule['value'] == $_REQUEST['post_format'];

		}
		return $match;
	}

	/**
	 * @filter 'acf/location/rule_match/post_status'
	 */
	function match_post_status( $match, $rule, $options ) {

		if ( isset( $_REQUEST['post_status'] ) ) {

			return $rule['operator'] == '==' && $rule['value'] == $_REQUEST['post_status'];

		}
		return $match;
	}

}
