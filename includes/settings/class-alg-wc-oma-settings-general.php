<?php
/**
 * Order Minimum Amount for WooCommerce - General Section Settings
 *
 * @version 3.4.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_General' ) ) :

class Alg_WC_OMA_Settings_General extends Alg_WC_OMA_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.4.0
	 * @since   1.0.0
	 * @todo    [next] Validate on add to cart: better description (e.g. note about payment gateways and shipping)
	 * @todo    [next] Hide "add to cart" button: better description (e.g. note about payment gateways and shipping)
	 * @todo    [next] Block checkout page: better description (e.g. note about payment gateways)
	 * @todo    [maybe] Require all: better description?
	 * @todo    [maybe] Block checkout process: better description?
	 * @todo    [maybe] better description for `Exclude (used only when "Order sum" is set to "Order total")`?
	 * @todo    [maybe] describe the order, i.e. per product / currencies / user / shipping / user role / general?
	 * @todo    [maybe] hide "Order Sum Options" if "Sum" is not enabled?
	 * @todo    [maybe] move "Advanced" to a separate section (i.e. vs subsection)?
	 */
	function get_settings() {

		$plugin_settings = array(
			array(
				'title'    => __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_plugin_options',
			),
			array(
				'title'    => __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_plugin_options',
			),
		);

		$general_settings = array(
			array(
				'title'    => __( 'General Options', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_general_options',
			),
			array(
				'title'    => __( 'Limits', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'If empty, all limits will be used.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_amount_limits',
				'default'  => array( 'min', 'max' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'min' => __( 'Minimum', 'order-minimum-amount-for-woocommerce' ),
					'max' => __( 'Maximum', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Types', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'If empty, all types will be used.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_amount_types',
				'default'  => array( 'sum', 'qty' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => alg_wc_oma()->core->amounts->get_types(),
			),
			array(
				'title'    => __( 'Require all types', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Enable this if you have enabled multiple "Types", and you want to require for all types to pass the amount check (e.g. "Sum" <strong>AND</strong> "Quantity"), vs at least one type (e.g. "Sum" <strong>OR</strong> "Quantity").', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_require_all_types',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_general_options',
			),
		);

		$order_sum_settings = array(
			array(
				'title'    => __( 'Order Sum Options', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Extra settings for min/max "sum" options.', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_sum_options',
			),
			array(
				'title'    => __( 'Order sum', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_order_sum',
				'default'  => 'total',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'total'    => __( 'Order total', 'order-minimum-amount-for-woocommerce' ),
					'subtotal' => __( 'Order subtotal', 'order-minimum-amount-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Exclude taxes', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Excludes taxes from order total/subtotal.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Exclude', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_exclude_taxes',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => sprintf( __( 'Exclude (ignored unless "%s" is set to "%s")', 'order-minimum-amount-for-woocommerce' ),
					__( 'Order sum', 'order-minimum-amount-for-woocommerce' ), __( 'Order total', 'order-minimum-amount-for-woocommerce' ) ),
				'desc'     => __( 'Exclude shipping', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_exclude_shipping',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'start',
			),
			array(
				'desc'     => __( 'Exclude discounts', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_exclude_discounts',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => '',
			),
			array(
				'desc'     => __( 'Exclude fees', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_exclude_fees',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => 'end',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_sum_options',
			),
		);

		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_advanced_options',
			),
			array(
				'title'    => __( 'Block checkout process', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'When disabled, will allow customer to finish the order even with wrong min/max amount.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_block_checkout_process',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Block checkout page', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Stops customer from reaching the checkout page on wrong min/max amount.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Customer is redirected back to the cart page.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_block_checkout',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Validate on add to cart', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Validates <strong>maximum</strong> limits when customer clicks "add to cart" button.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_validate_on_add_to_cart',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Hide "add to cart" button', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'Hides "add to cart" button for the product on shop pages if <strong>maximum</strong> limits are reached.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Shop', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_hide_add_to_cart_loop',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Shop content', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'You can optionally output something else if "add to cart" button is hidden for the product.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_hide_add_to_cart_loop_content',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_oma_raw' => true,
			),
			array(
				'desc_tip' => __( 'Hides "add to cart" button for the product on single product pages if <strong>maximum</strong> limits are reached.', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Single product', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_hide_add_to_cart_single',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Single product content', 'order-minimum-amount-for-woocommerce' ),
				'desc_tip' => __( 'You can optionally output something else if "add to cart" button is hidden for the product.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => 'alg_wc_oma_max_hide_add_to_cart_single_content',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_oma_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_advanced_options',
			),
		);

		return array_merge( $plugin_settings, $general_settings, $order_sum_settings, $advanced_settings );
	}

}

endif;

return new Alg_WC_OMA_Settings_General();
