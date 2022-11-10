<?php
/**
 *	Fieldgroup Admin fields before ACF 6.0.0
 */

namespace ACFQuickEdit\Admin;

use ACFQuickEdit\Core;
use ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');


class LegacyFieldGroup extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action('acf/render_field/type=column_setting', [ $this, 'render_column_setting' ] );

		add_action('acf/render_field/type=edit_setting', [ $this, 'render_edit_setting' ] );

		$this->init_acf_settings();

		parent::__construct();

	}

	/**
	 *	Initialize
	 */
	public function init_acf_settings() {
		$types = Fields\Field::get_types();

		foreach ( $types as $type => $supports ) {
			if ( $supports[ 'column' ] ) {
				add_action( "acf/render_field_settings/type={$type}", [ $this, 'render_column_settings' ] );
			}
			if ( $supports[ 'quickedit' ] || $supports[ 'bulkedit' ] ) {
				add_action( "acf/render_field_settings/type={$type}", [ $this, 'render_edit_settings' ] );
			}
			if ( $supports[ 'filter' ] ) {
				add_action( "acf/render_field_settings/type={$type}", [ $this, 'render_filter_settings' ] );
			}
		}
	}

	/**
	 *
	 */
	public function render_column_settings( $field ) {
		// show column: todo: allow sortable
		//*
		acf_render_field_setting( $field, [
			'label'			=> __('Column View','acf-quickedit-fields'),
			'instructions'	=> '',
			'type'			=> 'column_setting',
			'name'			=> 'column',
			'message'		=> __("Show a column in the posts list table", 'acf-quickedit-fields'),
			'width'			=> 50,
			'field'			=> $field,
		]);
	}

	/**
	 *	@action acf/render_field/type=column_setting
	 */
	public function render_column_setting( $field ) {

		$field_object = Fields\Field::getFieldObject( $field['field'] );

		echo '<div style="width:50%;float:left;">';

		acf_render_field_wrap( [
			'label'			=> __('Show Column','acf-quickedit-fields'),
			'instructions'	=> '',
			'type'			=> 'true_false',
			'name'			=> 'show_column',
			'ui'			=> 1,
			'message'		=> __("Show column in list tables", 'acf-quickedit-fields'),
			'prefix'		=> $field['prefix'],
			'value'			=> $field['field']['show_column'],
		], 'div', 'label' );

		if ( $field_object->is_sortable() ) {

			acf_render_field_wrap( [
				'label'			=> __('Sortable Column','acf-quickedit-fields'),
				'instructions'	=> '',
				'type'			=> 'true_false',
				'name'			=> 'show_column_sortable',
				'ui'			=> 1,
				'message'		=> __("Make this column sortable", 'acf-quickedit-fields'),
				'prefix'		=> $field['prefix'],
				'value'			=> $field['field']['show_column_sortable'],
			], 'div', 'label' );

		}

		echo '</div>';

		$weight_field = [
			'label'			=> __('Column Weight','acf-quickedit-fields'),
			'instructions'	=> __('Columns with a higher weight will be pushed to the right. The leftmost WordPress column has a weight of <em>0</em>, the next one <em>100</em> and so on. Leave empty to place a column to the rightmost position.','acf-quickedit-fields'),
			'type'			=> 'number',
			'name'			=> 'show_column_weight',
			'message'		=> __("Column Weight", 'acf-quickedit-fields'),
			'default_value'	=> '1000',
			'min'			=> '-10000',
			'max'			=> '10000',
			'step'			=> '1',
			'placeholder'	=> '',
			'wrapper'		=> [
				'width'			=> 50,
			],
			'prefix'		=> $field['prefix'],
		];

		if ( isset( $field['field']['show_column_weight'] ) ) {

			$weight_field['value'] = $field['field']['show_column_weight'];

		} else {

			$weight_field['value'] = $weight_field['default_value'];

		}

		acf_render_field_wrap( $weight_field, 'div', 'label' );
	}

	/**
	 *	@inheritdoc
	 */
	public function render_edit_settings( $field ) {
		// add to quick edit
		acf_render_field_setting( $field, [
			'label'			=> __('Editing','acf-quickedit-fields'),
			'instructions'	=> '',
			'type'			=> 'edit_setting',
			'name'			=> 'edit',
			'field'			=> $field,
		]);
	}

	/**
	 *	@action acf/render_field/type=edit_setting
	 */
	public function render_edit_setting( $field ) {

		$field_object = Fields\Field::getFieldObject( $field['field'] );
		$types = Fields\Field::get_types();

		// parse default values
		$field['field'] = wp_parse_args( $field['field'], [
			'allow_quickedit'	=> false,
			'allow_bulkedit'	=> false,
		] );


		if ( $types[ $field['field']['type'] ]['quickedit'] ) {
			acf_render_field_wrap( [
				'label'			=> __('QuickEdit','acf-quickedit-fields'),
				'instructions'	=> '',
				'type'			=> 'true_false',
				'name'			=> 'allow_quickedit',
				'ui'			=> 1,
				'ui_on_text'	=> __('Enabled','acf-quickedit-fields'),
				'ui_off_text'	=> __('Disabled','acf-quickedit-fields'),
				'prefix'		=> $field['prefix'],
				'value'			=> $field['field']['allow_quickedit'],
				'wrapper'		=> [
					'width'	=> 50,
				],
			], 'div', 'label' );
		}

		if ( $types[ $field['field']['type'] ]['bulkedit'] ) {
			acf_render_field_wrap( [
				'label'			=> __('Bulk Edit','acf-quickedit-fields'),
				'instructions'	=> '',
				'type'			=> 'true_false',
				'name'			=> 'allow_bulkedit',
				'ui'			=> 1,
				'ui_on_text'	=> __('Enabled','acf-quickedit-fields'),
				'ui_off_text'	=> __('Disabled','acf-quickedit-fields'),
				'prefix'		=> $field['prefix'],
				'value'			=> $field['field']['allow_bulkedit'],
				'wrapper'		=> [
					'width'	=> 50,
				],
			], 'div', 'label' );
		}
	}

	/**
	 *	@action acf/render_field_settings/type={$type}
	 */
	public function render_filter_settings( $field ) {

		acf_render_field_setting( $field, [
			'label'			=> __( 'Enable filter', 'acf-quickedit-fields' ),
			'instructions'	=> __( 'Filters will work with posts and user list tables. Terms lack an appropriate', 'acf-quickedit-fields'),
			'type'			=> 'true_false',
			'name'			=> 'show_column_filter',
			'ui'			=> 1,
			'wrapper'		=> [ 'width' => 33, ],
		]);
	}
}
