<?php
/**
 * Order Minimum Amount for WooCommerce - General Section Settings
 *
 * @version 4.4.3
 * @since   1.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

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
		 * @version 4.4.3
		 * @since   1.0.0
		 */
		function get_settings() {

			$enabled_limits = alg_wc_oma()->core->get_enabled_amount_limits();
			$enabled_types  = alg_wc_oma()->core->get_enabled_amount_types();

			$plugin_settings = array(
				array(
					'title' => __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_plugin_options',
				),
				array(
					'title'   => __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => '<strong>' . __( 'Enable plugin', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'      => 'alg_wc_oma_plugin_enabled',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_plugin_options',
				),
			);

			$general_settings = array(
				array(
					'title' => __( 'General options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_general_options',
				),
				array(
					'title'    => __( 'Amount limits', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'If empty, all limits will be used.', 'order-minimum-amount-for-woocommerce' ) . '<br><br>' .
					              __( '"Maximum" limit has additional settings which will be displayed on the current page after you "Save changes".', 'order-minimum-amount-for-woocommerce' ),
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
					'title'    => __( 'Amount types', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'If empty, all types will be used.', 'order-minimum-amount-for-woocommerce' ) . '<br><br>' .
					              __( 'Some types (e.g. "Sum") have additional settings which will be displayed on the current page after you "Save changes".', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_amount_types',
					'default'  => array( 'sum', 'qty' ),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => alg_wc_oma()->core->amounts->get_types(),
				),
				array(
					'title'    => __( 'Require all types', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Enable this if you have enabled multiple "Amount types", and you want to require for all types to pass the amount check (e.g. "Sum" <strong>AND</strong> "Quantity"), vs at least one type (e.g. "Sum" <strong>OR</strong> "Quantity").', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_require_all_types',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_general_options',
				),
			);

			$login_requirement_settings = array(
				array(
					'title' => __( 'Login requirement', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => 'Demand users to be logged in order to display the min/max requirements.',
					'type'  => 'title',
					'id'    => 'alg_wc_oma_login_requirement_options',
				),
				array(
					'title'   => __( 'Login requirement', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Enable login requirement', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_login_requirement_enabled',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Notice', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_login_requirement_notice_msg',
					'default' => __( 'Please login to access the min/max requirements for your order.', 'order-minimum-amount-for-woocommerce' ),
					'type'    => 'text',
				),
				array(
					'title'   => __( 'Notice type', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_login_requirement_notice_type',
					'default' => 'error',
					'type'    => 'select',
					'class'   => 'wc-enhanced-select',
					'options' => array(
						'error'  => __( 'Error', 'order-minimum-amount-for-woocommerce' ),
						'notice' => __( 'Notice', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'title'             => __( 'Display condition', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_login_requirement_display_condition',
					'default'           => array( 'is_cart', 'is_checkout' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'options'           => array(
						'is_woocommerce'      => __( 'Is WooCommerce', 'order-minimum-amount-for-woocommerce' ),
						'is_shop'             => __( 'Is Shop', 'order-minimum-amount-for-woocommerce' ),
						'is_product_category' => __( 'Is Product Category', 'order-minimum-amount-for-woocommerce' ),
						'is_product_tag'      => __( 'Is Product Tag', 'order-minimum-amount-for-woocommerce' ),
						'is_product'          => __( 'Is Product', 'order-minimum-amount-for-woocommerce' ),
						'is_cart'             => __( 'Is Cart', 'order-minimum-amount-for-woocommerce' ),
						'is_checkout'         => __( 'Is Checkout', 'order-minimum-amount-for-woocommerce' ),
						'is_account_page'     => __( 'Is Account Page', 'order-minimum-amount-for-woocommerce' ),
						'is_wc_endpoint_url'  => __( 'Is WC Endpoint URL', 'order-minimum-amount-for-woocommerce' )
					),
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_login_requirement_options',
				),
			);

			$cart_settings = array(
				array(
					'title' => __( 'Cart options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_checkout_options',
				),
				array(
					'title'   => __( 'Proceed to checkout button', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( '"Proceed to checkout" button behaviour when limits are not respected.', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_disable_block_checkout_btn',
					'default' => 'do_not_disable',
					'class'   => 'chosen_select',
					'type'    => 'select',
					'options' => array(
						'do_not_disable' => __( 'Leave it enabled', 'order-minimum-amount-for-woocommerce' ),
						'disable'        => __( 'Disable checkout button', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_login_requirement_options',
				),
			);

			$checkout_settings = array(
				array(
					'title' => __( 'Checkout options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_checkout_options',
				),
				array(
					'title'    => __( 'Block checkout process', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'When disabled, will allow customer to finish the order even with wrong min/max amount.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					              __( 'Most of our plugin users will keep this option enabled.', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Prevent users from placing the order with wrong amounts', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_block_checkout_process',
					'checkboxgroup' => 'start',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					//'title'    => __( 'Block store api request', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Enable it if you\'re using the Checkout Block.', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Prevent API users from placing orders with wrong amounts', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_block_store_api',
					'default'  => 'no',
					'checkboxgroup' => 'end',
					'type'     => 'checkbox',
				),
				array(
					'desc'     => __( 'Checkout hook.', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Checkout hook used to check for error messages.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_block_checkout_hook',
					'default'  => 'woocommerce_checkout_process',
					'options'  => array(
						'woocommerce_checkout_process'          => 'woocommerce_checkout_process',
						'woocommerce_after_checkout_validation' => 'woocommerce_after_checkout_validation',
					),
					'class'    => 'chosen_select',
					'type'     => 'select',
				),
				array(
					'title'    => __( 'Block checkout page', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Customer is redirected back to the cart page.', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Prevent users from reaching the checkout page on wrong min/max amount', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_block_checkout',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_checkout_options',
				),
			);

			$type_sum_settings = array();
			if ( in_array( 'sum', $enabled_types ) ) {
				$type_sum_settings = array(
					array(
						'title' => sprintf( __( '"%s" Amount Type options', 'order-minimum-amount-for-woocommerce' ),
							__( 'Sum', 'order-minimum-amount-for-woocommerce' ) ),
						'desc'  => sprintf( __( 'Extra settings for min/max "%s" options.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Sum', 'order-minimum-amount-for-woocommerce' ) ),
						'type'  => 'title',
						'id'    => 'alg_wc_oma_type_sum_options',
					),
					array(
						'title'   => __( 'Order sum', 'order-minimum-amount-for-woocommerce' ),
						'id'      => 'alg_wc_oma_order_sum',
						'default' => 'total',
						'type'    => 'select',
						'class'   => 'chosen_select',
						'options' => array(
							'total'    => __( 'Order total', 'order-minimum-amount-for-woocommerce' ),
							'subtotal' => __( 'Order subtotal', 'order-minimum-amount-for-woocommerce' ),
						),
					),
					array(
						'title'    => __( 'Cart total rounding', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'If the limit messages are still getting displayed even if the cart amount matches the limit, it\'s because the cart total amount being compared behind the scenes is raw. Here is how you can round it.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_type_sum_cart_total_rounding',
						'default'  => 'total',
						'type'     => 'select',
						'class'    => 'chosen_select',
						'options'  => array(
							'none'  => __( 'None', 'order-minimum-amount-for-woocommerce' ),
							'round' => __( 'Round', 'order-minimum-amount-for-woocommerce' ),
							'ceil'  => __( 'Ceil', 'order-minimum-amount-for-woocommerce' ),
							'floor' => __( 'Floor', 'order-minimum-amount-for-woocommerce' ),
						),
					),
					array(
						'desc'    => __( 'Precision', 'order-minimum-amount-for-woocommerce' ),
						'id'      => 'alg_wc_oma_type_sum_cart_total_rounding_precision',
						'default' => wc_get_price_decimals(),
						'type'    => 'number',
					),
					array(
						'title'   => __( 'Exclude taxes', 'order-minimum-amount-for-woocommerce' ),
						'desc'    => __( 'Exclude taxes from order total/subtotal', 'order-minimum-amount-for-woocommerce' ),
						'id'      => 'alg_wc_oma_exclude_taxes',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'title'         => sprintf( __( 'Exclude (ignored unless "%s" is set to "%s")', 'order-minimum-amount-for-woocommerce' ),
							__( 'Order sum', 'order-minimum-amount-for-woocommerce' ), __( 'Order total', 'order-minimum-amount-for-woocommerce' ) ),
						'desc'          => __( 'Exclude shipping', 'order-minimum-amount-for-woocommerce' ),
						'id'            => 'alg_wc_oma_exclude_shipping',
						'default'       => 'no',
						'type'          => 'checkbox',
						'checkboxgroup' => 'start',
					),
					array(
						'desc'          => __( 'Exclude discounts', 'order-minimum-amount-for-woocommerce' ),
						'id'            => 'alg_wc_oma_exclude_discounts',
						'default'       => 'no',
						'type'          => 'checkbox',
						'checkboxgroup' => '',
					),
					array(
						'desc'          => __( 'Exclude fees', 'order-minimum-amount-for-woocommerce' ),
						'id'            => 'alg_wc_oma_exclude_fees',
						'default'       => 'no',
						'type'          => 'checkbox',
						'checkboxgroup' => 'end',
					),
					array(
						'title'   => __( 'WC Subscription recurring amount', 'order-minimum-amount-for-woocommerce' ),
						'desc'    => __( 'Include WC Subscription recurring amount to cart total', 'order-minimum-amount-for-woocommerce' ),
						'id'      => 'alg_wc_oma_include_wc_subscription_cart_total',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_type_sum_options',
					),
				);
			}

			$type_product_cat_settings = array();
			if ( in_array( 'product_cat', $enabled_types ) ) {
				$type_product_cat_settings = array(
					array(
						'title' => sprintf( __( '"%s" Amount Type options', 'order-minimum-amount-for-woocommerce' ),
							__( 'Product categories', 'order-minimum-amount-for-woocommerce' ) ),
						'desc'  => sprintf( __( 'Extra settings for min/max "%s" options.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Product categories', 'order-minimum-amount-for-woocommerce' ) ),
						'type'  => 'title',
						'id'    => 'alg_wc_oma_type_product_cat_options',
					),
					array(
						'title'    => __( 'Product categories to include', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'If set, then only selected product categories will be counted (and all other categories will be ignored).', 'order-minimum-amount-for-woocommerce' ) . ' ' .
						              __( 'If empty, then all product categories will be counted.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_type_product_cat_terms_to_include',
						'default'  => array(),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->add_current_values( $this->get_terms( 'product_cat' ), 'alg_wc_oma_type_product_cat_terms_to_include', 'product_cat' ),
					),
					array(
						'title'    => __( 'Product categories to exclude', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'If set, then selected product categories will be ignored when counting categories for the product.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_type_product_cat_terms_to_exclude',
						'default'  => array(),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->add_current_values( $this->get_terms( 'product_cat' ), 'alg_wc_oma_type_product_cat_terms_to_exclude', 'product_cat' ),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_type_product_cat_options',
					),
				);
			}

			$type_product_tag_settings = array();
			if ( in_array( 'product_tag', $enabled_types ) ) {
				$type_product_tag_settings = array(
					array(
						'title' => sprintf( __( '"%s" Amount Type options', 'order-minimum-amount-for-woocommerce' ),
							__( 'Product tags', 'order-minimum-amount-for-woocommerce' ) ),
						'desc'  => sprintf( __( 'Extra settings for min/max "%s" options.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Product tags', 'order-minimum-amount-for-woocommerce' ) ),
						'type'  => 'title',
						'id'    => 'alg_wc_oma_type_product_tag_options',
					),
					array(
						'title'    => __( 'Product tags to include', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'If set, then only selected product tags will be counted (and all other tags will be ignored).', 'order-minimum-amount-for-woocommerce' ) . ' ' .
						              __( 'If empty, then all product tags will be counted.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_type_product_tag_terms_to_include',
						'default'  => array(),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->add_current_values( $this->get_terms( 'product_tag' ), 'alg_wc_oma_type_product_tag_terms_to_include', 'product_tag' ),
					),
					array(
						'title'    => __( 'Product tags to exclude', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'If set, then selected product tags will be ignored when counting tags for the product.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_type_product_tag_terms_to_exclude',
						'default'  => array(),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->add_current_values( $this->get_terms( 'product_tag' ), 'alg_wc_oma_type_product_tag_terms_to_exclude', 'product_tag' ),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_type_product_tag_options',
					),
				);
			}

			$max_limit_settings = array();
			if ( in_array( 'max', $enabled_limits ) ) {
				$max_limit_settings = array(
					array(
						'title' => __( '"Maximum" amount limit options', 'order-minimum-amount-for-woocommerce' ),
						'desc'  => sprintf( __( 'Extra settings for "%s" limit options.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Maximum', 'order-minimum-amount-for-woocommerce' ) ),
						'type'  => 'title',
						'id'    => 'alg_wc_oma_max_limit_options',
					),
					array(
						'title'   => __( 'Validate on add to cart', 'order-minimum-amount-for-woocommerce' ),
						'desc'    => __( 'Validate maximum limits when customer clicks "add to cart" button', 'order-minimum-amount-for-woocommerce' ),
						'id'      => 'alg_wc_oma_max_validate_on_add_to_cart',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'title'    => __( 'Hide "add to cart" button', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'Hides "add to cart" button for the product on shop pages if maximum limits are reached.', 'order-minimum-amount-for-woocommerce' ),
						'desc'     => __( 'Shop', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_max_hide_add_to_cart_loop',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'desc'           => __( 'Shop content', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip'       => __( 'You can optionally output something else if "add to cart" button is hidden for the product.', 'order-minimum-amount-for-woocommerce' ),
						'id'             => 'alg_wc_oma_max_hide_add_to_cart_loop_content',
						'default'        => '',
						'type'           => 'textarea',
						'css'            => 'width:100%;',
						'alg_wc_oma_raw' => true,
					),
					array(
						'desc_tip' => __( 'Hides "add to cart" button for the product on single product pages if maximum limits are reached.', 'order-minimum-amount-for-woocommerce' ),
						'desc'     => __( 'Single product', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_max_hide_add_to_cart_single',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'desc'           => __( 'Single product content', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip'       => __( 'You can optionally output something else if "add to cart" button is hidden for the product.', 'order-minimum-amount-for-woocommerce' ),
						'id'             => 'alg_wc_oma_max_hide_add_to_cart_single_content',
						'default'        => '',
						'type'           => 'textarea',
						'css'            => 'width:100%;',
						'alg_wc_oma_raw' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_max_limit_options',
					),
				);
			}

			$qty_type_settings = array();
			if ( in_array( 'qty', $enabled_types ) ) {
				$qty_type_settings = array(
					array(
						'title' => __( '"Quantity" amount type options', 'order-minimum-amount-for-woocommerce' ),
						'desc'  => sprintf( __( 'Extra settings for "%s" amount type options.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Quantity', 'order-minimum-amount-for-woocommerce' ) ),
						'type'  => 'title',
						'id'    => 'alg_wc_oma_qty_limit_options',
					),
					array(
						'title'             => __( 'Quantity input', 'order-minimum-amount-for-woocommerce' ),
						'desc'              => sprintf( __( 'Set default quantity based on %s minimum limits', 'order-minimum-amount-for-woocommerce' ), '"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"' ),
						'id'                => 'alg_wc_oma_default_qty_input_based_on_per_product_min',
						'default'           => 'no',
						'type'              => 'checkbox',
						'checkboxgroup'     => 'start',
						'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => sprintf( __( 'Set minimum parameter based on %s minimum limits', 'order-minimum-amount-for-woocommerce' ), '"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"' ),
						'id'                => 'alg_wc_oma_min_qty_input_based_on_per_product_min',
						'default'           => 'no',
						'type'              => 'checkbox',
						'checkboxgroup'     => '',
						'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => sprintf( __( 'Set maximum parameter based on %s maximum limits', 'order-minimum-amount-for-woocommerce' ), '"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"' ),
						'id'                => 'alg_wc_oma_max_qty_input_based_on_per_product_max',
						'default'           => 'no',
						'type'              => 'checkbox',
						'checkboxgroup'     => 'end',
						'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Add to cart button', 'order-minimum-amount-for-woocommerce' ),
						'desc'              => sprintf( __( 'Set quantity on loop pages based on %s minimum limits', 'order-minimum-amount-for-woocommerce' ), '"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"' ),
						'id'                => 'alg_wc_oma_loop_add_to_cart_set_qty_from_min',
						'default'           => 'no',
						'type'              => 'checkbox',
						'checkboxgroup'     => 'start',
						'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_qty_limit_options',
					),
				);
			}

			$rest_api_settings = array(
				array(
					'title' => __( 'REST API', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_rest_api_options',
				),
				array(
					'title'             => __( 'User metas', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Add user metas to the REST API', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => sprintf( __( 'It will be possible to use the %s route to read and update user metas like %s for example.', 'order-minimum-amount-for-woocommerce' ), '<code>' . '/wp-json/wp/v2/users/$user_id/' . '</code>', '<code>' . '_alg_wc_oma_min_sum' . '</code>' ) . '<br />' .
					                       __( 'For now only administrators can update user metas.', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_rest_api_user_metas',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_rest_api_options',
				),
			);

			return array_merge(
				$plugin_settings,
				$general_settings,
				$login_requirement_settings,
				$cart_settings,
				$checkout_settings,
				$type_sum_settings,
				$type_product_cat_settings,
				$type_product_tag_settings,
				$max_limit_settings,
				$qty_type_settings,
				$rest_api_settings
			);
		}

	}

endif;

return new Alg_WC_OMA_Settings_General();
