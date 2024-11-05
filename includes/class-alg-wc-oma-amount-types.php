<?php
/**
 * Order Minimum/Maximum Amount Limits for WooCommerce - Amount Types Class
 *
 * This class includes everything needed to add a new "amount type".
 *
 * @version 4.4.2
 * @since   3.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Amount_Types' ) ) :

	class Alg_WC_OMA_Amount_Types extends Alg_WC_OMA_Dynamic_Properties_Obj {

		/**
		 * Stored Cart Category Result.
		 *
		 * @var   1.0.0
		 * @since 1.0.0
		 */
		public $cart_category = array();

		/**
		 * Constructor.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
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
				'weight'      => __( 'Weight', 'order-minimum-amount-for-woocommerce' ),
				'volume'      => __( 'Volume', 'order-minimum-amount-for-woocommerce' ),
				'length'      => __( 'Length', 'order-minimum-amount-for-woocommerce' ),
				'width'       => __( 'Width', 'order-minimum-amount-for-woocommerce' ),
				'height'      => __( 'Height', 'order-minimum-amount-for-woocommerce' ),
				'area'        => __( 'Area (i.e. length x width)', 'order-minimum-amount-for-woocommerce' ),
				'product'     => __( 'Products (i.e. number of different products)', 'order-minimum-amount-for-woocommerce' ),
				'product_cat' => __( 'Product categories (i.e. number of different product categories)', 'order-minimum-amount-for-woocommerce' ),
				'product_tag' => __( 'Product tags (i.e. number of different product tags)', 'order-minimum-amount-for-woocommerce' ),
			) );
		}

		/**
		 * get_title.
		 *
		 * @version 4.0.8
		 * @since   3.0.0
		 */
		function get_title( $type ) {
			$result = '';
			switch ( $type ) {
				case 'sum':
					$result = __( 'sum', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'qty':
					$result = __( 'quantity', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'product':
					$result = __( 'products', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'product_cat':
					$result = __( 'product categories', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'product_tag':
					$result = __( 'product tags', 'order-minimum-amount-for-woocommerce' );
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
		 * @version 4.0.8
		 * @since   3.0.0
		 */
		function get_unit( $type, $currency = '' ) {
			$result = '';
			switch ( $type ) {
				case 'sum':
					$result = ( '' === $currency ? get_woocommerce_currency() : $currency );
					break;
				case 'qty':
					$result = __( 'pcs', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'product':
					$result = __( 'number of different products', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'product_cat':
					$result = __( 'number of different product categories', 'order-minimum-amount-for-woocommerce' );
					break;
				case 'product_tag':
					$result = __( 'number of different product tags', 'order-minimum-amount-for-woocommerce' );
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
			return apply_filters( 'alg_wc_oma_amount_unit', $result, $type, $currency );
		}

		/**
		 * format.
		 *
		 * @version 4.0.8
		 * @since   3.0.0
		 *
		 * @todo    `weight`, `volume`, `length`, `width`, `height`, `area`: use `&nbsp;` instead of "simple" space (including in `wc_format_weight()`)?
		 * @todo    `weight`, `volume`, `length`, `width`, `height`, `area`: optional unit conversions, e.g. `cm` to `m`, etc.?
		 */
		function format( $value, $type ) {
			$result = '';
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
		 * get_order_sum_option.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function get_order_sum_option( $option ) {
			if ( ! isset( $this->is_order_sum_options[ $option ] ) ) {
				switch ( $option ) {
					case 'is_subtotal':
						$value = ( 'subtotal' === get_option( 'alg_wc_oma_order_sum', 'total' ) );
						break;
					case 'do_exclude_taxes':
						$value = ( 'yes' === get_option( 'alg_wc_oma_exclude_taxes', 'no' ) );
						break;
					case 'do_exclude_shipping':
						$value = ( 'yes' === get_option( 'alg_wc_oma_exclude_shipping', 'no' ) );
						break;
					case 'do_exclude_discounts':
						$value = ( 'yes' === get_option( 'alg_wc_oma_exclude_discounts', 'no' ) );
						break;
					case 'do_exclude_fees':
						$value = ( 'yes' === get_option( 'alg_wc_oma_exclude_fees', 'no' ) );
						break;
				}
				$this->is_order_sum_options[ $option ] = $value;
			}
			return $this->is_order_sum_options[ $option ];
		}

		/**
		 * get_cart_total_rest_api.
		 *
		 * @version 4.3.2
		 * @since   4.3.2
		 *
		 * @param null $args
		 *
		 * @return int|mixed
		 */
		function get_cart_total_rest_api( $args = null ) {
			
			$args             = wp_parse_args( $args, array(
				'type'             => '',
				'product_id'       => false,
				'do_count_by_term' => false,
				'taxonomy'         => false,
				'limit_type'       => '',
				'order'            => null,
			) );
			$type             = $args['type'];
			$product_id       = $args['product_id'];
			$do_count_by_term = $args['do_count_by_term'];
			$taxonomy         = $args['taxonomy'];
			$limit_type       = $args['limit_type'];
			$order            = $args['order'];
			
			if(!$order){
				return 0;
			}
			
			$cart_terms = array();
			$result     = 0;

			$wc_subscription_cart_total = get_option( 'alg_wc_oma_include_wc_subscription_cart_total', 'no' );
			$subscription_sum = 0;
			
			if($order){
				
				$order_items = $order->get_items();
				
				
				if( isset( $order_items ) ){
					foreach ( $order_items as  $item_key => $item_values ) {
						$item_data = $item_values->get_changes();
						if( $item_data['product_id'] > 0 ) {
							$cart_item = array();
							$product_name = $item_data['name'];
							$product_id_data = $item_data['product_id'];
							$product_obj = wc_get_product( $product_id_data );
							$quantity = $item_data['quantity'];
							$line_total = $item_data['total'];
							$cart_item = $item_data;
							$cart_item['data'] = $product_obj;
							$cart_item['line_subtotal'] = $item_data['subtotal'];
							$cart_item['line_total'] = $item_data['total'];
							if ( apply_filters( 'alg_wc_oma_get_cart_total_do_count_product', true, $cart_item, $type, $product_id, $do_count_by_term, $taxonomy ) ) {
								
								$result = $this->get_cart_value( array(
									'result'     => $result,
									'type'       => $type,
									'cart_item'  => $cart_item,
									'cart_terms' => $cart_terms,
									'limit_type' => $limit_type
								) );
								
								if ( 'yes' === $wc_subscription_cart_total ) {
									$product = $cart_item['data'];
									
									if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) && $cart_item['$cart_item'] <= 0 ) {
										$subscription_price_item = WC_Subscriptions_Product::get_price($product);
										$resultsub = $subscription_price_item * $cart_item['quantity'];
										$subscription_sum = $subscription_sum + $resultsub;
									}
								}
							}
						}
					}
				}
				
			}
			
			

			if ( 'yes' === $wc_subscription_cart_total ) {
				$result = $result + $subscription_sum;
			}
			
			
			if(($limit_type == 'min' || $limit_type == 'max') && !empty($this->cart_category)) {
				$result = count(array_unique($this->cart_category));
			}
			
			

			if ( 'sum' === $type && ! $product_id && ! $this->get_order_sum_option( 'is_subtotal' ) ) {
				$shipping_total = 0;
				if( $order ){
					$shipping = $order->get_items( 'shipping' );
					if( isset( $shipping ) ){
						foreach( $shipping as $shipping_item_id => $shipping_item ){
							$shipping_each = $shipping_item->get_changes();
							if($shipping_each['total'] > 0){
								$shipping_total = $shipping_total + $shipping_each['total'];
							}
						}
					}
					$result += $shipping_total;
				}
			}
			// Rounding.
			if (
				'sum' === $type &&
				'none' !== ( $rounding = get_option( 'alg_wc_oma_type_sum_cart_total_rounding', 'none' ) )
			) {
				$precision = get_option( 'alg_wc_oma_type_sum_cart_total_rounding_precision', wc_get_price_decimals() );
				if ( 'round' === $rounding ) {
					$result = round( $result, $precision );
				} elseif ( 'ceil' === $rounding ) {
					$result = ceil( $result );
				} elseif ( 'floor' === $rounding ) {
					$result = floor( $result );
				}
			}
			
			return apply_filters( 'alg_wc_oma_amount_cart_total', $result, $type, $product_id, $do_count_by_term, $taxonomy );
		}

		/**
		 * get_cart_total.
		 *
		 * @version 4.3.2
		 * @since   3.0.0
		 *
		 * @param null $args
		 *
		 * @return int|mixed
		 */
		function get_cart_total( $args = null ) {

			$args             = wp_parse_args( $args, array(
				'type'             => '',
				'product_id'       => false,
				'do_count_by_term' => false,
				'taxonomy'         => false,
				'limit_type'       => ''
			) );
			$type             = $args['type'];
			$product_id       = $args['product_id'];
			$do_count_by_term = $args['do_count_by_term'];
			$taxonomy         = $args['taxonomy'];
			$limit_type       = $args['limit_type'];

			if ( ! isset( WC()->cart ) ) {
				return 0;
			}


			$cart_terms = array();
			$result     = 0;

			$wc_subscription_cart_total = get_option( 'alg_wc_oma_include_wc_subscription_cart_total', 'no' );
			$subscription_sum = 0;


			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( apply_filters( 'alg_wc_oma_get_cart_total_do_count_product', true, $cart_item, $type, $product_id, $do_count_by_term, $taxonomy ) ) {
					$result = $this->get_cart_value( array(
						'result'     => $result,
						'type'       => $type,
						'cart_item'  => $cart_item,
						'cart_terms' => $cart_terms,
						'limit_type' => $limit_type
					) );

					if ( 'yes' === $wc_subscription_cart_total ) {
						$product = $cart_item['data'];
						
						if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) && $cart_item['$cart_item'] <= 0 ) {
							$subscription_price_item = WC_Subscriptions_Product::get_price($product);
							$resultsub = $subscription_price_item * $cart_item['quantity'];
							$subscription_sum = $subscription_sum + $resultsub;
						}
					}
				}
			}

			if ( 'yes' === $wc_subscription_cart_total ) {
				$result = $result + $subscription_sum;
			}

			if(($limit_type == 'min' || $limit_type == 'max') && !empty($this->cart_category)) {
				$result = count(array_unique($this->cart_category));
			}

			if ( 'sum' === $type && ! $product_id && ! $this->get_order_sum_option( 'is_subtotal' ) ) {
				WC()->cart->calculate_totals();
				if ( ! $this->get_order_sum_option( 'do_exclude_shipping' ) ) {
					$result += ( WC()->cart->get_shipping_total() + ( $this->get_order_sum_option( 'do_exclude_taxes' ) ? 0 : WC()->cart->get_shipping_tax() ) );
				}
				if ( ! $this->get_order_sum_option( 'do_exclude_fees' ) ) {
					$result += ( WC()->cart->get_fee_total() + ( $this->get_order_sum_option( 'do_exclude_taxes' ) ? 0 : WC()->cart->get_fee_tax() ) );
				}
			}
			// Rounding.
			if (
				'sum' === $type &&
				'none' !== ( $rounding = get_option( 'alg_wc_oma_type_sum_cart_total_rounding', 'none' ) )
			) {
				$precision = get_option( 'alg_wc_oma_type_sum_cart_total_rounding_precision', wc_get_price_decimals() );
				if ( 'round' === $rounding ) {
					$result = round( $result, $precision );
				} elseif ( 'ceil' === $rounding ) {
					$result = ceil( $result );
				} elseif ( 'floor' === $rounding ) {
					$result = floor( $result );
				}
			}
			return apply_filters( 'alg_wc_oma_amount_cart_total', $result, $type, $product_id, $do_count_by_term, $taxonomy );
		}

		/**
		 * get_cart_value.
		 *
		 * @version 4.2.0
		 * @since   4.0.0
		 */
		function get_cart_value( $args = null ) {
			$args         = wp_parse_args( $args, array(
				'result'     => '',
				'type'       => '',
				'cart_item'  => '',
				'cart_terms' => '',
				'limit_type' => '',
			) );
			$result       = $args['result'];
			$type         = $args['type'];
			$cart_item    = $args['cart_item'];
			$cart_terms   = $args['cart_terms'];
			$limit_type   = $args['limit_type'];
			$final_result = false;
			switch ( $type ) {
				case 'sum':
					$cart_item['line_subtotal'] = ! isset( $cart_item['line_subtotal'] ) ? 0 : $cart_item['line_subtotal'];
					$value                      = ( $this->get_order_sum_option( 'is_subtotal' ) || $this->get_order_sum_option( 'do_exclude_discounts' ) ?
						$cart_item['line_subtotal'] + ( $this->get_order_sum_option( 'do_exclude_taxes' ) ? 0 : $cart_item['line_subtotal_tax'] ) :
						$cart_item['line_total'] + ( $this->get_order_sum_option( 'do_exclude_taxes' ) ? 0 : $cart_item['line_tax'] )
					);
					$final_result               = ( $result + $value );
					break;
				case 'qty':
					$final_result = ( $result + $cart_item['quantity'] );
					break;
				case 'product':
					$final_result = ( $result + 1 );
					break;
				case 'product_cat':
				case 'product_tag':
					$cart_terms   = alg_wc_oma()->core->amounts->add_product_terms( $cart_item['product_id'], $type, $cart_terms );

					if ( $type == 'product_cat' && isset( $cart_terms['product_cat'] ) && !empty( $cart_terms['product_cat'] ) ) {
						if ( empty( $this->cart_category ) ) {
							$this->cart_category = $cart_terms['product_cat'];
						} else {
							$this->cart_category = array_merge( $this->cart_category, $cart_terms['product_cat'] );
						}
					}

					$final_result = ( isset( $cart_terms[ $type ] ) ? count( $cart_terms[ $type ] ) : 0 );
					break;
				case 'weight':
					$product = $cart_item['data'];
					if ( $product->has_weight() ) {
						$result += ( float ) $product->get_weight() * $cart_item['quantity'];
					}
					$final_result = $result;
					break;
				case 'volume':
					$product = $cart_item['data'];
					if ( ! $product->get_virtual() ) {
						if ( 0 != ( $l = $product->get_length() ) && 0 != ( $w = $product->get_width() ) && 0 != ( $h = $product->get_height() ) ) {
							$result += ( float ) $l * ( float ) $w * ( float ) $h * $cart_item['quantity'];
						}
					}
					$final_result = $result;
					break;
				case 'length':
					$product = $cart_item['data'];
					if ( ! $product->get_virtual() ) {
						if ( 0 != ( $l = $product->get_length() ) ) {
							$result += ( float ) $l * $cart_item['quantity'];
						}
					}
					$final_result = $result;
					break;
				case 'width':
					$product = $cart_item['data'];
					if ( ! $product->get_virtual() ) {
						if ( 0 != ( $w = $product->get_width() ) ) {
							$result += ( float ) $w * $cart_item['quantity'];
						}
					}
					$final_result = $result;
					break;
				case 'height':
					$product = $cart_item['data'];
					if ( ! $product->get_virtual() ) {
						if ( 0 != ( $h = $product->get_height() ) ) {
							$result += ( float ) $h * $cart_item['quantity'];
						}
					}
					$final_result = $result;
					break;
				case 'area':
					$product = $cart_item['data'];
					if ( ! $product->get_virtual() ) {
						if ( 0 != ( $l = $product->get_length() ) && 0 != ( $w = $product->get_width() ) ) {
							$result += ( float ) $l * ( float ) $w * $cart_item['quantity'];
						}
					}
					$final_result = $result;
					break;
			}
			return apply_filters( 'alg_wc_oma_get_cart_value', $final_result, $args );
		}

		/**
		 * add_product_terms.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 *
		 * @todo    add option to count product's *first* term only?
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
					$terms[ $taxonomy ] = array_diff( $terms[ $taxonomy ], $terms_to_exclude );
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
