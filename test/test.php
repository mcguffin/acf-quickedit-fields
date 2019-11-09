<?php

namespace AcfDuplicateRepeater;

class PluginTest {

	private $current_json_save_path = null;

	public function __construct() {

		add_action( 'init', [ $this, 'init' ] );

		add_filter( 'acf/settings/load_json', [ $this, 'load_json' ] );

		add_filter( 'acf/settings/save_json', [ $this, 'save_json' ] );

		add_action( 'acf/delete_field_group', [ $this, 'mutate_field_group' ], 9 );
		add_action( 'acf/trash_field_group', [ $this, 'mutate_field_group' ], 9 );
		add_action( 'acf/untrash_field_group', [ $this, 'mutate_field_group' ], 9 );
		add_action( 'acf/update_field_group', [ $this, 'mutate_field_group' ], 9 );

		add_filter('pll_get_post_types', [ $this, 'pll_content_types'], 10, 2 );
		add_filter('pll_get_taxonomies', [ $this, 'pll_content_types'], 10, 2 );

		add_action( 'restrict_manage_posts', [$this, 'posts_taxonomy_filter'] , 10, 2);

		add_filter('acf/fields/google_map/api', function($api){
			$api['key'] = get_option('google_maps_api_key');
			return $api;
		});
	}

	function posts_taxonomy_filter( $post_type, $which ) {

		// Apply this only on a specific post type
		if ( 'acf-quef-test' !== $post_type )
			return;

		// A list of taxonomy slugs to filter by
		$taxonomies = ['acf-quef-test', 'acf-quef-test-2'];

		foreach ( $taxonomies as $taxonomy_slug ) {

			// Retrieve taxonomy data
			$taxonomy_obj = get_taxonomy( $taxonomy_slug );
			$taxonomy_name = $taxonomy_obj->labels->name;

			// Retrieve taxonomy terms
			$terms = get_terms( $taxonomy_slug );

			// Display filter HTML
			echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
			echo '<option value="">' . sprintf( esc_html__( 'Show All %s', 'text_domain' ), $taxonomy_name ) . '</option>';
			foreach ( $terms as $term ) {
				printf(
					'<option value="%1$s" %2$s>%3$s (%4$s)</option>',
					$term->slug,
					( ( isset( $_GET[$taxonomy_slug] ) && ( $_GET[$taxonomy_slug] == $term->slug ) ) ? ' selected="selected"' : '' ),
					$term->name,
					$term->count
				);
			}
			echo '</select>';
		}

	}


	/**
	*	@filter pll_get_post_types
	*	@filter pll_get_taxonomies
	 */
	public function pll_content_types( $types, $is_settings ) {
		if ( $is_settings ) {
			// hides 'my_cpt' from the list of custom post types in Polylang settings
			unset( $types['acf-quef-test'] );
		} else {
			// enables language and translation management for 'my_cpt'
			$types['acf-quef-test'] = 'acf-quef-test';
		}
		return $types;
	}

	/**
	 *	@action init
	 */
	public function init( $paths ) {
		register_post_type('acf-quef-test',[
			'label'			=> 'Quick Edit Tests',
			'public'		=> true,
			'supports'		=> ['title'],
		]);
		register_taxonomy('acf-quef-test','acf-quef-test',[
			'label'		=> 'QE Test Terms',
			'labels'	=> [
				'no_terms'	=> 'No Terms',
			],
			'public'	=> true,
		]);
		register_taxonomy('acf-quef-test-2','acf-quef-test',[
			'label'		=> 'QE Test Terms 2',
			'labels'	=> [
				'no_terms'	=> 'No Terms',
			],
			'public'	=> true,
			'show_admin_column'	=> true,
		]);

	}

	/**
	 *	@filter 'acf/settings/save_json'
	 */
	public function load_json( $paths ) {
		$paths[] = dirname(__FILE__).'/acf-json';
		return $paths;
	}

	/**
	 *	@filter 'acf/settings/save_json'
	 */
	public function save_json( $path ) {
		if ( ! is_null( $this->current_json_save_path ) ) {
			return $this->current_json_save_path;
		}
		return $path;
	}

	/**
	 *	Figure out where to save ACF JSON
	 *
	 *	@action 'acf/update_field_group'
	 */
	public function mutate_field_group( $field_group ) {
		// default

		if ( strpos( $field_group['key'], 'group_acf_qef_' ) === false ) {
			$this->current_json_save_path = null;
			return;
		}
		$this->current_json_save_path = dirname(__FILE__).'/acf-json';

	}
}

new PluginTest();
