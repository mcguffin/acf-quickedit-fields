<?php

/*
Plugin Name: ACF QuickEdit Fields
Plugin URI: http://wordpress.org/
Description: Show Advanced Custom Fields in post list table. Edit field values in Quick Edit and / or Bulk edit.
Author: Jörn Lund
Version: 1.0.1
Author URI: 
License: GPL3
*/

/*  Copyright 2015  Jörn Lund

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! class_exists( 'ACFToQuickEdit' ) ) :
class ACFToQuickEdit {
	private static $_instance = null;
	private $post_field_prefix = 'acf_qed_';

	private $column_fields = array();	
	private $quickedit_fields = array();	
	private $bulkedit_fields = array();	

	private $_left_columns = array();	

	/**
	 * Getting a singleton.
	 *
	 * @return object single instance of SteinPostTypePerson
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}
	/**
	 * Private constructor
	 */
	private function __construct() {
		add_action( 'plugins_loaded' , array( &$this , 'load_textdomain' ) );
		add_action( 'after_setup_theme' , array( &$this , 'setup' ) );
	}


	
	/**
	 * Hooked on 'plugins_loaded' 
	 * Load text domain
	 *
	 * @action 'plugins_loaded'
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'acf-quick-edit-fields' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	/**
	 * Setup plugin
	 *
	 * @action 'plugins_loaded'
	 */
	public function setup() {
		if ( class_exists( 'acf' ) ) {
			add_action( 'admin_init' , array(&$this,'admin_init') );
			add_action( 'admin_init' , array( &$this , 'init_columns' ) );
			add_action( 'load-admin-ajax.php' , array( &$this , 'init_columns' ) );
			add_action( 'wp_ajax_get_acf_post_meta' , array( &$this , 'ajax_get_acf_post_meta' ) );
			$this->_left_columns = apply_filters( 'acf_quick_edit_left_columns', array( 'cb' ) );
		}
	}
	
	/**
	 * @action 'admin_init'
	 */
	function admin_init() {
		// Suported ACF Fields
		$types_column = array( 'file' , 'image' , 'checkbox' , 'color_picker' , 'date_picker' , 'email' , 'number' , 'radio' , 'select' , 'text' , 'textarea' , 'true_false' , 'url' );
		$types_can_qe = array( 'checkbox' , 'color_picker' , 'date_picker' , 'email' , 'number' , 'radio' , 'select' , 'text' , 'textarea' , 'true_false' , 'url' );
		$types_can_be = array( 'checkbox' , 'color_picker' , 'date_picker' , 'email' , 'number' , 'radio' , 'select' , 'text' , 'textarea' , 'true_false' , 'url' );
		foreach ( $types_column as $type ) {
			add_action( "acf/render_field_settings/type={$type}" , array( &$this , 'render_column_settings' ) );
		}
		foreach ( $types_can_qe as $type ) {
			add_action( "acf/render_field_settings/type={$type}" , array( &$this , 'render_quick_edit_settings' ) );
		}
		foreach ( $types_can_be as $type ) {
			add_action( "acf/render_field_settings/type={$type}" , array( &$this , 'render_bulk_edit_settings' ) );
		}
	}
	
	/**
	 * @filter 'acf/format_value/type=radio'
	 */
	function format_radio( $value, $post_id, $field ) {
		if ( ( $nice_value = $field['choices'][$value]) )
			return $nice_value;
		return $value;
	}
	
	/**
	 * @action 'acf/render_field_settings/type={$type}'
	 */
	function render_column_settings( $field ) {
		$post = get_post($field['ID']);
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
					'instructions'	=> __('Columns with a lower value will be shifted left','acf-quick-edit-fields'),
					'type'			=> 'number',
					'name'			=> 'show_column_weight',
					'message'		=> __("Column weight", 'acf-quick-edit-fields'),
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
		$post = get_post($field['ID']);
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
	 * @action 'admin_init'
	 */
	function init_columns( $cols ) {
		global $typenow, $pagenow;
		$post_type = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : ( ! empty( $typenow ) ? $typenow : 'post' );
		if ( ! $post_type && $pagenow == 'upload.php' ) {
			$post_type = 'attachment';
			$field_groups = acf_get_field_groups( apply_filters( 'acf_quick_edit_fields_group_filter', array( 'attachment' => 'all|image' ) ) );
		} else {
			$field_groups = acf_get_field_groups( apply_filters( 'acf_quick_edit_fields_group_filter', array( 'post_type' => $post_type ) ) );
		}

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields($field_group);
			foreach ( $fields as $field ) {
				if ( isset($field['show_column']) && $field['show_column'] ) {
					$this->column_fields[$field['name']] = $field;
				}
				if ( isset($field['allow_quickedit']) && $field['allow_quickedit'] ) {
					$this->quickedit_fields[$field['name']] = $field;
				}
				if ( isset($field['allow_bulkedit']) && $field['allow_bulkedit'] ) {
					$this->bulkedit_fields[$field['name']] = $field;
				}
			}
		}
		if ( count( $this->column_fields ) ) {
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
			add_filter( $cols_hook ,    array( &$this , 'add_field_columns' ) );
			add_filter( $cols_hook , 	array( &$this , 'move_date_to_end' ) );
			add_filter( $display_hook , array( &$this , 'display_field_column' ) , 10 , 2 );
		}
		if ( count( $this->quickedit_fields ) ) {
			add_action( 'quick_edit_custom_box',  array(&$this,'display_quick_edit') , 10, 2);
			add_action( 'save_post', array( &$this , 'quickedit_save_acf_meta' ) );
			wp_enqueue_script( 'acf-quick-edit', plugins_url('js/acf-quickedit.js', __FILE__), array('jquery-ui-datepicker','inline-edit-post','wp-color-picker' ), null, true );
			wp_enqueue_style('acf-datepicker');
		}
		
		if ( count( $this->bulkedit_fields ) ) {
			add_action( 'bulk_edit_custom_box', array( &$this , 'display_bulk_edit' ), 10, 2 );
// 			add_action( 'post_updated', array( &$this , 'quickedit_save_acf_meta' ) );
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
			array_filter( $post_ids,'intval');
			foreach ( $post_ids as $post_id ) {
				if ( current_user_can( 'edit_post' , $post_id ) ) {
					$field_keys = $_REQUEST['acf_field_keys'];
					foreach ( $field_keys as $key ) {
						$field_obj = get_field_object( $key , $post_id );
						if ( ! isset( $result[ $key ] ) || $result[ $key ] == $field_obj['value'] ) 
							$result[ $key ] = $field_obj['value'];
						else 
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
		foreach ( $this->column_fields as $field_slug => $field ) {
			$columns[ $field_slug ] = $field['label'];
		}
		uksort($columns, array( $this, '_sort_columns_by_weight' ));
		return $columns;
	}

	/**
	 * @private
	 */
	private function _sort_columns_by_weight( $a_slug, $b_slug ) {
		$a = $b = 0;
		if ( in_array( $a_slug ,  $this->_left_columns ) ) {
			$a = -10001;
		} else if ( isset( $this->column_fields[ $a_slug ] ) ) {
			$a = $this->column_fields[ $a_slug ]['show_column_weight'];
		}
		if ( in_array( $b_slug ,  $this->_left_columns ) ) {
			$b = -10001;
		} else if ( isset( $this->column_fields[ $b_slug ] ) ) {
			$b = $this->column_fields[ $b_slug ]['show_column_weight'];
		}
		return $a - $b;
	}
	
	/**
	 * @filter manage_posts_custom_column
	 * @filter manage_media_custom_column
	 * @filter manage_{$post_type}_posts_custom_column
	 */
	function display_field_column( $column , $post_id ) {
		if ( isset( $this->column_fields[$column] ) ) {
			$field = $this->column_fields[$column];
			switch ( $field['type'] ) {
				case 'file':
					$value = acf_get_value( $post_id, $field );
					if ( ! is_null($value) && ! empty($value) ) {
						$file = get_post($value);
						printf( __('Edit: <a href="%s">%s</a>','acf-quick-edit-fields') , get_edit_post_link( $value ) , $file->post_title );
					}
					break;
				case 'image':
					$image_id = get_field( $field['key'] );
					if ( $image_id ) {
						if ( is_array( $image_id ) ) {
							// Image field is an object
							echo wp_get_attachment_image( $image_id['id'] , array(80,80) );
						} else if( is_numeric( $image_id ) ) {
							// Image field is an ID
							echo wp_get_attachment_image( $image_id , array(80,80) );
						} else {
							// Image field is a url
							echo '<img src="' . $image_id . '" width="80" height="80" />';
						};
					}
					break;
				case 'select':
				case 'radio':
				case 'checkbox':
					$field_value = get_field($field['key']);
					$values = array();
					foreach ( (array) $field_value as $value )
						$values[] = isset( $field['choices'][ $value ] ) 
										? $field['choices'][ $value ] 
										: $value;
					
					$output = implode( __(', ', 'acf-quick-edit-fields' ) , $values );
					if ( empty( $output ) )
						$output = __('(No value)', 'acf-quick-edit-fields');
					echo $output;
					break;
				case 'true_false':
					echo get_field($field['key']) ? __('Yes') : __('No');
					break;
				case 'color_picker':
					$value = get_field($field['key']);
					if ( $value )
						echo '<div class="color-indicator" style="border-radius:2px;border:1px solid #d2d2d2;width:26px;height:20px;background-color:'.$value.'"></div>';
					else
						_e('(No value)', 'acf-quick-edit-fields');
					break;
				case 'number':
					$value = get_field($field['key']);
					if ( $value === "" )
						_e('(No value)', 'acf-quick-edit-fields');
					else
						echo number_format_i18n($value, strlen(substr(strrchr($value, "."), 1)) );
					break;
				case 'textarea':
					?><pre><?php
						the_field($field['key']);
					?></pre><?php
					break;
				default:
					the_field($field['key']);
					break;
			}
		}
	}

	function move_date_to_end($defaults) {  
	    $date = $defaults['date'];
	    unset($defaults['date']);
	    $defaults['date'] = $date;
	    return $defaults; 
	} 
	
	function display_quick_edit( $column, $post_type ) {
		if ( isset($this->quickedit_fields[$column]) && $field = $this->quickedit_fields[$column] ) {
			$this->display_quickedit_field( $column, $post_type , $field  );
		}
	}
	function display_bulk_edit( $column, $post_type ) {
		if ( isset($this->bulkedit_fields[$column]) && $field = $this->bulkedit_fields[$column] ) {
			$this->display_quickedit_field( $column, $post_type , $field  );
		}
	}
	function display_quickedit_field( $column, $post_type , $field ) {
		?><fieldset class="inline-edit-col-left inline-edit-<?php echo $post_type ?>"><?php 
			?><div class="inline-edit-col column-<?php echo $column; ?>"><?php 
				?><label class="inline-edit-group"><?php 
					?><span class="title"><?php echo $field['label']; ?></span><?php
					?><span class="input-text-wrap"><?php
						$input_atts = array(
							'data-acf-field-key' => $field['key'],
							'name' => $this->post_field_prefix . $column,
						);
						switch ($field['type']) {
							case 'checkbox':
								$input_atts += array(
									'class' => 'acf-quick-edit',
									'id' => $this->post_field_prefix . $column,
								);
								$field['value'] = acf_get_array($field['value'], false);
								$input_atts['name'] .= '[]';
								foreach($field['choices'] as $value => $label) {
									$atts = array(
										'type' => 'checkbox',
										'value' => $value ) + $input_atts;
									if( in_array($value, $field['value']) ) {
										$atts['checked'] = 'checked';
									} else {
										$all_checked = false;
									}
									$atts['id'] .= '-'.$value;
									echo '<label><input ' . acf_esc_attr( $atts ) . '/>' . $label . '</label>';
								}
								break;
							case 'select':
								$input_atts += array(
									'class' => 'acf-quick-edit widefat',
									'id' => $this->post_field_prefix . $column,
								);
								if ( $field['multiple'] )
									$input_atts['multiple'] = 'multiple';

								?><select <?php echo acf_esc_attr( $input_atts ) ?>><?php
									if ( $field['allow_null'] ) {
										echo '<option value="">' . '- ' . __( 'Select', 'acf' ) . ' -';
									}
									foreach($field['choices'] as $name => $label) {
										echo '<option value="' . $name . '">' . $label;
									}
								?></select><?php
								break;
							case 'radio':
								// + others
								// 
								
								?><ul class="acf-radio-list<?php echo $field['other_choice'] ? ' other' : '' ?>" data-acf-field-key="<?php echo $field['key'] ?>"><?php
								foreach($field['choices'] as $name => $value) {
									?><li><label for="<?php echo $this->post_field_prefix . $column.'-'.$name; ?>"><?php
										?><input id="<?php echo $this->post_field_prefix . $column.'-'.$name; ?>" type="radio" value="<?php echo $name; ?>" 
										  class="acf-quick-edit" data-acf-field-key="<?php echo $field['key'] ?>"
										  name="<?php echo $this->post_field_prefix . $column; ?>" /><?php echo $value; ?><?php
									?></label></li><?php
								}
								if ( $field['other_choice'] ) {
									?><li><label for="<?php echo $this->post_field_prefix . $column.'-other'; ?>"><?php
										?><input id="<?php echo $this->post_field_prefix . $column.'-other'; ?>" type="radio" value="other" 
										  class="acf-quick-edit" data-acf-field-key="<?php echo $field['key'] ?>"
										  name="<?php echo $this->post_field_prefix . $column; ?>" /><?php
										?><input type="text" class="acf-quick-edit" data-acf-field-key="<?php echo $field['key'] ?>" 
											name="<?php echo $this->post_field_prefix . $column; ?>" style="width:initial" /><?php
										?></li><?php
									?></label><?php
								}
								?></ul><?php
								break;
							case 'true_false':
								?><ul class="acf-radio-list" data-acf-field-key="<?php echo $field['key'] ?>"><?php
									?><li><label for="<?php echo $this->post_field_prefix . $column; ?>-yes"><?php 
										?><input id="<?php echo $this->post_field_prefix . $column; ?>-yes" type="radio" value="1" class="acf-quick-edit" data-acf-field-key="<?php echo $field['key'] ?>" name="<?php echo $this->post_field_prefix . $column; ?>" /><?php
										_e('Yes')
									?></label></li><?php
									?><li><label for="<?php echo $this->post_field_prefix . $column; ?>-no"><?php 
										?><input id="<?php echo $this->post_field_prefix . $column; ?>-no"  type="radio" value="0" class="acf-quick-edit" data-acf-field-key="<?php echo $field['key'] ?>" name="<?php echo $this->post_field_prefix . $column; ?>" /><?php
										_e('No')
									?></label></li><?php
								?></ul><?php
								break;
							case 'number':
								$input_atts += array(
									'class' => 'acf-quick-edit',
									'type' => 'number', 
									'min' => $field['min'], 
									'max' => $field['max'],
									'step' => $field['step'], 
								);
								echo '<input '. acf_esc_attr( $input_atts ) .' />';
								break;

							case 'date_picker':
								$input_atts += array(
									'class' => 'acf-quick-edit acf-quick-edit-'.$field['type'],
									'type' => 'text', 
									'data-display_format' => acf_convert_date_to_js($field['display_format']),
									'data-first_day' => $field['first_day'],
									
								);
								echo '<input '. acf_esc_attr( $input_atts ) .' />';
								break;
							case 'textarea':
								$input_atts += array(
									'class' => 'acf-quick-edit acf-quick-edit-'.$field['type'],
									'type' => 'text', 
								);
								echo '<textarea '. acf_esc_attr( $input_atts ) .'>'.esc_textarea($field['value']).'</textarea>';
								break;
							default:
								$input_atts += array(
									'class' => 'acf-quick-edit acf-quick-edit-'.$field['type'],
									'type' => 'text', 
								);
								echo '<input '. acf_esc_attr( $input_atts ) .' />';
								break;
						}
					?></span><?php
				?></label><?php 
			?></div><?php 
		?></fieldset><?php
	}
	
	function quickedit_save_acf_meta( $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( $this->quickedit_fields as $field_name => $field ) {
			if ( isset( $_REQUEST[ $this->post_field_prefix . $field['name'] ] ) ) {
				update_post_meta( $post_id , $field['name'] , $_REQUEST[ $this->post_field_prefix . $field['name'] ] );
			}
		}
	}
	
	
}

ACFToQuickEdit::instance();
endif;
