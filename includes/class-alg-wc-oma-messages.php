<?php
/**
 * Order Minimum Amount for WooCommerce - Messages.
 *
 * @version 4.6.1
 * @since   4.0.4
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Messages' ) ) :

	class Alg_WC_OMA_Messages {

		/**
		 * $enabled_message_areas.
		 *
		 * @since 4.4.6
		 */
		protected $enabled_message_areas = null;

		/**
		 * Constructor.
		 *
		 * @version 4.4.6
		 * @since   4.0.4
		 */
		function __construct() {
			if ( 'yes' === get_option( 'alg_wc_oma_plugin_enabled', 'yes' ) && ! is_admin() ) {
				$messages_areas = $this->get_enabled_message_areas();
				foreach ( $messages_areas as $area ) {
					$positions = get_option( "alg_wc_oma_{$area}_area_message_positions", $this->get_message_default_positions( $area ) );
					if ( ! empty( $positions ) ) {
						foreach ( $positions as $position ) {
							add_action( $position, array( $this, 'display_dynamic_message' ) );
						}
					}
				}

				// Force checkout notice refresh.
				add_action( 'woocommerce_review_order_after_order_total', array( $this, 'force_checkout_notice_refresh' ), 10, 2 );
				add_action( 'woocommerce_review_order_before_submit', array( $this, 'force_checkout_notice_refresh' ) );
				add_filter( 'woocommerce_checkout_fields', array( $this, 'update_totals_on_checkout_field_change' ), PHP_INT_MAX );
			}

			// Blocks.
			add_action( 'wp_ajax_alg_wc_oma_get_block_cart_notices', array( $this, 'get_ajax_notices_on_block_cart_change' ) );
			add_action( 'wp_ajax_nopriv_alg_wc_oma_get_block_cart_notices', array( $this, 'get_ajax_notices_on_block_cart_change' ) );
			add_action( 'wp_footer', array( $this, 'check_limits_on_cart_block_change' ), PHP_INT_MAX );
			add_action( 'wp_footer', array( $this, 'cart_block_change_detector' ), PHP_INT_MAX );
		}

		/**
		 * get_ajax_notices_on_block_cart_change.
		 *
		 * @version 4.5.0
		 * @since   4.4.6
		 *
		 * @return void
		 */
		public function get_ajax_notices_on_block_cart_change() {
			$args           = wp_parse_args(
					$_POST,
					array(
							'wc_page_origin' => 'cart',
							'payment_method' => '',
							'cart'           => '',
					)
			);
			$messages_areas = $this->get_enabled_message_areas();
			$wc_page_origin = $args['wc_page_origin'];
			$cart           = $args['cart'];
			$payment_method = sanitize_text_field( $args['payment_method'] );
			if (
					in_array( $wc_page_origin, $messages_areas, true ) &&
					in_array( "woocommerce_blocks_{$wc_page_origin}_enqueue_data", get_option( "alg_wc_oma_{$wc_page_origin}_area_message_positions", $this->get_message_default_positions( $wc_page_origin ) ), true )
			) {

				// Force payment method setting.
				if ( ! empty( $payment_method ) ) {
					WC()->session->set( 'chosen_payment_method', sanitize_text_field( $payment_method ) );
				}

				// Check for limits.
				do_action( 'alg_wc_oma_check_notices_on_block_cart_change' );
				$this->display_dynamic_message(
						array(
								'area' => $wc_page_origin,
								'func' => 'wc_add_notice',
						)
				);
				wc_print_notices();
			}
			die();
		}

		/**
		 * check_limits_on_cart_change.
		 *
		 * @version 4.5.9
		 * @since   4.4.6
		 *
		 * @return void
		 */
		function check_limits_on_cart_block_change() {
			if ( ! is_cart() && ! is_checkout() ) {
				return;
			}
			$php_to_js = array(
				'action'           => 'alg_wc_oma_get_block_cart_notices',
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'wrapper_selector' => '.wp-block-woocommerce-cart,.wp-block-woocommerce-checkout', // Place where the notices will be appended to.
				'removal_selector' => array(
					'notices'        => '.wc-block-components-notice-banner,.woocommerce-info,.woocommerce-error,.woocommerce-message', // Notices that will be removed.
					'notice_wrapper' => '.alg-wc-oma-msg', // Only notices wrapped in this wrapper will be removed.
				),
			);
			?>
			<script>
				jQuery( document ).ready( function ( $ ) {
					let data = <?php echo json_encode( $php_to_js );?>;
					document.addEventListener( 'alg_wc_oma_cart_block_update', function ( e ) {
						$.post( data.ajax_url, {
							action: data.action,
							wc_page_origin: e.detail.wcPageOrigin,
							payment_method: e.detail.paymentMethod,
							cart: e.detail.cart,
						}, function ( response ) {
							// Remove other notices.
							$( data.removal_selector.notice_wrapper ).closest( data.removal_selector.notices ).remove();
							// Display notices on cart block.
							$( data.wrapper_selector ).eq( 0 ).prepend( response );
							let event = new CustomEvent( 'alg_wc_oma_msg_display_on_cart_block_update', {
								detail: e.detail
							} );
							document.dispatchEvent( event );
							if ( wp && wp.data ) {
								//wp.data.dispatch('wc/store/cart').invalidateResolutionForStore();
							}
						} );
					} );
				} );
			</script>
			<?php
		}

		/**
		 * cart_block_change_detector.
		 *
		 * @version 4.6.0
		 * @since   4.4.6
		 *
		 * @return void
		 */
		function cart_block_change_detector() {
			if ( ! is_cart() && ! is_checkout() ) {
				return;
			}
			$php_to_js = array(
				'wc_page_origin' => is_cart() ? 'cart' : 'checkout'
			);
			?>
			<script>
				( function () {
					if ( !window || !window.wp || !window.wp.data || !window.wc || !window.wc.wcBlocksData ) {
						return;
					}
					const { select, subscribe } = window.wp.data;
					const cartStoreKey = window.wc.wcBlocksData.CART_STORE_KEY;
					const paymentStoreKey = window.wc.wcBlocksData.PAYMENT_STORE_KEY;

					let previousCart = null;
					let previousPaymentMethod = null;
					const unsub = subscribe( onCartChange, cartStoreKey );
					const unsub2 = subscribe( onCartChange, paymentStoreKey );
					let data = <?php echo wp_json_encode( $php_to_js ); ?>;

					let updateEventTimeout;

					function onCartChange() {
						// Get the current cart data.
						const cart = select( cartStoreKey ).getCartData();
						const activePaymentMethod = select( paymentStoreKey ).getActivePaymentMethod();
						// Check if the cart has changed.
						if (
							JSON.stringify( cart ) !== JSON.stringify( previousCart ) ||
							activePaymentMethod != previousPaymentMethod
						) {
							clearTimeout( updateEventTimeout );
							updateEventTimeout = setTimeout( () => {
								let event = new CustomEvent( 'alg_wc_oma_cart_block_update', {
									detail: {
										cart: cart,
										paymentMethod: activePaymentMethod,
										wcPageOrigin: data.wc_page_origin
									}
								} );
								document.dispatchEvent( event );
							}, 600 ); // Wait before triggering the event

							// Update the previous cart state.
							previousCart = cart;
							previousPaymentMethod = activePaymentMethod;
						}
					}
					onCartChange();
				} )();
			</script>
			<?php
		}

		/**
		 * update_totals_on_checkout_field_change.
		 *
		 * @version 4.1.2
		 * @since   4.1.2
		 *
		 * @param $fields_sections
		 *
		 * @return mixed
		 */
		function update_totals_on_checkout_field_change( $fields_sections ) {
			if ( 'yes' === get_option( 'alg_wc_oma_checkout_force_refresh', 'no' ) ) {
				$words = array( 'country', 'address', 'city', 'postcode' );
				foreach ( $fields_sections as $section_key => $section ) {
					foreach ( $section as $field_key => $field ) {
						if ( preg_match( '(' . implode( '|', $words ) . ')', $field_key ) ) {
							if ( ! in_array( 'update_totals_on_change', $fields_sections[ $section_key ][ $field_key ]['class'] ) ) {
								$fields_sections[ $section_key ][ $field_key ]['class'][] = 'update_totals_on_change';
							}
						}
					}
				}
			}

			return $fields_sections;
		}

		/**
		 * Force checkout notice refresh.
		 *
		 * @version 4.1.2
		 * @since   4.0.5
		 *
		 */
		function force_checkout_notice_refresh() {
			if (
				'yes' === get_option( 'alg_wc_oma_checkout_force_refresh', 'no' ) &&
				isset( $_REQUEST['wc-ajax'] ) &&
				'update_order_review' == $_REQUEST['wc-ajax'] &&
				get_option( 'alg_wc_oma_checkout_force_refresh_hook', 'woocommerce_review_order_after_order_total' ) === current_filter()
			) {
				$this->display_dynamic_message( array(
					'area' => 'checkout',
					'func' => 'wc_add_notice',
				) );
			}
		}

		/**
		 * get_enabled_message_areas.
		 *
		 * @version 4.4.6
		 * @since   4.0.4
		 *
		 * @return array
		 */
		function get_enabled_message_areas() {
			if ( is_null( $this->enabled_message_areas ) ) {
				$possible_areas              = array( 'cart', 'mini_cart', 'checkout', 'product_page' );
				$areas                       = array_map(
					function ( $possible_area ) {
						return 'yes' === get_option( "alg_wc_oma_{$possible_area}_notice_enabled", 'no' ) ? $possible_area : null;
					},
					$possible_areas
				);
				$this->enabled_message_areas = array_filter( $areas );
			}

			return $this->enabled_message_areas;
		}

		/**
		 * display_dynamic_message.
		 *
		 * @version 4.4.5
		 * @since   4.0.4
		 *
		 * @param   null  $args
		 */
		function display_dynamic_message( $args = null ) {
			$args                  = wp_parse_args( $args, array(
				'position' => current_filter(),
				'func'     => false,
				'area'     => $this->get_area_from_position( current_filter() )
			) );
			$messages_info         = $this->get_messages_info();
			$position              = $args['position'];
			$func                  = $args['func'];
			$area                  = $args['area'];
			$notice_type           = isset( $messages_info[ $area ]['default_notice_type'] ) ? get_option( "alg_wc_oma_{$area}_notice_type", $messages_info[ $area ]['default_notice_type'] ) : false;
			$output_notices_params = $this->get_output_notices_params_from_position( $position );
			if ( false !== $output_notices_params ) {
				$func        = ! $func ? ( isset( $output_notices_params['func'] ) ? $output_notices_params['func'] : $func ) : $func;
				$notice_type = isset( $output_notices_params['notice_type'] ) ? $output_notices_params['notice_type'] : $notice_type;
			}
			$output = $this->output_notices(
				array(
					'area'        => $area,
					'func'        => $func,
					'notice_type' => $notice_type,
					'position'    => $position,
				)
			);
			if ( false === $func ) {
				echo $output;
			}
		}

		/**
		 * get_output_notices_params_from_position.
		 *
		 * @version 4.6.0
		 * @since   4.0.4
		 *
		 * @param $position
		 *
		 * @return bool|mixed
		 */
		function get_output_notices_params_from_position( $position ) {
			$params = array(
				'woocommerce_before_checkout_form'          => array(
					'func' => 'wc_print_notice',
				),
				'woocommerce_blocks_cart_enqueue_data'      => array(
					'func' => 'wc_add_notice',
				),
				'woocommerce_blocks_checkout_enqueue_data'  => array(
					'func' => 'wc_add_notice',
				),
				'woocommerce_before_single_product'         => array(
					'func' => 'wc_add_notice',
				),
				'woocommerce_before_single_product_summary' => array(
					'func' => 'wc_print_notice',
				),
				'woocommerce_before_cart'                   => array(
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
		 * @version 4.5.6
		 * @since   3.2.0
		 *
		 * @param   null  $args
		 *
		 * @return mixed
		 */
		function get_notices( $args = null ) {
			$args                       = wp_parse_args( $args, array(
				'area'                       => 'cart',
				'get_only_first_flat_notice' => false,
				'limits'                     => false,
				'types'                      => false,
				'from_rest_api'              => false,
				'order'                      => null,
			) );
			$area                       = $args['area'];
			$limits                     = $args['limits'];
			$types                      = $args['types'];
			$from_rest_api              = $args['from_rest_api'];
			$order                      = $args['order'];
			$get_only_first_flat_notice = $args['get_only_first_flat_notice'];
			$result                     = array();
			$category_total             = array();
			$alg_wc_oma_msg_force_display = 'yes' === get_option( 'alg_wc_oma_msg_force_display', 'no' );
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
						if ( ! $from_rest_api ) {
							$total = alg_wc_oma()->core->amounts->get_cart_total( array(
								'type'       => $amount_type,
								'limit_type' => $min_or_max
							) );
						} else {
							$total = alg_wc_oma()->core->amounts->get_cart_total_rest_api( array(
								'type'       => $amount_type,
								'limit_type' => $min_or_max,
								'order'      => $order
							) );
						}

						if ( ! $alg_wc_oma_msg_force_display ) {
							$result[ $min_or_max ][ $amount_type ][''] = ( ! alg_wc_oma()->core->check_min_max_amount( $min_or_max, $amount_type, $amount_data['amount'], $total ) ?
								$this->get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $area ) :
								true );
						} else {
							$result[ $min_or_max ][ $amount_type ][''] = $this->get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $area );
						}
					}
				}
			}

			// Filter
			if ( ! ( $from_rest_api && $order !== null ) ) {
				$result = apply_filters( 'alg_wc_oma_after_get_notices', $result, $area, $limits, $types );
			}

			// "Require all"
			$result = $raw_result = alg_wc_oma()->core->process_require_all_option( $result );
			// Preparing notices
			if ( ! empty( $result ) ) {
				$result = alg_wc_oma()->core->array_flatten( $result );
				$result = array_filter( $result, array( alg_wc_oma()->core, 'array_filter_true' ) );
				$result = array_unique( $result );
				$result = array_values( $result );
			}
			if ( $get_only_first_flat_notice && is_array( $result ) && count( $result ) > 0 ) {
				$result = array( $result[0] );
			}

			return apply_filters( 'alg_wc_oma_get_notices', array(
					'flat_notices' => $result,
					'raw_notices'  => $raw_result,
					'area'         => $area,
					'limits'       => $limits,
					'types'        => $types
				)
			);
		}

		/**
		 * output_notices.
		 *
		 * @version 4.5.9
		 * @since   1.0.0
		 *
		 * @param $args
		 *
		 * @return string|void
		 */
		function output_notices( $args = null ) {
			$args        = wp_parse_args( $args, array(
				'area'        => '',
				'func'        => false,
				'notice_type' => false,
				'position'    => ''
			) );
			$area        = $args['area'];
			$func        = $args['func'];
			$notice_type = $args['notice_type'];
			$position    = $args['position'];
			$result      = $this->get_notices( array(
				'area'                       => $area,
				'get_only_first_flat_notice' => 'no' === get_option( 'alg_wc_oma_display_multiple_msg', 'yes' ),
			) )['flat_notices'];

			$templates = apply_filters(
				'alg_wc_oma_msg_templates',
				array(
					'default'  => '<div class="alg-wc-oma-msg">{content}</div>',
					'in_table' => '<tr><th></th><td><div class="alg-wc-oma-msg">{content}</div></td></tr>',
				)
			);

			if ( ! $func ) {
				$wrapped_result = array_map( function ( $item ) use ( $templates, $position ) {
					$array_from_to = array(
						'{content}' => $item,
					);
					$template      = $templates['default'];
					if (
						in_array( $position, array(
							'woocommerce_cart_totals_before_shipping',
							'woocommerce_cart_totals_after_shipping',
							'woocommerce_cart_totals_before_order_total',
							'woocommerce_cart_totals_after_order_total',
							'woocommerce_review_order_before_shipping',
							'woocommerce_review_order_after_shipping'
						) )
					) {
						$template = $templates['in_table'];
					}

					return str_replace( array_keys( $array_from_to ), $array_from_to, $template );
				}, $result );

				return implode( '', $wrapped_result );
			} else {
				foreach ( $result as $content ) {
					$array_from_to = array(
						'{content}' => $content,
					);
					$final_content = str_replace( array_keys( $array_from_to ), $array_from_to, $templates['default'] );
					if ( 'wc_add_notice' === $func ) {
						if ( ! wc_has_notice( $final_content, $notice_type ) ) {
							call_user_func_array( $func, array( $final_content, $notice_type ) );
						}
					} else {
						call_user_func_array( $func, array( $final_content, $notice_type ) );
					}
				}
			}
		}

		/**
		 * get_notice_content.
		 *
		 * @version 4.0.8
		 * @since   2.2.0
		 */
		function get_notice_content( $min_or_max, $amount_type, $amount_data, $total, $cart_or_checkout, $scope = '', $product_id = false, $term_id = false ) {
			$id           = $this->get_message_option_id( $cart_or_checkout, $scope, $amount_data['source'] );
			$content      = get_option( "alg_wc_oma_{$min_or_max}_{$amount_type}_message", array() );
			$content      = ( isset( $content[ $id ] ) ? $content[ $id ] : $this->get_default_message( $min_or_max, $scope, $amount_data['source'], $amount_type ) );
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
		 * @version 4.0.8
		 * @since   3.0.0
		 *
		 * @todo    add more sources: `user`, `user_role`, `membership`?
		 */
		function get_default_message( $min_or_max, $scope = '', $source = '', $amount_type = '' ) {
			return apply_filters( 'alg_wc_oma_get_default_message',
				sprintf( __( 'You must have an order with a %s of %%amount%% to place your order, your current order total is %%total%%.', 'order-minimum-amount-for-woocommerce' ),
					( 'min' === $min_or_max ? __( 'minimum', 'order-minimum-amount-for-woocommerce' ) : __( 'maximum', 'order-minimum-amount-for-woocommerce' ) ) ),
				$min_or_max,
				$scope,
				$source,
				$amount_type
			);
		}

		/**
		 * get_messages_info.
		 *
		 * Adding the @ to suppress the warning Translation loading was triggered too early.
		 *
		 * @version 4.6.1
		 * @since   4.0.4
		 *
		 * @return array
		 */
		function get_messages_info() {
			return @array(
				'cart'         => array(
					'title'               => __( 'Cart', 'order-minimum-amount-for-woocommerce' ),
					'default_notice_type' => 'notice',
					'positions'           => array(
						'woocommerce_before_cart'                    => __( 'Before cart (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_blocks_cart_enqueue_data'       => __( 'Blocks cart enqueue data (Notice)', 'order-minimum-amount-for-woocommerce' ),
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
					'default_positions'   => array(
						'woocommerce_before_cart',
						'woocommerce_blocks_cart_enqueue_data'
					)
				),
				'mini_cart'    => array(
					'title'             => __( 'Mini-cart', 'order-minimum-amount-for-woocommerce' ),
					//'default_notice_type'  => 'notice',
					'positions'         => array(
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
				'checkout'     => array(
					'title'               => __( 'Checkout', 'order-minimum-amount-for-woocommerce' ),
					'default_notice_type' => 'error',
					'positions'           => array(
						'woocommerce_before_checkout_form'             => __( 'Before checkout form (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_blocks_checkout_enqueue_data'     => __( 'Blocks checkout enqueue data (Notice)', 'order-minimum-amount-for-woocommerce' ),
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
					'default_positions'   => array(
						'woocommerce_before_checkout_form',
						'woocommerce_blocks_checkout_enqueue_data'
					)
				),
				'product_page' => array(
					'title'               => __( 'Product page', 'order-minimum-amount-for-woocommerce' ),
					'default_notice_type' => 'notice',
					'positions'           => array(
						'woocommerce_before_single_product'         => __( 'Before single product (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_single_product_summary' => __( 'Before single product summary (Notice)', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_add_to_cart_form'       => __( 'Before add to cart form', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_add_to_cart_button'     => __( 'Before add to cart button', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_add_to_cart_button'      => __( 'After add to cart button', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_before_add_to_cart_quantity'   => __( 'Before add to cart quantity', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_add_to_cart_quantity'    => __( 'After add to cart quantity', 'order-minimum-amount-for-woocommerce' ),
					),
					'default_positions'   => array(
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
		 * @version 4.4.1
		 * @since   2.2.0
		 *
		 * @todo    `%term_title%`: add aliases `%category_title%` and `%tag_title%`?
		 */
		function get_placeholders( $min_or_max, $amount_type, $amount_data, $total, $product_id = false, $term_id = false ) {
			$diff         = ( 'min' === $min_or_max ? ( $amount_data['amount'] - $total ) : ( $total - $amount_data['amount'] ) );
			$placeholders = array(
				'%amount_type%'          => $amount_type,           // for debugging
				'%amount_source%'        => $amount_data['source'], // for debugging
				'%product_id%'           => $product_id,            // for debugging
				'%term_id%'              => $term_id,               // for debugging
				'%amount%'               => alg_wc_oma()->core->amounts->format( $amount_data['amount'], $amount_type ),
				'%total%'                => alg_wc_oma()->core->amounts->format( $total, $amount_type ),
				'%diff%'                 => alg_wc_oma()->core->amounts->format( $diff, $amount_type ),
				'%amount_raw%'           => $amount_data['amount'],
				'%total_raw%'            => $total,
				'%diff_raw%'             => $diff,
				'%product_title%'        => ( $product_id ? get_the_title( $product_id ) : '' ),
				'%term_title%'           => ( $term_id ? ( ( $term = get_term( $term_id ) ) && ! is_wp_error( $term ) ? $term->name : '' ) : '' ),
				'%term_link%'            => ( $term_id ? ( ( $term = get_term( $term_id ) ) && ! is_wp_error( $term ) ? get_term_link( $term ) : '' ) : '' ),
				'%term_title_with_link%' => ( $term_id ? ( ( $term = get_term( $term_id ) ) && ! is_wp_error( $term ) ? '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>' : '' ) : '' ),
			);

			return apply_filters( 'alg_wc_oma_placeholders', $placeholders, $min_or_max, $amount_type, $amount_data, $total, $diff, $product_id, $term_id );
		}
	}
endif;

return new Alg_WC_OMA_Messages();