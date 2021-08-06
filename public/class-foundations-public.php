<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://labzone.tech/foundations
 * @since      1.0.0
 *
 * @package    Foundations
 * @subpackage Foundations/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Foundations
 * @subpackage Foundations/public
 * @author     Martin Starosta <info@labzone.sk>
 */
class Foundations_Public {

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

		/** Logo Image Size */
		add_image_size( 'logo', 250, 50, true );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/foundations-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Adds shortcodes.
	 *
	 * @since      1.0.0
	 */
	public function add_shortcodes() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-foundations-shortcodes.php';
		$shortcodes = new Foundations_Shortcodes();
		$shortcodes->register_shortcodes();
	}

	/**
	 * Adds foundation information to products loop display.
	 *
	 * @since      1.0.0
	 */
	public function foundation_information_below_title() {
		global $product;

		$foundation_id = get_post_meta( $product->get_id(), 'foundation_id', true );
		$foundation    = get_post( $foundation_id );

		if ( ! empty( $foundation ) ){
			echo '<div class="foundation-information"><span class="foundation-title">' . esc_html( $foundation->post_title ) . '</span>' . get_the_post_thumbnail( $foundation_id, 'logo' ) . '</div>';
		}
	}

	/**
	 * List foundations which will be supported by this cart.
	 */
	public function list_foundations_on_cart() {
		global $woocommerce;
		$cart_items = $woocommerce->cart->get_cart();

		echo '<div class="foundations-contributed-list">';
		echo sprintf( '<h4>%s</h4>', esc_html( __( 'You will support these foundations by ordering these products.', 'foundations' ) ) );
		$this->list_foundations_html( $cart_items );
		echo '</div>';
	}

	/**
	 * Echoes HTML for foundations list.
	 *
	 * @param mixed $items Array of products.
	 * @since 1.0
	 */
	private function list_foundations_html( $items ) {

		$rendered_foundations      = array();
		$foundations_contributions = array();

		echo '<div class="foundations-list">';

		foreach ( $items as $item => $values ) {
			$product       = wc_get_product( $values['data']->get_id() );
			$foundation_id = get_post_meta( $product->get_id(), 'foundation_id', true );
			$quantity      = $values['quantity'];
			$price         = $product->get_price();
			$contribution  = $quantity * ( floatval( $price ) - floatval( get_post_meta( $product->get_id(), 'foundation_contribution', true ) ) );

			$foundations_contributions[ $foundation_id ] = ( isset( $foundations_contributions[ $foundation_id ] ) ) ? ( $foundations_contributions[ $foundation_id ] + $contribution ) : $contribution;
		}

		foreach ( $items as $item => $values ) { 
			$product       = wc_get_product( $values['data']->get_id() );
			$foundation_id = get_post_meta( $product->get_id(), 'foundation_id', true );

			if ( -1 !== intval( $foundation_id ) && ! in_array( $foundation_id, $rendered_foundations, true ) ) {
				$foundation = get_post( $foundation_id );
				echo '<div class="foundation-information">';
				if ( has_post_thumbnail( $foundation_id ) ) {
					echo get_the_post_thumbnail( $foundation_id, 'logo' );
				} else {
					echo '<span class="foundation-title">' . esc_html( $foundation->post_title ) . '</span>';
				}
				echo sprintf( '<div class="foundation-contribution">%s: %s</div>', esc_html( __('Contribution','foundations' ) ), wc_price( esc_html( $foundations_contributions[ $foundation_id ] ) ) );
				echo '</div>';
				array_push( $rendered_foundations, $foundation_id );
			}
		}
		echo '</div>';
	}
}
