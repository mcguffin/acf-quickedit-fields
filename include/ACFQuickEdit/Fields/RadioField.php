<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class RadioField extends Field {

	public static $quickedit = true;

	public static $bulkedit = true;
	
	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$field_value = get_field( $this->acf_field['key'], $object_id );
		$values = array();
		foreach ( (array) $field_value as $value )
			$values[] = isset( $this->acf_field['choices'][ $value ] ) 
							? $this->acf_field['choices'][ $value ] 
							: $value;
		
		$output = implode( __(', ', 'acf-quick-edit-fields' ) , $values );
		if ( empty( $output ) )
			$output = __('(No value)', 'acf-quick-edit-fields');
		return $output;

	}

	/**
	 *	@inheritdoc
	 */
	public function render_input( $input_atts, $column ) {

		?><ul class="acf-radio-list<?php echo $this->acf_field['other_choice'] ? ' other' : '' ?>" data-acf-field-key="<?php echo $this->acf_field['key'] ?>"><?php

		foreach($this->acf_field['choices'] as $name => $value) {

			?><li><label for="<?php echo $this->core->prefix( $column.'-'.$name ); ?>"><?php

				?><input id="<?php echo $this->core->prefix( $column.'-'.$name ); ?>" type="radio" value="<?php echo $name; ?>" 
				  class="acf-quick-edit" data-acf-field-key="<?php echo $this->acf_field['key'] ?>"
				  name="<?php echo $this->core->prefix( $column ); ?>" /><?php echo $value; ?><?php

			?></label></li><?php

		}
		if ( $this->acf_field['other_choice'] ) {
			?><li><label for="<?php echo $this->core->prefix( $column.'-other' ); ?>"><?php
				?><input id="<?php echo $this->core->prefix( $column.'-other' ); ?>" type="radio" value="other" 
				  class="acf-quick-edit" data-acf-field-key="<?php echo $this->acf_field['key'] ?>"
				  name="<?php echo $this->core->prefix( $column ); ?>" /><?php
				?><input type="text" class="acf-quick-edit" data-acf-field-key="<?php echo $this->acf_field['key'] ?>" 
					name="<?php echo $this->core->prefix( $column ); ?>" style="width:initial" /><?php
				?></li><?php
			?></label><?php
		}
		?></ul><?php
	}


}