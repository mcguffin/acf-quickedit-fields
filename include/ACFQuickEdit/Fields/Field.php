<?php

namespace ACFQuickEdit\Fields;
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
	 *	@var string value for the do-not-change checkbox in bulk edit
	 */
	protected $dont_change_value = '___do_not_change';

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
			'post_object'		=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'page_link'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'relationship'		=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'taxonomy'			=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ),
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
			'class'				=> 'acf-field inline-edit-col',
			'data-key' 			=> $this->acf_field['key'],
			'data-parent-key'	=> isset( $this->parent ) ? $this->parent->get_acf_field()['key'] : 'false',
			'data-field-type'	=> $this->acf_field['type'],
			'data-allow-null'	=> isset( $this->acf_field['allow_null'] ) ? $this->acf_field['allow_null'] : 0,
		);
		if ( isset( $this->acf_field['field_type'] ) ) {
			$wrapper_attr['data-field-sub-type'] = $this->acf_field['field_type'];
		}
		?>
			<div <?php echo acf_esc_attr( $wrapper_attr ) ?>>
				<label class="inline-edit-group">
					<span class="title"><?php echo $this->acf_field['label']; ?></span>
					<?php if ( $mode === 'bulk' ) {
						$this->render_bulk_do_not_change( $input_atts );
					} ?>
					<span class="<?php echo $this->wrapper_class ?>">
						<?php

							do_action( 'acf_quick_edit_field_' . $this->acf_field['type'], $this->acf_field, $post_type  );
							echo $this->render_input( $input_atts, $mode === 'quick' );

						?>
					</span>
				</label>
			</div>
		<?php

	}

	/**
	 *	Render the Do-Not-Chwnage Chackbox
	 *
	 *	@param array $input_atts Field input attributes
	 */
	protected function render_bulk_do_not_change( $input_atts ) {
		?>
		<span>
			<input <?php echo acf_esc_attr( array(
				'name'		=> $input_atts['name'],
				'value' 	=> $this->dont_change_value,
				'type'		=> 'checkbox',
				'checked'	=> 'checked',
				'data-is-do-not-change' => 'true',
			) ) ?> />
			<?php _e( 'Do not change', 'acf-quickedit-fields' ) ?>
		</span>
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
	 *	@return mixed value of acf field
	 */
	public function get_value( $object_id, $format_value = true ) {

		$dummy_field = $this->acf_field + array();

		if ( isset( $this->parent ) ) {

			$dummy_field['name'] = $this->parent->get_acf_field()['name'] . '_' . $dummy_field['name'];

		}

		$value = acf_get_value( $object_id, $dummy_field );

		if ( $format_value ) {

			$value = acf_format_value( $value, $object_id, $dummy_field );

		}

		return $value;

//		return get_field( $this->acf_field['key'], $post_id, false );
	}

	/**
	 *	Update field value if all conditions are met
	 *
	 *	@param int $post_id
	 *
	 *	@return null
	 */
	public function maybe_update( $post_id , $is_quickedit) {

		if ( $is_quickedit && $this->did_update === true ) {
			return;
		}

		if ( isset( $this->parent ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['acf'] ) ) {
			return;
		}

		$param_name = $this->acf_field['key'];

		if ( isset ( $_REQUEST['acf'][ $param_name ] ) ) {
			$value = $_REQUEST['acf'][ $param_name ];
		} else {
			$value = null;
		}

		if ( in_array( $this->dont_change_value, (array) $value ) ) {
			return;
		}

		// validate field value
		if ( ! acf_validate_value( $value, $this->acf_field, sprintf( 'acf[%s]', $param_name ) ) ) {
			return;
		}

		$this->update( $value, $post_id );
	}

	/**
	 *	Update field value
	 *
	 *	@param mixed $value
	 *	@param int/string $post_id
	 *
	 *	@return null
	 */
	public function update( $value, $post_id ) {
		$this->did_update = true;
		update_field( $this->acf_field['key'], $value, $post_id );
	}
}
