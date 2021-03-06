<?php
/**
 * Order Minimum Amount for WooCommerce - Messages
 *
 * @version 4.0.4
 * @since   4.0.4
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Messages' ) ) :

	class Alg_WC_OMA_Messages {

		/**
		 * Constructor.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 */
		function __construct() {
			if ( is_admin() ) {
				return;
			}
			$messages_areas = $this->get_enabled_message_areas();
			foreach ( $messages_areas as $area ) {
				$positions = get_option( "alg_wc_oma_{$area}_area_message_positions", $this->get_message_default_positions( $area ) );
				if ( ! empty( $positions ) ) {
					foreach ( $positions as $position ) {
						add_action( $position, array( $this, 'display_dynamic_message' ) );
					}
				}
			}
		}

		/**
		 * get_enabled_message_areas.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 *
		 * @return array
		 */
		function get_enabled_message_areas() {
			$possible_areas = array( 'cart', 'mini_cart', 'checkout', 'product_page' );
			$areas          = array_map( function ( $possible_area ) {
				return 'yes' === get_option( "alg_wc_oma_{$possible_area}_notice_enabled", 'no' ) ? $possible_area : null;
			}, $possible_areas );
			return array_filter( $areas );
		}

		/**
		 * display_dynamic_message.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 */
		function display_dynamic_message() {
			$position      = current_filter();
			$messages_info = $this->get_messages_info();
			$func          = false;
			$area          = $this->get_area_from_position( $position );
			$notice_type   = isset( $messages_info[ $area ]['default_notice_type'] ) ? get_option( "alg_wc_oma_{$area}_notice_type", $messages_info[ $area ]['default_notice_type'] ) : false;
			$params        = $this->get_output_notices_params_from_position( $position );
			if ( false !== $params ) {
				$func        = isset( $params['func'] ) ? $params['func'] : $func;
				$notice_type = isset( $params['notice_type'] ) ? $params['notice_type'] : $notice_type;
			}
			$output = $this->output_notices( $area, $func, $notice_type );
			if ( false === $func ) {
				echo $output;
			}
		}

		/**
		 * get_output_notices_params_from_position.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 *
		 * @param $position
		 *
		 * @return bool|mixed
		 */
		function get_output_notices_params_from_position( $position ) {
			$params = array(
				'woocommerce_before_checkout_form' => array(
					'func' => 'wc_print_notice',
				),
				'woocommerce_before_single_product_summary'=>array(
					'func' => 'wc_print_notice',
				),
				'woocommerce_before_cart'=>array(
					'func' => 'wc_print_notice',
				)
			);
			if ( isset( $params[ $position ] ) ) {
				return $params[ $position ];
			} else {
				return false;
			}
		}

		/**
		 * get_message_default_positions.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 *
		 * @param $area
		 *
		 * @return array|mixed
		 */
		function get_message_default_positions( $area ) {
			$messages_info     = $this->get_messages_info();
			$default_positions = array();
			if ( isset( $messages_info[ $area ] ) ) {
				$default_positions = $messages_info[ $area ]['default_positions'];
			}
			$deprecated_positions = get_option( 'alg_wc_oma_message_positions_' . $area, array() );
			$default_positions    = $default_positions + $deprecated_positions;
			return $default_positions;
		}

		/**
		 * get_notices.
		 *
		 * @version 4.0.4
		 * @since   3.2.0
		 *
		 * @param string $area 'cart' | 'checkout' | 'product_page'
		 * @param bool $limits
		 * @param bool $types
		 *
		 * @return mixed|void
		 */
		function get_notices( $area = 'cart', $limits = false, $types = false ) {
			$result = array();
			// Check amounts
			foreach ( alg_wc_oma()->core->get_enabled_amount_limits( $limits ) as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_amount_types( $types ) as $amount_type ) {
					$amount_data = alg_wc_oma()->core->get_min_max_amount_data( $min_or_max, $amount_type );
					if ( ! empty( $amount_data['amount'] )
					     && (
						     ( 'no' === ( $display_on_empty_cart = get_option( 'alg_wc_oma_display_messages_on_empty_cart', 'no' ) ) && ! alg_wc_oma()->core->is_cart_empty() ) ||
						     ( 'yes' === $display_on_empty_cart )
					     )
					) {
						$total = alg_wc_oma()->core->amounts->get_cart_total( $amount_type );
						$result[ $min_or_max ][ $amount_type ][''] = ( ! alg_wc_oma()->core->check_min_max_amount( $min_or_max, $amount_type, $amount_data['amount'], $total ) ?
							$this->get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $area ) :
							true );
					}
				}
			}
			// Filter
			$result = apply_filters( 'alg_wc_oma_after_get_notices', $result, $area, $limits, $types );
			// "Require all"
			$result = alg_wc_oma()->core->process_require_all_option( $result );
			// Preparing notices
			if ( ! empty( $result ) ) {
				$result = alg_wc_oma()->core->array_flatten( $result );
				$result = array_filter( $result, array( alg_wc_oma()->core, 'array_filter_true' ) );
				$result = array_unique( $result );
				$result = array_values( $result );
			}
			return apply_filters( 'alg_wc_oma_get_notices', $result, $area, $limits, $types );
		}

		/**
		 * output_notices.
		 *
		 * @version 4.0.4
		 * @since   1.0.0
		 *
		 * @param $area 'cart' | 'checkout' | 'product_page'
		 * @param bool $func
		 * @param bool $notice_type
		 *
		 * @return string
		 */
		function output_notices( $area, $func = false, $notice_type = false ) {
			$result = $this->get_notices( $area );
			if ( ! $func ) {
				return implode( '<br>', $result );
			} else {
				foreach ( $result as $content ) {
					call_user_func_array( $func, array( $content, $notice_type ) );
				}
			}
		}

		/**
		 * get_notice_content.
		 *
		 * @version 3.3.0
		 * @since   2.2.0
		 */
		function get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $cart_or_checkout, $scope = '', $product_id = false, $term_id = false ) {
			$id           = $this->get_message_option_id( $cart_or_checkout, $scope, $amount_data['source'] );
			$content      = get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}_message", array() );
			$content      = ( isset( $content[ $id ] ) ? $content[ $id ] : $this->get_default_message( $min_or_max, $scope, $amount_data['source'] ) );
			$content      = do_shortcode( $content );
			$placeholders = $this->get_placeholders( $min_or_max, $amount_type, $amount_data, $total, $product_id, $term_id );
			return str_replace( array_keys( $placeholders ), $placeholders, $content );
		}

		/**
		 * get_message_option_id.
		 *
		 * @version 4.0.0
		 * @since   3.3.0
		 */
		function get_message_option_id( $cart_or_checkout, $scope, $source ) {
			$id = $cart_or_checkout;
			if ( '' != $scope ) {
				$id .= '_' . $scope;
			}
			if ( '' != $source && apply_filters( 'alg_wc_oma_is_source_message', false, $source ) ) {
				$id .= '_' . $source;
			}
			return $id;
		}

		/**
		 * get_default_message.
		 *
		 * @version 4.0.0
		 * @since   3.0.0
		 *
		 * @todo    add more sources: `user`, `user_role`, `membership`?
		 */
		function get_default_message( $min_or_max, $scope = '', $source = '' ) {
			return apply_filters( 'alg_wc_oma_get_default_message',
				sprintf( __( 'You must have an order with a %s of %%amount%% to place your order, your current order total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
					( 'min' === $min_or_max ? __( 'minimum', 'order-minimum-amount-for-woocommerce' ) : __( 'maximum', 'order-minimum-amount-for-woocommerce' ) ) ),
				$min_or_max,
				$scope,
				$source
			);
		}

		/**
		 * get_messages_info.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 *
		 * @return array
		 */
		function get_messages_info(){
			return array(
				'cart' => array(
					'title'                => __( 'Cart', 'order-minimum-amount-for-woocommerce' ),
					'default_notice_type'  => 'notice',
					'positions' => array(
						'woocommerce_before_cart'                    => __( 'Before cart (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_cart_table'              => __( 'Before cart table', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_cart_contents'           => __( 'Before cart contents', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_contents'                  => __( 'Cart contents', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_coupon'                    => __( 'Cart coupon', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_actions'                   => __( 'Cart actions', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_cart_contents'            => __( 'After cart contents', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_cart_table'               => __( 'After cart table', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_cart_totals'             => __( 'Before cart totals', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_totals_before_shipping'    => __( 'Cart totals: Before shipping', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_totals_after_shipping'     => __( 'Cart totals: After shipping', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_totals_before_order_total' => __( 'Cart totals: Before order total', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_cart_totals_after_order_total'  => __( 'Cart totals: After order total', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_proceed_to_checkout'            => __( 'Proceed to checkout', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_cart_totals'              => __( 'After cart totals', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_shipping_calculator'     => __( 'Before shipping calculator', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_shipping_calculator'      => __( 'After shipping calculator', 'order-minimum-amount-for-woocommerce' ),
					),
					'default_positions' => array(
						'woocommerce_before_cart'
					)
				),
				'mini_cart' => array(
					'title'                => __( 'Mini-cart', 'order-minimum-amount-for-woocommerce' ),
					//'default_notice_type'  => 'notice',
					'positions' => array(
						'woocommerce_after_mini_cart'                     => __( 'After mini cart', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_mini_cart'                    => __( 'Before mini cart', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_mini_cart_contents'           => __( 'Before mini cart contents', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_mini_cart_contents'                  => __( 'After mini cart contents', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_widget_shopping_cart_total'          => __( 'In mini cart total', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_widget_shopping_cart_before_buttons' => __( 'Before mini cart buttons', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_widget_shopping_cart_buttons'        => __( 'In mini cart buttons', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_widget_shopping_cart_after_buttons'  => __( 'After mini cart buttons', 'order-minimum-amount-for-woocommerce' ),
					),
					'default_positions' => array(
						'woocommerce_after_mini_cart'
					)
				),
				'checkout' => array(
					'title'                => __( 'Checkout', 'order-minimum-amount-for-woocommerce' ),
					'default_notice_type'  => 'error',
					'positions' => array(
						'woocommerce_before_checkout_form'             => __( 'Before checkout form (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_before_customer_details' => __( 'Before customer details', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_billing'                 => __( 'Billing', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_shipping'                => __( 'Shipping', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_after_customer_details'  => __( 'After customer details', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_before_order_review'     => __( 'Before order review', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_order_review'            => __( 'Order review', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_review_order_before_shipping'     => __( 'Order review: Before shipping', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_review_order_after_shipping'      => __( 'Order review: After shipping', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_review_order_before_submit'       => __( 'Order review: Payment: Before submit button', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_review_order_after_submit'        => __( 'Order review: Payment: After submit button', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_checkout_after_order_review'      => __( 'After order review', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_checkout_form'              => __( 'After checkout form', 'order-minimum-amount-for-woocommerce' ),
					),
					'default_positions' => array(
						'woocommerce_before_checkout_form'
					)
				),
				'product_page' => array(
					'title'                => __( 'Product page', 'order-minimum-amount-for-woocommerce' ),
					'default_notice_type'  => 'notice',
					'positions' => array(
						'woocommerce_before_single_product_summary' => __( 'Before single product summary (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_add_to_cart_form'       => __( 'Before add to cart form', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_add_to_cart_button'     => __( 'Before add to cart button', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_add_to_cart_button'      => __( 'After add to cart button', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_add_to_cart_quantity'   => __( 'Before add to cart quantity', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_add_to_cart_quantity'    => __( 'After add to cart quantity', 'order-minimum-amount-for-woocommerce' ),
					),
					'default_positions' => array(
						'woocommerce_before_single_product_summary'
					)
				),
			);
		}

		/**
		 * get_area_from_position.
		 *
		 * @version 4.0.4
		 * @since   4.0.4
		 *
		 * @param $position
		 *
		 * @return int|string
		 */
		function get_area_from_position( $position ) {
			$messages_info = $this->get_messages_info();
			foreach ( $messages_info as $area_key => $area_value ) {
				foreach ( $area_value['positions'] as $pos_key => $pos_value ) {
					if ( $position == $pos_key ) {
						return $area_key;
					}
				}
			}
		}

		/**
		 * get_placeholders.
		 *
		 * @version 4.0.0
		 * @since   2.2.0
		 *
		 * @todo    `%term_title%`: add aliases `%category_title%` and `%tag_title%`?
		 */
		function get_placeholders( $min_or_max, $amount_type, $amount_data, $total, $product_id = false, $term_id = false ) {
			$diff = ( 'min' === $min_or_max ? ( $amount_data['amount'] - $total ) : ( $total - $amount_data['amount'] ) );
			$placeholders = array(
				'%amount_type%'   => $amount_type,           // for debugging
				'%amount_source%' => $amount_data['source'], // for debugging
				'%product_id%'    => $product_id,            // for debugging
				'%term_id%'       => $term_id,               // for debugging
				'%amount%'        => alg_wc_oma()->core->amounts->format( $amount_data['amount'], $amount_type ),
				'%total%'         => alg_wc_oma()->core->amounts->format( $total,                 $amount_type ),
				'%diff%'          => alg_wc_oma()->core->amounts->format( $diff,                  $amount_type ),
				'%amount_raw%'    => $amount_data['amount'],
				'%total_raw%'     => $total,
				'%diff_raw%'      => $diff,
				'%product_title%' => ( $product_id ? get_the_title( $product_id ) : '' ),
				'%term_title%'    => ( $term_id    ? ( ( $term = get_term( $term_id ) ) && ! is_wp_error( $term ) ? $term->name : '' ) : '' ),
			);
			return apply_filters( 'alg_wc_oma_placeholders', $placeholders, $min_or_max, $amount_type, $amount_data, $total, $diff, $product_id, $term_id );
		}
	}
endif;

return new Alg_WC_OMA_Messages();