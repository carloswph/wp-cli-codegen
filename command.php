<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

use WPH\CLI\Elementor;

require __DIR__ . '/vendor/autoload.php';

/**
 * Elementor custom functionality generator
 *
 * @when before_wp_load
 */
$elementor_gen = function($args, $assoc_args) {

	list($name) = $args;
	$it = new Elementor();
	$it->setVersion('1.0.1');
	if ( isset( $assoc_args['dir'] ) ) {
    	$it->setDir('./' . $assoc_args['dir']);
    }
	$result = $it->plugin($name);
        
    // Print the message with type
    WP_CLI::line($result);

};

WP_CLI::add_command( 'codegen', $elementor_gen );