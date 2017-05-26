<?php

namespace ACFQuickEdit\Fields;
use ACFQuickEdit\Core;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

abstract class Field {

	protected $acf_field;
	
	protected $dont_change_value = '___do_not_change';


	public static function getField( $acf_field ) {
		$field_class = explode( '_', $acf_field['type'] );
		$field_class = array_map( 'ucfirst', $field_class );
		$field_class = 'ACFQuickEdit\\Fields\\'.implode( '', $field_class ) . 'Field';
		return new $field_class( $acf_field );
	}

	protected function __construct( $acf_field ) {

		$this->core = Core\Core::instance();

		$this->acf_field = $acf_field;
	}

	public function get_acf_field() {
		return $this->acf_field;
	}

	/**
	 * @param int|string $object_id
	 *	@return string
	 */
	public function render_column( $object_id ) {

		return get_field( $this->acf_field['key'], $object_id );

	}


	function render_quickedit_field( $column, $post_type, $mode ) {

		$input_atts = array(
			'data-acf-field-key' => $this->acf_field['key'],
			'name' => sprintf( 'acf[%s]', $this->acf_field['key'] ),
		);

		do_action( 'acf_quick_edit_field_' . $this->acf_field['type'], $this->acf_field, $column, $post_type  );

		if ( ! apply_filters( 'acf_quick_edit_render_' . $this->acf_field['type'], true, $this->acf_field, $column, $post_type ) ) {
			return;
		}

		?>
			<div class="acf-field inline-edit-col column-<?php echo $column; ?>" data-key="<?php echo $this->acf_field['key'] ?>">
				<label class="inline-edit-group">
					<span class="title"><?php echo $this->acf_field['label']; ?></span>
					<span class="input-text-wrap"><?php

						$this->render_input( $input_atts, $column, $mode === 'quick' );

					?></span>
				</label>
			</div>
		<?php

	}

	public function render_input( $input_atts, $column, $is_quickedit = true ) {
		$input_atts += array(
			'class'	=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'	=> 'text', 
		);
		echo '<input '. acf_esc_attr( $input_atts ) .' />';
	}

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

