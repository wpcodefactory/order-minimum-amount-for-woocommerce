<?php
/**
 * Order Minimum Amount for WooCommerce - Products Cart Total Section Settings
 *
 * @version 4.6.6
 * @since   3.3.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

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
		 * @version 4.6.6
		 * @since   3.3.0
		 */
		function get_settings() {

			$enabled_types = alg_wc_oma()->core->get_enabled_amount_types();

			$settings = array(
				array(
					'title' => __( 'Cart Total', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Calculate cart total by selected products only.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_products_cart_total_options',
				),
				array(
					'title'             => __( 'Products in cart total', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_products_cart_total_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'    => __( 'Per product', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Enable this if you want this section to affect %s section options (i.e. "%s") as well.', 'order-minimum-amount-for-woocommerce' ),
						$this->get_section_link( 'products' ),
						__( 'Per product', 'order-minimum-amount-for-woocommerce' ) . ' / ' . __( 'Per product category', 'order-minimum-amount-for-woocommerce' ) . ' / ' .
						__( 'Per product tag', 'order-minimum-amount-for-woocommerce' ) ),
					'id'       => 'alg_wc_oma_products_cart_total_per_product_enabled',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'List variations', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Will add variable product variations to the products lists.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					              __( 'Variations will be added to the lists after you "Save changes".', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_products_cart_total_list_variations',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_products_cart_total_options',
				),
			);

			$notes = array();
			if ( in_array( 'sum', $enabled_types ) ) {
				$notes = array(
					array(
						'title' => __( 'Good to know', 'order-minimum-amount-for-woocommerce' ),
						'desc'  => $this->section_notes( array(
							sprintf( __( 'Please note that final order %s value will also be affected by the %s settings in %s section.', 'order-minimum-amount-for-woocommerce' ),
								'<strong>' . __( 'sum', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
								'<strong>' . sprintf( __( '"%s" Amount Type Options', 'order-minimum-amount-for-woocommerce' ),
									__( 'Sum', 'order-minimum-amount-for-woocommerce' ) ) . '</strong>',
								$this->get_section_link( 'general' ) ),
						) ),
						'type'  => 'title',
						'id'    => "alg_wc_oma_{$this->id}_notes",
					),
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_oma_{$this->id}_notes",
					),
				);
			}

			return array_merge( $settings, $this->get_products_options( '_cart_total', ( 'yes' === get_option( 'alg_wc_oma_products_cart_total_list_variations', 'no' ) ) ), $notes );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Products_Cart_Total();
