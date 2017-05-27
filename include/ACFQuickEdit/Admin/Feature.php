<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class Feature extends Core\Singleton {

	protected $styles = array();

	protected $scripts = array();

	protected $core;

	protected $fields = array();

	private $available_field_groups = null;
	/**
	 *	Constructor
	 */
	protected function __construct() {
		$this->core = Core\Core::instance();
		parent::__construct();
	}

	abstract function get_type();

	final public function get_styles() {
		return $this->styles;
	}

	final public function get_scripts() {
		return $this->scripts;
	}

	public function is_active() {
		return count( $this->fields ) > 0;
	}

	protected function add_field( $key, $field_object ){
		$this->fields[ $key ] = $field_object;
	}

	public function supports( $type ) {
		$types = Fields\Field::get_types();
		return isset( $types[ $type ] ) && $types[ $type ][ $this->get_type() ];
	}

	public function init_acf_settings() {
		$types = Fields\Field::get_types();

		foreach ( $types as $type => $supports ) {
			if ( $supports[ $this->get_type() ] ) {
				add_action( "acf/render_field_settings/type={$type}" , array( $this , 'render_acf_settings' ) );
			}
		}
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	abstract function render_acf_settings( $field );

	/**
	 * @return null
	 */
	abstract function init_fields();

	/**
	 * @return boolean
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
			if ( $_REQUEST['action'] === 'inline-save' ) {
				return 'post';
			} else if ( $_REQUEST['action'] === 'inline-save-tax' ) {
				return 'taxonomy';
			}
		}
	}


	protected function get_current_post_type() {
		global $typenow, $pagenow;

		if ( $pagenow === 'upload.php' ) {
			return 'attachment';

		} else if ( $pagenow === 'edit.php' ) {
			return isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : ( ! empty( $typenow ) ? $typenow : 'post' );

		} else if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			if ( $_REQUEST['action'] === 'inline-save' ) {
				return $_REQUEST['post_type'];
			}

		}
	}


	protected function get_available_field_groups() {
		global $typenow, $pagenow;

		if ( is_null( $this->available_field_groups ) ) {

			$content_type = $this->get_current_content_type();

			if ( is_null( $content_type ) ) {
				return null;
			}

			// gather conditions for field parts
			
			if ( $content_type === 'post' ) {
				$conditions = array( 'post_type' => $this->get_current_post_type() );

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					$conditions['post_id'] = intval( $_REQUEST['post_ID'] );
				} else {
					add_filter( 'acf/location/rule_match/post_taxonomy', array( $this, 'match_post_taxonomy' ), 11, 3 );
					add_filter( 'acf/location/rule_match/post_format', array( $this, 'match_post_format' ), 11, 3 );
					add_filter( 'acf/location/rule_match/post_status', array( $this, 'match_post_status' ), 11, 3 );
				}


			} else if ( $content_type === 'taxonomy' ) {

				$conditions = array( 'taxonomy' => $_REQUEST['taxonomy'] );

			} else if ( $content_type === 'user' ) {
				$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : 'all';
				$conditions = array( 'user_role' => $role );
			} else {
				return;
			}


			/**
			 * Getting the Field Groups to be displayed in posts list table
			 *
			 * @param array $conditions	Field group conditions being passed to `acf_get_field_groups()`
			 */
			$this->available_field_groups = acf_get_field_groups( apply_filters( 'acf_quick_edit_fields_group_filter', $conditions ) );

		}

		return $this->available_field_groups;
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