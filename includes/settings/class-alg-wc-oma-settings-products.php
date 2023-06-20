<?php
/**
 * Order Minimum Amount for WooCommerce - Products Section Settings
 *
 * @version 4.0.0
 * @since   3.4.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

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
		 * @version 4.0.0
		 * @since   3.4.0
		 */
		function get_settings() {
			return array(
				array(
					'title' => __( 'Products Options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Set different amounts per product, product category and/or product tag.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_scope_options',
				),
				array(
					'title'             => __( 'Per product', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'desc_tip'          => sprintf( __( 'This will add new meta box to each %s edit page.', 'order-minimum-amount-for-woocommerce' ),
						'<a target="_blank" href="' . admin_url( 'edit.php?post_type=product' ) . '">' . __( 'product', 'order-minimum-amount-for-woocommerce' ) . '</a>' ),
					'id'                => 'alg_wc_oma_per_product_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'          => __( 'List variations', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'      => __( 'Will add variable product variations to the options lists in product meta box.', 'order-minimum-amount-for-woocommerce' ),
					'id'            => 'alg_wc_oma_per_product_list_variations',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
				),
				array(
					'title'             => __( 'Per product category', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'desc_tip'          => sprintf( __( 'This will add new settings fields to each %s edit page.', 'order-minimum-amount-for-woocommerce' ),
						'<a target="_blank" href="' . admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ) . '">' . __( 'product category', 'order-minimum-amount-for-woocommerce' ) . '</a>' ),
					'id'                => 'alg_wc_oma_per_product_cat_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Per product tag', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'desc_tip'          => sprintf( __( 'This will add new settings fields to each %s edit page.', 'order-minimum-amount-for-woocommerce' ),
						'<a target="_blank" href="' . admin_url( 'edit-tags.php?taxonomy=product_tag&post_type=product' ) . '">' . __( 'product tag', 'order-minimum-amount-for-woocommerce' ) . '</a>' ),
					'id'                => 'alg_wc_oma_per_product_tag_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_scope_options',
				),
			);
		}

	}

endif;

return new Alg_WC_OMA_Settings_Products();
