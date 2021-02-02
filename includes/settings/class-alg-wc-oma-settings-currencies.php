<?php
/**
 * Order Minimum Amount for WooCommerce - Currencies Section Settings
 *
 * @version 3.2.0
 * @since   3.1.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
	 * @version 3.2.0
	 * @since   3.1.0
	 * @todo    [later] multiplier
	 * @todo    [later] per user role, user etc.
	 * @todo    [maybe] `strtolower( $currency )`?
	 */
	function get_settings() {

		$all_currencies = get_woocommerce_currencies();
		$currencies     = get_option( 'alg_wc_oma_currencies', array() );

		$settings = array(
			array(
				'title'    => __( 'Currencies', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'desc'     => __( 'Set different amounts for different currencies (i.e. multi-currency).', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					alg_wc_oma()->core->get_amounts_desc() . ' ' .
					__( 'For example, this is useful if you are using some currency switcher plugin on your site.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					__( 'Usually this is used for min/max "sum" amounts, however, you can set other amounts (e.g. "quantity") by currency as well.', 'order-minimum-amount-for-woocommerce' ) .
					$this->get_pro_msg( 'set amounts per currency' ),
				'id'       => 'alg_wc_oma_by_currency_options',
			),
			array(
				'title'    => __( 'Amount by currency', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_oma_by_currency_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
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
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_by_currency_options',
			),
		);

		foreach ( $currencies as $currency ) {
			$settings = array_merge( $settings, array(
				array(
					'title'    => ( isset( $all_currencies[ $currency ] ) ? $all_currencies[ $currency ] : $currency ),
					'type'     => 'title',
					'id'       => "alg_wc_oma_by_currency_{$currency}",
				),
			) );
			foreach ( alg_wc_oma()->core->get_enabled_limits() as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_types() as $amount_type ) {
					$settings = array_merge( $settings, array(
						array(
							'title'    => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
							'desc_tip' => alg_wc_oma()->core->amounts->get_unit( $amount_type, $currency ),
							'id'       => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_currency[{$currency}]",
							'default'  => 0,
							'type'     => 'number',
							'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
						),
					) );
				}
			}
			$settings = array_merge( $settings, array(
				array(
					'type'     => 'sectionend',
					'id'       => "alg_wc_oma_by_currency_{$currency}",
				),
			) );
		}

		return $settings;
	}

}

endif;

return new Alg_WC_OMA_Settings_Currencies();
