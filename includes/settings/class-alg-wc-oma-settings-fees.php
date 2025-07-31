<?php
/**
 * Order Minimum Amount for WooCommerce - Fees Settings.
 *
 * @version 4.6.6
 * @since   4.1.1
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Fees' ) ) :

	class Alg_WC_OMA_Settings_Fees extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 4.1.1
		 * @since   4.1.1
		 */
		function __construct() {
			$this->id   = 'fees';
			$this->desc = __( 'Fees', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.6.6
		 * @since   4.1.1
		 */
		function get_settings() {
			$fees_general_opts = array(
				array(
					'title' => __( 'Fees options', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => __( 'Here you can set fees for each limit reached.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					           sprintf( __( 'If you don\'t want to block the orders, you should also <strong>disable</strong> the %s options from the %s settings.', 'order-minimum-amount-for-woocommerce' ), '<strong>"' . __( 'Block checkout', 'order-minimum-amount-for-woocommerce' ) . '"</strong>', '<strong><a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma' ) . '">' . __( 'General > Checkout options', 'order-minimum-amount-for-woocommerce' ) . '</a></strong>' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_fees_options',
				),
				array(
					'title'             => __( 'Fees', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'desc_tip'          => sprintf( __( 'If enabled, there will be a new placeholder available on the <strong>messages</strong> section: %s', 'order-minimum-amount-for-woocommerce' ), '<code>%fee_amount%</code>' ),
					'id'                => 'alg_wc_oma_add_fee_for_each_limit',
					'default'           => 'no',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					'type'              => 'checkbox',
				),
				array(
					'title'    => __( 'Ignore fees for sum calculation', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Ignore fees from this section on cart total calculation for the "Sum" type', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Only noticeable if %s option is disabled.', 'order-minimum-amount-for-woocommerce' ), '"' . __( 'Sum Amount Type options > Exclude fees', 'order-minimum-amount-for-woocommerce' ) . '"' ),
					'id'       => 'alg_wc_oma_ignore_fees_for_sum_type',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Taxes', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Apply tax', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Apply tax to fee.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_apply_tax_to_fee',
					'default'  => wc_tax_enabled() ? 'yes' : 'no',
					'type'     => 'checkbox',
				),
				array(
					'desc'     => __( 'Tax class.', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( '', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_fee_tax_class',
					'default'  => 'standard',
					'type'     => 'text',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_fees_options',
				),
			);

			$fees_opts = array();
			foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
				foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
					if ( 'yes' === get_option( 'alg_wc_oma_add_fee_for_each_limit', 'no' ) ) {
						$fees_opts = array_merge( $fees_opts, array(
							array(
								'title' => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ) . ' ' . __( 'fee', 'order-minimum-amount-for-woocommerce' ),
								'type'  => 'title',
								'id'    => "alg_wc_oma_{$min_or_max}_{$amount_type}_fee_options",
							),
							array(
								'title'             => __( 'Title', 'order-minimum-amount-for-woocommerce' ),
								'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_fee_label",
								'default'           => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ) . ' ' . __( 'fee', 'order-minimum-amount-for-woocommerce' ),
								'type'              => 'text',
								'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts( 0 ),
							),
							array(
								'title'             => __( 'Amount', 'order-minimum-amount-for-woocommerce' ),
								'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_fee",
								'default'           => 0,
								'type'              => 'number',
								'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts( 0 ),
							),
							array(
								'type' => 'sectionend',
								'id'   => "alg_wc_oma_{$min_or_max}_{$amount_type}_fee_options",
							),
						) );
					}
				}
			}

			// Notes.
			$notes[] = __( 'If you don\'t want to block the orders, you should also <strong>disable</strong> the <strong>"Block checkout"</strong> options from the <strong>"General > Checkout options"</strong> settings.', 'order-minimum-amount-for-woocommerce' );
			$notes[] = sprintf( __( 'If the taxes options are enabled, you might want to enable %s on %s section.', 'order-minimum-amount-for-woocommerce' ), '<strong>'.__( 'Exclude fees' ).'</strong>', '<a href="'.admin_url('admin.php?page=wc-settings&tab=alg_wc_oma#alg_wc_oma_type_sum_options-description').'">' . __( '"Sum" Amount Type options' ) . '</a>' );
			$notes[] = sprintf( __( 'If this section is enabled, there will be a new placeholder available on the <strong>messages</strong> section: %s.', 'order-minimum-amount-for-woocommerce' ), '<code>%fee_amount%</code>' );
			$notes[] = __( 'If you want to disable some fee, set the amount as zero or leave it empty.', 'order-minimum-amount-for-woocommerce' );

			$notes_settings = array(
				array(
					'title' => __( 'Good to Know', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => $this->section_notes( $notes ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_fees_notes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_fees_notes',
				),
			);

			return array_merge( $fees_general_opts, $fees_opts, $notes_settings );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Fees();
