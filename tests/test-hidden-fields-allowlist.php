<?php
/**
 * Regression tests for the Hidden Fields wp_kses allowlist.
 *
 * @package kraftbj/genesis-enews-extended
 */

/**
 * Covers issue #164 (inline style attributes stripped from <input>) and
 * the broader 2.3.0 → 2.4.0 attribute-allowlist widening that resolved it.
 */
class Test_Hidden_Fields_Allowlist extends \WorDBless\BaseTestCase {

	/**
	 * Widget instance under test.
	 *
	 * @var BJGK_Genesis_ENews_Extended
	 */
	protected static $widget;

	/**
	 * Reflected `sanitize_hidden_fields` method, the plugin's full Hidden
	 * Fields sanitization path (wp_kses + scoped safe_style_css filter).
	 *
	 * @var ReflectionMethod
	 */
	protected static $sanitize;

	public static function set_up_before_class() {
		parent::set_up_before_class();

		self::$widget   = new BJGK_Genesis_ENews_Extended();
		self::$sanitize = new ReflectionMethod( self::$widget, 'sanitize_hidden_fields' );
		self::$sanitize->setAccessible( true );
	}

	/**
	 * Run a value through the widget's full Hidden Fields sanitization path.
	 *
	 * @param string $value Raw Hidden Fields markup.
	 * @return string Sanitized output.
	 */
	protected function sanitize( $value ) {
		return self::$sanitize->invoke( self::$widget, $value );
	}

	/**
	 * Issue #164: Flodesk's hidden tracking pixel uses an inline style on
	 * <input>. 2.3.0 stripped the style attribute because it was missing
	 * from the wp_kses allowlist, leaving a visible empty input.
	 */
	public function test_issue_164_flodesk_tracking_pixel_style_survives() {
		$input  = '<input type="text" name="confirm_email_address" style="display: none; background-image: url(https://example.com/p.gif)">';
		$output = $this->sanitize( $input );

		// `safecss_filter_attr` reserializes the value; the inter-declaration space after `;` is dropped.
		$this->assertStringContainsString(
			'style="display: none;background-image: url(https://example.com/p.gif)"',
			$output,
			'expected the full Flodesk-pixel style attribute to round-trip through sanitization intact'
		);
	}

	/**
	 * Event handlers must continue to be stripped — we widened the
	 * allowlist for legitimate form attributes, not for arbitrary HTML.
	 */
	public function test_event_handlers_are_stripped() {
		$input  = '<input type="text" name="email" onclick="alert(1)" onfocus="alert(2)">';
		$output = $this->sanitize( $input );

		$this->assertStringNotContainsString( 'onclick', $output );
		$this->assertStringNotContainsString( 'onfocus', $output );
		$this->assertStringNotContainsString( 'alert(', $output );
	}

	/**
	 * The safe_style_css extension is scoped to Hidden Fields. After
	 * sanitize_hidden_fields() runs, an unrelated wp_kses_post() call on
	 * the same request must not see the widened CSS allowlist.
	 */
	public function test_safe_style_css_filter_does_not_leak() {
		$this->sanitize( '<input type="text" name="x" style="display: none">' );

		$leaked = wp_kses_post( '<p style="display: none">leak</p>' );

		$this->assertStringNotContainsString( 'display: none', $leaked, 'display: none should be stripped outside the Hidden Fields path' );
	}

	/**
	 * `background-image: url(...)` is allowed for tracking pixels, but
	 * the URL must still be filtered for safe protocols. A `javascript:`
	 * scheme inside the url() must be stripped.
	 */
	public function test_background_image_javascript_protocol_is_stripped() {
		$input  = '<input type="text" name="x" style="background-image: url(javascript:alert(1))">';
		$output = $this->sanitize( $input );

		// Positive oracle: the input element itself must survive, so a regression
		// that strips the whole tag fails this test rather than passing it.
		$this->assertStringContainsString( 'name="x"', $output, 'the <input> tag was stripped entirely' );
		$this->assertStringNotContainsString( 'javascript:', $output );
		$this->assertStringNotContainsString( 'alert(', $output );
	}
}
