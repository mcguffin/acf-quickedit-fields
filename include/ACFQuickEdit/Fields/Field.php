<?php

namespace ACFQuickEdit\Fields;
use ACFQuickEdit\Core;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class Field {

	protected $acf_field;
	
	protected $dont_change_value = '___do_not_change';
	private static $fields = [];

	public static function get_types() {
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
			'oembed'			=> array( 'column' => true,		'quickedit' => false,	'bulkedit' => false ),
			'image'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
			'file'				=> array( 'column' => true,		'quickedit' => true,	'bulkedit' => true ), 
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
		return apply_filters( 'acf_quick_edit_fields_types', $types );
	}

	public static function getFieldObject( $acf_field ) {
		if ( is_null($acf_field) ) {
			return;
		}
		if ( ! isset( self::$fields[ $acf_field['key'] ] ) ) {
			$field_class = explode( '_', $acf_field['type'] );
			$field_class = array_map( 'ucfirst', $field_class );
			$field_class = 'ACFQuickEdit\\Fields\\' . implode( '', $field_class ) . 'Field';
			self::$fields[ $acf_field['key'] ] = new $field_class( $acf_field );
		}

		return self::$fields[ $acf_field['key'] ];

	}

	protected function __construct( $acf_field ) {

		$this->core = Core\Core::instance();

		$this->acf_field = $acf_field;
	}

	public function get_acf_field() {
		return $this->acf_field;
	}

	/**
	 *	Render Column content
	 *
	 *	@param int|string $object_id
	 *	@return string
	 */
	public function render_column( $object_id ) {

		return get_field( $this->acf_field['key'], $object_id );

	}


	/**
	 *	Render Field Input
	 *
	 *	@param string $column
	 *	@param string $post_type
	 *	@param string $mode 'bulk' | 'quick'
	 *
	 *	@return null
	 */
	final public function render_quickedit_field( $post_type, $mode ) {

		$input_atts = array(
			'data-acf-field-key' => $this->acf_field['key'],
			'name' => sprintf( 'acf[%s]', $this->acf_field['key'] ),
		);
		if ( $mode === 'bulk' ) {
			$input_atts['disabled'] = 'disabled';
		}

		do_action( 'acf_quick_edit_field_' . $this->acf_field['type'], $this->acf_field, $post_type  );

		if ( ! apply_filters( 'acf_quick_edit_render_' . $this->acf_field['type'], true, $this->acf_field, $post_type ) ) {
			return;
		}

		?>
			<div class="acf-field inline-edit-col" data-key="<?php echo $this->acf_field['key'] ?>" data-field-type="<?php echo $this->acf_field['type'] ?>">
				<label class="inline-edit-group">
					<span class="title"><?php echo $this->acf_field['label']; ?></span>
					<span class="input-text-wrap">
						<?php if ( $mode === 'bulk' ) { ?>
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
						<?php } ?>
						<?php echo $this->render_input( $input_atts, $mode === 'quick' ); ?>
					</span>
				</label>
			</div>
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
			'name'					=> sprintf( 'acf[%s]', $this->acf_field['key'] ),
		);

		return '<input '. acf_esc_attr( $input_atts ) .' />';
	}


	public function get_value( $post_id ) {
		return get_field( $this->acf_field['key'], $post_id, false );
	}

	/**
	 *	Update field value
	 *
	 *	@param int $post_id
	 *	@param bool $is_quickedit
	 *
	 *	@return null
	 */
	public function update( $post_id, $is_quickedit = true ) {
		$param_name = $this->acf_field['key'];

		if ( ! isset( $_REQUEST['acf'] ) ) {
			return;
		}

		if ( isset ( $_REQUEST['acf'][ $param_name ] ) ) {
			$value = $_REQUEST['acf'][ $param_name ];
		} else {
			$value = null;
		} 

		if ( in_array( $this->dont_change_value, (array) $value ) ) {
			return;
		}


		update_field( $this->acf_field['key'], $value, $post_id );
	}
}

