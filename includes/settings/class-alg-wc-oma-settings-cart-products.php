<?php
/**
 * Order Minimum Amount for WooCommerce - Cart Products Section Settings
 *
 * @version 3.4.0
 * @since   3.1.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Cart_Products' ) ) :

class Alg_WC_OMA_Settings_Cart_Products extends Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.4.0
	 * @since   3.1.0
	 */
	function __construct() {
		$this->id   = 'cart_products';
		$this->desc = __( 'Cart Products', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.3.0
	 * @since   3.1.0
	 * @todo    [later] Validate all: better desc?
	 * @todo    [maybe] Validate all: for each option separately?
	 */
	function get_settings() {

		$settings = array(
			array(
				'title'    => __( 'Cart Products', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'Skip min/max amount checks if there are selected products in cart.', 'order-minimum-amount-for-woocommerce' ) .
					$this->get_pro_msg( 'enable this section' ),
				'id'       => 'alg_wc_oma_products_options',
			),
			array(
				'title'    => __( 'Products', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_products_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Validate all products', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Choose if you want to validate all products in the cart, or at least one product.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_products_validate_all_products',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_products_options',
			),
		);

		$settings = array_merge( $settings, $this->get_products_options() );

		return $settings;
	}

}

endif;

return new Alg_WC_OMA_Settings_Cart_Products();
