<?php

/**
 * Genesis eNews Extended
 *
 * @package   BJGK\Genesis_enews_extended
 * @version   1.4.1
 * @author    Brandon Kraft <public@brandonkraft.com>
 * @link      http://www.brandonkraft.com/genesis-enews-extended/
 * @copyright Copyright (c) 2012-2015, Brandon Kraft
 * @license   GPL-2.0+
 */

add_filter( 'genesis-enews-extended-args' , 'gee_wpml_compat' );

/**
 * Compatibility for WPML (3rd Party Plugin)
 *
 * @since 1.5.0
 */
function gee_wpml_compat( $instance ) {
	if ( function_exists( 'icl_translate' ) ) {
		foreach ( $instance as $field ) {
			$field = icl_translate( 'genesis-enews-extended', $field );
		}
	}
	return $instance;
}