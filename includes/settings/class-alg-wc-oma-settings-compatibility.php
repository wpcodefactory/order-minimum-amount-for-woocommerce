<?php
/**
 * Order Minimum Amount for WooCommerce - Compatibility Settings
 *
 * @version 4.0.8
 * @since   4.0.8
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
		 * @version 4.0.8
		 * @since   4.0.8
		 */
		function get_settings() {
			$prod_bundle_opts = array(
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
			$wc_subscriptions_opts = array(
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
			return array_merge(
				$prod_bundle_opts, $wc_subscriptions_opts
			);
		}
	}

endif;

return new Alg_WC_OMA_Settings_Compatibility();
