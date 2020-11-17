<?php
/**
 * Genesis eNews Extended
 *
 * @package   BJGK\Genesis_enews_extended
 * @version   2.1.4
 * @author    Brandon Kraft <public@brandonkraft.com>
 * @link      https://kraft.blog/genesis-enews-extended/
 * @copyright Copyright (c) 2012-2018, Brandon Kraft
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
			'id'               => '',
			'email-field'      => '',
			'action'           => '',
			'display_privacy'  => 0,
			'mailpoet_check'   => __( 'Check your inbox or spam folder now to confirm your subscription.', 'genesis-enews-extended' ),
			'mailpoet_subbed'  => __( "You've successfully subscribed.", 'genesis-enews-extended' ),
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
	 * The WordPress.CSRF.NonceVerification sniff is disabled since we are dealing with intentionally logged-out submissions.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// phpcs:disable WordPress.CSRF.NonceVerification
		$before_widget = $args['before_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$after_widget  = $args['after_widget'];

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$instance = apply_filters( 'genesis-enews-extended-args', $instance ); //phpcs:ignore WordPress.NamingConventions.ValidHookName

		// Checks if MailPoet exists. If so, a check for form submission will take place.
        // phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( class_exists( 'WYSIJA' ) && isset( $_POST['submission-type'] ) && 'mailpoet' === $_POST['submission-type'] && ! empty( $instance['mailpoet-list'] ) ) { // Input var okay.
			$subscriber_data = array(
				'user'      => array(
					'firstname' => isset( $_POST['mailpoet-firstname'] ) ? sanitize_title( wp_unslash( $_POST['mailpoet-firstname'] ) ) : '', // Input var okay.
					'lastname'  => isset( $_POST['mailpoet-lastname'] ) ? sanitize_title( wp_unslash( $_POST['mailpoet-lastname'] ) ) : '', // Input var okay.
					'email'     => isset( $_POST['mailpoet-email'] ) ? sanitize_email( wp_unslash( $_POST['mailpoet-email'] ) ) : '', // Input var okay.
				),
				'user_list' => array(
					'list_ids' => array_values( $instance['mailpoet-list'] ),
				),
			);

			$mailpoet_subscriber_id = WYSIJA::get( 'user', 'helper' )->addSubscriber( $subscriber_data );
		}

		// phpcs:enable

		// Set default fname_text, lname_text for backwards compat for installs upgraded from 0.1.6+ to 0.3.0+.
		if ( empty( $instance['fname_text'] ) ) {
			$instance['fname_text'] = 'First Name';
		}
		if ( empty( $instance['lname_text'] ) ) {
			$instance['lname_text'] = 'Last Name';
		}

		// Establishes current URL for MailPoet action fields.
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ); // Input var okay; sanitization okay.

		// We run KSES on update since we want to allow some HTML, so ignoring the ouput escape check.
		echo $before_widget . '<div class="enews">'; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			// We run KSES on update since we want to allow some HTML, so ignoring the ouput escape check.
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		// We run KSES on update since we want to allow some HTML, so ignoring the ouput escape check.
		echo wpautop( apply_filters( 'gee_text', $instance['text'] ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['id'] ) ) : ?>
			<form
					id="subscribe-<?php echo esc_attr( $this->id ); ?>"
					action="https://feedburner.google.com/fb/a/mailverify"
					method="post"
					name="<?php echo esc_attr( $this->id ); ?>"
				<?php if ( ! $this->is_amp() ) : ?>
					target="popupwindow"
					onsubmit="window.open( 'https://feedburner.google.com/fb/a/mailverify?uri=<?php echo esc_js( $instance['id'] ); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true"
				<?php else : ?>
					on="<?php echo esc_attr( sprintf( 'submit-success:AMP.navigateTo( url=%s, target=_blank )', wp_json_encode( 'https://feedburner.google.com/fb/a/mailverify?uri=' . $instance['id'], JSON_UNESCAPED_SLASHES ) ) ); ?>"
				<?php endif; ?>
					xmlns="http://www.w3.org/1999/html">
				<input type="<?php echo current_theme_supports( 'html5' ) ? 'email' : 'text'; ?>" value="" id="subbox" aria-label="<?php echo esc_attr( $instance['input_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['input_text'] ); ?>" name="email"
																	<?php
																	if ( current_theme_supports( 'html5' ) ) :
																		?>
																		required="required"<?php endif; ?> />
				<input type="hidden" name="uri" value="<?php echo esc_attr( $instance['id'] ); ?>" />
				<input type="hidden" name="loc" value="<?php echo esc_attr( get_locale() ); ?>" />
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" id="subbutton" />

				<?php if ( $this->is_amp() ) : ?>
					<div submit-success><!-- Suppress the success message from the AMP plugin because the result is shown in the opened window. --></div>
				<?php endif; ?>
			</form>
		<?php elseif ( ! empty( $instance['action'] ) ) : ?>
			<form id="subscribe<?php echo esc_attr( $this->id ); ?>" action="<?php echo esc_attr( $instance['action'] ); ?>" method="post"
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
					<input type="text" id="subbox1" class="enews-subbox" value="" aria-label="<?php echo esc_attr( $instance['fname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['fname_text'] ); ?>" name="<?php echo esc_attr( $instance['fname-field'] ); ?>" /><?php endif ?>
				<?php
				if ( ! empty( $instance['lname-field'] ) ) :
					?>
					<input type="text" id="subbox2" class="enews-subbox" value="" aria-label="<?php echo esc_attr( $instance['lname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['lname_text'] ); ?>" name="<?php echo esc_attr( $instance['lname-field'] ); ?>" /><?php endif ?>
				<input type="<?php echo current_theme_supports( 'html5' ) ? 'email' : 'text'; ?>" value="" id="subbox" aria-label="<?php echo esc_attr( $instance['input_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['input_text'] ); ?>" name="<?php echo esc_js( $instance['email-field'] ); ?>"
																	<?php
																	if ( current_theme_supports( 'html5' ) ) :
																		?>
																		required="required"<?php endif; ?> />
				<?php echo $instance['hidden_fields']; // phpcs:ignore ?>
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" id="subbutton" />
			</form>
		<?php elseif ( ! empty( $instance['mailpoet-list'] ) && 'disabled' !== $instance['mailpoet-list'] ) : ?>
			<form id="subscribe<?php echo esc_attr( $this->id ); ?>" action="<?php echo esc_attr( $current_url ); ?>" method="post" name="<?php echo esc_attr( $this->id ); ?>">
				<?php
				if ( ! empty( $mailpoet_subscriber_id ) && is_int( $mailpoet_subscriber_id ) ) :
					// confirmation message phrasing depends on whether the user has to verify his subscription or not.
					$mailpoet_needs_confirmation = WYSIJA::get( 'config', 'model' )->getValue( 'confirm_dbleoptin' ); // bool.
					$success_message             = $mailpoet_needs_confirmation ? $instance['mailpoet_check'] : $instance['mailpoet_subbed'];
					?>
				<div class="mailpoet-message mailpoet-success <?php echo $mailpoet_needs_confirmation ? 'mailpoet-needs-confirmation' : 'mailpoet-confirmed'; ?>">
					<?php echo esc_html( $success_message ); ?>
				</div>
				<?php endif; ?>
				<?php
				if ( isset( $instance['mailpoet-show-fname'] ) ) :
					?>
					<input type="text" id="subbox1" class="enews-subbox" value="" aria-label="<?php echo esc_attr( $instance['fname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['fname_text'] ); ?>" name="mailpoet-firstname" /><?php endif ?>
				<?php
				if ( isset( $instance['mailpoet-show-lname'] ) ) :
					?>
					<input type="text" id="subbox2" class="enews-subbox" value="" aria-label="<?php echo esc_attr( $instance['lname_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['lname_text'] ); ?>" name="mailpoet-lastname" /><?php endif ?>
				<input type="<?php echo current_theme_supports( 'html5' ) ? 'email' : 'text'; ?>" value="" id="subbox" aria-label="<?php echo esc_attr( $instance['input_text'] ); ?>" placeholder="<?php echo esc_attr( $instance['input_text'] ); ?>" name="mailpoet-email"
																	<?php
																	if ( current_theme_supports( 'html5' ) ) :
																		?>
																		required="required"<?php endif; ?> />
				<?php echo $instance['hidden_fields']; // phpcs:ignore ?>
				<input type="hidden" name="submission-type" value="mailpoet" />
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" id="subbutton" />
			</form>
			<?php
		endif;
		if ( $instance['display_privacy'] && function_exists( 'the_privacy_policy_link' ) ) {
			the_privacy_policy_link( '<small class="enews-privacy">', '</small>' );

		}
		// We run KSES on update since we want to allow some HTML, so ignoring the ouput escape check.
		echo wpautop( apply_filters( 'gee_after_text', $instance['after_text'] ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		echo '</div>' . $after_widget; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		// phpcs:enable WordPress.CSRF.NonceVerification
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
		$new_instance['title']           = trim( strip_tags( $new_instance['title'], '<i>' ) );
		$new_instance['text']            = trim( wp_kses_post( $new_instance['text'] ) );
		$new_instance['hidden_fields']   = strip_tags( $new_instance['hidden_fields'], '<a>, <div>, <fieldset>, <input>, <label>, <legend>, <option>, <optgroup>, <select>, <textarea>' );
		$new_instance['after_text']      = trim( wp_kses_post( $new_instance['after_text'] ) );
		$new_instance['id']              = trim( str_replace( 'http://feeds.feedburner.com/', '', $new_instance['id'] ) );
		$new_instance['display_privacy'] = ( isset( $new_instance['display_privacy'] ) ) ? (int) $new_instance['display_privacy'] : 0;

		if ( isset( $new_instance['mailpoet_check'] ) ) {
			$new_instance['mailpoet_check'] = wp_kses_post( $new_instance['mailpoet_check'] );
		}

		if ( isset( $new_instance['mailpoet_subbed'] ) ) {
			$new_instance['mailpoet_subbed'] = wp_kses_post( $new_instance['mailpoet_subbed'] );
		}

		return $new_instance;
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
		<?php
		if ( class_exists( 'WYSIJA' ) ) :
			$mp_model_list = WYSIJA::get( 'list', 'model' );
			$mp_lists      = $mp_model_list->get(
				array( 'name', 'list_id' ),
				array(
					'is_enabled' => 1,
				)
			);
			?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'mailpoet-list' ) ); ?>"><?php esc_html_e( 'MailPoet List', 'genesis-enews-extended' ); ?>:</label>
			<fieldset>
				<ul>
					<?php foreach ( $mp_lists as $mp_list ) : ?>
					<li>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'mailpoet-list' ) ); ?>[]" value="<?php echo esc_attr( $mp_list['list_id'] ); ?>"
																	<?php
																	if ( isset( $instance['mailpoet-list'] ) ) {
																		checked( in_array( $mp_list['list_id'], (array) $instance['mailpoet-list'], true ) ); }
																	?>
/>
							<?php echo esc_html( $mp_list['name'] ); ?>
						</label>
					</li>
					<?php endforeach; ?>
				</ul>

				<small>
					<?php esc_html_e( 'Show Fields:', 'genesis-enews-extended' ); ?><br/>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'mailpoet-show-fname' ) ); ?>" value="1" <?php checked( isset( $instance['mailpoet-show-fname'] ) ); ?> />
						<?php esc_html_e( 'First Name', 'genesis-enews-extended' ); ?>
					</label>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'mailpoet-show-lname' ) ); ?>" value="1" <?php checked( isset( $instance['mailpoet-show-lname'] ) ); ?> />
						<?php esc_html_e( 'Last Name', 'genesis-enews-extended' ); ?>
					</label>

				</small>

				<p>
					<label><?php esc_html_e( 'Text Displayed If Confirmation Needed', 'genesis-enews-extended' ); ?>:<br />
					<textarea id="<?php echo esc_attr( $this->get_field_id( 'mailpoet_check' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mailpoet_check' ) ); ?>" class="widefat" rows="6" cols="4"><?php echo esc_html( $instance['mailpoet_check'] ); ?></textarea>
					</label>
				</p>
				<p>
					<><?php esc_html_e( 'Text Displayed If Subscribed', 'genesis-enews-extended' ); ?>:<br />
					<textarea id="<?php echo esc_attr( $this->get_field_id( 'mailpoet_subbed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mailpoet_subbed' ) ); ?>" class="widefat" rows="6" cols="4"><?php echo esc_html( $instance['mailpoet_subbed'] ); ?></textarea>
					</label>
				</p>
			</fieldset>
		</p>
		<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;">
		<?php endif; ?>
		<p>
			<label><?php esc_html_e( 'Google/Feedburner ID', 'genesis-enews-extended' ); ?>:
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" value="<?php echo esc_attr( $instance['id'] ); ?>" class="widefat" /><br />
			</label>
			<small><?php esc_html_e( 'Entering your Feedburner ID here will deactivate the custom options below.', 'genesis-enews-extended' ); ?></small>
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
