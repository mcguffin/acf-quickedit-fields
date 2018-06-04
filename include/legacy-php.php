<?php


// boolval() was introduced in PHP 5.5
if ( ! function_exists( 'boolval' ) ) {
	function boolval( $var ) {
		return intval( $var ) && true;
	}
}
