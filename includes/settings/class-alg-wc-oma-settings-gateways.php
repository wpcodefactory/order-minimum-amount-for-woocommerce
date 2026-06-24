<?php
/**
 * Order Minimum Amount for WooCommerce - Gateways Section Settings.
 *
 * @version 4.7.4
 * @since   4.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Gateways' ) ) :

	class Alg_WC_OMA_Settings_Gateways extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function __construct() {
			$this->id   = 'gateways';
			$this->desc = __( 'Payment Gateways', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.7.4
		 * @since   4.0.0
		 */
		function get_settings() {
			/* translators: %s: section link */
			$messages_link_text = sprintf( __( 'This will enable separate messages for payment gateways in %s section.', 'order-minimum-amount-for-woocommerce' ), $this->get_section_link( 'messages' ) );

			// Build hide gateways tip text.
			/* translators: %1$s: option name, %2$s: section link */
			$hide_disable_text = sprintf( __( 'we suggest disabling the "%1$s" option in %2$s section.', 'order-minimum-amount-for-woocommerce' ), __( 'Checkout notices', 'order-minimum-amount-for-woocommerce' ), $this->get_section_link( 'messages' ) );
			/* translators: %1$s: option name, %2$s: example position */
			$hide_optional_text = sprintf( __( 'You can also optionally set "%1$s" option there to e.g. "%2$s" or any other position that is updated automatically when user changes payment gateway.', 'order-minimum-amount-for-woocommerce' ), __( 'Additional positions', 'order-minimum-amount-for-woocommerce' ), __( 'Order review: Payment: Before submit button', 'order-minimum-amount-for-woocommerce' ) );
			/* translators: %1$s: option list, %2$s: section link */
			$hide_keep_text = sprintf( __( 'You\'ll probably want to keep %1$s options (in %2$s section) disabled, so your customer would have a chance to change payment gateway on wrong amounts.', 'order-minimum-amount-for-woocommerce' ), '<strong>' . implode( '</strong>, <strong>', array( __( 'Checkout notices', 'order-minimum-amount-for-woocommerce' ), __( 'Block checkout page', 'order-minimum-amount-for-woocommerce' ) ) ) . '</strong>', $this->get_section_link( 'general' ) );
			$hide_gateway_tip = __( 'If <strong>checkout </strong> notices aren\'t updating automatically without a page reload,', 'order-minimum-amount-for-woocommerce' ) . ' ' . $hide_disable_text . ' ' . __( 'This way, if order amount will be wrong, the customer will see the correct notice when he will click "Place order" button.', 'order-minimum-amount-for-woocommerce' ) . ' ' . $hide_optional_text . ' ' . $hide_keep_text;

			$settings = array(
				array(
					'title' => __( 'Payment Gateways', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Optional amounts per payment gateway.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_by_gateway_options',
				),
				array(
					'title'             => __( 'Amount per gateway', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_by_gateway_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'    => __( 'Messages', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable separate messages for payment gateways', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => $messages_link_text,
					'id'       => 'alg_wc_oma_by_gateway_messages_enabled',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Hide', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Hide payment gateways on exceeded amounts', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => $hide_gateway_tip,
					'id'       => 'alg_wc_oma_by_gateway_hide',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_by_gateway_options',
				),
			);

			$gateways = WC()->payment_gateways->payment_gateways();
			foreach ( $gateways as $key => $gateway ) {
				$settings = array_merge( $settings, array(
					array(
						'title' => $gateway->title,
						'type'  => 'title',
						'id'    => "alg_wc_oma_by_gateway_{$key}",
					),
				) );
				foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
					foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
						$settings = array_merge( $settings, array(
							array(
								'title'             => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
								'desc_tip'          => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
								'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_gateway[{$key}]",
								'default'           => 0,
								'type'              => apply_filters( 'alg_wc_oma_amount_input_type', 'number', 'gateways' ),
								'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
							),
						) );
					}
				}
				$settings = array_merge( $settings, array(
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_oma_by_gateway_{$key}",
					),
				) );
			}

			$notes_settings = array(
				array(
					'title' => __( 'Good to Know', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => $this->section_notes( array( alg_wc_oma()->core->get_amounts_desc() ) ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_gateways_notes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_gateways_notes',
				),
			);

			return array_merge( $settings, $this->get_priority_options( 'alg_wc_oma_by_gateway_priority', 40 ), $notes_settings );
		}
	}

endif;

return new Alg_WC_OMA_Settings_Gateways();