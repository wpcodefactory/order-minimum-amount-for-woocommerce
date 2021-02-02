<?php
/**
 * Order Minimum Amount for WooCommerce - Products Cart Total Section Settings
 *
 * @version 3.3.0
 * @since   3.3.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Products_Cart_Total' ) ) :

class Alg_WC_OMA_Settings_Products_Cart_Total extends Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 */
	function __construct() {
		$this->id   = 'products_cart_total';
		$this->desc = __( 'Cart Total', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.3.0
	 * @since   3.3.0
	 */
	function get_settings() {

		$settings = array(
			array(
				'title'    => __( 'Cart Total', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'Calculate cart total by selected products only.', 'order-minimum-amount-for-woocommerce' ) .
					$this->get_pro_msg( 'enable this section' ),
				'id'       => 'alg_wc_oma_products_cart_total_options',
			),
			array(
				'title'    => __( 'Products in cart total', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_products_cart_total_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per product', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Enable this if you want this section to affect "%s" options (i.e. "%s") as well.', 'order-minimum-amount-for-woocommerce' ),
					__( 'General', 'order-minimum-amount-for-woocommerce' ) . ' > ' . __( 'Scope Options', 'order-minimum-amount-for-woocommerce' ),
					__( 'Per product', 'order-minimum-amount-for-woocommerce' ) . ' / ' . __( 'Per product category', 'order-minimum-amount-for-woocommerce' ) . ' / ' .
						__( 'Per product tag', 'order-minimum-amount-for-woocommerce' ) ),
				'id'       => 'alg_wc_oma_products_cart_total_per_product_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_products_cart_total_options',
			),
		);

		$settings = array_merge( $settings, $this->get_products_options( '_cart_total' ) );

		return $settings;
	}

}

endif;

return new Alg_WC_OMA_Settings_Products_Cart_Total();
