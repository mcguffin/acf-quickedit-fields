<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class LinkField extends Field {

	/**
	 *	@inheritdoc
	 */
	public function render_column( $object_id ) {
		$value = $this->get_value( $object_id );
		if ( ! is_array( $value ) ) {
			return '';
		}

		return sprintf( '<a href="%s"%s>%s</a>',
			$value['url'],
			! empty($value['target']) ? sprintf(' target="%s"', $value['target'] ) : '',
			! empty($value['title']) ? $value['title'] : $value['url']
		);
		$this->get_value( $object_id );

	}


}
