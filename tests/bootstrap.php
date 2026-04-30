<?php
/**
 * Bootstrap the plugin testing environment using WorDBless.
 *
 * @package kraftbj/genesis-enews-extended
 */

$autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! file_exists( $autoload ) ) {
	fwrite( STDERR, "Composer dependencies not found. Run `composer install` first.\n" );
	exit( 1 );
}

require_once $autoload;

// Pass `persist=true` so WorDBless skips the SQLite cleanup call that
// references ABSPATH before defining it (broken in 0.6.0 on PHP 8+).
\WorDBless\Load::load( 'dbless', true );

require_once dirname( __DIR__ ) . '/plugin.php';
