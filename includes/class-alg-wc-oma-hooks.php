<?php
/**
 * Order Minimum Amount for WooCommerce - Hooks Class
 *
 * @version 4.0.0
 * @since   3.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Hooks' ) ) :

class Alg_WC_OMA_Hooks {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_oma_plugin_enabled', 'yes' ) ) {
			add_action( 'init', array( $this, 'add_hooks' ) );
		}
	}

	/**
	 * add_hooks.
	 *
	 * @version 4.0.0
	 * @since   1.0.0
	 */
	function add_hooks() {
		// Checkout: Process
		if ( 'yes' === get_option( 'alg_wc_oma_block_checkout_process', 'yes' ) ) {
			add_action( 'woocommerce_checkout_process', array( $this, 'checkout_process_notices' ) );
		}
		// Checkout: Block page
		if ( 'yes' === get_option( 'alg_wc_oma_block_checkout', 'no' ) ) {
			add_action( 'wp', array( $this, 'block_checkout' ), PHP_INT_MAX );
		}
		// Checkout: Notices
		if ( 'yes' === get_option( 'alg_wc_oma_checkout_notice_enabled', 'no' ) ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'checkout_notices' ) );
		}
		// Cart: Notices
		if ( 'yes' === get_option( 'alg_wc_oma_cart_notice_enabled', 'no' ) ) {
			add_action( 'woocommerce_before_cart', array( $this, 'cart_notices' ) );
		}
		// Additional positions
		foreach ( array( 'cart', 'checkout' ) as $cart_or_checkout ) {
			$positions = get_option( 'alg_wc_oma_message_positions_' . $cart_or_checkout, array() );
			if ( ! empty( $positions ) ) {
				foreach ( $positions as $position ) {
					add_action( $position, array( $this, $cart_or_checkout . '_text' ) );
				}
			}
		}
		// Remove old notices
		if ( 'yes' === get_option( 'alg_wc_oma_remove_notices_on_added_to_cart', 'no' ) ) {
			add_action( 'wp_footer', array( $this, 'remove_notices_on_added_to_cart' ) );
		}
		// Shipping script
		add_action( 'wp_footer', array( $this, 'add_shipping_script' ) );
		// Maximum limit options
		if ( in_array( 'max', alg_wc_oma()->core->get_enabled_amount_limits() ) ) {
			// Validate on add to cart
			if ( 'yes' === get_option( 'alg_wc_oma_max_validate_on_add_to_cart', 'no' ) ) {
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_max_on_add_to_cart' ), PHP_INT_MAX, 4 );
			}
			// Hide "add to cart" button
			if ( 'yes' === get_option( 'alg_wc_oma_max_hide_add_to_cart_loop', 'no' ) ) {
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_loop' ), PHP_INT_MAX, 3 );
				$this->max_hide_add_to_cart_loop_content = get_option( 'alg_wc_oma_max_hide_add_to_cart_loop_content', '' );
			}
			if ( 'yes' === get_option( 'alg_wc_oma_max_hide_add_to_cart_single', 'no' ) ) {
				add_action( 'woocommerce_single_product_summary', array( $this, 'hide_add_to_cart_single' ), 29, 3 );
			}
		}
	}

	/**
	 * add_shipping_script.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @todo    [later] update checkout (use `updated_checkout` event + AJAX?)
	 * @todo    [maybe] move this to a separate js file (e.g. `alg-wc-oma-by-shipping.js`)?
	 */
	function add_shipping_script() {
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			$do_load = (
				in_array( 'sum', alg_wc_oma()->core->get_enabled_amount_types() ) &&
				'no' === get_option( 'alg_wc_oma_exclude_shipping', 'no' ) &&
				'yes' === get_option( 'alg_wc_oma_cart_notice_enabled', 'no' )
			);
			if ( apply_filters( 'alg_wc_oma_do_add_shipping_script', $do_load ) ) {
				?><script>
					jQuery( document ).ready( function() {
						jQuery( 'body' ).on( 'updated_shipping_method', function() {
							jQuery( 'body' ).trigger( 'wc_update_cart' );
						} );
					} );
				</script><?php
			}
		}
	}

	/**
	 * remove_notices_on_added_to_cart.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @todo    [now] on cart page only?
	 * @todo    [maybe] move to a separate `js` file?
	 */
	function remove_notices_on_added_to_cart() {
		?><script>
			jQuery( document.body ).on( 'added_to_cart', function() {
				jQuery( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
			} );
		</script><?php
	}

	/**
	 * hide_add_to_cart_single.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 *
	 * @todo    [next] optional `show_notices`?
	 * @todo    [next] `remove_action()` on `init` hook?
	 * @todo    [next] variable: disable on per variation basis
	 */
	function hide_add_to_cart_single() {
		global $product;
		if ( ! $this->check_product_max_amount( $product->get_id(), 1, 0, false, true, $product ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			echo get_option( 'alg_wc_oma_max_hide_add_to_cart_single_content', '' );
		}
	}

	/**
	 * hide_add_to_cart_loop.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 */
	function hide_add_to_cart_loop( $link, $product, $args ) {
		if ( '' != $link ) {
			$product_id = $product->get_id();
			if ( ! isset( $this->product_cache['hide_add_to_cart_loop'][ $product_id ] ) ) {
				$this->product_cache['hide_add_to_cart_loop'][ $product_id ] = $link;
				if ( ! $this->check_product_max_amount( $product_id, 1, 0, false, true, $product ) ) {
					$this->product_cache['hide_add_to_cart_loop'][ $product_id ] = $this->max_hide_add_to_cart_loop_content;
				}
			}
			return $this->product_cache['hide_add_to_cart_loop'][ $product_id ];
		}
		return $link;
	}

	/**
	 * validate_max_on_add_to_cart.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 *
	 * @todo    [next] `$passed`?
	 */
	function validate_max_on_add_to_cart( $passed, $product_id, $quantity, $variation_id = 0 ) {
		if ( $passed ) {
			$passed = $this->check_product_max_amount( $product_id, $quantity, $variation_id, true );
		}
		return $passed;
	}

	/**
	 * check_product_max_amount.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 *
	 * @see     https://woocommerce.github.io/code-reference/classes/WC-Cart.html#method_add_to_cart
	 * @see     https://woocommerce.github.io/code-reference/classes/WC-Cart.html#method_set_quantity
	 *
	 * @todo    [next] re-check shipping / payment gateways / etc.?
	 * @todo    [next] `set_quantity`: `$refresh_totals`?
	 * @todo    [next] optionally try to auto-correct qty: `WC_Cart::add_to_cart(): apply_filters( 'woocommerce_add_to_cart_quantity', $quantity, $product_id );`?
	 * @todo    [next] disable products (hide (`woocommerce_product_is_visible`), remove add to cart (`woocommerce_is_purchasable`, `woocommerce_variation_is_purchasable`), etc.)
	 * @todo    [later] move to core?
	 */
	function check_product_max_amount( $product_id, $quantity, $variation_id = 0, $show_notices = false, $is_simplified = false, $product = false ) {
		$cart_item_key = ( $is_simplified ?
			$this->add_to_cart_simplified( $product_id, ( $product ? $product : wc_get_product( $product_id ) ), $quantity ) :
			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) );
		if ( $cart_item_key ) {
			$notices = $this->get_notices( 'cart', array( 'max' ) );
			WC()->cart->set_quantity( $cart_item_key, ( WC()->cart->cart_contents[ $cart_item_key ]['quantity'] - $quantity ) );
			if ( ! empty( $notices ) ) {
				if ( $show_notices ) {
					foreach ( $notices as $notice ) {
						wc_add_notice( $notice, 'error' );
					}
				}
				return false;
			}
		}
		return true;
	}

	/**
	 * add_to_cart_simplified.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 *
	 * @todo    [next] `set_quantity`: `$refresh_totals`?
	 * @todo    [next] `get_cart`: call only once?
	 * @todo    [later] move to core?
	 */
	function add_to_cart_simplified( $product_id, $product, $quantity ) {
		if ( '' === $product->get_price() ) {
			return false;
		}
		WC()->cart->get_cart();
		$cart_id       = WC()->cart->generate_cart_id( $product_id );
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );
		if ( $cart_item_key ) {
			$new_quantity = $quantity + WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
			WC()->cart->set_quantity( $cart_item_key, $new_quantity, false );
		} else {
			$cart_item_key = $cart_id;
			WC()->cart->cart_contents[ $cart_item_key ] = array(
				'key'          => $cart_item_key,
				'product_id'   => $product_id,
				'variation_id' => 0,
				'variation'    => array(),
				'quantity'     => $quantity,
				'data'         => $product,
				'data_hash'    => wc_get_cart_item_data_hash( $product ),
			);
		}
		return $cart_item_key;
	}

	/**
	 * array_flatten.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 *
	 * @see     https://stackoverflow.com/questions/526556/how-to-flatten-a-multi-dimensional-array-to-simple-one-in-php
	 *
	 * @todo    [later] move to core?
	 */
	function array_flatten( $array ) {
		$return = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$return = array_merge( $return, $this->array_flatten( $value ) );
			} else {
				$return[] = $value;
			}
		}
		return $return;
	}

	/**
	 * array_filter_true.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 *
	 * @see     https://www.php.net/manual/en/function.array-filter.php
	 *
	 * @todo    [later] move to core?
	 */
	function array_filter_true( $var ) {
		return ! ( true === $var );
	}

	/**
	 * process_require_all_option.
	 *
	 * @version 4.0.0
	 * @since   3.3.0
	 */
	function process_require_all_option( $result ) {
		if ( ! empty( $result ) && 'no' === get_option( 'alg_wc_oma_require_all_types', 'yes' ) ) {
			foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
				if ( $this->check_limits_for_amount_type_in_result( $result, $amount_type ) ) {
					return array();
				}
			}
		}
		return $result;
	}

	/**
	 * check_limits_for_amount_type_in_result.
	 *
	 * @version 4.0.0
	 * @since   3.3.0
	 */
	function check_limits_for_amount_type_in_result( $result, $amount_type ) {
		foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
			foreach ( apply_filters( 'alg_wc_oma_enabled_scopes', array( '' ) ) as $scope ) {
				if ( ! empty( $result[ $min_or_max ][ $amount_type ][ $scope ] ) && true !== $result[ $min_or_max ][ $amount_type ][ $scope ] ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * get_notices.
	 *
	 * @version 4.0.0
	 * @since   3.2.0
	 *
	 * @todo    [maybe] return `false` (instead of `array()`) in case if there are no notices?
	 */
	function get_notices( $cart_or_checkout = 'cart', $limits = false, $types = false ) {
		$result = array();
		// Check amounts
		foreach ( alg_wc_oma()->core->get_enabled_amount_limits( $limits ) as $min_or_max ) {
			foreach ( alg_wc_oma()->core->get_enabled_amount_types( $types ) as $amount_type ) {
				$amount_data = alg_wc_oma()->core->get_min_max_amount_data( $min_or_max, $amount_type );
				if ( ! empty( $amount_data['amount'] ) && ! alg_wc_oma()->core->is_cart_empty() ) {
					$total = alg_wc_oma()->core->amounts->get_cart_total( $amount_type );
					$result[ $min_or_max ][ $amount_type ][''] = ( ! alg_wc_oma()->core->check_min_max_amount( $min_or_max, $amount_type, $amount_data['amount'], $total ) ?
						alg_wc_oma()->core->get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $cart_or_checkout ) :
						true );
				}
			}
		}
		// Filter
		$result = apply_filters( 'alg_wc_oma_after_get_notices', $result, $cart_or_checkout, $limits, $types );
		// "Require all"
		$result = $this->process_require_all_option( $result );
		// Preparing notices
		if ( ! empty( $result ) ) {
			$result = $this->array_flatten( $result );
			$result = array_filter( $result, array( $this, 'array_filter_true' ) );
			$result = array_unique( $result );
			$result = array_values( $result );
		}
		return apply_filters( 'alg_wc_oma_get_notices', $result, $cart_or_checkout, $limits, $types );
	}

	/**
	 * output_notices.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 *
	 * @todo    [maybe] customizable glue (i.e. instead of `<br>`)?
	 */
	function output_notices( $cart_or_checkout, $func = false, $notice_type = false ) {
		$result = $this->get_notices( $cart_or_checkout );
		if ( ! $func ) {
			return implode( '<br>', $result );
		} else {
			foreach ( $result as $content ) {
				$func( $content, $notice_type );
			}
		}
	}

	/**
	 * checkout_process_notices.
	 *
	 * @version 3.2.0
	 * @since   2.2.0
	 */
	function checkout_process_notices() {
		$this->output_notices( 'checkout', 'wc_add_notice', 'error' );
	}

	/**
	 * cart_notices.
	 *
	 * @version 3.2.0
	 * @since   2.2.0
	 */
	function cart_notices() {
		$this->output_notices( 'cart', 'wc_print_notice', get_option( 'alg_wc_oma_cart_notice_type', 'notice' ) );
	}

	/**
	 * checkout_notices.
	 *
	 * @version 3.2.0
	 * @since   2.2.0
	 */
	function checkout_notices() {
		$this->output_notices( 'checkout', 'wc_print_notice', get_option( 'alg_wc_oma_checkout_notice_type', 'error' ) );
	}

	/**
	 * cart_text.
	 *
	 * @version 3.2.0
	 * @since   2.2.0
	 */
	function cart_text() {
		echo $this->output_notices( 'cart' );
	}

	/**
	 * checkout_text.
	 *
	 * @version 3.2.0
	 * @since   2.2.0
	 */
	function checkout_text() {
		echo $this->output_notices( 'checkout' );
	}

	/**
	 * block_checkout.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 */
	function block_checkout( $wp ) {
		if ( ! is_checkout() || ! apply_filters( 'alg_wc_oma_block_checkout', true ) ) {
			return;
		}
		$result = $this->get_notices( 'cart' );
		if ( ! empty( $result ) ) {
			wp_safe_redirect( version_compare( get_option( 'woocommerce_version', null ), '2.5.0', '<' ) ? WC()->cart->get_cart_url() : wc_get_cart_url() );
			exit;
		}
	}

}

endif;

return new Alg_WC_OMA_Hooks();
