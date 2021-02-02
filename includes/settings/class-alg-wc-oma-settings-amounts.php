<?php
/**
 * Order Minimum Amount for WooCommerce - Amounts Section Settings
 *
 * @version 3.2.0
 * @since   3.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
	 * @version 3.2.0
	 * @since   3.0.0
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Amounts', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => __( 'Ignored if set to zero.', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_oma_amounts_options',
			),
		);
		foreach ( alg_wc_oma()->core->get_enabled_limits() as $min_or_max ) {
			foreach ( alg_wc_oma()->core->get_enabled_types() as $amount_type ) {
				$settings = array_merge( $settings, array(
					array(
						'title'    => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
						'desc_tip' => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
						'id'       => "alg_wc_oma_{$min_or_max}_{$amount_type}",
						'default'  => 0,
						'type'     => 'number',
						'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts( 0 ),
					),
				) );
			}
		}
		$settings = array_merge( $settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_oma_amounts_options',
			),
		) );
		return $settings;
	}

}

endif;

return new Alg_WC_OMA_Settings_Amounts();
