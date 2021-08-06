<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://labzone.tech/foundations
 * @since      1.0.0
 *
 * @package    Foundations
 * @subpackage Foundations/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Foundations
 * @subpackage Foundations/admin
 * @author     Martin Starosta <info@labzone.sk>
 */
class Foundations_Admin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Adds foundations tab to product.
	 *
	 * @param array $tabs Tabs content.
	 * @since      1.0.0
	 */
	public function foundations_product_settings_tabs( $tabs ) {

		$tabs['foundations'] = array(
			'label'    => __( 'Foundations', 'foundations' ),
			'target'   => 'foundations_product_data',
			'class'    => array( 'foundations_settings_tab' ),
			'priority' => 21,
		);
		return $tabs;
	}

	/**
	 * Adds content to foundations tab on the product.
	 *
	 * @since      1.0.0
	 */
	public function foundations_product_panels() {
		global  $woocommerce;

		echo '<div id="foundations_product_data" class="panel woocommerce_options_panel hidden">';

		$foundations         = get_posts(
			array(
				'post_type'        => 'foundations',
				'post_status'      => 'publish',
				'suppress_filters' => false,
				'posts_per_page'   => -1,
			)
		);
		$foundations_options = array( '-1' => __( 'Please select foundation', 'foundations' ) );
		foreach ( $foundations as $foundation_post ) {
			$foundations_options[ $foundation_post->ID ] = $foundation_post->post_title;
		}

		woocommerce_wp_select(
			array(
				'id'      => 'foundation_id',
				'value'   => get_post_meta( get_the_ID(), 'foundation_id', true ),
				'label'   => __( 'Foundation', 'foundations' ),
				'options' => $foundations_options,
			)
		);

		$contribute_all_value = get_post_meta( get_the_ID(), 'foundation_contribute_all', true );
		woocommerce_wp_checkbox(
			array(
				'id'          => 'foundation_contribute_all',
				'label'       => __( 'Contribute all', 'foundations' ),
				'description' => __( 'When checked, whole product price will go to foundation.', 'foundations' ),
				'value'       => $contribute_all_value,
				'cbvalue'     => ( ! empty( $contribute_all_value ) ) ? $contribute_all_value : 'yes',
			),
			get_post_meta( get_the_ID(), 'foundation_contribute_all', true )
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'foundation_contribution',
				'value'       => get_post_meta( get_the_ID(), 'foundation_contribution', true ),
				'label'       => __( 'Product cost', 'foundations' ) . ' [' . get_woocommerce_currency_symbol() . ']',
				'description' => __( 'Contribution to foundation is Sale price - Product cost', 'foundations' ),
				'type'        => 'price',
			)
		);

		echo '</div>';
	}

	/**
	 * Saves custom metadata to product.
	 *
	 * @param int $post_id Product(post) ID.
	 * @since      1.0.0
	 */
	public function foundations_save_product_metadata( $post_id ) {

		$foundation_id = isset( $_POST['foundation_id'] ) ? sanitize_text_field( $_POST['foundation_id'] ) : -1;
		update_post_meta( $post_id, 'foundation_id', esc_attr( $foundation_id ) );

		$foundation_contribution = isset( $_POST['foundation_contribution'] ) ? sanitize_text_field( $_POST['foundation_contribution'] ) : 0.0;
		update_post_meta( $post_id, 'foundation_contribution', esc_attr( $foundation_contribution ) );

		$foundation_contribution_all = ( isset( $_POST['foundation_contribute_all'] ) && 'yes' === sanitize_text_field( $_POST['foundation_contribute_all'] ) ) ? true : false;
		update_post_meta( $post_id, 'foundation_contribute_all', $foundation_contribution_all );
	}

	/**
	 * Registers foundation post type.
	 *
	 * @since      1.0.0
	 */
	public function register_foundation_post_type() {
		$foundations_labels = array(
			'name'          => 'foundations',
			'singular_name' => __( 'Foundation', 'foundations' ),
			'menu_name'     => __( 'Foundations', 'foundations' ),
		);
		$foundations_args   = array(
			'labels'          => $foundations_labels,
			'public'          => true,
			'capability_type' => 'post',
			'has_archive'     => true,
			'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'rewrite'         => array(
				'slug' => __( 'foundation_slug', 'foundations' ),
			),
		);
		register_post_type( 'foundations', $foundations_args );
	}

	/**
	 * Registers Caron fields library
	 *
	 * @since      1.0.0
	 */
	function load_carbon_fields() {
		\Carbon_Fields\Carbon_Fields::boot();
	}
}
