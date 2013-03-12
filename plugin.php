<?php
/**
 * Genesis eNews Extended
 *
 * @package BJGK\Genesis_enews_extended
 * @version 1.1.2
 * @author Brandon Kraft <public@brandonkraft.com>
 * @copyright Copyright (c) 2012, Brandon Kraft
 * @link http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/
 * @license GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Genesis eNews Extended
 * Plugin URI: http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/
 * Description: Replaces the Genesis eNews Widget to allow easier use of additional mailing services.
 * Version: 1.1.2
 * Author: Brandon Kraft
 * Author URI: http://www.brandonkraft.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: genesis-enews-extended
 * Domain Path: /languages/
 */
 /*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * Code based on original eNews Widget in the Genesis Framework by StudioPress - http://www.studiopress.com
 */

add_action( 'init', 'bjgk_genesis_enews_load_translations', 1 );
/**
 * Load the textdomain / translations for the plugin.
 *
 * @since 0.1.4
 */
function bjgk_genesis_enews_load_translations() {
	$domain = 'genesis-enews-extended';
	// The "plugin_locale" filter is also used in load_plugin_textdomain()
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	load_textdomain( 'genesis-enews-extended', WP_LANG_DIR . '/genesis-enews-extended/' . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( 'genesis-enews-extended', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

include 'class-bjgk-genesis-enews-extended.php';

add_action( 'widgets_init', 'bjgk_genesis_enews_load_widgets' );
/**
 * Register widget.
 *
 * @since 0.1.0
 */
function bjgk_genesis_enews_load_widgets() {
	register_widget( 'BJGK_Genesis_eNews_Extended' );
}