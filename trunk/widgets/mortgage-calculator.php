<?php
class Geodigs_Mortgage_Calculator_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes

		parent::__construct(
			'gd_mortgage_calculator', // Base ID
			__( 'Geodigs Mortgage Calculator', 'text_domain' ), // Name
			array( 'description' => __( 'A tool to calculate mortgages', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo do_shortcode('[gd_mortgage_calculator]');
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved

		return $new_instance;
	}
}