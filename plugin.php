<?php
/**
 * Genesis eNews Extended
 *
 * @package BJGK\Genesis_enews_extended
 * @version 0.2.0
 * @author Brandon Kraft <public@brandonkraft.com>
 * @copyright Copyright (c) 2012, Brandon Kraft
 * @link http://www.brandonkraft.com/
 * @license GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Genesis eNews Extended
 * Plugin URI: http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/
 * Description: Replaces the Genesis eNews Widget to allow easier use of additional mailing services.
 * Version: 0.2.0
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

add_action( 'init', 'bjgk_genesis_enews_load_translations' );
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

/**
 * Main plugin class.
 *
 * @package BJGK\Genesis_enews_extended
 * @author Brandon Kraft <public@brandonkraft.com>
 */
class BJGK_Genesis_eNews_Extended extends WP_Widget {

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
	function __construct() {
		$this->defaults = array(
			'title'				=> '',
			'text'				=> '',
			'hidden_fields'		=> '',
			'open_same_window'	=> 0,
			'fname-field'		=> '',
			'lname-field'		=> '',
			'input_text'		=> '',
			'fname_text'		=> '',
			'lname_text'		=> '',
			'button_text'		=> '',
			'action'			=> '',
		);

		$widget_ops = array(
			'classname'   => 'enews-widget',
			'description' => __( 'Displays subscribe form', 'genesis-enews-extended' ),
		);

		parent::__construct( 'enews-ext', __( 'Genesis - eNews Extended', 'genesis-enews-extended' ), $widget_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget . '<div class="enews">';

			if ( ! empty( $instance['title'] ) )
				echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

			echo wpautop( $instance['text'] ); // We run KSES on update

			if ( ! empty( $instance['id'] ) ) : ?>
			<form id="subscribe" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo esc_js( $instance['id'] ); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
				<input type="text" value="<?php echo esc_attr( $instance['input_text'] ); ?>" id="subbox" onfocus="if ( this.value == '<?php echo esc_js( $instance['input_text'] ); ?>') { this.value = ''; }" onblur="if ( this.value == '' ) { this.value = '<?php echo esc_js( $instance['input_text'] ); ?>'; }" name="email" />
				<input type="hidden" name="uri" value="<?php echo esc_attr( $instance['id'] ); ?>" />
				<input type="hidden" name="loc" value="<?php echo esc_attr( get_locale() ); ?>" />
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" id="subbutton" />
			</form>
			<?php elseif ( ! empty( $instance['action'] ) ) : ?>
			<form id="subscribe" action="<?php echo esc_js( $instance['action'] ); ?>" method="post" <?php if ($instance['open_same_window'] == 0 ) : ?> target="_blank"<?php endif; ?>>
				<?php if ( ! empty($instance['fname-field'] ) ) : ?><input type="text" id="subbox1" class="enews-subbox" value="<?php echo esc_attr( $instance['fname_text'] ); ?>" onfocus="if ( this.value == '<?php echo esc_attr( $instance['fname_text'] ); ?>') { this.value = ''; }" onblur="if ( this.value == '' ) { this.value = '<?php echo esc_attr( $instance['fname_text'] ); ?>'; }" name="<?php echo esc_js( $instance['fname-field'] ); ?>" /><?php endif ?>
				<?php if ( ! empty($instance['lname-field'] ) ) : ?><input type="text" id="subbox2" class="enews-subbox" value="<?php echo esc_attr( $instance['lname_text'] ); ?>" onfocus="if ( this.value == '<?php echo esc_attr( $instance['lname_text'] ); ?>') { this.value = ''; }" onblur="if ( this.value == '' ) { this.value = '<?php echo esc_attr( $instance['lname_text'] ); ?>'; }" name="<?php echo esc_js( $instance['lname-field'] ); ?>" /><?php endif ?>
				<input type="text" value="<?php echo esc_attr( $instance['input_text'] ); ?>" id="subbox" onfocus="if ( this.value == '<?php echo esc_js( $instance['input_text'] ); ?>') { this.value = ''; }" onblur="if ( this.value == '' ) { this.value = '<?php echo esc_js( $instance['input_text'] ); ?>'; }" name="<?php echo esc_js( $instance['email-field'] ); ?>" />
				<?php echo $instance['hidden_fields']; ?>
				<input type="submit" value="<?php echo esc_attr( $instance['button_text'] ); ?>" id="subbutton" />
			</form>
			<?php endif;

		echo '</div>' . $after_widget;
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If false is returned, the instance won't be saved / updated.
	 *
	 * @since 0.1.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {
		$new_instance['title']         = strip_tags( $new_instance['title'] );
		$new_instance['text']          = wp_kses( $new_instance['text'], genesis_formatting_allowedtags() );
		$new_instance['hidden_fields'] = strip_tags( $new_instance['hidden_fields'], "<input>" );
		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.0
	 *
	 * @param array $instance Current settings.
	 */
	function form( $instance ) {
		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis-enews-extended' ); ?>:</label><br />
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text To Show', 'genesis-enews-extended' ); ?>:</label><br />
			<textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" class="widefat" rows="6" cols="4"><?php echo htmlspecialchars( $instance['text'] ); ?></textarea>
		</p>
		<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;">
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Google/Feedburner ID', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo esc_attr( $instance['id'] ); ?>" class="widefat" /><br />
			<small><?php _e( 'Entering your Feedburner ID here will deactivate the custom options below.', 'genesis-enews-extended' ); ?></small>
		</p>
		<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;">
		<p>
			<label for="<?php echo $this->get_field_id( 'action' ); ?>"><?php _e( 'Form Action', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'action' ); ?>" name="<?php echo $this->get_field_name( 'action' ); ?>" value="<?php echo esc_attr( $instance['action'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'email-field' ); ?>"><?php _e( 'E-Mail Field', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'email-field' ); ?>" name="<?php echo $this->get_field_name( 'email-field' ); ?>" value="<?php echo esc_attr( $instance['email-field'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'fname-field' ); ?>"><?php _e( 'First Name Field', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'fname-field' ); ?>" name="<?php echo $this->get_field_name( 'fname-field' ); ?>" value="<?php echo esc_attr( $instance['fname-field'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'lname-field' ); ?>"><?php _e( 'Last Name Field', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'lname-field' ); ?>" name="<?php echo $this->get_field_name( 'lname-field' ); ?>" value="<?php echo esc_attr( $instance['lname-field'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'hidden_fields' ); ?>"><?php _e( 'Hidden Fields', 'genesis-enews-extended' ); ?>:</label>
			<textarea id="<?php echo $this->get_field_id( 'hidden_fields' ); ?>" name="<?php echo $this->get_field_name( 'hidden_fields' ); ?>" class="widefat"><?php echo esc_attr( $instance['hidden_fields'] ); ?></textarea>
			<br><small><?php _e( 'Not all services use hidden fields.', 'genesis-enews-extended'); ?></small>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'open_same_window' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'open_same_window' ); ?>" value="1" <?php checked( $instance['open_same_window'] ); ?>/>
			<label for="<?php echo $this->get_field_id( 'open_same_window' ); ?>"><?php _e( 'Open confirmation page in same window?', 'genesis-enews-extended' ); ?></label>
		</p>
		<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;">
		<p>
			<?php $fname_text = empty( $instance['fname_text'] ) ? __( 'First Name...', 'genesis-enews-extended' ) : $instance['fname_text']; ?>
			<label for="<?php echo $this->get_field_id( 'fname_text' ); ?>"><?php _e( 'First Name Input Text', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'fname_text' ); ?>" name="<?php echo $this->get_field_name( 'fname_text' ); ?>" value="<?php echo esc_attr( $fname_text ); ?>" class="widefat" />
		</p>
		<p>
			<?php $lname_text = empty( $instance['lname_text'] ) ? __( 'Last Name...', 'genesis-enews-extended' ) : $instance['lname_text']; ?>
			<label for="<?php echo $this->get_field_id( 'lname_text' ); ?>"><?php _e( 'Last Name Input Text', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'lname_text' ); ?>" name="<?php echo $this->get_field_name( 'lname_text' ); ?>" value="<?php echo esc_attr( $lname_text ); ?>" class="widefat" />
		</p>
		<p>
			<?php $input_text = empty( $instance['input_text'] ) ? __( 'Enter your email address...', 'genesis-enews-extended' ) : $instance['input_text']; ?>
			<label for="<?php echo $this->get_field_id( 'input_text' ); ?>"><?php _e( 'E-Mail Input Text', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'input_text' ); ?>" name="<?php echo $this->get_field_name( 'input_text' ); ?>" value="<?php echo esc_attr( $input_text ); ?>" class="widefat" />
		</p>

		<p>
			<?php $button_text = empty( $instance['button_text'] ) ? __( 'Go', 'genesis-enews-extended' ) : $instance['button_text']; ?>
			<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text', 'genesis-enews-extended' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo esc_attr( $button_text ); ?>" class="widefat" />
		</p>

	<?php
	}

}

add_action( 'widgets_init', 'bjgk_genesis_enews_load_widgets' );
/**
 * Register widget.
 *
 * @since 0.1.0
 */
function bjgk_genesis_enews_load_widgets() {
	register_widget( 'BJGK_Genesis_eNews_Extended' );
}