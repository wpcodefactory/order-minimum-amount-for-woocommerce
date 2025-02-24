<?php
/**
 * Order Minimum Amount for WooCommerce - Core Class.
 *
 * @version 4.6.0
 * @since   1.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Core' ) ) :

	class Alg_WC_OMA_Core extends Alg_WC_OMA_Dynamic_Properties_Obj {

		/**
		 * Constructor.
		 *
		 * @version 4.0.4
		 * @since   1.0.0
		 */
		function __construct() {
			$this->messages   = require_once( 'class-alg-wc-oma-messages.php' );
			$this->shortcodes = require_once( 'class-alg-wc-oma-shortcodes.php' );
			$this->amounts    = require_once( 'class-alg-wc-oma-amount-types.php' );
			if ( 'yes' === get_option( 'alg_wc_oma_plugin_enabled', 'yes' ) ) {
				add_action( 'init', array( $this, 'add_hooks' ) );
			}
			do_action( 'alg_wc_oma_core_loaded', $this );
		}

		/**
		 * add_to_log.
		 *
		 * For debugging.
		 *
		 * @version 4.0.0
		 * @since   3.0.0
		 */
		function add_to_log( $message ) {
			if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
				$log->log( 'info', $message, array( 'source' => 'order-minimum-amount-for-woocommerce' ) );
			}
		}

		/**
		 * add_hooks.
		 *
		 * @version 4.6.0
		 * @since   1.0.0
		 */
		function add_hooks() {
			// Amount per user role
			add_filter( 'alg_wc_oma_get_min_max_amount_data', array( $this, 'get_min_max_amount_by_user_role' ), get_option( 'alg_wc_oma_by_user_role_priority', 100 ), 3 );
			// Checkout: Process
			if ( 'yes' === get_option( 'alg_wc_oma_block_checkout_process', 'yes' ) ) {
				add_action( 'woocommerce_checkout_process', array( $this, 'checkout_process_notices' ) );
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_checkout_notices' ), 10, 2 );
				add_action( 'woocommerce_review_order_after_shipping', array( $this, 'checkout_process_notices' ), 10 );
				add_action( 'woocommerce_before_checkout_form', array( $this, 'checkout_process_notices' ) );
				if ( 'yes' === get_option( 'alg_wc_oma_block_store_api', 'no' ) ) {
					add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'checkout_process_notices' ) );
					add_filter( 'woocommerce_rest_pre_insert_shop_order_object', array( $this, 'checkout_process_notices_rest_api' ), 10, 3 );
				}
			}
			// Checkout: Block page
			add_action( 'wp', array( $this, 'block_checkout' ), PHP_INT_MAX );
			// Remove old notices
			if ( 'yes' === get_option( 'alg_wc_oma_remove_notices_on_added_to_cart', 'no' ) ) {
				add_action( 'wp_footer', array( $this, 'remove_notices_on_added_to_cart' ) );
			}
			// Shipping script
			add_action( 'wp_footer', array( $this, 'add_shipping_script' ) );
			// Maximum limit options
			if ( in_array( 'max', $this->get_enabled_amount_limits() ) ) {
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
					add_action( 'woocommerce_available_variation', array( $this, 'add_add_to_cart_single_variation_data' ), 29, 3 );
					add_action( 'wp_footer', array( $this, 'hide_variation_add_to_cart_single' ), PHP_INT_MAX );
				}
			}
			// Login requirement.
			add_filter( 'alg_wc_oma_get_notices', array( $this, 'wipe_notices_if_login_requirement_is_enabled' ), 10 );
			add_filter( 'wp', array( $this, 'display_login_requirement_notice' ), 10, 4 );

			// Disable "Proceed to checkout" button on cart page.
			add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'set_cookie_on_cart_updated' ) );
			add_action( 'wp', array( $this, 'set_cookie_on_cart' ) );
			add_action( 'wp_footer', array( $this, 'add_disable_checkout_script' ), PHP_INT_MAX );
			add_action( 'alg_wc_oma_check_notices_on_block_cart_change', array( $this, 'set_cookie_on_block_cart_updated' ), PHP_INT_MAX );
		}

		/**
		 * set_cookie_on_block_cart_updated.
		 *
		 * @version 4.4.8
		 * @since   4.4.8
		 *
		 * @return void
		 */
		function set_cookie_on_block_cart_updated() {
			$this->set_cookie_if_has_notices();
		}

		/**
		 * check_checkout_notices.
		 *
		 * @version 4.3.6
		 * @since   4.3.6
		 *
		 * @param $data
		 * @param $errors
		 *
		 * @return void
		 */
		function check_checkout_notices( $data, $errors ) {
			if (
				'woocommerce_after_checkout_validation' === get_option( 'alg_wc_oma_block_checkout_hook', 'woocommerce_checkout_process' ) &&
				! empty( $notice = $this->messages->get_notices( array(
					'area'                       => 'checkout',
					'get_only_first_flat_notice' => true,
				) )['flat_notices'] )
			) {
				$errors->add( 'alg_wc_oma_error', $notice[0] );
			}
		}

		/**
		 * Set cookie if there is any notices in cart page.
		 *
		 * @version 4.2.4
		 * @since   4.1.2
		 */
		function set_cookie_on_cart() {
			if (
				is_cart() ||
				( isset( $_REQUEST['wc-ajax'] ) && 'get_refreshed_fragments' === $_REQUEST['wc-ajax'] )
			) {
				$this->set_cookie_if_has_notices();
			}
		}

		/**
		 * @version 4.2.3
		 * @since   4.2.3
		 *
		 * @param $updated
		 *
		 * @return mixed
		 */
		function set_cookie_on_cart_updated( $updated ) {
			$this->set_cookie_if_has_notices();
			return $updated;
		}

		/**
		 * Set cookie if there is any notices.
		 *
		 * @version 4.3.2
		 * @since   4.1.2
		 */
		function set_cookie_if_has_notices() {
			if (
				'disable' === get_option( 'alg_wc_oma_disable_block_checkout_btn', 'do_not_disable' ) &&
				! empty( $this->messages->get_notices( array( 'area' => 'cart' ) )['flat_notices'] )
			) {
				wc_setcookie( 'alg_wc_oma_has_notices', true, time() + 31556926 );
			} else {
				wc_setcookie( 'alg_wc_oma_has_notices', '', 1 );
			}
		}

		/**
		 * Add script to disable checkout button in cart page.
		 *
		 * @version 4.4.8
		 * @since   4.1.2
		 */
		function add_disable_checkout_script() {
			if (
				! is_cart() ||
				'disable' !== get_option( 'alg_wc_oma_disable_block_checkout_btn', 'do_not_disable' )
			) {
				return;
			}
			?>
			<style>
				.disable-checkout-btn {
					pointer-events: none;
					color: #fff !important;
					background-color: rgba(0, 0, 0, 0.5) !important;
				}
			</style>
			<script>
				jQuery(document).ready(function ($) {
					function getCookie(name) {
						let value = `; ${document.cookie}`;
						let parts = value.split(`; ${name}=`);
						if (parts.length === 2) return parts.pop().split(';').shift();
					}

					function disableOrEnableBtn() {
						let checkoutButton = $('.wc-proceed-to-checkout > a, .wp-block-woocommerce-proceed-to-checkout-block a');
						if (typeof checkoutButton === 'undefined' || checkoutButton.length <= 0) {
							return;
						}
						if (getCookie('alg_wc_oma_has_notices')) {
							checkoutButton.addClass('disable-checkout-btn');
						} else {
							checkoutButton.removeClass('disable-checkout-btn');
						}
					}

					$(document.body).on('updated_cart_totals', disableOrEnableBtn);
					$(document.body).on('wc_fragments_refreshed', disableOrEnableBtn);
					document.addEventListener('alg_wc_oma_msg_display_on_cart_block_update', disableOrEnableBtn);
					disableOrEnableBtn();
				});
			</script>
			<?php
		}

		/**
		 * display_login_requirement_notice.
		 *
		 * @version 4.0.9
		 * @since   4.0.9
		 */
		function display_login_requirement_notice() {
			if (
				'yes' === get_option( 'alg_wc_oma_login_requirement_enabled', 'no' ) &&
				! is_user_logged_in() &&
				! empty( $conditions = apply_filters( 'alg_wc_oma_login_requirement_display_conditions', array( 'is_cart', 'is_checkout' ) ) ) &&
				in_array( true, array_map( function ( $condition ) {
					return function_exists( $condition ) && call_user_func( $condition );
				}, $conditions ) )
			) {
				$notice_content = get_option( 'alg_wc_oma_login_requirement_notice_msg', __( 'Please login to access the min/max requirements for your order.', 'order-minimum-amount-for-woocommerce' ) );
				$notice_type    = get_option( 'alg_wc_oma_login_requirement_notice_type', 'error' );
				wc_add_notice( $notice_content, $notice_type );
			}
		}

		/**
		 * wipe_notices_if_login_requirement_is_enabled.
		 *
		 * @version 4.1.1
		 * @since   4.0.9
		 *
		 * @param $info
		 *
		 * @return array
		 */
		function wipe_notices_if_login_requirement_is_enabled( $info ) {
			if (
				'yes' === get_option( 'alg_wc_oma_login_requirement_enabled', 'no' ) &&
				! is_user_logged_in()
			) {
				$info['flat_notices'] = array();
			}
			return $info;
		}

		/**
		 * add_shipping_script.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 *
		 * @todo    update notices on the *checkout* page!
		 */
		function add_shipping_script() {
			if ( function_exists( 'is_cart' ) && is_cart() ) {
				$do_load = (
					in_array( 'sum', $this->get_enabled_amount_types() ) &&
					'no' === get_option( 'alg_wc_oma_exclude_shipping', 'no' ) &&
					'yes' === get_option( 'alg_wc_oma_cart_notice_enabled', 'no' )
				);
				if ( apply_filters( 'alg_wc_oma_do_add_shipping_script', $do_load ) ) {
					?>
					<script>
						jQuery(document).ready(function () {
							jQuery('body').on('updated_shipping_method', function () {
								jQuery('body').trigger('wc_update_cart');
							});
						});
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
		 * @todo    run this on *cart* page only?
		 */
		function remove_notices_on_added_to_cart() {
			?>
			<script>
				jQuery(document.body).on('added_to_cart', function () {
					jQuery('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
				});
			</script><?php
		}

		/**
		 * hide_add_to_cart_single.
		 *
		 * @version 4.2.0
		 * @since   3.4.0
		 *
		 * @todo    variable: disable on per variation basis?
		 */
		function hide_add_to_cart_single() {
			global $product;
			if ( ! $this->check_product_max_amount( array(
				'product_id'    => $product->get_id(),
				'quantity'      => 1,
				'variation_id'  => 0,
				'show_notices'  => false,
				'is_simplified' => true,
				'product'       => $product,
			) ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				echo get_option( 'alg_wc_oma_max_hide_add_to_cart_single_content', '' );
			}
		}

		/**
		 * hide_variation_add_to_cart_single.
		 *
		 * @version 4.2.2
		 * @since   4.2.0
		 */
		function hide_variation_add_to_cart_single() {
			global $product;
			if (
				! is_product() ||
				! is_a( $product, 'WC_Product_Variable' )
			) {
				return;
			}
			?>
			<script>
				jQuery(document).ready(function ($) {
					$(".single_variation_wrap").on("show_variation", function (event, variation) {
						if (variation.hasOwnProperty('alg_wc_oma_hide_add_to_cart_btn_content')) {
							$('.woocommerce-variation-add-to-cart').hide();
							if (!$('.alg-wc-oma-custom-text-on-hidden-add-to-cart').length) {
								$(".woocommerce-product-details__short-description").append("<div class='alg-wc-oma-custom-text-on-hidden-add-to-cart'>" + variation.alg_wc_oma_hide_add_to_cart_btn_content + "</div>");
							}
							$('.alg-wc-oma-custom-text-on-hidden-add-to-cart').show();
						} else {
							$('.alg-wc-oma-custom-text-on-hidden-add-to-cart').hide();
							$('.woocommerce-variation-add-to-cart').show();
						}
					});
				});
			</script>
			<?php
		}

		/**
		 * add_variation_data_regarding_add_to_cart_single.
		 *
		 * @version 4.2.0
		 * @since   4.2.0
		 *
		 * @param $data
		 * @param $variable_product
		 * @param $variation
		 *
		 * @return mixed
		 * @throws Exception
		 */
		function add_add_to_cart_single_variation_data( $data, $variable_product, $variation ) {
			if ( ! $this->check_product_max_amount( array(
				'quantity'      => ! empty( $amount_data = alg_wc_oma()->core->get_min_max_amount_data( 'min', 'qty', $variation->get_id(), 'product' ) ) && isset( $amount_data['amount'] ) ? $amount_data['amount'] : 1,
				'show_notices'  => false,
				'is_simplified' => true,
				'product'       => $variation,
			) ) ) {
				$data['alg_wc_oma_hide_add_to_cart_btn_content'] = get_option( 'alg_wc_oma_max_hide_add_to_cart_single_content', '' );
			}
			return $data;
		}

		/**
		 * hide_add_to_cart_loop.
		 *
		 * @version 4.2.0
		 * @since   3.4.0
		 */
		function hide_add_to_cart_loop( $link, $product, $args ) {
			if ( '' != $link ) {
				$product_id = $product->get_id();
				if ( ! isset( $this->product_cache['hide_add_to_cart_loop'][ $product_id ] ) ) {
					$this->product_cache['hide_add_to_cart_loop'][ $product_id ] = $link;
					if ( ! $this->check_product_max_amount( array(
						'product_id'    => $product_id,
						'quantity'      => 1,
						'variation_id'  => 0,
						'show_notices'  => false,
						'is_simplified' => true,
						'product'       => $product,
					) ) ) {
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
		 * @version 4.2.0
		 * @since   3.4.0
		 */
		function validate_max_on_add_to_cart( $passed, $product_id, $quantity, $variation_id = 0 ) {
			if ( $passed ) {
				$passed = $this->check_product_max_amount( array(
					'product_id'   => $product_id,
					'quantity'     => $quantity,
					'variation_id' => $variation_id,
					'show_notices' => true,
				) );
			}
			return $passed;
		}

		/**
		 * check_product_max_amount.
		 *
		 * @version 4.2.0
		 * @since   3.4.0
		 *
		 * @see     https://woocommerce.github.io/code-reference/classes/WC-Cart.html#method_add_to_cart
		 * @see     https://woocommerce.github.io/code-reference/classes/WC-Cart.html#method_set_quantity
		 *
		 * @todo    `set_quantity()`: `$refresh_totals`?
		 * @todo    add option to auto-correct qty: `WC_Cart::add_to_cart(): apply_filters( 'woocommerce_add_to_cart_quantity', $quantity, $product_id );`?
		 * @todo    add option to disable products (hide (`woocommerce_product_is_visible`), remove "add to cart" (`woocommerce_is_purchasable`, `woocommerce_variation_is_purchasable`), etc.)?
		 *
		 * @param null $args
		 *
		 * @return bool
		 * @throws Exception
		 */
		function check_product_max_amount( $args = null ) {
			$args            = wp_parse_args( $args, array(
				'product_id'      => '',
				'quantity'        => '',
				'variation_id'    => 0,
				'undo_qty_method' => 'after_get_notices', // before_get_notices | after_get_notices
				'show_notices'    => false,
				'is_simplified'   => false,
				'product'         => false,
			) );
			$product_id      = $args['product_id'];
			$quantity        = $args['quantity'];
			$variation_id    = $args['variation_id'];
			$show_notices    = $args['show_notices'];
			$is_simplified   = $args['is_simplified'];
			$product         = $args['product'];
			$undo_qty_method = $args['undo_qty_method'];
			$cart_item_key   = ( $is_simplified ?
				$this->add_to_cart_simplified( $product_id, ( $product ? $product : wc_get_product( $product_id ) ), $quantity ) :
				WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) );
			if ( $cart_item_key ) {
				if ( 'before_get_notices' === $undo_qty_method ) {
					WC()->cart->set_quantity( $cart_item_key, ( WC()->cart->cart_contents[ $cart_item_key ]['quantity'] - $quantity ) );
				}
				$notices = $this->messages->get_notices( array(
					'area'   => 'cart',
					'limits' => array( 'max' ),
				) )['flat_notices'];
				if ( 'after_get_notices' === $undo_qty_method ) {
					WC()->cart->set_quantity( $cart_item_key, ( WC()->cart->cart_contents[ $cart_item_key ]['quantity'] - $quantity ) );
				}
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
		 * @version 4.0.8
		 * @since   3.4.0
		 *
		 * @todo    `set_quantity()`: `$refresh_totals`?
		 * @todo    `WC()->cart->get_cart()`: call only once?
		 */
		function add_to_cart_simplified( $product_id, $product, $quantity ) {
			if (
				'' === $product->get_price() ||
				empty( WC()->cart )
			) {
				return false;
			}
			WC()->cart->get_cart();
			$cart_id       = WC()->cart->generate_cart_id( $product_id );
			$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );
			if ( $cart_item_key ) {
				$new_quantity = $quantity + WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
				WC()->cart->set_quantity( $cart_item_key, $new_quantity, false );
			} else {
				$cart_item_key                              = $cart_id;
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
				foreach ( $this->get_enabled_amount_types() as $amount_type ) {
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
			foreach ( $this->get_enabled_amount_limits() as $min_or_max ) {
				foreach ( apply_filters( 'alg_wc_oma_enabled_scopes', array( '' ) ) as $scope ) {
					if ( ! empty( $result[ $min_or_max ][ $amount_type ][ $scope ] ) && true !== $result[ $min_or_max ][ $amount_type ][ $scope ] ) {
						return false;
					}
				}
			}
			return true;
		}

		/**
		 * checkout_process_notices.
		 *
		 * @version 4.4.5
		 * @since   2.2.0
		 */
		function checkout_process_notices() {
			if ( 'woocommerce_checkout_process' === get_option( 'alg_wc_oma_block_checkout_hook', 'woocommerce_checkout_process' ) ) {
				$this->messages->output_notices( array(
					'area'        => 'checkout',
					'func'        => 'wc_add_notice',
					'notice_type' => 'error'
				) );
			}
		}

		/**
		 * checkout_process_notices_rest_api.
		 *
		 * @version 4.3.2
		 * @since   4.3.2
		 *
		 * @param $order
		 * @param $request
		 * @param $creating
		 *
		 * @return mixed|WP_Error
		 */
		function checkout_process_notices_rest_api( $order, $request, $creating ) {
			
			if ( ! empty( $notice = $this->messages->get_notices( array(
				'area'                       => 'cart',
				'get_only_first_flat_notice' => true,
				'from_rest_api' 			 => true,
				'order' 					 => $order
			) )['flat_notices'] ) ) {
				/*return new WP_Error( 'alg_wc_oma_error', $notice[0], array( 'status' => 400 ) );*/
				throw new WC_REST_Exception( 'alg_wc_oma_error', $notice[0], 400 );
			}

			return $order;
		}

		/**
		 * block_checkout.
		 *
		 * @version 4.3.2
		 * @since   1.0.0
		 */
		function block_checkout( $wp ) {
			if (
				( function_exists( 'is_checkout' ) && is_checkout() ) &&
				! is_wc_endpoint_url( 'order-received' ) &&
				! is_wc_endpoint_url( 'order-pay' ) &&
				'yes' === get_option( 'alg_wc_oma_block_checkout', 'no' ) &&
				apply_filters( 'alg_wc_oma_block_checkout', true ) &&
				! empty( $notices = $this->messages->get_notices( array( 'area' => 'cart' ) )['flat_notices'] )
			) {
				wp_safe_redirect( version_compare( get_option( 'woocommerce_version', null ), '2.5.0', '<' ) ? WC()->cart->get_cart_url() : wc_get_cart_url() );
				exit;
			}
		}

		/**
		 * get_min_max_amount_data.
		 *
		 * @version 4.0.0
		 * @since   1.0.0
		 */
		function get_min_max_amount_data( $min_or_max, $amount_type, $product_id = false, $scope = false ) {
			$amount_data = false;
			$amount_data = apply_filters( 'alg_wc_oma_before_get_min_max_amount_data', $amount_data, $min_or_max, $amount_type, $product_id, $scope );
			if ( false !== $amount_data ) {
				return $amount_data;
			}
			$amount_data = apply_filters( 'alg_wc_oma_get_min_max_amount_data', $amount_data, $min_or_max, $amount_type, $product_id, $scope );
			if ( empty( $amount_data['amount'] ) ) {
				$amount_data = array( 'amount' => get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}", 0 ), 'source' => '' ); // "General" amount
			}
			$amount_data           = apply_filters( 'alg_wc_oma_after_get_min_max_amount_data', $amount_data, $min_or_max, $amount_type, $product_id, $scope );
			$amount_data['amount'] = max( $amount_data['amount'], 0 );
			return $amount_data;
		}

		/**
		 * get_min_max_amount_by_user_role.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 *
		 * @todo    move `$data_version_user_role` etc. to `class-alg-wc-oma-deprecated.php`
		 */
		function get_min_max_amount_by_user_role( $amount_data, $min_or_max, $amount_type ) {
			if ( empty( $amount_data['amount'] ) && 'yes' === get_option( 'alg_wc_oma_by_user_role_enabled', 'no' ) ) {
				$current_user_roles      = $this->get_current_user_roles();
				$enabled_user_roles_keys = array_keys( $this->get_enabled_user_roles() );
				$data_version            = get_option( 'alg_wc_oma_data_version', array() );
				$data_version_user_role  = ( isset( $data_version['user_role'] ) ? $data_version['user_role'] : 0 );
				foreach ( $current_user_roles as $role_key ) {
					if ( empty( $role_key ) ) {
						$role_key = 'guest';
					}
					if ( in_array( $role_key, $enabled_user_roles_keys ) ) {
						if ( 'min' === $min_or_max && 'sum' === $amount_type && version_compare( $data_version_user_role, '2.0.0', '<' ) ) {
							if ( ( $order_minimum_sum = get_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key, 0 ) ) > 0 ) {
								return array( 'amount' => $order_minimum_sum, 'source' => 'user_role' );
							}
						} else {
							if ( ! isset( $this->amount_by_user_role[ $min_or_max ][ $amount_type ] ) ) {
								$this->amount_by_user_role[ $min_or_max ][ $amount_type ] = get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}_by_user_role", array() );
							}
							$amount_by_user_role = ( isset( $this->amount_by_user_role[ $min_or_max ][ $amount_type ][ $role_key ] ) ?
								$this->amount_by_user_role[ $min_or_max ][ $amount_type ][ $role_key ] : 0 );
							if ( 0 != $amount_by_user_role ) {
								return array( 'amount' => $amount_by_user_role, 'source' => 'user_role' );
							}
						}
					}
				}
			}
			return $amount_data;
		}

		/**
		 * get_current_user_roles.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 *
		 * @todo    cache it in `$this->current_user_roles`?
		 */
		function get_current_user_roles() {
			$current_user = wp_get_current_user();
			return ( ! $current_user->exists() ? array( 'guest' ) : $current_user->roles );
		}

		/**
		 * is_cart_empty.
		 *
		 * @version 3.3.0
		 * @since   3.3.0
		 */
		function is_cart_empty() {
			return ( ! function_exists( 'WC' ) || ! isset( WC()->cart ) || WC()->cart->is_empty() );
		}

		/**
		 * is_equal.
		 *
		 * @version 3.1.1
		 * @since   3.0.0
		 *
		 * @todo    better epsilon value, e.g. `defined( 'PHP_FLOAT_EPSILON' ) ? PHP_FLOAT_EPSILON : $this->get_amount_step()`?
		 */
		function is_equal( $float1, $float2 ) {
			$epsilon = $this->get_amount_step();
			return ( abs( $float1 - $float2 ) < $epsilon );
		}

		/**
		 * check_min_max_amount.
		 *
		 * @version 4.0.0
		 * @since   2.0.0
		 */
		function check_min_max_amount( $min_or_max, $amount_type, $amount, $total ) {
			$amount = floatval( $amount );
			$total  = floatval( $total );
			$passed = ( ! $amount || $this->is_equal( $amount, $total ) ? true : ( 'min' === $min_or_max ? $total > $amount : $total < $amount ) );

			return apply_filters( 'alg_wc_oma_check_order_min_max_amount', $passed, $min_or_max, $amount_type, $amount, $total );
		}

		/**
		 * get_all_user_roles.
		 *
		 * @version 4.3.5
		 * @since   1.0.0
		 */
		function get_all_user_roles( $do_rearrange = false ) {
			global $wp_roles;
			$get_user_roles_method = get_option( 'alg_wc_oma_get_user_roles_method', 'editable_roles' );
			$roles                 = ( isset( $wp_roles ) && is_object( $wp_roles ) ? $wp_roles->roles : array() );
			if ( 'editable_roles' === $get_user_roles_method ) {
				$roles = apply_filters( 'editable_roles', $roles );
			}
			$roles = wp_list_pluck( $roles, 'name' );
			$roles = array_merge( array( 'guest' => __( 'Guest', 'order-minimum-amount-for-woocommerce' ) ), $roles );
			if ( $do_rearrange && isset( $roles['customer'] ) ) {
				$customer_title = $roles['customer'];
				unset( $roles['customer'] );
				$roles = array_merge( array( 'customer' => $customer_title ), $roles );
			}

			return $roles;
		}

		/**
		 * get_enabled_user_roles.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function get_enabled_user_roles( $do_rearrange = false ) {
			$enabled_user_roles = get_option( 'alg_wc_oma_enabled_user_roles', array() );
			$all_user_roles     = $this->get_all_user_roles( $do_rearrange );
			return ( empty( $enabled_user_roles ) ? $all_user_roles : array_intersect_key( $all_user_roles, array_flip( $enabled_user_roles ) ) );
		}

		/**
		 * get_title.
		 *
		 * @version 4.0.0
		 * @since   3.0.0
		 */
		function get_title( $min_or_max, $amount_type, $desc = array(), $do_strip_tags = false ) {
			$title = sprintf( ( 'min' === $min_or_max ? __( 'Min %s', 'order-minimum-amount-for-woocommerce' ) : __( 'Max %s', 'order-minimum-amount-for-woocommerce' ) ),
				$this->amounts->get_title( $amount_type ) );
			$desc  = array_filter( $desc );
			if ( ! empty( $desc ) ) {
				$title .= ' [' . implode( '] [', $desc ) . ']';
			}
			return ( $do_strip_tags ? strip_tags( $title ) : $title );
		}

		/**
		 * get_enabled_amount_limits.
		 *
		 * @version 4.0.0
		 * @since   3.0.0
		 */
		function get_enabled_amount_limits( $limits = false ) {
			$result = get_option( 'alg_wc_oma_amount_limits', array( 'min', 'max' ) );
			$result = ( empty( $result ) ? array( 'min', 'max' ) : $result );
			return ( ! $limits ? $result : array_intersect( $result, $limits ) );
		}

		/**
		 * get_enabled_amount_types.
		 *
		 * @version 4.0.0
		 * @since   3.0.0
		 */
		function get_enabled_amount_types( $types = false ) {
			$result = get_option( 'alg_wc_oma_amount_types', array( 'sum', 'qty' ) );
			$result = ( empty( $result ) ? array_keys( $this->amounts->get_types() ) : $result );
			return ( ! $types ? $result : array_intersect( $result, $types ) );
		}

		/**
		 * get_amount_step.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
		 */
		function get_amount_step() {
			return 0.000001;
		}

		/**
		 * get_amount_custom_atts.
		 *
		 * @version 3.1.0
		 * @since   3.0.0
		 */
		function get_amount_custom_atts( $min = - 1 ) {
			return array( 'step' => $this->get_amount_step(), 'min' => $min );
		}

		/**
		 * get_amounts_desc.
		 *
		 * @version 4.0.0
		 * @since   3.1.0
		 */
		function get_amounts_desc() {
			return sprintf( __( 'Amount is <strong>ignored</strong> if set to zero (%s), i.e. next level (e.g. "General") amount will be applied.', 'order-minimum-amount-for-woocommerce' ), '<code>0</code>' ) . ' ' .
			       sprintf( __( '<strong>No amount</strong> (i.e. no limit) will be applied if set to a negative value (e.g. %s).', 'order-minimum-amount-for-woocommerce' ), '<code>-1</code>' );
		}

		/**
		 * get_shipping_methods.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function get_shipping_methods() {
			$shipping_methods = array();
			foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
				$shipping_methods[ $method->id ] = $method->get_method_title();
			}
			return $shipping_methods;
		}

		/**
		 * get_shipping_zones.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function get_shipping_zones( $include_empty_zone = true ) {
			$zones = WC_Shipping_Zones::get_zones();
			if ( $include_empty_zone ) {
				$zone                                                = new WC_Shipping_Zone( 0 );
				$zones[ $zone->get_id() ]                            = $zone->get_data();
				$zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
				$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
				$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
			}
			return $zones;
		}

		/**
		 * get_shipping_methods_instances.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function get_shipping_methods_instances( $full_data = false ) {
			$shipping_methods = array();
			foreach ( $this->get_shipping_zones() as $zone_id => $zone_data ) {
				foreach ( $zone_data['shipping_methods'] as $shipping_method ) {
					if ( $full_data ) {
						$shipping_methods[ $shipping_method->instance_id ] = array(
							'zone_id'                     => $zone_id,
							'zone_name'                   => $zone_data['zone_name'],
							'formatted_zone_location'     => $zone_data['formatted_zone_location'],
							'shipping_method_title'       => $shipping_method->title,
							'shipping_method_id'          => $shipping_method->id,
							'shipping_method_instance_id' => $shipping_method->instance_id,
						);
					} else {
						$shipping_methods[ $shipping_method->instance_id ] = $zone_data['zone_name'] . ': ' . $shipping_method->title;
					}
				}
			}
			return $shipping_methods;
		}

		/**
		 * get_shipping_options.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function get_shipping_options( $shipping_type ) {
			switch ( $shipping_type ) {
				case 'method':
					return $this->get_shipping_methods();
				case 'instance':
					return $this->get_shipping_methods_instances();
				case 'zone':
					$options = array();
					$zones   = $this->get_shipping_zones();
					foreach ( $zones as $id => $data ) {
						$options[ 'z' . $id ] = $data['zone_name'];
					}
					return $options;
			}
		}

		/**
		 * get_memberships.
		 *
		 * @version 4.1.7
		 * @since   3.4.0
		 *
		 * @see     https://docs.woocommerce.com/document/woocommerce-memberships-function-reference/
		 * @see     https://docs.memberpress.com/
		 */
		function get_memberships() {
			$memberships = array();
			// WooCommerce Memberships
			if ( function_exists( 'wc_memberships_get_membership_plans' ) ) {
				foreach ( wc_memberships_get_membership_plans() as $membership ) {
					$memberships[ 'alg_wc_wcm_' . $membership->slug ] = array(
						'title' => $membership->name . ' (' . __( 'WooCommerce Memberships', 'order-minimum-amount-for-woocommerce' ) . ')',
						'type'  => 'wc_memberships',
					);
				}
			}
			// MemberPress
			if ( class_exists( 'MeprUser' ) ) {
				$args = array(
					'post_type'      => 'memberpressproduct',
					'post_status'    => 'any',
					'posts_per_page' => - 1,
					'orderby'        => 'ID',
					'order'          => 'DESC',
				);
				$loop = new WP_Query( $args );
				if ( $loop->have_posts() ) {
					foreach ( $loop->posts as $membership ) {
						$memberships[ 'alg_wc_mepr_' . $membership->ID ] = array(
							'title' => $membership->post_title . ' (' . __( 'MemberPress', 'order-minimum-amount-for-woocommerce' ) . ')',
							'type'  => 'memberpress',
						);
					}
				}
			}
			// SUMO Memberships
			if ( class_exists( 'SUMOMemberships' ) ) {
				foreach ( sumo_get_membership_levels() as $membership_id => $membership_name ) {
					$memberships[ 'alg_wc_sumo_' . $membership_id ] = array(
						'title' => $membership_name . ' (' . __( 'SUMO Memberships', 'order-minimum-amount-for-woocommerce' ) . ')',
						'type'  => 'sumo_memberships',
					);
				}
			}
			return $memberships;
		}

	}

endif;

return new Alg_WC_OMA_Core();
