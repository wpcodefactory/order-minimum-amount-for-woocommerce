<?php
/**
 * Order Minimum Amount for WooCommerce - Shipping Section Settings.
 *
 * @version 4.6.6
 * @since   3.2.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Shipping' ) ) :

	class Alg_WC_OMA_Settings_Shipping extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function __construct() {
			$this->id   = 'shipping';
			$this->desc = __( 'Shipping', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_settings.
		 *
		 * @version 4.6.6
		 * @since   3.2.0
		 */
		function get_settings() {

			$shipping_type    = get_option( 'alg_wc_oma_by_shipping_type', 'method' );
			$shipping_options = alg_wc_oma()->core->get_shipping_options( $shipping_type );

			$settings = array(
				array(
					'title' => __( 'Shipping', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Optional amounts per shipping method/instance/zone.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_by_shipping_options',
				),
				array(
					'title'             => __( 'Amount per shipping', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_by_shipping_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'    => __( 'Type', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => $this->get_save_changes_desc(),
					'type'     => 'select',
					'class'    => 'chosen_select',
					'id'       => 'alg_wc_oma_by_shipping_type',
					'default'  => 'method',
					'options'  => array(
						'method'   => __( 'Per shipping method', 'order-minimum-amount-for-woocommerce' ),
						'instance' => __( 'Per shipping instance', 'order-minimum-amount-for-woocommerce' ),
						'zone'     => __( 'Per shipping zone', 'order-minimum-amount-for-woocommerce' ),
					),
				),
				array(
					'title'    => __( 'Shipping messages', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'This will enable separate messages for shipping in %s section.', 'order-minimum-amount-for-woocommerce' ),
						$this->get_section_link( 'messages' ) ),
					'id'       => 'alg_wc_oma_by_shipping_messages_enabled',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Hide unavailable', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Hide', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Will hide unavailable shipping methods.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					              __( 'Please note that this option will take into account results from other plugin sections (e.g. "User Roles", etc.) as well.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_by_shipping_hide',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
			);
			$settings = array_merge( $settings, array(
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_by_shipping_options',
				),
			) );

			if ( 'zone' != $shipping_type ) {
				$settings = array_merge( $settings, array(
					array(
						'title' => __( 'Special cases', 'order-minimum-amount-for-woocommerce' ),
						'type'  => 'title',
						'desc'  => __( 'If you are experiencing issues with some non-standard shipping method, you may need to set it up here.', 'order-minimum-amount-for-woocommerce' ),
						'id'    => 'alg_wc_oma_by_shipping_advanced',
					),
					array(
						'title'    => __( 'Identification', 'order-minimum-amount-for-woocommerce' ),
						'desc'     => sprintf( __( 'Find special cases by splitting current method until the %s character', 'order-minimum-amount-for-woocommerce' ), '<code>:</code>' ),
						'desc_tip' => __( 'Methods splitted in parts different than 2 will be considered special cases.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_by_shipping_sc_find_by_colon_splitting',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'   => __( 'Comparison method', 'order-minimum-amount-for-woocommerce' ),
						'id'      => 'alg_wc_oma_by_shipping_sc_comparison_method',
						'options' => array(
							'comp_method_1' => __( 'Method 1: Compares special case ID with the selected method rate ID until the "_" character.', 'order-minimum-amount-for-woocommerce' ),
							'comp_method_2' => __( 'Method 2: Checks if selected method rate ID contains the special case ID.', 'order-minimum-amount-for-woocommerce' ),
						),
						'default' => 'comp_method_1',
						'type'    => 'radio',
					),
					array(
						'title'    => __( 'Shipping IDs', 'order-minimum-amount-for-woocommerce' ),
						'desc'     => sprintf( __( 'Set as comma-separated list of shipping method IDs, e.g.: %s', 'order-minimum-amount-for-woocommerce' ),
							'<code>flexible_shipping,jem_table_rate</code>' ),
						'desc_tip' => __( 'Shipping IDs from special cases.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
						              __( 'Leave empty if unsure.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => 'alg_wc_oma_by_shipping_special_cases',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'desc'    => sprintf( __( 'Try to autodetect Shipping IDs from %s', 'order-minimum-amount-for-woocommerce' ),
							'<code>WC_Shipping::load_shipping_methods()</code>' ),
						'id'      => 'alg_wc_oma_by_shipping_sc_auto_detect',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_by_shipping_advanced',
					),
				) );
			}

			foreach ( $shipping_options as $id => $title ) {
				$settings = array_merge( $settings, array(
					array(
						'title' => $title,
						'type'  => 'title',
						'id'    => 'alg_wc_oma_by_shipping_' . $id,
					),
				) );
				foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
					foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
						$settings = array_merge( $settings, array(
							array(
								'title'             => alg_wc_oma()->core->get_title( $min_or_max, $amount_type ),
								'desc_tip'          => alg_wc_oma()->core->amounts->get_unit( $amount_type ),
								'id'                => "alg_wc_oma_{$min_or_max}_{$amount_type}_by_shipping_{$shipping_type}[{$id}]",
								'default'           => 0,
								'type'              => apply_filters( 'alg_wc_oma_amount_input_type', 'number', 'shipping' ),
								'custom_attributes' => alg_wc_oma()->core->get_amount_custom_atts(),
							),
						) );
					}
				}
				$settings = array_merge( $settings, array(
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_oma_by_shipping_' . $id,
					),
				) );
			}

			$notes = array(
				array(
					'title' => __( 'Good to know', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => $this->section_notes( array(
						alg_wc_oma()->core->get_amounts_desc(),
						__( 'If <strong>checkout </strong> notices aren\'t updating automatically without a page reload,', 'order-minimum-amount-for-woocommerce' ) . ' ' .
						__( 'you could try these solutions:', 'order-minimum-amount-for-woocommerce' ) . ' ' .
						'<ol>' .
						'<li>' .
						sprintf( __( 'Enable "%s" option in %s section.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Block checkout page', 'order-minimum-amount-for-woocommerce' ), $this->get_section_link( 'general' ) ) . ' ' .
						__( 'This way, the customer will never reach the checkout page, and instead he will be seeing the notices on the cart page (which are working normally).', 'order-minimum-amount-for-woocommerce' ) .
						'</li>' .
						'<li>' .
						sprintf( __( 'Disable "%s" option in %s section.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Checkout notices', 'order-minimum-amount-for-woocommerce' ), $this->get_section_link( 'messages' ) ) . ' ' .
						__( 'This way, if order amount will be wrong, the customer will see the correct notice when he will click "Place order" button.', 'order-minimum-amount-for-woocommerce' ) . '<br>' .
						sprintf( __( 'You can also optionally set "%s" option there to e.g. "%s" or any other position that is updated automatically when user changes shipping method or zone.', 'order-minimum-amount-for-woocommerce' ),
							__( 'Additional positions', 'order-minimum-amount-for-woocommerce' ),
							__( 'Order review: Payment: Before submit button', 'order-minimum-amount-for-woocommerce' ) ) .
						'</li>' .
						'</ol>',
						sprintf( __( 'You\'ll probably want to keep %s options (in %s section) disabled, so your customer would have a chance to change shipping method on exceeded amounts.', 'order-minimum-amount-for-woocommerce' ),
							'<strong>' . implode( '</strong>, <strong>', array(
								__( 'Validate on add to cart', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
								__( 'Hide "add to cart" button', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
							) ) . '</strong>',
							$this->get_section_link( 'general' ) ),
					) ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_shipping_notes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_shipping_notes',
				),
			);

			return array_merge( $settings, $this->get_priority_options( 'alg_wc_oma_by_shipping_priority', 30 ), $notes );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Shipping();
