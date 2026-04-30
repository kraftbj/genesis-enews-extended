<?php
/**
 * Genesis eNews Extended
 *
 * @package   BJGK\Genesis_enews_extended
 * @version   2.4.0
 * @author    Brandon Kraft <public@brandonkraft.com>
 * @link      https://kraft.blog/genesis-enews-extended/
 * @copyright Copyright (c) 2012-2026, Brandon Kraft
 * @license   GPL-2.0+
 */

/**
 * Main plugin class.
 *
 * @package BJGK\Genesis_enews_extended
 * @author Brandon Kraft <public@brandonkraft.com>
 */
class BJGK_Genesis_ENews_Extended extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->defaults = array(
			'title'            => '',
			'text'             => '',
			'after_text'       => '',
			'hidden_fields'    => '',
			'open_same_window' => 0,
			'fname-field'      => '',
			'lname-field'      => '',
			'input_text'       => '',
			'fname_text'       => '',
			'lname_text'       => '',
			'button_text'      => '',
			'email-field'      => '',
			'action'           => '',
			'display_privacy'  => 0,
		);

		$widget_ops = array(
			'classname'   => 'enews-widget',
			'description' => __( 'Displays subscribe form', 'genesis-enews-extended' ),
		);

		parent::__construct( 'enews-ext', __( 'Genesis - eNews Extended', 'genesis-enews-extended' ), $widget_ops );
	}

	/**
	 * Returns whether it is an AMP page.
	 *
	 * @return bool
	 */
	protected function is_amp() {
		return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
	}

	/**
	 * Echo the widget content.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		$before_widget = $args['before_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$after_widget  = $args['after_widget'];

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$instance = apply_filters( 'genesis-enews-extended-args', $instance ); //phpcs:ignore WordPress.NamingConventions.ValidHookName

		// Set default fname_text, lname_text for backwards compat for installs upgraded from 0.1.6+ to 0.3.0+.
		if ( empty( $instance['fname_text'] ) ) {
			$instance['fname_text'] = 'First Name';
		}
		if ( empty( $instance['lname_text'] ) ) {
			$instance['lname_text'] = 'Last Name';
		}

		// Get field count for wrapper class.
		$field_count = 1;

		if ( ! empty( $instance['fname-field'] ) ) {
			$field_count++;
		}

		if ( ! empty( $instance['lname-field'] ) ) {
			$field_count++;
		}

		// Adds classes, including field count classes.
		/* translators: %s: number of fields */
		$classes = 'enews ' . sprintf( _n( 'enews-%s-field', 'enews-%s-fields', $field_count, 'genesis-enews-extended' ), $field_count );

		// We run KSES on update since we want to allow some HTML, so ignoring the output escape check.
		echo $before_widget; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		// If Genesis is the parent theme.
		if ( function_exists( 'genesis_markup' ) ) {
			genesis_markup(
				array(
					'open'    => '<div %s>',
					'context' => 'enews',
					'echo'    => true,
					'atts'    => array(
						'class' => $classes,
					),
					'params'  => array(
						'instance' => $instance,
					),
				)
			);
		} else {
			printf( '<div class="%s">', esc_attr( $classes ) );
		}

		if ( ! empty( $instance['title'] ) ) {
			// We run KSES on update since we want to allow some HTML, so ignoring the output escape check.
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		// We run KSES on update since we want to allow some HTML, so ignoring the output escape check.
		// gee_text_content runs after wpautop, matching core's widget_text/widget_text_content split, so block-level shortcode output isn't mangled by wpautop.
		echo apply_filters( 'gee_text_content', wpautop( apply_filters( 'gee_text', $instance['text'] ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['action'] ) ) : ?>
			<form id="subscribe<?php echo esc_attr( $this->id ); ?>" class="enews-form" action="<?php echo esc_url( $instance['action'] ); ?>" method="post"
				<?php
				// The AMP condition is used here because if the form submission handler does a redirect, the amp-form component will error with:
				// "Redirecting to target=_blank using AMP-Redirect-To is currently not supported, use target=_top instead".
				if ( 0 === $instance['open_same_window'] && ! $this->is_amp() ) {
					echo ' target="_blank" ';
				}
				?>
				name="<?php echo esc_attr( $this->id ); ?>"
			>
				<?php
				if ( ! empty( $instance['fname-field'] ) ) :
					?>
					<input type="text" id="subbox1" class="enews-subbox enews-fname subbox1" value="" aria-label="<?php echo esc_attr( $instance['fname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['fname_text'] ); ?>" name="<?php echo esc_attr( $instance['fname-field'] ); ?>" /><?php endif ?>
				<?php
				if ( ! empty( $instance['lname-field'] ) ) :
					?>
					<input type="text" id="subbox2" class="enews-subbox enews-lname subbox2" value="" aria-label="<?php echo esc_attr( $instance['lname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['lname_text'] ); ?>" name="<?php echo esc_attr( $instance['lname-field'] ); ?>" /><?php endif ?>
				<input type="<?php echo current_theme_supports( 'html5' ) ? 'email' : 'text'; ?>" id="subbox" value="" class="enews-email subbox" aria-label="<?php echo esc_attr( $instance['input_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['input_text'] ); ?>" name="<?php echo esc_attr( $instance['email-field'] ); ?>"
					<?php
					if ( current_theme_supports( 'html5' ) ) :
						?>
						required="required"<?php endif; ?> />
				<?php echo $this->sanitize_hidden_fields( $instance['hidden_fields'] ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
				<input type="submit" id="subbutton" value="<?php echo esc_attr( $instance['button_text'] ); ?>" class="enews-submit subbutton" />
			</form>
			<?php
		else :
			// Show admin-only warning when form action is empty.
			if ( current_user_can( 'manage_options' ) ) :
				?>
				<div class="enews-admin-notice" role="alert" style="padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;">
					<strong><?php esc_html_e( 'Configuration Required', 'genesis-enews-extended' ); ?>:</strong>
					<?php esc_html_e( 'This widget will not display a subscription form until you configure the Form Action URL in the widget settings.', 'genesis-enews-extended' ); ?>
				</div>
				<?php
			endif;
		endif;
		if ( $instance['display_privacy'] && function_exists( 'the_privacy_policy_link' ) ) {
			the_privacy_policy_link( '<small class="enews-privacy">', '</small>' );
		}
		// We run KSES on update since we want to allow some HTML, so ignoring the output escape check.
		// gee_after_text_content runs after wpautop, matching core's widget_text/widget_text_content split, so block-level shortcode output isn't mangled by wpautop.
		echo apply_filters( 'gee_after_text_content', wpautop( apply_filters( 'gee_after_text', $instance['after_text'] ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		// If Genesis is the parent theme.
		if ( function_exists( 'genesis_markup' ) ) {
			genesis_markup(
				array(
					'close'   => '</div>',
					'context' => 'enews',
					'echo'    => true,
					'params'  => array(
						'instance' => $instance,
					),
				)
			);
		} else {
			echo '</div>';
		}

		echo $after_widget; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If false is returned, the instance won't be saved / updated.
	 *
	 * @since 0.1.0
	 * @since 2.0.3 Allow "a" tags in the Hidden Fields setting.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$new_instance['title']           = trim( wp_kses( $new_instance['title'], array( 'i' => array() ) ) );
		$new_instance['text']            = trim( wp_kses_post( $new_instance['text'] ) );
		$new_instance['hidden_fields']   = $this->sanitize_hidden_fields( $new_instance['hidden_fields'] );
		$new_instance['after_text']      = trim( wp_kses_post( $new_instance['after_text'] ) );
		$new_instance['action']          = esc_url_raw( trim( $new_instance['action'] ) );
		$new_instance['display_privacy'] = ( isset( $new_instance['display_privacy'] ) ) ? (int) $new_instance['display_privacy'] : 0;

		return $new_instance;
	}

	/**
	 * Sanitize the Hidden Fields markup with the plugin's allowlist while
	 * temporarily widening WordPress's CSS property allowlist so the inline
	 * `display: none` and `background-image` declarations vendor newsletter
	 * snippets rely on (e.g. Flodesk's tracking pixel) survive
	 * `safecss_filter_attr`.
	 *
	 * `safe_style_css` is a global filter, so callers MUST keep the
	 * `add_filter` / `remove_filter` pair tightly scoped — anything between
	 * them runs with the widened allowlist for every `wp_kses` consumer on
	 * the request, including unrelated content.
	 *
	 * The widened set is intentionally limited to declarations that hide an
	 * element in place (`display`, `visibility`, `opacity`) plus
	 * `background-image` (added to core's `safe_style_css` in WP 5.0; the
	 * plugin still supports 4.9.6).
	 *
	 * @since 2.4.0
	 *
	 * @param string $value Raw Hidden Fields markup.
	 * @return string Sanitized markup.
	 */
	protected function sanitize_hidden_fields( $value ) {
		add_filter( 'safe_style_css', array( $this, 'extend_safe_style_css' ) );
		try {
			$sanitized = wp_kses( $value, $this->get_hidden_fields_allowed_html() );
		} catch ( Exception $e ) {
			// `try`/`finally` would be cleaner but is PHP 5.5+; the plugin's floor is 5.4.
			remove_filter( 'safe_style_css', array( $this, 'extend_safe_style_css' ) );
			throw $e;
		}
		remove_filter( 'safe_style_css', array( $this, 'extend_safe_style_css' ) );

		return $sanitized;
	}

	/**
	 * Extend WordPress's `safe_style_css` allowlist with the declarations
	 * needed to visually hide a Hidden Fields element in place.
	 *
	 * Deliberately narrow: nothing here can be combined with arbitrary
	 * markup to overlay or reposition unrelated page content. See
	 * `sanitize_hidden_fields()` for the full rationale.
	 *
	 * Public so it can be registered as a `safe_style_css` filter callback.
	 * Callers are responsible for adding and removing it as a pair.
	 *
	 * @since 2.4.0
	 *
	 * @param string[] $properties CSS properties allowed in inline style attributes.
	 * @return string[] Properties with hidden-field-friendly entries appended.
	 */
	public function extend_safe_style_css( $properties ) {
		return array_merge(
			$properties,
			array(
				'display',
				'visibility',
				'opacity',
				'background-image',
			)
		);
	}

	/**
	 * Returns allowed HTML tags and attributes for the hidden fields setting.
	 *
	 * Permits the attributes vendor newsletter snippets typically rely on
	 * (placeholder, required, aria-*, data-*, validation/autocomplete hints,
	 * etc.) while excluding event handlers and form-overriding attributes
	 * (on*, formaction, formmethod, formtarget, formenctype, formnovalidate)
	 * that could otherwise redirect submissions or execute JavaScript.
	 *
	 * @since 2.3.0
	 *
	 * @return array Allowed HTML elements and their attributes.
	 */
	protected function get_hidden_fields_allowed_html() {
		$global = array(
			'id'                => array(),
			'class'             => array(),
			'style'             => array(),
			'title'             => array(),
			'role'              => array(),
			'tabindex'          => array(),
			'hidden'            => array(),
			'lang'              => array(),
			'dir'               => array(),
			'data-*'            => true,
			// `aria-*` wildcards are not honored by wp_kses; common aria attributes are enumerated.
			'aria-atomic'       => array(),
			'aria-busy'         => array(),
			'aria-controls'     => array(),
			'aria-current'      => array(),
			'aria-describedby'  => array(),
			'aria-details'      => array(),
			'aria-disabled'     => array(),
			'aria-expanded'     => array(),
			'aria-haspopup'     => array(),
			'aria-hidden'       => array(),
			'aria-invalid'      => array(),
			'aria-label'        => array(),
			'aria-labelledby'   => array(),
			'aria-live'         => array(),
			'aria-placeholder'  => array(),
			'aria-pressed'      => array(),
			'aria-readonly'     => array(),
			'aria-required'     => array(),
			'aria-valuetext'    => array(),
		);

		return array(
			'a'        => array_merge(
				$global,
				array(
					'href'     => array(),
					'target'   => array(),
					'rel'      => array(),
					'download' => array(),
				)
			),
			'div'      => $global,
			'fieldset' => array_merge(
				$global,
				array(
					'name'     => array(),
					'disabled' => array(),
					'form'     => array(),
				)
			),
			'input'    => array_merge(
				$global,
				array(
					'type'           => array(),
					'name'           => array(),
					'value'          => array(),
					'placeholder'    => array(),
					'required'       => array(),
					'readonly'       => array(),
					'disabled'       => array(),
					'checked'        => array(),
					'autocomplete'   => array(),
					'autocapitalize' => array(),
					'autocorrect'    => array(),
					'spellcheck'     => array(),
					'inputmode'      => array(),
					'pattern'        => array(),
					'min'            => array(),
					'max'            => array(),
					'step'           => array(),
					'minlength'      => array(),
					'maxlength'      => array(),
					'size'           => array(),
					'list'           => array(),
					'multiple'       => array(),
					'form'           => array(),
				)
			),
			'label'    => array_merge( $global, array( 'for' => array() ) ),
			'legend'   => $global,
			'option'   => array_merge(
				$global,
				array(
					'value'    => array(),
					'selected' => array(),
					'disabled' => array(),
					'label'    => array(),
				)
			),
			'optgroup' => array_merge(
				$global,
				array(
					'label'    => array(),
					'disabled' => array(),
				)
			),
			'select'   => array_merge(
				$global,
				array(
					'name'         => array(),
					'required'     => array(),
					'disabled'     => array(),
					'multiple'     => array(),
					'size'         => array(),
					'autocomplete' => array(),
					'form'         => array(),
				)
			),
			'textarea' => array_merge(
				$global,
				array(
					'name'           => array(),
					'rows'           => array(),
					'cols'           => array(),
					'placeholder'    => array(),
					'required'       => array(),
					'readonly'       => array(),
					'disabled'       => array(),
					'minlength'      => array(),
					'maxlength'      => array(),
					'autocomplete'   => array(),
					'autocapitalize' => array(),
					'spellcheck'     => array(),
					'wrap'           => array(),
					'form'           => array(),
				)
			),
		);
	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label><?php esc_html_e( 'Title', 'genesis-enews-extended' ); ?>:<br />
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
			</label>
		</p>

		<p>
			<label><?php esc_html_e( 'Text To Show Before Form', 'genesis-enews-extended' ); ?>:<br />
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" class="widefat" rows="6" cols="4"><?php echo esc_html( $instance['text'] ); ?></textarea>
			</label>
		</p>
		<p>
			<label><?php esc_html_e( 'Text To Show After Form', 'genesis-enews-extended' ); ?>:<br />
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'after_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'after_text' ) ); ?>" class="widefat" rows="6" cols="4"><?php echo esc_html( $instance['after_text'] ); ?></textarea>
			</label>
		</p>

		<hr style="background-color: #ccc; border: 0; height: 1px; margin: 20px 0;">
		<p>
			<label><?php esc_html_e( 'Form Action', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'action' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'action' ) ); ?>" value="<?php echo esc_attr( $instance['action'] ); ?>" class="widefat" />
			</label>
		</p>

		<p>
			<label><?php esc_html_e( 'E-Mail Field', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'email-field' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email-field' ) ); ?>" value="<?php echo esc_attr( $instance['email-field'] ); ?>" class="widefat" />
			</label>
		</p>

		<p>
			<label><?php esc_html_e( 'First Name Field', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'fname-field' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fname-field' ) ); ?>" value="<?php echo esc_attr( $instance['fname-field'] ); ?>" class="widefat" />
			</label>
		</p>

		<p>
			<label><?php esc_html_e( 'Last Name Field', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'lname-field' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'lname-field' ) ); ?>" value="<?php echo esc_attr( $instance['lname-field'] ); ?>" class="widefat" />
			</label>
		</p>

		<p>
			<label><?php esc_html_e( 'Hidden Fields', 'genesis-enews-extended' ); ?>:
			<textarea id="<?php echo esc_attr( $this->get_field_id( 'hidden_fields' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hidden_fields' ) ); ?>" class="widefat"><?php echo esc_attr( $instance['hidden_fields'] ); ?></textarea>
			</label>
			<br><small><?php esc_html_e( 'Not all services use hidden fields.', 'genesis-enews-extended' ); ?></small>
		</p>

		<p><label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'open_same_window' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'open_same_window' ) ); ?>" value="1" <?php checked( $instance['open_same_window'] ); ?>/>
			<?php esc_html_e( 'Open confirmation page in same window?', 'genesis-enews-extended' ); ?>
			</label>
		</p>
		<hr style="background-color: #ccc; border: 0; height: 1px; margin: 20px 0;">
		<p>
			<?php $fname_text = empty( $instance['fname_text'] ) ? __( 'First Name', 'genesis-enews-extended' ) : $instance['fname_text']; ?>
			<label><?php esc_html_e( 'First Name Input Text', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'fname_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fname_text' ) ); ?>" value="<?php echo esc_attr( $fname_text ); ?>" class="widefat" />
			</label>
		</p>
		<p>
			<?php $lname_text = empty( $instance['lname_text'] ) ? __( 'Last Name', 'genesis-enews-extended' ) : $instance['lname_text']; ?>
			<label><?php esc_html_e( 'Last Name Input Text', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'lname_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'lname_text' ) ); ?>" value="<?php echo esc_attr( $lname_text ); ?>" class="widefat" />
			</label>
		</p>
		<p>
			<?php $input_text = empty( $instance['input_text'] ) ? __( 'E-Mail Address', 'genesis-enews-extended' ) : $instance['input_text']; ?>
			<label><?php esc_html_e( 'E-Mail Input Text', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'input_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_text' ) ); ?>" value="<?php echo esc_attr( $input_text ); ?>" class="widefat" />
			</label>
		</p>

		<p>
			<?php $button_text = empty( $instance['button_text'] ) ? __( 'Go', 'genesis-enews-extended' ) : $instance['button_text']; ?>
			<label><?php esc_html_e( 'Button Text', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" value="<?php echo esc_attr( $button_text ); ?>" class="widefat" />
			</label>
		</p>
		<p>
			<label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'display_privacy' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'display_privacy' ) ); ?>" value="1" <?php checked( $instance['display_privacy'] ); ?>/>
			<?php esc_html_e( 'Display link to privacy policy?', 'genesis-enews-extended' ); ?></label>
		</p>

		<?php
	}

}
