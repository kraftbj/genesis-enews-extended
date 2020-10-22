<?php
/**
 * PHPUnit Tests.
 *
 * @package kraftbj/genesis-enews-extended
 */

/**
 * Tests to test that that testing framework is testing tests. Meta, huh?
 *
 * @package wordpress-plugins-tests
 */
class WP_Test_WordPress_Plugin_Tests extends WP_UnitTestCase {

	/**
	 * Run a simple test to ensure that the tests are running
	 */
	public function test_tests() {

		$this->assertTrue( true );

	}

	/**
	 * Ensure that the plugin has been installed and activated.
	 */
	public function test_plugin_activated() {

		$this->assertTrue( is_plugin_active( 'genesis-enews-extended/plugin.php' ) );

	}

}
