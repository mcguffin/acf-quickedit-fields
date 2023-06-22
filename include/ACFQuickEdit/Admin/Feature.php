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

		$this->admin = Admin::instance();

		if ( wp_doing_ajax() ) {
			add_action( 'admin_init', [ $this, 'init_fields' ] );
		} else {
			add_action( 'current_screen', [ $this, 'init_fields' ] );
		}

		add_filter( 'acf/load_field', [ $this, 'load_field' ] );

		parent::__construct();
	}


	/**
	 *	@filter acf/load_field
	 */
	 abstract public function load_field( $field );

	/**
	 *	@param string $content_kind
	 */
	public function init_meta_query() {

		$content_kind = CurrentView::instance()->get_object_kind();

		if ( 'post' === $content_kind && ! has_action( 'pre_get_posts', [ $this, 'parse_query' ] ) ) {

			add_action( 'pre_get_posts', [ $this, 'parse_query' ] );

		} else if ( 'term' === $content_kind && ! has_action( 'parse_term_query', [ $this, 'parse_term_query' ] ) ) {

			add_action( 'parse_term_query', [ $this, 'parse_term_query' ] );

		} else if ( 'user' === $content_kind && ! has_filter( 'pre_get_users', [ $this, 'pre_get_users' ] )  ) {

			add_filter( 'pre_get_users', [ $this, 'pre_get_users' ] );

		}

	}

	/**
	 *	@action pre_get_posts
	 */
	public function parse_query( $query ) {

		if ( $meta_query = $this->get_meta_query( $query ) ) {

			$query->set( 'meta_key', "" );
			$query->set( 'meta_query', $meta_query );

		}
	}

	/**
	 *	@action parse_term_query
	 */
	public function parse_term_query( $query ) {

		// Note: WP_Term_Query does not have a get() method.
		if ( $meta_query = $this->get_meta_query( $query ) ) {
			$query->query_vars['meta_key'] = '';
			$query->query_vars['meta_query'] = $meta_query;
		}
	}

	/**
	 *	@action pre_get_users
	 */
	public function pre_get_users( $query ) {

		// Note: WP_User_Query does not have a get() method.
		if ( $meta_query = $this->get_meta_query( $query ) ) {
			$query->query_vars['meta_query'] = $meta_query;
		}
	}

	/**
	 *	@param string $by Column to sort on
	 */
	protected function get_meta_query( $wp_query = null ) {

		if ( ! isset( $_REQUEST['meta_query'] ) ) {
			if ( ! is_null( $wp_query ) && isset( $wp_query->query_vars['meta_query'] ) ) {
				return $wp_query->query_vars['meta_query'];
			} else {
				return [];
			}
		}

		$meta_query = wp_unslash( $_REQUEST['meta_query'] );

		$meta_query = array_filter( $meta_query, function($clause) {
			if ( ! is_array( $clause ) ) {
				return true;
			}
			$clause = wp_parse_args( $clause, [ 'value' => '' ] );
			return $clause['value'] !== '';
		} );
		if ( 1 === count( $meta_query ) && isset( $meta_query['relation'] ) ) {
			$meta_query = [];
		}

		return apply_filters( 'acf_qef_meta_query_request', $meta_query );
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
	 *	@return boolean
	 *	@action admin_init
	 */
	public function init_fields() {

		$current_view = CurrentView::instance();

		if ( ! in_array( $current_view->get_object_kind(), ['post','term','user'] ) ) {
			return false;
		}

		$field_store = acf_get_store( 'fields' );


		$fields_query = [];
		$fields_query[ $this->get_fieldgroup_option() ] = true;

		$current_fields = $current_view->get_fields( $fields_query );

		foreach ( $current_fields as $field ) {

			if ( ! $this->supports( $field[ 'type' ] ) ) {
				continue;
			}

			$field = $this->load_field( $field );
			$field_store->set( $field['key'], $field );

			if ( $field_object = Fields\Field::getFieldObject( $field ) ) {
				$this->add_field( $field_object->get_meta_key(), $field_object, false );
			}
		}

		return $this->is_active();
	}
}
