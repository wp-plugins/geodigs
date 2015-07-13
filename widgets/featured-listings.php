<?php
class Geodigs_Featured_Listings_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes

		parent::__construct(
			'gd_featured_listings', // Base ID
			__( 'Geodigs Featured Listings', 'text_domain' ), // Name
			array( 'description' => __( 'A list of your featured listings', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo do_shortcode($instance['shortcode']);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin

		$defaults = array(
			'shortcode' => '[gd_featured]',
		);
		$instance = wp_parse_args((array)$instance, $defaults);
		?>

		<p>
			<label for="<?=$this->get_field_id('shortcode')?>">Enter Geodigs Featured Listings shortcode</label>
			<input id="<?=$this->get_field_id('shortcode')?>" name="<?=$this->get_field_name('shortcode')?>" class="widefat" type="text" value="<?=$instance['shortcode']?>"/>
		</p>
	<? }
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