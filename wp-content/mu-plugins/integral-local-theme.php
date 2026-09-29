<?php
/**
 * Local dual-site helper:
 * - Port 8080 / default → leave DB theme as-is (legacy rebuild reference)
 * - Port 8081 container → force the new Integral theme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$force = getenv( 'INTEGRAL_FORCE_THEME' );
if ( $force ) {
	add_filter( 'template', function () use ( $force ) {
		return $force;
	} );
	add_filter( 'stylesheet', function () use ( $force ) {
		return $force;
	} );
}
