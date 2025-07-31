<?php
/**
 * Order Minimum Amount for WooCommerce - Users Section Settings.
 *
 * @version 4.6.6
 * @since   2.1.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Users' ) ) :

	class Alg_WC_OMA_Settings_Users extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 */
		function __construct() {
			$this->id   = 'users';
			$this->desc = __( 'Users', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.6.6
		 * @since   2.1.0
		 */
		function get_settings() {

			$settings = array(
				array(
					'title' => __( 'Users', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Optional amounts per user.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_by_user_options',
				),
				array(
					'title'             => __( 'Amount per user', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'desc_tip'          => __( 'When enabled, you can set amounts per user on each user\'s profile edit page (in "Users > Edit user").', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_by_user_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_by_user_options',
				),
			);

			$guest_fallback_settings = array(
				array(
					'title' => __( 'Guest Fallback', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => __( 'This is used for non-registered users (i.e. guests) as a fallback.', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_by_user_guest_options',
				),
			);
			foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
					$guest_fallback_settings = array_merge( $guest_fallback_settings, array(
						array(
							'title'             => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
							'desc_tip'          => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
							'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_user_guest",
							'default'           => 0,
							'type'              => apply_filters( 'alg_wc_oma_amount_input_type', 'number', 'users' ),
							'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
						),
					) );
				}
			}
			$guest_fallback_settings = array_merge( $guest_fallback_settings, array(
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_by_user_guest_options',
				),
			) );

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

			return array_merge( $settings, $guest_fallback_settings, $this->get_priority_options( 'alg_wc_oma_by_user_priority', 20 ), $notes );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Users();
