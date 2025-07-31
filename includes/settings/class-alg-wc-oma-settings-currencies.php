<?php
/**
 * Order Minimum Amount for WooCommerce - Currencies Section Settings.
 *
 * @version 4.6.6
 * @since   3.1.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Currencies' ) ) :

	class Alg_WC_OMA_Settings_Currencies extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 */
		function __construct() {
			$this->id   = 'currencies';
			$this->desc = __( 'Currencies', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.6.6
		 * @since   3.1.0
		 */
		function get_settings() {

			$all_currencies = get_woocommerce_currencies();
			$currencies     = get_option( 'alg_wc_oma_currencies', array() );

			$settings = array(
				array(
					'title' => __( 'Currencies', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Set different amounts for different currencies (i.e. multi-currency).', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					           __( 'For example, this is useful if you are using some currency switcher plugin on your site.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					           __( 'Usually this is used for min/max "sum" amounts, however, you can set other amounts (e.g. "quantity") by currency as well.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_by_currency_options',
				),
				array(
					'title'             => __( 'Amount by currency', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_by_currency_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'   => __( 'Section overriding', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => sprintf( __( 'Using %s in amounts from other sections will prevent this section from overriding them', 'order-minimum-amount-for-woocommerce' ), '<code>-1</code>' ),
					'id'      => 'alg_wc_oma_currencies_prevent_overriding_minusone_values_sections',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Calculation method', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_currencies_calculation_method',
					'default' => 'amount_types',
					'type'    => 'select',
					'class'   => 'chosen_select',
					'options' => array(
						'amount_types'   => __( 'Amount types per currency', 'order-minimum-amount-for-woocommerce' ),
						'exchange_rates' => __( 'Exchange rates', 'order-minimum-amount-for-woocommerce' ),
					)
				),
				array(
					'title'    => __( 'Currencies', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( '"Save changes" after you add new currencies here - new settings fields will be displayed.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_currencies',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $all_currencies,
				),
			);

			if ( 'amount_types' === get_option( 'alg_wc_oma_currencies_calculation_method', 'amount_types' ) ) {
				$settings = array_merge( $settings, array(
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_by_currency_options',
					)
				) );
				foreach ( $currencies as $currency ) {
					$settings = array_merge( $settings, array(
						array(
							'title' => ( isset( $all_currencies[ $currency ] ) ? $all_currencies[ $currency ] : $currency ),
							'type'  => 'title',
							'id'    => "alg_wc_oma_by_currency_{$currency}",
						),
					) );
					foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
						foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
							$settings = array_merge( $settings, array(
								array(
									'title'             => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
									'desc_tip'          => alg_wc_oma()->core->amounts->get_unit( $amount_type, $currency ),
									'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_currency[{$currency}]",
									'default'           => 0,
									'type'              => apply_filters( 'alg_wc_oma_amount_input_type', 'number', 'currencies' ),
									'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
								),
							) );
						}
					}
					$settings = array_merge( $settings, array(
						array(
							'type' => 'sectionend',
							'id'   => "alg_wc_oma_by_currency_{$currency}",
						),
					) );
				}
			} elseif ( 'exchange_rates' === get_option( 'alg_wc_oma_currencies_calculation_method', 'amount_types' ) ) {
				$wc_currency = get_option( 'woocommerce_currency' );
				foreach ( $currencies as $currency ) {
					$pair     = $wc_currency . $currency;
					$settings = array_merge( $settings, array(
						array(
							'title'             => $pair,
							'type'              => 'number',
							'id'                => "alg_wc_oma_exchange_rates[{$pair}]",
							'default'           => 1,
							'custom_attributes' => array( 'step' => '0.000001' ),
						),
					) );
				}
				$settings = array_merge( $settings, array(
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_oma_by_currency_{$currency}",
					),
				) );
			}

			$notes = array(
				array(
					'title' => __( 'Good to know', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => $this->section_notes( array( alg_wc_oma()->core->get_amounts_desc() ) ),
					'type'  => 'title',
					'id'    => "alg_wc_oma_{$this->id}_notes",
				),
				array(
					'type' => 'sectionend',
					'id'   => "alg_wc_oma_{$this->id}_notes",
				),
			);

			return array_merge( $settings, $notes );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Currencies();
