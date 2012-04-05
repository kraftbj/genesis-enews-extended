<?php
/**
 * Plugin Name: Genesis eNews Extended
 * Plugin URI: http://www.brandonkraft.com/contrib/plugins/genesis-enews-extended/
 * Description: Replaces the Genesis eNews Widget to allow easier use of additional mailing services.
 * Version: 0.1.1
 * Author: Brandon Kraft
 * Author URI: http://www.brandonkraft.com
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package BJGK_Genesis_enews_extended
 * @version 0.1.1
 * @author Brandon Kraft <bk@kraft.im>
 * @copyright Copyright (c) 2012, Brandon Kraft
 * @link http://www.brandonkraft.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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
	 */
	function __construct() {

		$this->defaults = array(
			'title'			=> '',
			'text'			=> '',
			'hidden_fields'	=> '',
			'input_text'	=> '',
			'button_text'	=> '',
			'action'	=> '',
		);

		$widget_ops = array(
			'classname'   => 'enews-extended-widget',
			'description' => __( 'Displays subscribe form', 'genesis' ),
		);

		$this->WP_Widget( 'enews-ext', __( 'Genesis - eNews Extended', 'genesis' ), $widget_ops );

	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		extract( $args );

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget . '<div class="enews">';

			if ( ! empty( $instance['title'] ) )
				echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

			echo wpautop( $instance['text'] ); // We run KSES on update

			if ( ! empty( $instance['action'] ) ) : ?>
			<form id="subscribe" action="<?php echo esc_js( $instance['action'] ); ?>" method="post" target="_blank">
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
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title'] = strip_tags( $new_instance['title'] );
		$new_instance['text']  = wp_kses( $new_instance['text'], genesis_formatting_allowedtags() );
		/** $new_instance['hidden_fields'] = strip_tags( $new_instance['hidden_fields'], "input" ); */
		return $new_instance;

	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label><br />
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text To Show', 'genesis' ); ?>:</label><br />
			<textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" class="widefat" rows="6" cols="4"><?php echo htmlspecialchars( $instance['text'] ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'action' ); ?>"><?php _e( 'Form Action', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'action' ); ?>" name="<?php echo $this->get_field_name( 'action' ); ?>" value="<?php echo esc_attr( $instance['action'] ); ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'email-field' ); ?>"><?php _e( 'E-Mail Field', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'email-field' ); ?>" name="<?php echo $this->get_field_name( 'email-field' ); ?>" value="<?php echo esc_attr( $instance['email-field'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'hidden_fields' ); ?>"><?php _e( 'Hidden Fields', 'genesis' ); ?>:</label>
			<textarea id="<?php echo $this->get_field_id( 'hidden_fields' ); ?>" name="<?php echo $this->get_field_name( 'hidden_fields' ); ?>" class="widefat"><?php echo esc_attr( $instance['hidden_fields'] ); ?></textarea>
			<br><small>Not all services use hidden fields.</small>
		</p>
		
		<p>
			<?php $input_text = empty( $instance['input_text'] ) ? __( 'Enter your email address...', 'genesis' ) : $instance['input_text']; ?>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Input Text', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'input_text' ); ?>" name="<?php echo $this->get_field_name( 'input_text' ); ?>" value="<?php echo esc_attr( $input_text ); ?>" class="widefat" />
		</p>

		<p>
			<?php $button_text = empty( $instance['button_text'] ) ? __( 'Go', 'genesis' ) : $instance['button_text']; ?>
			<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo esc_attr( $button_text ); ?>" class="widefat" />
		</p>

	<?php
	}

}
add_action( 'widgets_init', 'bjgk_genesis_enews_load_widgets' );

function bjgk_genesis_enews_load_widgets() {

	register_widget( 'BJGK_Genesis_eNews_Extended' );

}