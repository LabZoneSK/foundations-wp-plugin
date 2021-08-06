<?php
/**
 * Shortcodes defined for Foundations plugin.
 *
 * @link       http://labzone.tech/foundations
 * @since      1.0.0
 *
 * @package    Foundations
 * @subpackage Foundations/public
 */

/**
 * Shortcodes defined for Foundations plugin.
 *
 * @since      1.0.0
 * @package    Foundations
 * @subpackage Foundations/public
 * @author     Martin Starosta <info@labzone.sk>
 */
class Foundations_Shortcodes {

	/**
	 * Helper functions provider.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Foundations_Helper    $helper    Provides helper functions.
	 */
	protected $helper;

	/**
	 * Defines shortcodes.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->helper = new Foundations_Helper();
	}

	/**
	 * Add shortcodes.
	 *
	 * @since      1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'product_detail_contribution_text', array( $this, 'product_detail_contribution_text_shortcode' ) );
		add_shortcode( 'product_contribution', array( $this, 'product_contribution_text_shortcode' ) );
		add_shortcode( 'product_contributed', array( $this, 'product_detail_contributed_text_shortcode' ) );
		add_shortcode( 'product_contributed_all', array( $this, 'product_detail_contributed_all_shortcode' ) );
		add_shortcode( 'contributed_all', array( $this, 'product_detail_contributed_all_foundations_shortcode' ) );

		/** Foundation information getter shortcodes. */
		add_shortcode( 'foundation_name', array( $this, 'foundation_name_shortcode' ) );
		add_shortcode( 'foundation_text', array( $this, 'foundation_summary_text_shortcode' ) );
		add_shortcode( 'foundation_meta', array( $this, 'foundation_meta_value_shortcode' ) );
	}

	/**
	 * Show, how much contribution from product.
	 *
	 * @since      1.0.0
	 */
	public function product_detail_contribution_text_shortcode() {
		global $post;

		$product = wc_get_product( $post->ID );

		$price         = $product->get_price() - get_post_meta( $post->ID, 'foundation_contribution', true );
		$foundation_id = get_post_meta( $post->ID, 'foundation_id', true );
		$foundation    = get_post( $foundation_id );

		if ( empty( $foundation ) ) {
			return;
		}

		$data = $this->helper->foundations_contributions_data();

		$product_contribution = $this->helper->get_product_contribution( $data, $foundation_id, $post->ID );
		$total_sold           = $this->helper->get_foundation_contribution( $data, $foundation_id );

		$message  = '<p>';
		$message .= wp_sprintf(
			/* translators: 1: Name of the organization */
			__( 'Foundation name %s', 'foundations' ) . ':<br/>',
			$foundation->post_title
		);

		$message .= wp_sprintf(
			/* translators: 1: Amount from product price which will be send to foundation. */
			__('- get %s from each product sold', 'foundations' ) . '<br/>',
			wc_price( $price )
		);

		if ( $product_contribution > 0 ) {
			$message .= wp_sprintf(
				/* translators: 1: Amount collected by this product sale*/
				__( '- total amount collected by selling this product: %s', 'foundations' ) . '<br/>',
				wc_price( $product_contribution )
			);
		} else {
			$message .= __( '- be first to contribute by ordering this product', 'foundations' ) . '<br/>';
		}

		if ( $total_sold > 0 ) {
			$message .= wp_sprintf(
				/* translators: 1: Total sum of money collected */
				__( '- total amount collected from all orders: %s.', 'foundations' ),
				wc_price( $total_sold )
			);
		} else {
			$message .= __( '- organization did not raised any money yet. Be their hero!', 'foundations' );
		}

		$message .= '</p>';

		return $message;
	}

	/**
	 * Show how much contributed from all products.
	 *
	 * @since      1.0.0
	 */
	public function product_detail_contributed_all_shortcode() {
		global $post;

		$foundation_id = get_post_meta( $post->ID, 'foundation_id', true );
		$data          = $this->helper->foundations_contributions_data();
		$total_sold    = $this->helper->get_foundation_contribution( $data, $foundation_id );

		if ( empty( $foundation_id ) || $total_sold <= 0 ) {
			return;
		}

		return wp_sprintf( 
			/* translators: 1: Total sum of money collected */
			__( 'Collected total: %s.', 'foundations' ),
			wc_price( $total_sold )
		);
	}

	/**
	 * Show how much contributed for all foundations.
	 *
	 * @since      1.0.0
	 */
	public function product_detail_contributed_all_foundations_shortcode() {

		$data       = $this->helper->foundations_contributions_data();
		$total_sold = $this->helper->get_all_foundations_contribution( $data );

		if ( $total_sold <= 0 ) {
			return;
		}

		return wp_sprintf( 
			/* translators: 1: Total sum of money collected */
			__( 'Total collected for all foundations: %s.', 'foundations' ),
			wc_price( $total_sold )
		);
	}

	/**
	 * Show how much contributes from this product.
	 *
	 * @since      1.0.0
	 */
	public function product_detail_contributed_text_shortcode() {
		global $post;

		$foundation_id        = get_post_meta( $post->ID, 'foundation_id', true );
		$data                 = $this->helper->foundations_contributions_data();
		$product_contribution = $this->helper->get_product_contribution( $data, $foundation_id, $post->ID );

		if ( $product_contribution <= 0 ) {
			return;
		}

		return wp_sprintf(
			/* translators: 1: Total sum of money collected */
			__( 'Collected by sale of this product: %s.', 'foundations' ),
			wc_price( $product_contribution )
		);
	}

	/**
	 * Show contribution from product.
	 *
	 * @since      1.0.0
	 */
	public function product_contribution_text_shortcode() {
		global $post;

		$price = get_post_meta( $post->ID, 'foundation_contribution', true );

		if ( empty( $price ) || floatval( $price ) <= 0.0 ) {
			return;
		}

		return wp_sprintf(
			/* translators: 1: Amount will collect from sale of this product.*/
			__( 'Will get from this product: %s', 'foundations' ),
			wc_price( $price )
		);
	}

	/**
	 * Returns foundation name.
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @since      1.0.0
	 */
	public function foundation_name_shortcode( $atts ) {
		global $post;

		$foundation_id = isset( $atts['id'] ) ? $atts['id'] : get_post_meta( $post->ID, 'foundation_id', true );
		$foundation    = get_post( $foundation_id );

		return esc_html( $foundation->post_title );
	}

	/**
	 * Returns foundation summary text.
	 *
	 * @since      1.0.0
	 */
	public function foundation_summary_text_shortcode( $atts ) {
		global $post;

		$foundation_id = isset( $atts['id'] ) ? $atts['id'] : get_post_meta( $post->ID, 'foundation_id', true );

		return apply_filters( 'the_content', get_post_field( 'post_content', $foundation_id ) );
	}

	/**
	 * Returns foundation meta data.
	 *
	 * @since      1.0.0
	 */
	public function foundation_meta_value_shortcode( $atts ) {
		global $post;
		$key = $atts['key'];

		$foundation_id       = isset( $atts['id'] ) ? $atts['id'] : get_post_meta( $post->ID, 'foundation_id', true );
		$foundation_meta_key = isset( $key ) ? get_post_meta( $foundation_id, $key, true ) : '';

		return "<span class='foundation-meta foundation-meta-key_$key'>$foundation_meta_key</span>";
	}
}
