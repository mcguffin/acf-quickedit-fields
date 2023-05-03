<?php

namespace ACFQuickEdit\Fields;

if ( ! defined( 'ABSPATH' ) )
	die('Nope.');

class LinkField extends Field {

	/**
	 *	@inheritdoc
	 */
	protected function _render_column( $object_id ) {
		$value = $this->get_value( $object_id );

		if ( ! is_array( $value ) ) {
			return '';
		}

		return sprintf( '<a href="%s"%s>%s</a>',
			esc_url( $value['url'] ),
			! empty($value['target']) ? sprintf(' target="%s"', esc_attr( $value['target'] ) ) : '',
			! empty($value['title']) ? esc_html( $value['title'] ) : esc_html($value['url'])
		);
		$this->get_value( $object_id );

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

		$input_atts += [
			'class'					=> 'acf-quick-edit acf-quick-edit-'.$this->acf_field['type'],
			'type'					=> 'hidden',
			'data-acf-field-key'	=> $this->acf_field['key'],
			'name'					=> $this->get_input_name(),
		];

		$output = '';
		foreach ( [ 'title', 'url', 'target' ] as $prop ) {
			$atts = [] + $input_atts;
			$atts['name'] .= "[{$prop}]";
			$atts['data-link-prop'] = $prop;
			$atts['value'] = '';
			$output .= '<input '. acf_esc_attr( $atts ) .' />';
		}
		$output .= '<span class="link-content"></span>';
		$output .= sprintf( '<button class="button-secondary select-link">%s</button>', __('Select Link', 'acf-quickedit-fields') );
		$output .= sprintf( '<button class="button-link remove-link dashicons dashicons-dismiss"><span class="screen-reader-text">%s</span></button>', __('Remove Link', 'acf-quickedit-fields') );
		if ( ! has_action('print_media_templates', [ $this, 'print_media_templates' ] ) ) {
			add_action('print_media_templates', [ $this, 'print_media_templates' ] );
		}

		return $output;
	}

	/**
	 *	@action print_media_templates
	 */
	public function print_media_templates() {

		if ( ! class_exists( '\_WP_Editors', false ) ) {
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
		}

		\_WP_Editors::wp_link_dialog();
		printf(
			'<input type="hidden" value="%s" id="_ajax_linking_nonce" />',
			esc_attr( wp_create_nonce( 'internal-linking' ) )
		);
	}

	/**
	 *	@param mixed $value
	 */
	public function sanitize_value( $value, $context = 'db' ) {

		$value = wp_parse_args( (array) $value, [
			'title'		=> '',
			'url'		=> '',
			'target'	=> '',
		] );
		extract( $value );
		$url = esc_url_raw( $url );
		$title = sanitize_text_field( $title );
		if ( $target !== 'blank' ) {
			$target = '';
		}
		$value = compact( 'title', 'url', 'target' );
		return $value;
	}
}
