<?php
/**
 * Order Minimum/Maximum Amount for WooCommerce - Amount Types Class
 *
 * This class includes everything needed to add a new "amount type".
 *
 * @version 4.0.0
 * @since   3.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Amount_Types' ) ) :

class Alg_WC_OMA_Amount_Types {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 *
	 * @todo    [maybe] `weight`, `volume`, `length`, `width`, `height`, `area`: optional unit conversions
	 * @todo    [maybe] add new types: `length x height`, `width x height`
	 * @todo    [maybe] add `custom` type
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_types.
	 *
	 * @version 4.0.0
	 * @since   3.0.0
	 */
	function get_types() {
		return apply_filters( 'alg_wc_oma_amount_types', array(
			'sum'         => __( 'Sum', 'order-minimum-amount-for-woocommerce' ),
			'qty'         => __( 'Quantity', 'order-minimum-amount-for-woocommerce' ),
			'product'     => __( 'Products', 'order-minimum-amount-for-woocommerce' ),
			'product_cat' => __( 'Product categories', 'order-minimum-amount-for-woocommerce' ),
			'product_tag' => __( 'Product tags', 'order-minimum-amount-for-woocommerce' ),
			'weight'      => __( 'Weight', 'order-minimum-amount-for-woocommerce' ),
			'volume'      => __( 'Volume', 'order-minimum-amount-for-woocommerce' ),
			'length'      => __( 'Length', 'order-minimum-amount-for-woocommerce' ),
			'width'       => __( 'Width', 'order-minimum-amount-for-woocommerce' ),
			'height'      => __( 'Height', 'order-minimum-amount-for-woocommerce' ),
			'area'        => __( 'Area', 'order-minimum-amount-for-woocommerce' ), // length x width
		) );
	}

	/**
	 * get_title.
	 *
	 * @version 4.0.0
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
			case 'product':
				$result = __( 'product(s)', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'product_cat':
				$result = __( 'product category(-ies)', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'product_tag':
				$result = __( 'product tag(s)', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'weight':
				$result = __( 'weight', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'volume':
				$result = __( 'volume', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'length':
				$result = __( 'length', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'width':
				$result = __( 'width', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'height':
				$result = __( 'height', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'area':
				$result = __( 'area', 'order-minimum-amount-for-woocommerce' );
				break;
		}
		return apply_filters( 'alg_wc_oma_amount_title', $result, $type );
	}

	/**
	 * get_unit.
	 *
	 * @version 4.0.0
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
			case 'product':
				$result = __( 'product(s)', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'product_cat':
				$result = __( 'product category(-ies)', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'product_tag':
				$result = __( 'product tag(s)', 'order-minimum-amount-for-woocommerce' );
				break;
			case 'weight':
				$result = get_option( 'woocommerce_weight_unit' );
				break;
			case 'volume':
				$result = get_option( 'woocommerce_dimension_unit' ) . '<sup>3</sup>';
				break;
			case 'length':
			case 'width':
			case 'height':
				$result = get_option( 'woocommerce_dimension_unit' );
				break;
			case 'area':
				$result = get_option( 'woocommerce_dimension_unit' ) . '<sup>2</sup>';
				break;
		}
		return apply_filters( 'alg_wc_oma_amount_unit', $result, $type );
	}

	/**
	 * format.
	 *
	 * @version 4.0.0
	 * @since   3.0.0
	 *
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
				case 'product':
					$result = sprintf( __( '%s product(s)', 'order-minimum-amount-for-woocommerce' ), $value );
					break;
				case 'product_cat':
					$result = sprintf( __( '%s product category(-ies)', 'order-minimum-amount-for-woocommerce' ), $value );
					break;
				case 'product_tag':
					$result = sprintf( __( '%s product tag(s)', 'order-minimum-amount-for-woocommerce' ), $value );
					break;
				case 'weight':
					$result = wc_format_weight( $value );
					break;
				case 'volume':
					$result = $value . ' ' . get_option( 'woocommerce_dimension_unit' ) . '<sup>3</sup>';
					break;
				case 'length':
				case 'width':
				case 'height':
					$result = $value . ' ' . get_option( 'woocommerce_dimension_unit' );
				case 'area':
					$result = $value . ' ' . get_option( 'woocommerce_dimension_unit' ) . '<sup>2</sup>';
					break;
			}
		} else {
			$result = $value;
		}
		return apply_filters( 'alg_wc_oma_amount_format', $result, $value, $type );
	}

	/**
	 * do_exclude_taxes.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function do_exclude_taxes() {
		if ( ! isset( $this->do_exclude_taxes ) ) {
			$this->do_exclude_taxes = ( 'yes' === get_option( 'alg_wc_oma_exclude_taxes', 'no' ) );
		}
		return $this->do_exclude_taxes;
	}

	/**
	 * is_order_sum_subtotal.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @todo    [maybe] `alg_wc_oma_order_sum`: default to `subtotal`
	 */
	function is_order_sum_subtotal() {
		if ( ! isset( $this->is_order_sum_subtotal ) ) {
			$this->is_order_sum_subtotal = ( 'subtotal' === get_option( 'alg_wc_oma_order_sum', 'total' ) );
		}
		return $this->is_order_sum_subtotal;
	}

	/**
	 * get_cart_total.
	 *
	 * @version 4.0.0
	 * @since   3.0.0
	 */
	function get_cart_total( $type, $product_id = false, $do_count_by_term = false, $taxonomy = false ) {
		if ( ! isset( WC()->cart ) ) {
			return 0;
		}
		$cart_terms = array();
		$result     = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item_values ) {
			if ( apply_filters( 'alg_wc_oma_get_cart_total_do_count_product', true, $cart_item_values['data'], $type, $product_id, $do_count_by_term, $taxonomy ) ) {
				$result = $this->get_cart_value( $result, $type, $cart_item_values, $cart_terms );
			}
		}
		if ( 'sum' === $type && ! $product_id && ! $this->is_order_sum_subtotal() ) {
			WC()->cart->calculate_totals();
			if ( 'no' === get_option( 'alg_wc_oma_exclude_shipping', 'no' ) ) {
				$result += ( WC()->cart->get_shipping_total() + ( $this->do_exclude_taxes() ? 0 : WC()->cart->get_shipping_tax() ) );
			}
			if ( 'yes' === get_option( 'alg_wc_oma_exclude_discounts', 'no' ) ) {
				$result += ( WC()->cart->get_discount_total() + ( $this->do_exclude_taxes() ? 0 : WC()->cart->get_discount_tax() ) );
			}
			if ( 'no' === get_option( 'alg_wc_oma_exclude_fees', 'no' ) ) {
				$result += ( WC()->cart->get_fee_total()      + ( $this->do_exclude_taxes() ? 0 : WC()->cart->get_fee_tax() ) );
			}
		}
		return apply_filters( 'alg_wc_oma_amount_cart_total', $result, $type );
	}

	/**
	 * get_cart_value.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function get_cart_value( $result, $type, $cart_item_values, $cart_terms ) {
		switch ( $type ) {
			case 'sum':
				$value = ( $this->is_order_sum_subtotal() ?
					$cart_item_values['line_subtotal'] + ( $this->do_exclude_taxes() ? 0 : $cart_item_values['line_subtotal_tax'] ) :
					$cart_item_values['line_total']    + ( $this->do_exclude_taxes() ? 0 : $cart_item_values['line_tax'] )
				);
				return ( $result + $value );
			case 'qty':
				return ( $result + $cart_item_values['quantity'] );
			case 'product':
				return ( $result + 1 );
			case 'product_cat':
			case 'product_tag':
				$cart_terms = alg_wc_oma()->core->amounts->add_product_terms( $cart_item_values['product_id'], $type, $cart_terms );
				return ( isset( $cart_terms[ $type ] ) ? count( $cart_terms[ $type ] ) : 0 );
			case 'weight':
				$product = $cart_item_values['data'];
				if ( $product->has_weight() ) {
					$result += ( float ) $product->get_weight() * $cart_item_values['quantity'];
				}
				return $result;
			case 'volume':
				$product = $cart_item_values['data'];
				if ( ! $product->get_virtual() ) {
					if ( 0 != ( $l = $product->get_length() ) && 0 != ( $w = $product->get_width() ) && 0 != ( $h = $product->get_height() ) ) {
						$result += ( float ) $l * ( float ) $w * ( float ) $h * $cart_item_values['quantity'];
					}
				}
			case 'length':
				$product = $cart_item_values['data'];
				if ( ! $product->get_virtual() ) {
					if ( 0 != ( $l = $product->get_length() ) ) {
						$result += ( float ) $l * $cart_item_values['quantity'];
					}
				}
			case 'width':
				$product = $cart_item_values['data'];
				if ( ! $product->get_virtual() ) {
					if ( 0 != ( $w = $product->get_width() ) ) {
						$result += ( float ) $w * $cart_item_values['quantity'];
					}
				}
			case 'height':
				$product = $cart_item_values['data'];
				if ( ! $product->get_virtual() ) {
					if ( 0 != ( $h = $product->get_height() ) ) {
						$result += ( float ) $h * $cart_item_values['quantity'];
					}
				}
				return $result;
			case 'area':
				$product = $cart_item_values['data'];
				if ( ! $product->get_virtual() ) {
					if ( 0 != ( $l = $product->get_length() ) && 0 != ( $w = $product->get_width() ) ) {
						$result += ( float ) $l * ( float ) $w * $cart_item_values['quantity'];
					}
				}
				return $result;
		}
	}

	/**
	 * add_product_terms.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @todo    [maybe] add option to count product's *first* term only
	 */
	function add_product_terms( $product_id, $taxonomy, $terms = array() ) {
		$product_terms = get_the_terms( $product_id, $taxonomy );
		if ( $product_terms && ! is_wp_error( $product_terms ) ) {
			$product_terms      = wp_list_pluck( $product_terms, 'term_id' );
			$terms[ $taxonomy ] = array_merge( ( isset( $terms[ $taxonomy ] ) ? $terms[ $taxonomy ] : array() ), $product_terms );
			$terms[ $taxonomy ] = array_unique( $terms[ $taxonomy ] );
			$terms_to_include   = $this->get_terms_to_include_or_exclude( 'include', $taxonomy );
			$terms_to_exclude   = $this->get_terms_to_include_or_exclude( 'exclude', $taxonomy );
			if ( ! empty( $terms_to_include ) ) {
				$terms[ $taxonomy ] = array_intersect( $terms[ $taxonomy ], $terms_to_include );
			}
			if ( ! empty( $terms_to_exclude ) ) {
				$terms[ $taxonomy ] = array_diff(      $terms[ $taxonomy ], $terms_to_exclude );
			}
		}
		return $terms;
	}

	/**
	 * get_terms_to_include_or_exclude.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function get_terms_to_include_or_exclude( $include_or_exclude, $taxonomy ) {
		if ( ! isset( $this->terms_to_include_or_exclude[ $include_or_exclude ][ $taxonomy ] ) ) {
			$this->terms_to_include_or_exclude[ $include_or_exclude ][ $taxonomy ] = get_option( "alg_wc_oma_type_{$taxonomy}_terms_to_{$include_or_exclude}", array() );
		}
		return $this->terms_to_include_or_exclude[ $include_or_exclude ][ $taxonomy ];
	}

}

endif;

return new Alg_WC_OMA_Amount_Types();
