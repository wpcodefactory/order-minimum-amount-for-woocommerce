<?php
/**
 * Order Minimum Amount for WooCommerce - General Section Settings.
 *
 * @version 4.7.4
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
		 * @version 4.7.4
		 * @since   1.0.0
		 */
		function get_settings() {
			$cart_notices_desc_tip = sprintf(
				/* translators: %s: admin URL to messages section */
				__( 'If enabled, you might want to change the %s option.', 'order-minimum-amount-for-woocommerce' ),
				sprintf(
					'<a href="%s">%s</a>',
					admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma&section=messages' ),
					__( 'Cart Notices', 'order-minimum-amount-for-woocommerce' )
				)
			);

			/* translators: %1$s: option name, %2$s: option value */
			$exclude_format = __( 'Exclude (ignored unless "%1$s" is set to "%2$s")', 'order-minimum-amount-for-woocommerce' );

			$rest_api_desc_tip = sprintf(
				/* translators: %1$s: route, %2$s: user meta example */
				__( 'It will be possible to use the %1$s route to read and update user metas like %2$s for example.', 'order-minimum-amount-for-woocommerce' ),
				'<code>/wp-json/wp/v2/users/$user_id/</code>',
				'<code>_alg_wc_oma_min_sum</code>'
			) . '<br />' . __( 'For now only administrators can update user metas.', 'order-minimum-amount-for-woocommerce' );

			$settings = array(
				array(
					'title' => __( 'General Options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'General plugin settings here.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_general_options',
				),
				array(
					'title'   => __( 'Plugin', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_plugin_enabled',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Amount limits', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Minimum / Maximum', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_amount_limits',
					'default' => array( 'min', 'max' ),
					'type'    => 'multiselect',
					'class'   => 'chosen_select',
					'options' => array(
						'min' => __( 'Minimum', 'order-minimum-amount-for-woocommerce' ),
						'max' => __( 'Maximum', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'title'   => __( 'Amount types', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_amount_types',
					'default' => array( 'sum', 'qty' ),
					'type'    => 'multiselect',
					'class'   => 'chosen_select',
					'options' => alg_wc_oma()->core->amounts->get_types(),
				),
				array(
					'title'   => __( 'Require all', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Require all selected amount types to meet min/max values', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_require_all_types',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_general_options',
				),
			);

			$checkout_options = array(
				array(
					'title' => __( 'Checkout options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_checkout_options',
				),
				array(
					'title'   => __( 'Block checkout page', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Block checkout page if min/max amounts are not met', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_block_checkout',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Block checkout process', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Block checkout process on min/max amounts', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_block_checkout_process',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Block checkout hook', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_block_checkout_hook',
					'default' => 'woocommerce_checkout_process',
					'type'    => 'select',
					'class'   => 'chosen_select',
					'options' => array(
						'woocommerce_checkout_process'           => __( 'woocommerce_checkout_process', 'order-minimum-amount-for-woocommerce' ),
						'woocommerce_after_checkout_validation'  => __( 'woocommerce_after_checkout_validation', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'title'    => __( 'Disable proceed to checkout button', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_disable_block_checkout_btn',
					'default'  => 'do_not_disable',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'options'  => array(
						'do_not_disable' => __( 'Do not disable', 'order-minimum-amount-for-woocommerce' ),
						'disable'        => __( 'Disable checkout button', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'title'    => __( 'Cart block notices', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable cart block notices as errors', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => $cart_notices_desc_tip,
					'id'       => 'alg_wc_oma_cart_block_notices_as_errors',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_checkout_options',
				),
			);

			$sum_amount_type_options = array(
				array(
					/* translators: %s: amount type name */
					'title'             => sprintf( __( '"%s" Amount Type options', 'order-minimum-amount-for-woocommerce' ), __( 'Sum', 'order-minimum-amount-for-woocommerce' ) ),
					'type'              => 'title',
					/* translators: %s: amount type name */
					'desc'              => sprintf( __( 'Extra settings for min/max "%s" options.', 'order-minimum-amount-for-woocommerce' ), __( 'Sum', 'order-minimum-amount-for-woocommerce' ) ),
					'id'                => 'alg_wc_oma_type_sum_options_description',
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
					'title'   => __( 'Exclude taxes', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Exclude taxes from order sum', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_exclude_taxes',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Exclude shipping', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Exclude shipping from order sum', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_exclude_shipping',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Exclude discounts', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Exclude discounts from order sum', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_exclude_discounts',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Exclude fees', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Exclude fees from order sum', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_exclude_fees',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(

					'title'    => __( 'Cart total rounding', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_type_sum_cart_total_rounding',
					'default'  => 'none',
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
					'title'             => __( 'Rounding precision', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => __( 'Number of decimals.', 'order-minimum-amount-for-woocommerce' ),
					'id'                => 'alg_wc_oma_type_sum_cart_total_rounding_precision',
					'default'           => wc_get_price_decimals(),
					'type'              => 'number',
					'custom_attributes' => array( 'min' => 0 ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_type_sum_options_description',
				),
			);

			$product_cat_options = array(
				array(
					/* translators: %s: amount type name */
					'title'             => sprintf( __( '"%s" Amount Type options', 'order-minimum-amount-for-woocommerce' ), __( 'Product categories', 'order-minimum-amount-for-woocommerce' ) ),
					'type'              => 'title',
					/* translators: %s: amount type name */
					'desc'              => sprintf( __( 'Extra settings for min/max "%s" options.', 'order-minimum-amount-for-woocommerce' ), __( 'Product categories', 'order-minimum-amount-for-woocommerce' ) ),
					'id'                => 'alg_wc_oma_type_product_cat_options',
				),
				array(
					'title'    => __( 'Terms to include', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Leave empty to include all.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_type_product_cat_terms_to_include',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $this->get_terms( 'product_cat' ),
				),
				array(
					'title'    => __( 'Terms to exclude', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Leave empty to exclude none.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_type_product_cat_terms_to_exclude',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $this->get_terms( 'product_cat' ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_type_product_cat_options',
				),
			);

			$product_tag_options = array(
				array(
					/* translators: %s: amount type name */
					'title'             => sprintf( __( '"%s" Amount Type options', 'order-minimum-amount-for-woocommerce' ), __( 'Product tags', 'order-minimum-amount-for-woocommerce' ) ),
					'type'              => 'title',
					/* translators: %s: amount type name */
					'desc'              => sprintf( __( 'Extra settings for min/max "%s" options.', 'order-minimum-amount-for-woocommerce' ), __( 'Product tags', 'order-minimum-amount-for-woocommerce' ) ),
					'id'                => 'alg_wc_oma_type_product_tag_options',
				),
				array(
					'title'    => __( 'Terms to include', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Leave empty to include all.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_type_product_tag_terms_to_include',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $this->get_terms( 'product_tag' ),
				),
				array(
					'title'    => __( 'Terms to exclude', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Leave empty to exclude none.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_type_product_tag_terms_to_exclude',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $this->get_terms( 'product_tag' ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_type_product_tag_options',
				),
			);

			$max_options = array(
				array(
					'title' => __( '"Maximum" amount limit options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					/* translators: %s: amount type name */
					'desc'  => sprintf( __( 'Extra settings for "%s" limit options.', 'order-minimum-amount-for-woocommerce' ), __( 'Maximum', 'order-minimum-amount-for-woocommerce' ) ),
					'id'    => 'alg_wc_oma_type_max_options',
				),
				array(
					'title'   => __( 'Validate on add to cart', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Validate max amount on add to cart', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_max_validate_on_add_to_cart',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'    => __( 'Hide add to cart button on loop pages', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Hide on archives / loop', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_max_hide_add_to_cart_loop',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Hide add to cart button on single product page', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Hide on single product page', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_max_hide_add_to_cart_single',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Hide add to cart loop content', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Content to replace the "add to cart" button on loop pages.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_max_hide_add_to_cart_loop_content',
					'default'  => '',
					'type'     => 'textarea',
				),
				array(
					'title'    => __( 'Hide add to cart single content', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Content to replace the "add to cart" button on single product page.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_max_hide_add_to_cart_single_content',
					'default'  => '',
					'type'     => 'textarea',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_type_max_options',
				),
			);

			$qty_options = array(
				array(
					'title' => __( '"Quantity" amount type options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					/* translators: %s: amount type name */
					'desc'  => sprintf( __( 'Extra settings for "%s" amount type options.', 'order-minimum-amount-for-woocommerce' ), __( 'Quantity', 'order-minimum-amount-for-woocommerce' ) ),
					'id'    => 'alg_wc_oma_type_qty_options',
				),
				array(
					'title'             => __( 'Quantity input', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => sprintf(
						/* translators: %s: option name */
						__( 'Set default quantity based on %s minimum limits', 'order-minimum-amount-for-woocommerce' ),
						'"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"'
					),
					'id'                => 'alg_wc_oma_default_qty_input_based_on_per_product_min',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => sprintf(
						/* translators: %s: option name */
						__( 'Set minimum parameter based on %s minimum limits', 'order-minimum-amount-for-woocommerce' ),
						'"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"'
					),
					'id'                => 'alg_wc_oma_min_qty_input_based_on_per_product_min',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => sprintf(
						/* translators: %s: option name */
						__( 'Set maximum parameter based on %s maximum limits', 'order-minimum-amount-for-woocommerce' ),
						'"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"'
					),
					'id'                => 'alg_wc_oma_max_qty_input_based_on_per_product_max',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Add to cart button', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => sprintf(
						/* translators: %s: option name */
						__( 'Set quantity on loop pages based on %s minimum limits', 'order-minimum-amount-for-woocommerce' ),
						'"' . __( 'Per product', 'order-minimum-amount-for-woocommerce' ) . '"'
					),
					'id'                => 'alg_wc_oma_loop_add_to_cart_set_qty_from_min',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_type_qty_options',
				),
			);

			$rest_api_opts = array(
				array(
					'title' => __( 'REST API', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Add user metas to the REST API', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_rest_api_options',
				),
				array(
					'title'             => __( 'User metas', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => $rest_api_desc_tip,
					'id'                => 'alg_wc_oma_rest_api_user_metas_enabled',
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
				$settings,
				$checkout_options,
				$sum_amount_type_options,
				$product_cat_options,
				$product_tag_options,
				$max_options,
				$qty_options,
				$rest_api_opts
			);
		}

		/**
		 * get_terms.
		 *
		 * @version 4.7.4
		 * @since   4.0.0
		 */
		function get_terms( $taxonomy ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			) );
			$options = array();
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$options[ $term->term_id ] = $term->name;
				}
			}
			return $options;
		}

	}

endif;

return new Alg_WC_OMA_Settings_General();