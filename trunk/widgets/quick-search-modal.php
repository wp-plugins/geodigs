<?php
class Geodigs_Quick_Search_Modal_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes

		parent::__construct(
			'gd_quick_search_modal', // Base ID
			__( 'Geodigs Quick Search Modal', 'text_domain' ), // Name
			array( 'description' => __( 'A quick-search form for real estate listings as a modal', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		include GD_DIR_MODALS . 'quick-search.php';
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
	}
}