<?php

namespace ACFQuickEdit\Fields;

use ACFQuickEdit\Admin;
use ACFQuickEdit\Core;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class Field {

	/**
	 *	@var array ACF fields
	 */
	private static $fields = [];

	/**
	 *	@var bool whether the field was already updated
	 */
	private $did_update = false;

	/**
	 *	@var Core\Core
	 */
	protected $core = null;

	/**
	 *	@var string ACF field
	 */
	private $_acf_field_key;

	/**
	 *	@var array ACF field
	 */
	protected $acf_parent;

	/**
	 *	@var ACFQuickEdit\Fields\Field
	 */
	protected $parent = false;

	/**
	 *	@var string classname to be wrapped aroud input element
	 */
	protected $wrapper_class = 'acf-input-wrap';

	/**
	 *	@return array supported acf fields
	 */
	public static function get_types() {
		$types = [
			// basic
			'text'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false, ],
			'textarea'			=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false, ],
			'number'			=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false, ],
			'email'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false, ],
			'url'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false, ],
			'password'			=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => false,	'filter' => false, ],
			'range'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false, ],

			// Content
			'wysiwyg'			=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false,	'filter' => false ],
			'oembed'			=> [ 'column' => true,	'quickedit' => false,	'bulkedit' => false,	'filter' => false ],
			'image'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false ],
			'file'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false ],
			'gallery'			=> [ 'column' => true,	'quickedit' => false,	'bulkedit' => false,	'filter' => false ],

			// Choice
			'select'			=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => true  ],
			'checkbox'			=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => true  ],
			'radio'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => true  ],
			'true_false'		=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => true  ],
			'button_group'		=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => true  ],

			// relational
			'post_object'		=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false  ], // TODO: select post filter
			'page_link'			=> [ 'column' => true,	'quickedit' => false,	'bulkedit' => false,	'filter' => false  ],
			'link'				=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false  ],
			'relationship'		=> [ 'column' => true,	'quickedit' => false,	'bulkedit' => false,	'filter' => false  ], // TODO: select post filter
			'taxonomy'			=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => true  ],
			'user'				=> [
				'column'	=> current_user_can('list_users'),
				'quickedit'	=> current_user_can('list_users'),
				'bulkedit'	=> current_user_can('list_users'),
				'filter'	=> false, // current_user_can('list_users'),
			], // TODO: select user filter

			// jQuery
			'google_map'		=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false,	'filter' => false  ],
			'date_picker'		=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false  ], // TODO: select year/month/day
			'date_time_picker'	=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false  ], // TODO: select year/month/day
			'time_picker'		=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false  ],
			'color_picker'		=> [ 'column' => true,	'quickedit' => true,	'bulkedit' => true,		'filter' => false  ],

			// Layout (unsupported)
			'message'			=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ],
			'tab'				=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ],
			'repeater'			=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ],
			'group'				=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ],
			'flexible_content'	=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ],
			'clone'				=> [ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ],
		];

		/**
		 * Filter field type support of ACF Quick Edit Fields
		 *
		 * @param array $fields		An associative array of field type support having the ACF field name as keys
		 *							and an array of supported fetaures as values.
		 *							Features are 'column', 'quickedit' and 'bulkedit'.
		 */
		$types = apply_filters( 'acf_quick_edit_fields_types', $types );
		return array_map( function ( $type ) {
			return wp_parse_args(
				$type,
				[ 'column' => false,	'quickedit' => false,	'bulkedit' => false, 'filter' => false  ]
			);
		}, $types );
	}

	/**
	 *	Factory method
	 *	@param string|array $acf_field Field Array or Field key
	 *	@return ACFQuickEdit\Fields\Field
	 */
	public static function getFieldObject( $acf_field ) {
		if ( is_string( $acf_field ) ) {
			$acf_field = get_field_object( $acf_field );
		}
		if ( ! is_array( $acf_field ) ) {
			return;
		}

		if ( ! isset( self::$fields[ $acf_field['key'] ] ) ) {
			$field_class = preg_split( '/[-_]/', $acf_field['type'] );
			$field_class = array_map( 'ucfirst', $field_class );
			$field_class = 'ACFQuickEdit\\Fields\\' . implode( '', $field_class ) . 'Field';
			try {
				self::$fields[ $acf_field['key'] ] = new $field_class( $acf_field );
			} catch( \Exception $exc ) {
				self::$fields[ $acf_field['key'] ] = new Generic( $acf_field );
			}
		}

		return self::$fields[ $acf_field['key'] ];
	}

	/**
	 *	@inheritdoc
	 */
	protected function __construct( $acf_field ) {

		$this->core = Core\Core::instance();

		$this->acf_field = $acf_field;

		$parent_key	= '';

		if ( !empty( $this->acf_field['parent'] ) ) {
			if ( is_numeric( $this->acf_field['parent'] ) ) {
				// int: field stored in DB
				$parent = get_post( $this->acf_field['parent'] );
				$parent_key = $parent->post_name;
			} else {
				// local json field
				$parent_key = $this->acf_field['parent'];
			}
		}

		if (  'field_' === substr( $parent_key, 0, 6 ) ) {
			// local json
			$this->parent = self::getFieldObject( get_field_object( $parent_key ) );
		}
	}

	/**
	 *	@param string $what
	 */
	public function __get( $what ) {
		if ( 'acf_field' === $what ) {
			return acf_get_store( 'fields' )->get( $this->_acf_field_key );
		}
	}

	/**
	 *	@param string $what
	 *	@param string $value
	 */
	public function __set( $what, $value ) {
		if ( 'acf_field' === $what ) {
			$this->_acf_field_key = $value['key'];
		}
	}

	/**
	 *	@return array acf field
	 */
	public function get_acf_field() {
		return $this->acf_field;
	}

	/**
	 *	@return array acf field
	 */
	public function get_parent() {
		return $this->parent;
	}

	/**
	 *	Render Column content
	 *
	 *	@param int|string $object_id
	 *	@return string
	 */
	final public function render_column( $object_id ) {

		$column_html = $this->_render_column( $object_id );

		/**
		 *	Column HTML Content
		 *
		 *	@param string $column_html
		 *	@param string/int $object_id
		 *	@param array $acf_field
		 *
		 *	@since ?
		 */
		return apply_filters( 'acf_qef_column_html_' . $this->acf_field['type'], $column_html, $object_id, $this->acf_field );

	}

	protected function _render_column( $object_id ) {
		return $this->get_value( $object_id );
	}

	/**
	 *	Render Filter
	 *
	 *	@param int|string $object_id
	 *	@return string
	 */
	public function render_filter( $index, $selected = '' ) {
	}

	/**
	 *	Bulk operations.
	 *
	 *	@return array
	 */
	public function get_bulk_operations() {
		return [];
	}

	/**
	 *	@return bool
	 */
	public function is_sortable() {
		return false;
	}

	/**
	 *	Render Field Input
	 *
	 *	@param string $post_type
	 *	@param string $mode 'bulk' | 'quick'
	 *	@param string $input_atts	array
	 *
	 *	@return null
	 */
	public function render_quickedit_field( $post_type, $mode, $input_atts = [] ) {

		$input_atts = wp_parse_args( $input_atts, [
			'data-acf-field-key'	=> $this->acf_field['key'],
			'name' 					=> $this->get_input_name(),
		]);

		if ( $mode === 'bulk' ) {
			$input_atts['disabled'] = 'disabled';
		}
		if ( isset( $this->acf_field['maxlength'] ) && intval( $this->acf_field['maxlength'] ) ) {
			$input_atts['maxlength'] = intval( $this->acf_field['maxlength'] );
		}

		if ( ! apply_filters( 'acf_quick_edit_render_' . $this->acf_field['type'], true, $this->acf_field, $post_type ) ) {
			return;
		}
		$wrapper_attr = [
			'class'				=> 'acf-field',
			'data-key' 			=> $this->acf_field['key'],
			'data-field-type'	=> $this->acf_field['type'],
			'data-allow-null'	=> isset( $this->acf_field['allow_null'] ) ? $this->acf_field['allow_null'] : 0,
		];
		$wrapper_attr = $this->get_wrapper_attributes( $wrapper_attr, $mode === 'quick' );
		if ( isset( $this->acf_field['field_type'] ) ) {
			$wrapper_attr['data-field-sub-type'] = $this->acf_field['field_type'];
		}

		$wrapper_class = explode( ' ', $this->wrapper_class );
		$wrapper_class = array_map( 'sanitize_html_class', $wrapper_class );
		?>
			<div <?php echo acf_esc_attr( $wrapper_attr ) ?>>
				<div class="inline-edit-group">
					<?php if ( $mode === 'bulk' ) {
						echo $this->render_bulk_operations();
					} ?>
					<label for="<?php echo esc_attr( $this->get_input_id( $mode === 'quick' ) ) ?>" class="title"><?php esc_html_e( $this->acf_field['label'] ); ?></label>
					<span class="<?php echo implode(' ', $wrapper_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>">
						<?php

							do_action( 'acf_quick_edit_field_' . $this->acf_field['type'], $this->acf_field, $post_type );
							// sanitiation happens in render_input()
							echo $this->render_input( $input_atts, $mode === 'quick' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						?>
					</span>
					<?php if ( $mode === 'bulk' ) {
						$this->render_bulk_do_not_change( $input_atts );
					} ?>
				</div>
			</div>
		<?php
	}

	/**
	 *	@param array $wrapper_attr Field input attributes
	 *	@return array
	 */
	protected function get_wrapper_attributes( $wrapper_attr, $is_quickedit = true ) {
		return $wrapper_attr;
	}

	/**
	 *	Render the Do-Not-Chwnage Chackbox
	 *
	 *	@param array $input_atts Field input attributes
	 */
	protected function render_bulk_do_not_change( $input_atts ) {
		$bulk = Admin\Bulkedit::instance();
		?>
		<label class="bulk-do-not-change">
			<input <?php echo acf_esc_attr( [
				'name'		=> $input_atts['name'],
				'value' 	=> $bulk->get_dont_change_value(),
				'type'		=> 'checkbox',
				'checked'	=> 'checked',
				'data-is-do-not-change' => 'true',
				'autocomplete' => 'off',
			] ) ?> />
			<?php esc_html_e( 'Do not change', 'acf-quickedit-fields' ) ?>
		</label>
		<?php
	}

	/**
	 *	Render Input element
	 *
	 *	@param array $input_attr
	 *	@param bool $is_quickedit
	 *
	 *	@return string
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += [
			'class'					=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'					=> 'text',
			'data-acf-field-key'	=> $this->acf_field['key'],
			'name'					=> $this->get_input_name(),
			'id'					=> $this->get_input_id( $is_quickedit ),
		];

		return '<input '. acf_esc_attr( $input_atts ) .' />';
	}

	/**
	 *	Render Input element
	 *
	 *	@return string
	 */
	protected function render_bulk_operations() {

		$bulk_operations = $this->get_bulk_operations();
		if ( 0 === count( $bulk_operations ) ) {
			return;
		}

		$bulk = Admin\Bulkedit::instance();

		$input_attr = [
			'name' => sprintf( 'acf[%s][%s]', $bulk->get_bulk_operation_key(), $this->acf_field['key'] ),
			'autocomplete' => 'off',
		];

		?>
		<label class="bulk-operations">
			<?php

			if ( 1 === count( $bulk_operations ) ) {
				$op = array_key_first( $bulk_operations );
					?>
					<input <?php echo acf_esc_attr( $input_attr + [
						'value' 	=> $op,
						'type'		=> 'checkbox',
					] ) ?> />
					<?php echo esc_html( $bulk_operations[$op] ); ?>
					<?php
			} else {
				?>
				<select <?php echo acf_esc_attr( $input_attr ) ?>>
					<option value="" selected><?php esc_html_e( '– Operation –', 'acf-quickedit-fields' ); ?></option>
					<?php
					foreach ( $bulk_operations as $operation => $label ) {
						?>
						<option <?php echo acf_esc_attr( [ 'value' => $operation ] ); ?>><?php echo esc_html($label); ?></option>
						<?php
					}
					?>
				</select>
				<?php
			}
			?>
		</label>
		<?php
	}

	/**
	 *	Perform a bulk operation
	 *
	 *	@param string $operation
	 *	@param mixed $new_value
	 *	@return mixed
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {
		return $new_value;
	}

	/**
	 *	@return	string
	 */
	protected function get_input_name() {

		$parts = [];
		$current = $this;
		while ( $current ) {
			$parts[] = $current->acf_field['key'];
			$current = $current->get_parent();
		}

		return 'acf' . implode( '', array_map( function($k){
			return "[{$k}]";
		}, array_reverse( $parts ) ) );

	}

	/**
	 *	@return	string
	 */
	protected function get_input_id( $is_quickedit = true ) {
		return sanitize_key( $this->get_input_name() ) . ( $is_quickedit ? '-q' : '-b' );
	}

	/**
	 *	@return string The Meta key
	 */
	final public function get_meta_key() {

		$parts = [];
		$current = $this;
		while ( $current ) {
			$parts[] = $current->acf_field['name'];
			$current = $current->get_parent();
		}

		return implode( '_', array_reverse( $parts ) );
	}

	/**
	 *	@return string No Value
	 */
	public function __no_value() {
		return esc_html__( '(No value)', 'acf-quickedit-fields' );
	}

	/**
	 *	@return mixed Unsanitized value of acf field.
	 */
	public function get_value( $object_id, $format_value = true ) {

		$dummy_field = [ 'name' => $this->get_meta_key() ] + $this->acf_field;

		$value = acf_get_value( $object_id, $dummy_field );

		if ( $format_value ) {
			// sanitation done in acf_format_value
			$value = acf_format_value( $value, $object_id, $dummy_field );
		}

		return $value;
	}

	/**
	 *	Sanitize field value before it is written into db
	 *
	 *	@param mixed $value
	 *	@param string $context Sanitation context. Defaut 'db'
	 *	@return mixed Sanitized $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		if ( 'ajax' === $context ) {
			return $value;
		}
		return sanitize_text_field( $value );
	}

	/**
	 *	Validate value for Bulk operation
	 *	@param boolean $valid What ACF vaildation says
	 *	@param mixed $new_value
	 *	@param string $operation
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $operation ) {
		return $valid;
	}

	/**
	 *	Sanitize array keys and values
	 *
	 *	@param array $arr
	 */
	protected function sanitize_strings_array( $arr ) {

		return array_combine(
			array_map( [ $this, 'sanitize_string_or_leave_int' ], array_keys( $arr ) ),
			array_map( 'sanitize_text_field', array_values( $arr ) )
		);
	}

	/**
	 *	array_walk callback
	 */
	private function sanitize_string_or_leave_int( $value ) {
		if ( is_int( $value ) ) {
			return $value;
		}
		return sanitize_text_field( $value );
	}
}
