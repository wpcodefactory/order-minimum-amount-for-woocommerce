<?php
/**
 * Order Minimum Amount for WooCommerce - Products Section Settings
 *
 * @version 3.4.0
 * @since   3.4.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Products' ) ) :

class Alg_WC_OMA_Settings_Products extends Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 */
	function __construct() {
		$this->id   = 'products';
		$this->desc = __( 'Products', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 * @todo    [maybe] better desc for checkboxes?
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Products Options', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'Set different amounts per product, product category and/or product tag.', 'order-minimum-amount-for-woocommerce' ) .
					$this->get_pro_msg( 'set amounts per products' ),
				'id'       => 'alg_wc_oma_scope_options',
			),
			array(
				'title'    => __( 'Per product', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'This will add new meta box to each product edit page.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_per_product_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per product category', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'This will add new settings fields to each product category edit page.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_per_product_cat_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'    => __( 'Per product tag', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'This will add new settings fields to each product tag edit page.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_per_product_tag_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_scope_options',
			),
		);
	}

}

endif;

return new Alg_WC_OMA_Settings_Products();
