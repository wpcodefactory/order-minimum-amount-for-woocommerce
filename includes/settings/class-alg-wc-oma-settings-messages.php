<?php
/**
 * Order Minimum Amount for WooCommerce - Messages Section Settings
 *
 * @version 4.0.1
 * @since   1.2.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Messages' ) ) :

class Alg_WC_OMA_Settings_Messages extends Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id   = 'messages';
		$this->desc = __( 'Messages', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_scope_title.
	 *
	 * @version 3.2.0
	 * @since   3.2.0
	 */
	function get_scope_title( $scope ) {
		switch ( $scope ) {
			case 'product':
				return __( 'Per product', 'order-minimum-amount-for-woocommerce' );
			case 'product_cat':
				return __( 'Per product category', 'order-minimum-amount-for-woocommerce' );
			case 'product_tag':
				return __( 'Per product tag', 'order-minimum-amount-for-woocommerce' );
			default:
				return '';
		}
	}

	/**
	 * get_source_title.
	 *
	 * @version 4.0.0
	 * @since   3.3.0
	 */
	function get_source_title( $source ) {
		switch ( $source ) {
			case 'shipping':
				return __( 'Shipping', 'order-minimum-amount-for-woocommerce' );
			case 'gateway':
				return __( 'Payment Gateways', 'order-minimum-amount-for-woocommerce' );
			default:
				return '';
		}
	}

	/**
	 * get_dynamic_settings_info.
	 *
	 * @version 4.0.1
	 * @since   4.0.1
	 *
	 * @return array
	 */
	function get_dynamic_settings_info(){
		$dynamic_settings_info = array(
			'cart' => array(
				'title'                => __( 'Cart', 'order-minimum-amount-for-woocommerce' ),
				'default_notice_type'  => 'notice',
				'additional_positions' => array(
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
				)
			),
			'checkout' => array(
				'title'                => __( 'Checkout', 'order-minimum-amount-for-woocommerce' ),
				'default_notice_type'  => 'error',
				'additional_positions' => array(
					'woocommerce_before_checkout_form'             => __( 'Before checkout form', 'order-minimum-amount-for-woocommerce' ),
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
				)
			),
			'product_page' => array(
				'title'                => __( 'Product page', 'order-minimum-amount-for-woocommerce' ),
				'default_notice_type'  => 'notice',
				'additional_positions' => array(
					'woocommerce_before_add_to_cart_form'      => __( 'Before add to cart form', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_before_add_to_cart_button'    => __( 'Before add to cart button', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_add_to_cart_button'     => __( 'After add to cart button', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_before_add_to_cart_quantity'  => __( 'Before add to cart quantity', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_add_to_cart_quantity'   => __( 'After add to cart quantity', 'order-minimum-amount-for-woocommerce' ),
					'woocommerce_after_single_product_summary' => __( 'After single product summary', 'order-minimum-amount-for-woocommerce' ),
				)
			),
		);
		return $dynamic_settings_info;
	}

	/**
	 * get_settings.
	 *
	 * @version 4.0.1
	 * @since   1.2.0
	 *
	 * @todo    add optional "Message on requirements met"
	 * @todo    deprecate "Checkout" messages, i.e. use "Cart" messages everywhere?
	 */
	function get_settings() {

		$header = array(
			array(
				'title'    => __( 'Messages', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Customer messages when order does not meet the amount requirements.', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_message_header',
			),
			array(
				'title'    => __( 'Display on empty cart', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Display messages even if cart is empty', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'This option will probably make more sense if the product page notice is enabled.', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'checkbox',
				'default'  => 'no',
				'id'       => 'alg_wc_oma_display_messages_on_empty_cart',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_message_header',
			),
		);

		$dynamic_settings      = array();
		$dynamic_settings_info = $this->get_dynamic_settings_info();
		foreach ( $dynamic_settings_info as $setting_key => $info ) {
			$title            = $info['title'];
			$dynamic_settings = array_merge( $dynamic_settings, array(
				array(
					'title' => $title,
					'type'  => 'title',
					'id'    => "alg_wc_oma_message_content_{$setting_key}_options",
				),
				array(
					'title'   => sprintf( __( '%s notices', 'order-minimum-amount-for-woocommerce' ), $title ),
					'desc'    => '<strong>' . __( 'Enable', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'      => "alg_wc_oma_{$setting_key}_notice_enabled",
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'    => __( 'Notice type', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Styling.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => "alg_wc_oma_{$setting_key}_notice_type",
					'default'  => $info['default_notice_type'],
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'options'  => array(
						'error'  => __( 'Error', 'order-minimum-amount-for-woocommerce' ),
						'notice' => __( 'Notice', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'title'   => __( 'Additional positions', 'order-minimum-amount-for-woocommerce' ),
					'id'      => "alg_wc_oma_message_positions_{$setting_key}",
					'default' => array(),
					'type'    => 'multiselect',
					'class'   => 'chosen_select',
					'options' => $info['additional_positions']
				),
			) );
			foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
					foreach ( apply_filters( 'alg_wc_oma_enabled_scopes', array( '' ) ) as $scope ) {
						foreach ( apply_filters( 'alg_wc_oma_enabled_message_sources', array( '' ) ) as $source ) {
							if ( '' != $scope && '' != $source ) {
								continue;
							}
							$id = alg_wc_oma()->core->get_message_option_id( $setting_key, $scope, $source );
							$dynamic_settings = array_merge( $dynamic_settings, array(
								array(
									'title'    => alg_wc_oma()->core->get_title( $min_or_max, $amount_type, array( $this->get_scope_title( $scope ), $this->get_source_title( $source ) ), true ),
									'id'       => "alg_wc_oma_{$min_or_max}_{$amount_type}_message[{$id}]",
									'default'  => alg_wc_oma()->core->get_default_message( $min_or_max, $scope, $source ),
									'type'     => 'textarea',
									'css'      => 'width:100%;',
									'alg_wc_oma_raw' => true,
								),
							) );
						}
					}
				}
			}
			$dynamic_settings = array_merge( $dynamic_settings, array(
				array(
					'type'     => 'sectionend',
					'id'       => "alg_wc_oma_message_content_{$setting_key}_options",
				),
			) );
		}

		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced Options', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_message_advanced_options',
			),
			array(
				'title'    => __( 'Format amounts', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Affects %s placeholders.', 'order-minimum-amount-for-woocommerce' ),
					'<code>' . implode( '</code>, <code>', array( '%amount%', '%total%', '%diff%' ) ) . '</code>' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_oma_message_format_types_enabled',
				'default'  => 'yes',
			),
			array(
				'desc_tip' => __( 'Choose which amount types should be formatted in messages. E.g. it will add "pcs" to the "Quantity" amounts, or it will round and add currency symbol to the "Sum" amounts.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Leave empty to format all amount types.', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => alg_wc_oma()->core->amounts->get_types(),
				'id'       => 'alg_wc_oma_message_format_types',
				'default'  => array(),
			),
			array(
				'title'    => __( 'Remove old notices', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Will remove old WooCommerce notices on AJAX add to cart.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					sprintf( __( 'This is useful if you have checked "%s" option in %s and there are cross-sells products available on the cart page.', 'order-minimum-amount-for-woocommerce' ),
						__( 'Enable AJAX add to cart buttons on archives', 'order-minimum-amount-for-woocommerce' ),
						'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=products' ) . '">' .
							__( 'WooCommerce > Settings > Products', 'order-minimum-amount-for-woocommerce' ) .
						'</a>' ),
				'desc'     => __( 'Remove', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'checkbox',
				'id'       => 'alg_wc_oma_remove_notices_on_added_to_cart',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_message_advanced_options',
			),
		);

		$notes = array();

		$notes[] = sprintf( __( 'Available placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
			'<div style="padding: 0px 0px 15px;">' .
				'<code>' . implode( '</code>, <code>', array( '%amount%', '%total%', '%diff%', '%amount_raw%', '%total_raw%', '%diff_raw%' ) ) . '</code>' .
			'</div>' );

		if ( array() != apply_filters( 'alg_wc_oma_enabled_scopes', array() ) ) {
			$notes[] = sprintf( __( 'For "Per product", "Per product category" and "Per product tag" messages you can also use these additional placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
				'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%product_title%', '%term_title%' ) ) . '</code>' .
				'</div>' );
		}

		if ( 'yes' === get_option( 'alg_wc_oma_by_shipping_enabled', 'no' ) && 'yes' === get_option( 'alg_wc_oma_by_shipping_messages_enabled', 'no' ) ) {
			$notes[] = sprintf( __( 'For "Shipping" messages you can also use these additional placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
				'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%shipping_method%', '%shipping_zone%', '%shipping_zone_locations%' ) ) . '</code>' .
				'</div>' );
		}

		if ( 'yes' === get_option( 'alg_wc_oma_by_gateway_enabled', 'no' ) && 'yes' === get_option( 'alg_wc_oma_by_gateway_messages_enabled', 'no' ) ) {
			$notes[] = sprintf( __( 'For "Payment Gateways" messages you can also use this additional placeholder: %s', 'order-minimum-amount-for-woocommerce' ),
				'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%payment_gateway%' ) ) . '</code>' .
				'</div>' );
		}

		$notes[] = __( 'Identical messages will be filtered, i.e. only one of them will be shown on the frontend.', 'order-minimum-amount-for-woocommerce' );

		$notes[] = __( 'You can use HTML in the messages.', 'order-minimum-amount-for-woocommerce' );

		$notes[] = sprintf( __( 'You can also use shortcodes in the messages, for example, for WPML/Polylang translations: %s', 'order-minimum-amount-for-woocommerce' ),
			'<br><pre style="background-color: #E0E0E0; padding: 15px; margin-bottom:0">' .
				'[alg_wc_oma_translate lang="DE"]Text for DE[/alg_wc_oma_translate]' .
				'[alg_wc_oma_translate lang="NL"]Text for NL[/alg_wc_oma_translate]' .
				'[alg_wc_oma_translate not_lang="DE,NL"]Text for other languages[/alg_wc_oma_translate]' .
			'</pre>' );

		$notes_settings = array(
			array(
				'title'    => __( 'Notes', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => $this->format_notes( $notes ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_message_notes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_message_notes',
			),
		);

		return array_merge( $header, $dynamic_settings, $advanced_settings, $notes_settings );
	}

}

endif;

return new Alg_WC_OMA_Settings_Messages();
