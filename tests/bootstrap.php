<?php
/**
 * PHPUnit bootstrap.
 *
 * @package weForms
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'HOME' ) . '/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Load weForms for the test suite.
 *
 * @return void
 */
function _weforms_manually_load_plugin() {
	require dirname( __DIR__ ) . '/weforms.php';
}

tests_add_filter( 'muplugins_loaded', '_weforms_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
