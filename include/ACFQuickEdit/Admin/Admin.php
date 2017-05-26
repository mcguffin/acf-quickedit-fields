<?php

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class Admin extends Core\Singleton {

	private $column_fields = array();	

	private $quickedit_fields = array();	

	private $quickedit_field_groups = array();	

	private $bulkedit_fields = array();	

	private $bulkedit_field_groups = array();	

	private $_wp_column_weights = array();	


	/**
	 * Private constructor
	 */
	protected function __construct() {
		$this->core = Core\Core::instance();
		add_action( 'after_setup_theme' , array( $this , 'setup' ) );
	}

	/**
	 * Setup plugin
	 *
	 * @action plugins_loaded
	 */
	public function setup() {

		if ( class_exists( 'acf' ) && function_exists( 'acf_get_field_groups' ) ) {
			add_action( 'admin_init' , array( $this, 'admin_init' ) );
			add_action( 'admin_init' , array( $this, 'init_columns' ) );
			add_action( 'load-admin-ajax.php' , array( $this, 'init_columns' ) );
			add_action( 'wp_ajax_get_acf_post_meta' , array( $this, 'ajax_get_acf_post_meta' ) );
			add_action( 'load-edit.php' , array( $this, 'enqueue_assets' ) );
			add_action( 'load-edit-tags.php' , array( $this, 'enqueue_assets' ) );
		} else if ( class_exists( 'acf' ) && current_user_can( 'activate_plugins' ) ) {
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
		

		// Suported ACF Fields
		$types = array( 
			// basic
			'text'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'textarea'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'number'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'email'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'url'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'password'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => false ),

			// Content
			'wysiwyg'			=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'oembed'			=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'image'				=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ), 
			'file'				=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ), 
			'gallery'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),

			// Choice
			'select'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'checkbox'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'radio'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'true_false'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 

			// relational
			'post_object'		=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ), 
			'page_link'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'relationship'		=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ), 
			'taxonomy'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'user'				=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),

			// jQuery
			'google_map'		=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'date_picker'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'date_time_picker'	=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'time_picker'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'color_picker'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			
			// Layout (unsupported)
			'message'			=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'tab'				=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'repeater'			=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'flexible_content'	=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'clone'				=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
		);

		/**
		 * Filter field type support of ACF Quick Edit Fields
		 *
		 * @param array $fields		An associative array of field type support having the ACF field name as keys 
		 *							and an array of supported fetaures as values. 
		 *							Features are 'column', 'quickedit' and 'bulkedit'.
		 */
		$types = apply_filters( 'acf_quick_edit_fields_types', $types );

		foreach ( $types as $type => $supports ) {
			if ( $supports['column'] ) {
				add_action( "acf/render_field_settings/type={$type}" , array( $this , 'render_column_settings' ) );
			}
			if ( $supports['quickedit'] ) {
				add_action( "acf/render_field_settings/type={$type}" , array( $this , 'render_quick_edit_settings' ) );
			}
			if ( $supports['bulkedit'] ) {
				add_action( "acf/render_field_settings/type={$type}" , array( $this , 'render_bulk_edit_settings' ) );
			}
		}
	}

	/**
	 * @filter 'acf/format_value/type=radio'
	 */
	function format_radio( $value, $post_id, $field ) {
		if ( ( $nice_value = $field['choices'][$value]) ) {
			return $nice_value;
		}
		return $value;
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	function render_column_settings( $field ) {
		$post = get_post( $field['ID'] );
		if ( $post ) {
			$parent = get_post( $post->post_parent );
		
			if ( $parent->post_type == 'acf-field-group' ) {
				// show column: todo: allow sortable
				acf_render_field_setting( $field, array(
					'label'			=> __('Show Column','acf-quick-edit-fields'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'show_column',
					'message'		=> __("Show a column in the posts list table", 'acf-quick-edit-fields'),
					'width'			=> 50,
				));

				acf_render_field_setting( $field, array(
					'label'			=> __('Column Weight','acf-quick-edit-fields'),
					'instructions'	=> __('Columns with a higher weight will be pushed to the right. The leftmost WordPress column has a weight of <em>0</em>, the next one <em>100</em> and so on. Leave empty to place a column to the rightmost position.','acf-quick-edit-fields'),
					'type'			=> 'number',
					'name'			=> 'show_column_weight',
					'message'		=> __("Column Weight", 'acf-quick-edit-fields'),
					'default_value'	=> '0',
					'min'			=> '-10000',
					'max'			=> '10000',
					'step'			=> '1',
					'placeholder'	=> '',
					'width'			=> '50',
				));
			}
		}
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	function render_quick_edit_settings( $field ) {
		$post = get_post( $field['ID'] );
		if ( $post ) {
			$parent = get_post( $post->post_parent );
			$parent = get_post( $post->post_parent );

			if ( $parent->post_type == 'acf-field-group' ) {
				// add to quick edit
				acf_render_field_setting( $field, array(
					'label'			=> __('Allow QuickEdit','acf-quick-edit-fields'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'allow_quickedit',
					'message'		=> __("Allow editing this field in QuickEdit mode", 'acf-quick-edit-fields')
				));
		
			}
		}
	}

	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	function render_bulk_edit_settings( $field ) {
		$post = get_post($field['ID']);
		if ( $post ) {
			$parent = get_post( $post->post_parent );
			$parent = get_post( $post->post_parent );

			if ( $parent->post_type == 'acf-field-group' ) {
				// show column: todo: allow sortable
				// add to bulk edit
				acf_render_field_setting( $field, array(
					'label'			=> __('Allow Bulk Edit','acf-quick-edit-fields'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'allow_bulkedit',
					'message'		=> __("Allow editing this field in Bulk edit mode", 'acf-quick-edit-fields')
				));
			}
		}
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

	/**
	 * @action 'admin_init'
	 */
	function init_columns( $cols ) {
		global $typenow, $pagenow;
		
		$content_type = null;

		// gather conditions for field parts

		if ( $pagenow == 'upload.php' ) {

			$content_type = 'post';

			$post_type = 'attachment';

			$conditions = array( 'attachment' => 'all|image' );

		} else if ( $pagenow == 'edit.php' ) {

			$content_type = 'post';

			$post_type = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : ( ! empty( $typenow ) ? $typenow : 'post' );

			$conditions = array( 'post_type' => $post_type );

			add_filter( 'acf/location/rule_match/post_taxonomy', array( $this, 'match_post_taxonomy' ), 11, 3 );
			add_filter( 'acf/location/rule_match/post_format', array( $this, 'match_post_format' ), 11, 3 );
			add_filter( 'acf/location/rule_match/post_status', array( $this, 'match_post_status' ), 11, 3 );

		} else if ( $pagenow == 'edit-tags.php' ) {

			if ( taxonomy_exists( $_REQUEST['taxonomy'] ) ) {

				$content_type = 'taxonomy';

				$taxonomy = $_REQUEST['taxonomy'];

				$conditions = array( 'taxonomy' => $_REQUEST['taxonomy'] );
			}
		} else if ( $pagenow == 'users.php' ) {

			$content_type = 'user';

			$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : 'all';

			$conditions = array( 'user_role' => $role );

		} else if ( defined( 'DOING_AJAX' ) && DOING_AJAX  ) {

			if ( $_REQUEST['action'] === 'inline-save' /* && isset( $_REQUEST['post_ID'] ) && isset($_REQUEST['post_type']) */ ) {

				$content_type = 'post';

				$post_type = $_REQUEST['post_type'];

				$conditions = array( 
					'post_type'	=> $post_type,
					'post_id'	=> intval( $_REQUEST['post_ID'] ),
				);

			} else if ( $_REQUEST['action'] === 'inline-save-tax' ) {

				$content_type = 'taxonomy';

				$taxonomy = $_REQUEST['taxonomy'];

				$conditions = array( 'taxonomy' => $_REQUEST['taxonomy'] );
			}
		}
		if ( is_null( $content_type ) ) {
			return;
		}


		/**
		 * Getting the Field Groups to be displayed in posts list table
		 *
		 * @param array $conditions	Field group conditions passed to `acf_get_field_groups()`
		 */
		$field_groups = acf_get_field_groups( apply_filters( 'acf_quick_edit_fields_group_filter', $conditions ) );

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group );

			if ( ! $fields ) {
				continue;
			}

			foreach ( $fields as $field ) {
				$field_object = Fields\Field::getField( $field );
				// register column display
				if ( isset($field['show_column']) && $field['show_column'] ) {
					$this->column_fields[$field['name']] = $field_object;
				}
				if ( 'user' != $content_type ) {
					// register bulk and quick edit
					if ( isset($field['allow_quickedit']) && $field['allow_quickedit'] ) {
						$this->quickedit_fields[ $field['name'] ] = $field_object;

						if ( ! isset( $this->quickedit_field_groups[ $field_group['ID'] ] ) ) {
							$this->quickedit_field_groups[ $field_group['ID'] ] = $field_group + array( 'rendered' => false );
						}
						$this->quickedit_field_groups[ $field_group['ID'] ]['fields'][ $field['name'] ] = $field_object;
					}
					if ( isset($field['allow_bulkedit']) && $field['allow_bulkedit'] ) {
						$this->bulkedit_fields[$field['name']] = $field_object;

						if ( ! isset( $this->bulkedit_field_groups[ $field_group['ID'] ] ) ) {
							$this->bulkedit_field_groups[ $field_group['ID'] ] = $field_group + array( 'rendered' => false );
						}
						$this->bulkedit_field_groups[ $field_group['ID'] ]['fields'][ $field['name'] ] = $field_object;
					}
				}
			}
		}

		if ( count( $this->column_fields ) ) {
			if ( 'post' == $content_type ) {
				if ( 'post' == $post_type ) {
					$cols_hook		= 'manage_posts_columns';
					$display_hook	= 'manage_posts_custom_column';
				} else if ( 'page' == $post_type ) {
					$cols_hook		= 'manage_pages_columns';
					$display_hook	= 'manage_pages_custom_column';
				} else if ( 'attachment' == $post_type ) {
					$cols_hook		= 'manage_media_columns';
					$display_hook	= 'manage_media_custom_column';
				} else {
					$cols_hook		= "manage_{$post_type}_posts_columns";
					$display_hook	= "manage_{$post_type}_posts_custom_column";
				}
				add_filter( $cols_hook,		array( $this, 'move_date_to_end' ), 11 );
				add_filter( $display_hook,	array( $this, 'display_post_field_column' ), 10, 2 );
			} else if ( 'taxonomy' == $content_type ) {
				$cols_hook		= "manage_edit-{$taxonomy}_columns";
				$display_hook	= "manage_{$taxonomy}_custom_column";
				add_filter( $display_hook,	array( $this, 'display_term_field_column' ), 10, 3 );
			} else if ( 'user' == $content_type ) {
				$cols_hook		= "manage_users_columns";
				$display_hook	= "manage_users_custom_column";
				add_filter( $display_hook,	array( $this, 'display_user_field_column' ), 10, 3 );
			}
			add_filter( $cols_hook,		array( $this, 'add_field_columns' ) );
		}

		if ( count( $this->column_fields ) ) {
			$has_thumbnail		= false;
			foreach ( $this->column_fields as $field_object ) {
				$field = $field_object->get_acf_field();
				if ( $field['type'] == 'image' || $field['type'] == 'gallery' ) {
					$has_thumbnail = true;
					break;
				}
			}
		}

		wp_enqueue_style( 'acf-qef-thumbnail-col', plugins_url( 'css/thumbnail-col.css', ACFQUICKEDIT_FILE ) );
		
		// register quickedit
		if ( count( $this->quickedit_fields ) ) {
			// enqueue scripts ...
			add_action( 'quick_edit_custom_box',  array( $this, 'display_quick_edit' ), 10, 2);
			if ( $content_type == 'post' ) {
				add_action( 'save_post', array( $this, 'quickedit_save_acf_post_meta' ) );
			} else if ( $content_type = 'taxonomy' ) {
				add_action( 'edit_term', array( $this, 'quickedit_save_acf_term_meta' ), 10, 3 );
			}

		}

		// register bulkedit
		if ( count( $this->bulkedit_fields ) ) {
			add_action( 'bulk_edit_custom_box', array( $this , 'display_bulk_edit' ), 10, 2 );
		}
	}

	/**
	 * @action 'load-edit.php'
	 */
	function enqueue_assets() {
		global $typenow, $pagenow;
		if ( count( $this->column_fields ) ) {
			$has_thumbnail		= false;
			foreach ( $this->column_fields as $field_object ) {
				$field = $field_object->get_acf_field();
				if ( $field['type'] == 'image' || $field['type'] == 'gallery' ) {
					$has_thumbnail = true;
					break;
				}
			}
		}

		// register quickedit
		if ( count( $this->quickedit_fields ) ) {
			// enqueue scripts ...
			$has_datepicker		= false;
			$has_colorpicker	= false;
			foreach ( $this->quickedit_fields as $field_object ) {
				$field = $field_object->get_acf_field();
				if ( $field['type'] == 'date_picker' || $field['type'] == 'time_picker' || $field['type'] == 'date_time_picker'  ) {
					$has_datepicker = true;
				}
				if ( $field['type'] == 'color_picker' ) {
					$has_colorpicker = true;
				}
			}

			// ... if necessary
			if ( $has_datepicker ) {
				// datepicker
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style('acf-datepicker', acf_get_dir('assets/inc/datepicker/jquery-ui.min.css') );

				// timepicker. Contains some usefull parsing mathods even for dates.
				wp_enqueue_script('acf-timepicker', acf_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.js'), array('jquery-ui-datepicker') );
				wp_enqueue_style('acf-timepicker', acf_get_dir('assets/inc/timepicker/jquery-ui-timepicker-addon.min.css') );
			}

			if ( $has_colorpicker ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}

			wp_enqueue_style( 'acf-quick-edit', plugins_url( 'css/acf-quickedit.css', ACFQUICKEDIT_FILE ) );
			if ( $pagenow == 'edit-tags.php' ) {
				wp_enqueue_script( 'acf-quick-edit', plugins_url( 'js/acf-quickedit.min.js', ACFQUICKEDIT_FILE ), array( 'inline-edit-tax' ), null, true );
			} else {
				wp_enqueue_script( 'acf-quick-edit', plugins_url( 'js/acf-quickedit.min.js', ACFQUICKEDIT_FILE ), array( 'inline-edit-post' ), null, true );
			}
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

					$field_obj = get_field_object( $key , $post_id );

					switch ( $field_obj['type'] ) {
						case 'date_time_picker':
						case 'time_picker':
						case 'date_picker':
							$field_val	= acf_get_metadata( $post_id, $field_obj['name'] );
							break;
						default:
							$field_val	= $field_obj['value'];
							break;
					}
					if ( ! isset( $result[ $key ] ) || $result[ $key ] == $field_val ) {

						$result[ $key ]	= $field_val;

					} else {

						$result[ $key ] = '';

					}
				}
			}

			echo json_encode( $result );

			exit();
		}
	}

	/**
	 * @filter manage_posts_columns
	 * @filter manage_media_columns
	 * @filter manage_{$post_type}_posts_columns
	 */
	function add_field_columns( $columns ) {

		$this->_wp_column_weights = array_map( array( $this, '_mul_100' ) , array_flip( array_keys( $columns ) ) );

		foreach ( $this->column_fields as $field_slug => $field_object ) {
			$field = $field_object->get_acf_field();
			if ( in_array( $field['type'], array('image','gallery'))) {
				$field_slug .= '-qef-thumbnail';
			}
			$columns[ $field_slug ] = $field['label'];
		}
		uksort($columns, array( $this, '_sort_columns_by_weight' ));
		return $columns;
	}

	private function _mul_100( $val ) {
		return intval( $val ) * 100;
	}

	/**
	 * @private
	 */
	private function _sort_columns_by_weight( $a_slug, $b_slug ) {
		$a = $b = 0;
		$a = $this->_get_column_weight( $a_slug );
		$b = $this->_get_column_weight( $b_slug );
		return $a - $b;
	}

	private function _get_column_weight( $column_slug ) {

		$column_slug = str_replace('-qef-thumbnail','',$column_slug);

		if ( isset( $this->_wp_column_weights[ $column_slug ] ) ) {
			return intval( $this->_wp_column_weights[ $column_slug ] );
		}
		
		if ( isset( $this->column_fields[ $column_slug ] ) ) {
			$field_object = $this->column_fields[ $column_slug ];
			$field = $field_object->get_acf_field();
			if ( isset( $field['show_column_weight'] ) ) {
				return intval( $field['show_column_weight'] );
			}
		}

		return max( $this->_wp_column_weights ) + 1;
	}

	/**
	 * @action manage_posts_custom_column
	 * @action manage_media_custom_column
	 * @action manage_{$post_type}_posts_custom_column
	 */
	function display_post_field_column( $wp_column_slug , $object_id ) {
		echo $this->display_field_column( $wp_column_slug , $object_id );
	}

	/**
	 * @action manage_edit-{$taxonomy}_custom_column
	 */
	function display_term_field_column( $content, $wp_column_slug , $object_id ) {

		$object = get_term( $object_id );

		if ( $object ) {

			return $this->display_field_column( $wp_column_slug , sprintf( '%s_%s', $object->taxonomy, $object_id ) );

		}
	}

	/**
	 * @action manage_user_custom_column
	 */
	function display_user_field_column( $content, $wp_column_slug , $object_id ) {
		
		return $this->display_field_column( $wp_column_slug , sprintf( 'user_%s', $object_id ) );

	}

	function display_field_column( $wp_column_slug , $object_id ) {

		$args = func_get_args();

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );

		if ( isset( $this->column_fields[$column] ) ) {
			$field_object = $this->column_fields[$column];
			return $field_object->render_column( $object_id );
		}
		return '';
	}

	private function get_post_object_link( $post_id ) {
		$result = '';
		$title = get_the_title( $post_id );

		if ( current_user_can( 'edit_post', $post_id ) ) {
			$result .= sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post_id ), $title );
		} else if ( current_user_can( 'read_post', $post_id ) ) {
			$result .= sprintf( '<a href="%s">%s</a>', get_permalink( $post_id ), $title );
		} else {
			$result .= $title;
		}

		if ( 'attachment' !== get_post_type( $post_id ) && 'private' === get_post_status( $post_id ) ) {	
			$result .= ' &mdash; ' . __('Private', 'acf-quick-edit-fields' );
		}
		return $result;
	}

	function move_date_to_end($defaults) {  
	    $date = $defaults['date'];
	    unset($defaults['date']);
	    $defaults['date'] = $date;
	    return $defaults; 
	} 

	function display_quick_edit( $wp_column_slug, $post_type ) {

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );

		if ( isset( $this->quickedit_fields[$column] ) && $field_object = $this->quickedit_fields[ $column ] ) {
			$field = $field_object->get_acf_field();

			$field_group = $this->quickedit_field_groups[ $field['parent'] ];

			if ( $field_group['rendered'] ) {
				return;
			}

			printf( '<fieldset class="inline-edit-col-qed inline-edit-%s acf-quick-edit">', $post_type );
			printf( '<legend>%s</legend>', $field_group['title'] );
				

			foreach ( $field_group['fields'] as $sub_field_object ) {
				$sub_field_object->render_quickedit_field( $column, $post_type, 'quick' );
			}
			echo '</fieldset>';
			$this->quickedit_field_groups[ $field['parent'] ]['rendered'] = true;
		}

	}

	function display_bulk_edit( $wp_column_slug, $post_type ) {

		$column = str_replace('-qef-thumbnail','', $wp_column_slug );

		if ( isset($this->bulkedit_fields[ $column ]) && $field_object = $this->bulkedit_fields[$column] ) {

			$field_object->render_quickedit_field( $column, $post_type, 'bulk' );

		}

	}

	/**
	 *	@action save_term
	 */
	function quickedit_save_acf_term_meta( $term_id, $tt_id, $taxonomy ) {

		$object_id = sprintf( '%s_%s', $taxonomy, $term_id );

		if ( ! current_user_can( 'edit_term', $term_id ) ) {
			return;
		}

		return $this->quickedit_save_acf_meta( $object_id, true );
	}

	/**
	 *	@action save_post
	 */
	function quickedit_save_acf_post_meta( $post_id ) {

		$is_quickedit = is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		return $this->quickedit_save_acf_meta( $post_id, $is_quickedit );
	}

	function quickedit_save_acf_meta( $post_id, $is_quickedit = true ) {

		foreach ( $this->quickedit_fields as $field_name => $field_object ) {
			/*
			$param_name = $this->core->prefix( $field['name'] );
			switch ( $field['type'] ) {
				case 'checkbox':
					$do_update	= true;
					$value		= isset( $_REQUEST[ $param_name ] ) 
									? $_REQUEST[ $param_name ] 
									: null;
					break;
				default:
					$do_update	= $is_quickedit 
									? isset( $_REQUEST[ $param_name ] )
									: isset( $_REQUEST[ $param_name ] ) && ! empty( $_REQUEST[ $param_name ] );
					$value		= $_REQUEST[ $param_name ];
					break;
			}
			if ( $do_update ) {
				update_field( $field['name'], $value, $post_id );
			}
			/*/
			$field_object->update( $post_id, $is_quickedit );
			//*/
		}
	}
}