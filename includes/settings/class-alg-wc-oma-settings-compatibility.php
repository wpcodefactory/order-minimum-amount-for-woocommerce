<?php
/**
 * Order Minimum Amount for WooCommerce - Compatibility Settings.
 *
 * @version 4.3.8
 * @since   4.0.8
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Compatibility' ) ) :

	class Alg_WC_OMA_Settings_Compatibility extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 4.0.8
		 * @since   4.0.8
		 */
		function __construct() {
			$this->id   = 'compatibility';
			$this->desc = __( 'Compatibility', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.3.8
		 * @since   4.0.8
		 */
		function get_settings() {
			$prod_bundle_opts             = array(
				array(
					'title' => __( 'Product Bundles', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'order-minimum-amount-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://woocommerce.com/products/product-bundles/', __( 'Product Bundles', 'order-minimum-amount-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_oma_product_bundles_compatibility_options',
				),
				array(
					'title'             => __( 'Bundled cart item', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Include bundled cart item on cart total calculation', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => __( 'Only affects the sum amount type.', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_prod_bundles_bundled_item_count',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Amount types', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Create bundle price amount type', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => __( 'Allows to set up a min/max price for bundle product in cart.', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_prod_bundles_bundle_price_amount_type',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_product_bundles_compatibility_options',
				),
			);
			$wc_subscriptions_opts        = array(
				array(
					'title' => __( 'WooCommerce Subscriptions', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf( __( 'Compatibility with %s plugin.', 'order-minimum-amount-for-woocommerce' ), sprintf( '<a href="%s" target="_blank">%s</a>', 'https://woocommerce.com/pt-br/products/woocommerce-subscriptions/', __( 'WooCommerce Subscriptions', 'order-minimum-amount-for-woocommerce' ) ) ),
					'id'    => 'alg_wc_oma_wc_subscriptions_compatibility_options',
				),
				array(
					'title'             => __( 'Subscription switching', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Skip min/max amount checks if the user has a switching subscription item in cart', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_wc_subscriptions_skip_subscription_switching',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_wc_subscriptions_compatibility_options',
				),
			);
			$currency_switcher_wpham_opts = array(
				array(
					'title' => __( 'Currency Switcher for WooCommerce', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf(
						__( 'Compatibility with %s plugin.', 'order-minimum-amount-for-woocommerce' ),
						sprintf( '<a href="%s" target="_blank">%s</a> (%s)',
							'https://wordpress.org/plugins/currency-switcher-woocommerce/',
							__( 'Currency Switcher for WooCommerce', 'order-minimum-amount-for-woocommerce' ),
							__( 'By WP Wham', 'order-minimum-amount-for-woocommerce' )
						)
					),
					'id'    => 'alg_wc_oma_wc_currency_switcher_wpham_options',
				),
				array(
					'title'             => __( 'Exchange rates', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Get exchange rates from the Currency Switcher plugin', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'It\'s necessary to enable the %s section and set its %s option as %s.', 'order-minimum-amount-for-woocommerce' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma&section=currencies' ) . '">' . __( 'Currencies', 'order-minimum-amount-for-woocommerce' ) . '</a>', '<strong>' . __( 'Calculation method', 'order-minimum-amount-for-woocommerce' ) . '</strong>', '<code>' . __( 'Exchange rates', 'order-minimum-amount-for-woocommerce' ) . '</code>' ),
					'id'                => 'alg_wc_oma_wc_currency_switcher_wpham_get_exchange_rates',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_wc_currency_switcher_wpham_options',
				),
			);
			$wc_paypal_opts = array(
				array(
					'title' => __( 'WooCommerce PayPal Payments', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => sprintf(
						           __( 'Compatibility with %s plugin.', 'order-minimum-amount-for-woocommerce' ),
						           sprintf(
							           '<a href="%s" target="_blank">%s</a> (%s)',
							           'https://wordpress.org/plugins/woocommerce-paypal-payments/',
							           __( 'WooCommerce PayPal Payments', 'order-minimum-amount-for-woocommerce' ),
							           __( 'By WooCommerce', 'order-minimum-amount-for-woocommerce' )
						           )
					           ) . ' ' .
					           sprintf(
						           __( 'Paypal will probably work better if the %s option is set to %s.', 'order-minimum-amount-for-woocommerce' ),
						           sprintf(
							           '<a href="%s">%s</a>',
							           admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma&section=' ),
							           __( 'Checkout hook', 'order-minimum-amount-for-woocommerce' )
						           ),
						           '<code>woocommerce_after_checkout_validation</code>'
					           ),
					'id'    => 'alg_wc_oma_wc_wc_paypal_payments_opts',
				),
				array(
					'title'             => __( 'PayPal buttons', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Disable PayPal buttons if the limits are not respected', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => __( 'On product pages, the button will need a page reload to refresh the changes.', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_wc_wc_paypal_payments_disable_btn',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_wc_wc_paypal_payments_opts',
				),
			);
			return array_merge(
				$prod_bundle_opts, $wc_subscriptions_opts, $currency_switcher_wpham_opts, $wc_paypal_opts
			);
		}
	}

endif;

return new Alg_WC_OMA_Settings_Compatibility();
