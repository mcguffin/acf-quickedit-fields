<?php

namespace ACFQuickEdit\Fields\Traits;

trait BulkOperationURL {

	/**
	 *	@inheritdoc
	 */
	public function get_bulk_operations() {
		return [
			'suffix'       => __( 'Append', 'acf-quickedit-fields' ),
			'query_add'    => __( 'Add GET-Variable', 'acf-quickedit-fields' ),
			'query_remove' => __( 'Remove GET-Variable', 'acf-quickedit-fields' ),
			'path_add'     => __( 'Add path segment', 'acf-quickedit-fields' ),
			'path_remove'  => __( 'Remove path segment', 'acf-quickedit-fields' ),
			'hostname'     => __( 'Replace host', 'acf-quickedit-fields' ),
		];
	}

	/**
	 *	@inheritdoc
	 */
	public function do_bulk_operation( $operation, $new_value, $object_id ) {

		$old_value = $this->get_value( $object_id, false );

		if ( empty( $old_value ) ) {
			return $old_value;
		}

		if ( 'suffix' === $operation ) {
			$value = $old_value . $new_value;

		} else if ( 'query_add' === $operation ) {

			$parsed = parse_url( $old_value );
			if ( ! isset( $parsed['query'] ) ) {
				$parsed['query'] = '';
			} else {
				$parsed['query'] .= '&';
			}
			$parsed['query'] .= $new_value;
			$value = $this->build_url( $parsed );

		} else if ( 'query_remove' === $operation ) {

			$value = remove_query_arg( $new_value, $old_value );

		} else if ( 'path_add' === $operation ) {

			$parsed = parse_url( $old_value );
			$parsed['path'] = trailingslashit( $parsed['path'] ) . ltrim( $new_value, '/' );
			$value = $this->build_url( $parsed );

		} else if ( 'path_remove' === $operation ) {

			$parsed = parse_url( $old_value );
			$path = explode( '/', $parsed['path'] );
			$path = array_filter( $path, function($segment) use ( $new_value ) {
				return $segment !== $new_value;
			} );
			$parsed['path'] = implode( '/', $path );
			$value = $this->build_url( $parsed );

		} else if ( 'hostname' === $operation ) {
			$parsed = parse_url( $old_value );
			if ( false !== strpos( $new_value, ':' ) ) {
				list( $host, $port ) = explode(':', $new_value );
				$parsed['host'] = $host;
				$parsed['port'] = $port;
			} else {
				$parsed['host'] = $new_value;
				if ( isset( $parsed['port'] ) ) {
					unset( $parsed['port'] );
				}
			}
			$value = $this->build_url( $parsed );
		} else {
			$value = $new_value;
		}
		return $value;
	}

	/**
	 *	@param array
	 */
	private function build_url( $parsed_url ) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

	/**
	 *	@inheritdoc
	 */
	public function validate_bulk_operation_value( $valid, $new_value, $operation ) {
		if ( 'suffix' === $operation ) {
			// allow empty
			return true;
		} else if ( 'query_add' === $operation ) {
			// allow empty
			return true;
		} else if ( 'query_remove' === $operation ) {
			return ! empty( $new_value );
		} else if ( 'path_add' === $operation ) {
			return ! empty( $new_value );
		} else if ( 'path_remove' === $operation ) {
			return ! empty( $new_value );
		} else if ( 'hostname' === $operation ) {

			$valid = true;
			if ( false !== strpos( $new_value, ':' ) ) {
				list( $host, $port ) = explode(':', $new_value );
				$valid &= is_numeric( $port );
			} else {
				$host = $new_value;
			}
			$valid &= ! is_null( filter_var( $host, FILTER_VALIDATE_DOMAIN, FILTER_NULL_ON_FAILURE ) );
		}
		return $valid;
	}
}
