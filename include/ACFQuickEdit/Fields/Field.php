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
	 *	@var array ACF field
	 */
	protected $acf_field;

	/**
	 *	@var array ACF field
	 */
	protected $acf_parent;

	/**
	 *	@var array ACFQuickEdit\Fields\Field
	 */
	protected $parent;

	/**
	 *	@var string classname to be wrapped aroud input element
	 */
	protected $wrapper_class = 'acf-input-wrap';

	/**
	 *	@return array supported acf fields
	 */
	public static function get_types() {
		$types = array(
			// basic
			'text'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'textarea'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'number'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'email'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'url'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'password'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => false ),
			'range'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),

			// Content
			'wysiwyg'			=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
			'oembed'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'image'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'file'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'gallery'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),

			// Choice
			'select'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'checkbox'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'radio'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'true_false'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'button_group'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),

			// relational
			'post_object'		=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'page_link'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'link'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'relationship'		=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'taxonomy'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
			'user'				=> array(
				'column'	=> current_user_can('list_users'),
				'quickedit'	=> false,
				'bulkedit'	=> false
			),

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
			'group'				=> array( 'column' => false,	'quickedit' => false,	'bulkedit' => false ),
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
		return apply_filters( 'acf_quick_edit_fields_types', $types );
	}

	/**
	 *	Factory method
	 *	@param array $acf_field
	 *	@return ACFQuickEdit\Fields\Field
	 */
	public static function getFieldObject( $acf_field ) {
		if ( ! $acf_field || is_null($acf_field) ) {
			return;
		}
		$acf_field = wp_parse_args( $acf_field, array(
			'allow_bulkedit'		=> false,
			'allow_quickedit'		=> false,
			'show_column'			=> false,
			'show_column_weight'	=> 1000,
			'show_column_sortable'	=> false,
		));
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

		if ( 'field_' === substr( $this->acf_field['parent'], 0, 6 ) ) {
			$this->parent = self::getFieldObject( get_field_object( $this->acf_field['parent'] ) );
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
	public function render_column( $object_id ) {

		return $this->get_value( $object_id );

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
	public function render_quickedit_field( $post_type, $mode, $input_atts = array() ) {

		$input_atts = wp_parse_args( $input_atts, array(
			'data-acf-field-key'	=> $this->acf_field['key'],
			'name' 					=> $this->get_input_name(),
		));

		if ( $mode === 'bulk' ) {
			$input_atts['disabled'] = 'disabled';
		}
		if ( isset( $this->acf_field['maxlength'] ) && intval( $this->acf_field['maxlength'] ) ) {
			$input_atts['maxlength'] = intval( $this->acf_field['maxlength'] );
		}

		if ( ! apply_filters( 'acf_quick_edit_render_' . $this->acf_field['type'], true, $this->acf_field, $post_type ) ) {
			return;
		}
		$wrapper_attr = array(
			'class'				=> 'acf-field',
			'data-key' 			=> $this->acf_field['key'],
			'data-parent-key'	=> isset( $this->parent ) ? $this->parent->get_acf_field()['key'] : 'false',
			'data-field-type'	=> $this->acf_field['type'],
			'data-allow-null'	=> isset( $this->acf_field['allow_null'] ) ? $this->acf_field['allow_null'] : 0,
		);
		if ( isset( $this->acf_field['field_type'] ) ) {
			$wrapper_attr['data-field-sub-type'] = $this->acf_field['field_type'];
		}

		$wrapper_class = explode( ' ', $this->wrapper_class );
		$wrapper_class = array_map( 'sanitize_html_class', $wrapper_class );
		?>
			<div <?php echo acf_esc_attr( $wrapper_attr ) ?>>
				<div class="inline-edit-group">
					<label for="<?php echo $this->get_input_id( $mode === 'quick' ) ?>" class="title"><?php esc_html_e( $this->acf_field['label'] ); ?></label>
					<span class="<?php echo implode(' ',$wrapper_class )  ?>">
						<?php

							do_action( 'acf_quick_edit_field_' . $this->acf_field['type'], $this->acf_field, $post_type  );
							// sanitiation happens in render_input()
							echo $this->render_input( $input_atts, $mode === 'quick' );

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
	 *	Render the Do-Not-Chwnage Chackbox
	 *
	 *	@param array $input_atts Field input attributes
	 */
	protected function render_bulk_do_not_change( $input_atts ) {
		$bulk = Admin\Bulkedit::instance();
		?>
		<label class="bulk-do-not-change">
			<input <?php echo acf_esc_attr( array(
				'name'		=> $input_atts['name'],
				'value' 	=> $bulk->get_dont_change_value(),
				'type'		=> 'checkbox',
				'checked'	=> 'checked',
				'data-is-do-not-change' => 'true'
			) ) ?> />
			<?php _e( 'Do not change', 'acf-quickedit-fields' ) ?>
		</label>
		<?php
	}

	/**
	 *	Render Input element
	 *
	 *	@param array $input_attr
	 *	@param string $column
	 *	@param bool $is_quickedit
	 *
	 *	@return string
	 */
	protected function render_input( $input_atts, $is_quickedit = true ) {
		$input_atts += array(
			'class'					=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'					=> 'text',
			'data-acf-field-key'	=> $this->acf_field['key'],
			'name'					=> $this->get_input_name(),
			'id'					=> $this->get_input_id( $is_quickedit ),
		);

		return '<input '. acf_esc_attr( $input_atts ) .' />';
	}

	/**
	 *	@return	string
	 */
	protected function get_input_name() {
		if ( isset( $this->parent ) ) {
			$input_name = sprintf( 'acf[%s][%s]', $this->parent->get_acf_field()['key'], $this->acf_field['key'] );
		} else {
			$input_name = sprintf( 'acf[%s]', $this->acf_field['key'] );
		}
		return $input_name;
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
		if ( isset( $this->parent ) ) {
			$name = $this->parent->get_meta_key() . '_' . $this->acf_field['name'];
		} else {
			$name = $this->acf_field['name'];
		}
		return $name;
	}

	/**
	 *	@return mixed value of acf field
	 */
	public function get_value( $object_id, $format_value = true ) {

		$dummy_field = array( 'name' => $this->get_meta_key() ) + $this->acf_field;

		$value = acf_get_value( $object_id, $dummy_field );

		if ( $format_value ) {
			// sanitation don in acf_format_value
			$value = acf_format_value( $value, $object_id, $dummy_field );

		} else {
			$value = $this->sanitize_value( $value );
		}

		return $value;

//		return get_field( $this->acf_field['key'], $post_id, false );
	}


	/**
	 *	Sanitize field value before it is written into db
	 *
	 *	@param mixed $value
	 *	@param string $context Sanitation context. Defaut 'db'
	 *	@return mixed Sanitized $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {
		return sanitize_text_field( $value );
	}

	/**
	 *	Sanitize array keys and values
	 *
	 *	@param array $arr
	 */
	protected function sanitize_strings_array( $arr ) {
		$arr = $arr;
		array_walk( $arr, array( $this, '_sanitize_strings_array_cb' ) );
		return $arr;
	}

	/**
	 *	array_walk callback
	 */
	private function _sanitize_strings_array_cb( &$value, &$key ) {
		if ( ! is_int( $key ) ) {
			$key = sanitize_text_field( $key );
		}
		$value = sanitize_text_field( $value );
	}

}
