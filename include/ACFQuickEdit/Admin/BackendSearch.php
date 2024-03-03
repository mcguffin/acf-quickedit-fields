<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class BackendSearch extends Feature {

	/**
	 *	@inheritdoc
	 */
	public function get_type() {
		return 'backendsearch';
	}

	/**
	 *	@inheritdoc
	 */
	public function get_fieldgroup_option() {
		return 'allow_backendsearch';
	}

	/**
	 *	@inheritdoc
	 */
	public function load_field( $field ) {
		return wp_parse_args( $field, [
			'allow_backendsearch' => false,
		]);
	}

	/**
	 *	@inheritdoc
	 */
	public function init_fields() {
		$is_active = parent::init_fields();
		if ( $is_active ) {
			$this->init_meta_query();
		}
		return $is_active;
	}


	/**
	 *	@inheritdoc
	 */
	public function parse_query( $query ) {
		if ( ! $query->get('s') ) {
			return;
		}
		global $wpdb;

		$sql = $this->get_search_query($query->get('s'))->get_sql( 'post', $wpdb->posts, 'ID', $query );

		add_filter( 'posts_search', function( $search ) use ( $sql ) {
			$meta_where = preg_replace( '/(^ AND \(|\)$)/', '', $sql['where']);
			$search = preg_replace( '/\)\)\s?$/', '', $search );
			$search = "$search OR $meta_where))";

			return $search;
		});
		add_filter( 'posts_join', function($join) use ( $sql ) {
			if ( strpos( $join, $sql['join'] ) === false ) {
				$join .=  $sql['join'];
			}
			return $join;
		});

		add_filter('posts_groupby', function($group) use( $wpdb ) {
			$posts_group = "{$wpdb->posts}.ID";
			if ( strpos( $group, $posts_group ) === false ) {
				$group .= " {$posts_group}";
			}
			return $group;
		});
	}

	/**
	 *	@inheritdoc
	 */
	public function parse_term_query( $query ) {
		if ( ! $query->query_vars['search'] ) {
			return;
		}
		global $wpdb;

		$sql = $this->get_search_query( $query->query_vars['search'] )->get_sql( 'term', 't', 'term_id', $query );
		$sql['where'] = preg_replace( '/(^ AND \(|\)$)/', '', $sql['where']);

		add_filter( 'terms_clauses', function($clauses) use ( $sql ) {
			// JOIN
			if ( strpos( $clauses['join'], $sql['join'] ) === false ) {
				$clauses['join'] .=  $sql['join'];
			}
			// WHERE
			if ( strpos( $clauses['where'], $sql['where'] ) === false ) {
				$clauses['where'] = preg_replace_callback( '/\(\(.*LIKE.*\)\)/imsU', function($matches) use ($sql) {
					$search = substr($matches[0],0,-1);
					$search .= ' OR ' . $sql['where'] . ')';
					return $search;
				}, $clauses['where'] );
			}

			// GROUP
			$groupby = 'GROUP BY t.term_id';
			if ( false === strpos( $clauses['order'], $groupby ) ) {
				$clauses['orderby'] = " {$groupby} {$clauses['orderby']}";
			}

			return $clauses;
		});
	}

	/**
	 *	@inheritdoc
	 */
	public function pre_get_users( $query ) {
		if ( ! $query->get('search') ) {
			return;
		}
		global $wpdb;

		$sql = $this->get_search_query( $query->get('search') )->get_sql( 'user', $wpdb->users, 'ID', $query );

		add_action( 'pre_user_query', function($query) use ($sql) {
			if ( strpos( $query->query_from, $sql['join'] ) === false ) {
				$query->query_from .=  $sql['join'];
			}
		});

		add_filter( 'user_search_columns', function( $columns ) use ($wpdb) {
			array_map( function($field) use ( &$columns, $wpdb ){
				$columns[] = $wpdb->prepare(
					"{$wpdb->usermeta}.meta_key = %s AND {$wpdb->usermeta}.meta_value",
					$field->get_meta_key()
				);

			}, $this->fields );
			return $columns;
		} );
	}


	/**
	 *	@param string $search
	 *	@return WP_Meta_Query
	 */
	private function get_search_query( $search ) {

		$search_query = [
			'relation' => 'OR',
		];
		array_map( function($field) use ( &$search_query, $search ){
			$search_query[] = [
				'key'     => $field->get_meta_key(),
				'value'   => $search,
				'compare' => 'LIKE',
			];
		}, $this->fields );

		$meta_query = new \WP_Meta_Query();
		$meta_query->parse_query_vars( [ 'meta_query' => $search_query ] );
		return $meta_query;
	}
}
