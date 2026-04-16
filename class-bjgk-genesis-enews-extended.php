<?php
/**
 * Genesis eNews Extended
 *
 * @package   BJGK\Genesis_enews_extended
 * @version   2.3.1
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
		echo wpautop( apply_filters( 'gee_text', $instance['text'] ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['action'] ) ) : ?>
			<form id="subscribe-<?php echo esc_attr( $this->id ); ?>" class="enews-form" action="<?php echo esc_url( $instance['action'] ); ?>" method="post"
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
					<input type="text" class="enews-subbox enews-fname subbox1" value="" aria-label="<?php echo esc_attr( $instance['fname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['fname_text'] ); ?>" name="<?php echo esc_attr( $instance['fname-field'] ); ?>" /><?php endif ?>
				<?php
				if ( ! empty( $instance['lname-field'] ) ) :
					?>
					<input type="text" class="enews-subbox enews-lname subbox2" value="" aria-label="<?php echo esc_attr( $instance['lname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['lname_text'] ); ?>" name="<?php echo esc_attr( $instance['lname-field'] ); ?>" /><?php endif ?>
				<input type="<?php echo current_theme_supports( 'html5' ) ? 'email' : 'text'; ?>" value="" class="enews-email subbox" aria-label="<?php echo esc_attr( $instance['input_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['input_text'] ); ?>" name="<?php echo esc_attr( $instance['email-field'] ); ?>"
					<?php
					if ( current_theme_supports( 'html5' ) ) :
						?>
						required="required"<?php endif; ?> />
				<?php echo wp_kses( $instance['hidden_fields'], $this->get_hidden_fields_allowed_html() ); ?>
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" class="enews-submit subbutton" />
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
		echo wpautop( apply_filters( 'gee_after_text', $instance['after_text'] ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

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
		$new_instance['hidden_fields']   = wp_kses( $new_instance['hidden_fields'], $this->get_hidden_fields_allowed_html() );
		$new_instance['after_text']      = trim( wp_kses_post( $new_instance['after_text'] ) );
		$new_instance['action']          = esc_url_raw( trim( $new_instance['action'] ) );
		$new_instance['display_privacy'] = ( isset( $new_instance['display_privacy'] ) ) ? (int) $new_instance['display_privacy'] : 0;

		return $new_instance;
	}

	/**
	 * Returns allowed HTML tags and attributes for the hidden fields setting.
	 *
	 * @since 2.3.0
	 *
	 * @return array Allowed HTML elements and their attributes.
	 */
	protected function get_hidden_fields_allowed_html() {
		return array(
			'a'        => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
				'rel'    => array(),
			),
			'div'      => array(
				'class' => array(),
				'id'    => array(),
				'style' => array(),
			),
			'fieldset' => array(),
			'input'    => array(
				'type'  => array(),
				'name'  => array(),
				'value' => array(),
				'id'    => array(),
				'class' => array(),
			),
			'label'    => array(
				'for'   => array(),
				'class' => array(),
			),
			'legend'   => array(),
			'option'   => array(
				'value'    => array(),
				'selected' => array(),
			),
			'optgroup' => array(
				'label' => array(),
			),
			'select'   => array(
				'name'  => array(),
				'id'    => array(),
				'class' => array(),
			),
			'textarea' => array(
				'name'  => array(),
				'id'    => array(),
				'class' => array(),
				'rows'  => array(),
				'cols'  => array(),
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
