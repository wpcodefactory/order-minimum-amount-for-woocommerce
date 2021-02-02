<?php
/**
 * Order Minimum/Maximum Amount for WooCommerce - Amount Types Class
 *
 * @version 3.3.0
 * @since   3.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Amount_Types' ) ) :

class Alg_WC_OMA_Amount_Types {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 * @todo    [maybe] more types, e.g. length, width, height, area etc.
	 * @todo    [maybe] volume: optional unit conversion
	 * @todo    [maybe] add `custom` type
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_types.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function get_types() {
		return apply_filters( 'alg_wc_oma_amount_types', array(
			'sum'    => __( 'Sum', 'order-minimum-amount-for-woocommerce' ),
			'qty'    => __( 'Quantity', 'order-minimum-amount-for-woocommerce' ),
			'weight' => __( 'Weight', 'order-minimum-amount-for-woocommerce' ),
			'volume' => __( 'Volume', 'order-minimum-amount-for-woocommerce' ),
		) );
	}

	/**
	 * get_title.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function get_title( $type ) {
		switch ( $type ) {
			case 'sum':
				$result = __( 'sum', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'qty':
				$result = __( 'quantity', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'weight':
				$result = __( 'weight', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'volume':
				$result = __( 'volume', 'order-minimum-amount-for-woocommerce' );
				break;
		}
		return apply_filters( 'alg_wc_oma_amount_title', $result, $type );
	}

	/**
	 * get_unit.
	 *
	 * @version 3.1.0
	 * @since   3.0.0
	 */
	function get_unit( $type, $currency = '' ) {
		switch ( $type ) {
			case 'sum':
				$result = ( '' === $currency ? get_woocommerce_currency() : $currency );
				break;
			case 'qty':
				$result = __( 'pcs', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'weight':
				$result = get_option( 'woocommerce_weight_unit' );
				break;
			case 'volume':
				$result = get_option( 'woocommerce_dimension_unit' ) . '<sup>3</sup>';
				break;
		}
		return apply_filters( 'alg_wc_oma_amount_unit', $result, $type );
	}

	/**
	 * format.
	 *
	 * @version 3.3.0
	 * @since   3.0.0
	 * @todo    [maybe] add `$this->format_types` to `alg_wc_oma_amount_format` filter?
	 * @todo    [maybe] `&nbsp;` instead of "simple" space (including in `wc_format_weight()`)?
	 */
	function format( $value, $type ) {
		if ( ! isset( $this->format_types ) ) {
			$this->format_types = array();
			if ( 'yes' === get_option( 'alg_wc_oma_message_format_types_enabled', 'yes' ) ) {
				$this->format_types = get_option( 'alg_wc_oma_message_format_types', array() );
				if ( empty( $this->format_types ) ) {
					$this->format_types = array_keys( $this->get_types() );
				}
			}
		}
		if ( in_array( $type, $this->format_types ) ) {
			switch ( $type ) {
				case 'sum':
					$result = wc_price( $value );
					break;
				case 'qty':
					$result = sprintf( __( '%s pcs', 'order-minimum-amount-for-woocommerce' ), $value );
					break;
				case 'weight':
					$result = wc_format_weight( $value );
					break;
				case 'volume':
					$result = $value . ' ' . get_option( 'woocommerce_dimension_unit' ) . '<sup>3</sup>';
					break;
			}
		} else {
			$result = $value;
		}
		return apply_filters( 'alg_wc_oma_amount_format', $result, $value, $type );
	}

	/**
	 * get_cart_total.
	 *
	 * @version 3.3.0
	 * @since   3.0.0
	 */
	function get_cart_total( $type, $product_id = false, $do_count_by_term = false, $taxonomy = false ) {
		if ( false !== ( $cart_total = apply_filters( 'alg_wc_oma_before_get_amount_cart_total', false, $type, $product_id, $do_count_by_term, $taxonomy ) ) ) {
			$result = $cart_total;
		} else {
			switch ( $type ) {
				case 'sum':
					$do_exclude_taxes = ( 'yes' === get_option( 'alg_wc_oma_exclude_taxes', 'no' ) );
					if ( 'subtotal' === get_option( 'alg_wc_oma_order_sum', 'total' ) ) {
						$result = WC()->cart->get_subtotal() + ( $do_exclude_taxes ? 0 : WC()->cart->get_subtotal_tax() );
					} else {
						$result = WC()->cart->get_total( 'edit' ) - ( $do_exclude_taxes ? WC()->cart->get_total_tax() : 0 );
						if ( 'yes' === get_option( 'alg_wc_oma_exclude_shipping', 'no' ) ) {
							$result -= ( WC()->cart->get_shipping_total() + ( $do_exclude_taxes ? 0 : WC()->cart->get_shipping_tax() ) );
						}
						if ( 'yes' === get_option( 'alg_wc_oma_exclude_discounts', 'no' ) ) {
							$result += ( WC()->cart->get_discount_total() + ( $do_exclude_taxes ? 0 : WC()->cart->get_discount_tax() ) );
						}
						if ( 'yes' === get_option( 'alg_wc_oma_exclude_fees', 'no' ) ) {
							$result -= ( WC()->cart->get_fee_total()      + ( $do_exclude_taxes ? 0 : WC()->cart->get_fee_tax() ) );
						}
					}
					break;
				case 'qty':
					$result = WC()->cart->get_cart_contents_count();
					break;
				case 'weight':
					$result = WC()->cart->get_cart_contents_weight();
					break;
				case 'volume':
					$result = 0;
					foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
						if ( ! $values['data']->get_virtual() ) {
							if ( 0 != ( $l = $values['data']->get_length() ) && 0 != ( $w = $values['data']->get_width() ) && 0 != ( $h = $values['data']->get_height() ) ) {
								$result += ( float ) $l * ( float ) $w * ( float ) $h * $values['quantity'];
							}
						}
					}
					break;
			}
		}
		return apply_filters( 'alg_wc_oma_amount_cart_total', $result, $type );
	}

}

endif;

return new Alg_WC_OMA_Amount_Types();
