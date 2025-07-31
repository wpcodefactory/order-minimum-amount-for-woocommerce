<?php
/**
 * Order Minimum Amount for WooCommerce - Memberships Section Settings.
 *
 * @version 4.6.6
 * @since   3.4.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Memberships' ) ) :

	class Alg_WC_OMA_Settings_Memberships extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 3.4.0
		 * @since   3.4.0
		 */
		function __construct() {
			$this->id   = 'memberships';
			$this->desc = __( 'Memberships', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.6.6
		 * @since   3.4.0
		 */
		function get_settings() {

			$settings = array(
				array(
					'title' => __( 'Memberships', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Optional amounts per membership.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					           sprintf( __( 'Compatible with %s, %s and %s plugins.', 'order-minimum-amount-for-woocommerce' ),
						           '<a href="https://woocommerce.com/products/woocommerce-memberships/" target="_blank">' .
						           __( 'WooCommerce Memberships', 'order-minimum-amount-for-woocommerce' ) . '</a>',
						           '<a href="https://memberpress.com/" target="_blank">' .
						           __( 'MemberPress', 'order-minimum-amount-for-woocommerce' ) . '</a>',
						           '<a href="https://fantasticplugins.com/shop/sumo-memberships/" target="_blank">' .
						           __( 'SUMO Memberships', 'order-minimum-amount-for-woocommerce' ) . '</a>' ),
					'id'    => 'alg_wc_oma_by_membership_options',
				),
				array(
					'title'             => __( 'Amount per membership', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_by_membership_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_by_membership_options',
				),
			);

			$memberships = alg_wc_oma()->core->get_memberships();
			if ( ! empty( $memberships ) ) {
				foreach ( $memberships as $membership_id => $membership ) {
					$settings = array_merge( $settings, array(
						array(
							'title' => $membership['title'],
							'type'  => 'title',
							'id'    => 'alg_wc_oma_by_membership_' . $membership_id,
						),
					) );
					foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
						foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
							$settings = array_merge( $settings, array(
								array(
									'title'             => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
									'desc_tip'          => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
									'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_membership[{$membership_id}]",
									'default'           => 0,
									'type'              => apply_filters( 'alg_wc_oma_amount_input_type', 'number', 'memberships' ),
									'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
								),
							) );
						}
					}
					$settings = array_merge( $settings, array(
						array(
							'type' => 'sectionend',
							'id'   => 'alg_wc_oma_by_membership_' . $membership_id,
						),
					) );
				}
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

			return array_merge( $settings, $this->get_priority_options( 'alg_wc_oma_by_membership_priority', 50 ), $notes );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Memberships();
