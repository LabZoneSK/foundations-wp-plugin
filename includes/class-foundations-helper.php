<?php

/**
 * Helper functions for foundations plugin.
 *
 * @link              https://labzone.tech/foundations
 * @since             1.0.0
 *
 * @package    Foundations
 * @subpackage Foundations/includes
 */

/**
 * Define helper functions.
 *
 * Creates helper functions and tools to work with foundations, orders, products, etc.
 *
 * @since      1.0.0
 * @package    Foundations
 * @subpackage Foundations/public
 * @author     Martin Starosta <info@labzone.sk>
 */

class Foundations_Helper {

	/**
	 * Creates multidimensional array with contributions data from completed orders.
	 *
	 * @return array Contributions data Foundation_id -> product_id -> Value
	 * @since      1.0.0
	 */
	public function foundations_contributions_data() {
		$args = array(
			'status' => array( 'wc-completed' ),
		);
		$orders = wc_get_orders( $args );

		$contribution_data = array();

		foreach ( $orders as $order ) {
			foreach ( $order->get_items() as $item_id => $item ) {
				$product_id              = $item->get_product_id();
				$foundation_id           = get_post_meta( $product_id, 'foundation_id', true );
				$foundation_contribution = self::get_product_cost( $product_id );
				$contribute_all          = get_post_meta( $product_id, 'foundation_contribute_all', true );
				$total_price             = $item->get_total();
				$quantity                = $item->get_quantity();

				$contribution = ( ! empty( $contribute_all ) ) ? $total_price : ( $total_price - $foundation_contribution );

				if ( ! isset( $contribution_data[ $foundation_id ][ $product_id ] ) ) {
					$contribution_data[ $foundation_id ][ $product_id ] = $contribution * $quantity;
				} else {
					$contribution_data[ $foundation_id ][ $product_id ] += $contribution * $quantity;
				}
			}
		}

		return $contribution_data;
	}

	/**
	 * Returns contributed amount by product.
	 *
	 * @param array $data Contribution data.
	 * @param int   $foundation_id Foundation ID.
	 * @param int   $product_id Product ID.
	 * @return float Amount contributed by this product
	 * @since      1.0.0
	 */
	public function get_product_contribution( $data, $foundation_id, $product_id ) {
		if ( isset( $data[ $foundation_id ][ $product_id ] ) ) {
			return floatval( $data[ $foundation_id ][ $product_id ] );
		}
	}

	/**
	 * Returns contributed for foundation.
	 *
	 * @param array $data Contribution data.
	 * @param int   $foundation_id Foundation ID.
	 * @return float Amount contributed to this foundation.
	 * @since      1.0.0
	 */
	public function get_foundation_contribution( $data, $foundation_id ) {
		if ( isset( $data[ $foundation_id ] ) ) {
			return floatval(
				array_reduce(
					$data[ $foundation_id ],
					function( $sum, $item ) {
						$sum += floatval( $item );
						return $sum;
					}
				)
			);
		}
	}

	/**
	 * Returns contributed amount to all foundations.
	 *
	 * @param array $data Contribution data.
	 * @return float Amount contributed to all foundations.
	 * @since      1.0.0
	 */
	public function get_all_foundations_contribution( $data ) {
		$total_collected = 0;
		foreach ( $data as $foundation_id => $values ) {
			$total_collected += $this->get_foundation_contribution( $data, $foundation_id );
		}
		return $total_collected;
	}

	/**
	 * Returns costs for the product.
	 *
	 * Costs are taken from product meta data or from associated product category.
	 *
	 * @param int $product_id Product ID.
	 * @return float Product cost.
	 * @since      1.0.0
	 */
	public static function get_product_cost( $product_id ) {
		$product_cost = floatval( get_post_meta( $product_id, 'foundation_contribution', true ) );

		if ( 0.0 !== $product_cost ) {
			return floatval( $product_cost );
		}

		$terms         = get_the_terms( $product_id, 'product_cat' );
		$category_cost = carbon_get_term_meta( $terms[0]->term_id, 'foundation_category_cost' );

		if ( 0.0 !== $category_cost ) {
			return floatval( $category_cost );
		}

		return 0.0;
	}
}