<?php
/**
 * Order Minimum Amount for WooCommerce - Amounts Section Settings.
 *
 * @version 4.1.4
 * @since   3.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Amounts' ) ) :

	class Alg_WC_OMA_Settings_Amounts extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
		 */
		function __construct() {
			$this->id   = 'amounts';
			$this->desc = __( 'Amounts', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.1.4
		 * @since   3.0.0
		 */
		function get_settings() {
			$settings = array(
				array(
					'title' => __( 'Amounts', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => __( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_amounts_options',
				),
			);
			foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
					$settings = array_merge( $settings, array(
						array(
							'title'             => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
							'desc_tip'          => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
							'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}",
							'default'           => 0,
							'type'              => apply_filters( 'alg_wc_oma_amount_input_type', 'number', 'amounts' ),
							'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts( 0 ),
						),
					) );
				}
			}
			$settings       = array_merge( $settings, array(
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_amounts_options',
				),
			) );
			$shortcode_opts = array(
				array(
					'title' => __( 'Amount shortcodes', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'It\'s possible to set multiple shortcodes on the same amount input. In that case, the first one with a non-empty result will be used.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_amounts_shortcode_options',
				),
				array(
					'title'             => __( 'Shortcodes', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Allow to add shortcodes to the amounts', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => __( 'The amount input type will be changed from "number" to "text".', 'order-minimum-amount-for-woocommerce' ),
					'type'              => 'checkbox',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					'id'                => 'alg_wc_oma_amounts_shortcodes_allowed',
				),
				array(
					'title'             => __( '[alg_wc_oma_amount]', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => __( 'Create the <code>[alg_wc_oma_amount]</code> shortcode', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip'          => __( 'For now, it allows to set different values for different customer types.', 'order-minimum-amount-for-woocommerce' ) . '<br />' .
					                       sprintf( __( 'More <a href="%s" target="_blank">info</a>.', 'order-minimum-amount-for-woocommerce' ), 'https://wpfactory.com/docs/order-min-max/shortcodes/understanding-placeholders/#1-algwcomaamount' ) . '<br />',
					'type'              => 'checkbox',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					'id'                => 'alg_wc_oma_amounts_alg_wc_oma_amount_enabled',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_amounts_shortcode_options',
				),
			);
			return array_merge( $settings, $shortcode_opts );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Amounts();
