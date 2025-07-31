<?php
/**
 * Order Minimum Amount for WooCommerce - Messages Section Settings.
 *
 * @version 4.6.6
 * @since   1.2.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Messages' ) ) :

	class Alg_WC_OMA_Settings_Messages extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.2.0
		 * @since   1.2.0
		 */
		function __construct() {
			$this->id   = 'messages';
			$this->desc = __( 'Messages', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_scope_title.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 */
		function get_scope_title( $scope ) {
			switch ( $scope ) {
				case 'product':
					return __( 'Per product', 'order-minimum-amount-for-woocommerce' );
				case 'product_cat':
					return __( 'Per product category', 'order-minimum-amount-for-woocommerce' );
				case 'product_tag':
					return __( 'Per product tag', 'order-minimum-amount-for-woocommerce' );
				default:
					return '';
			}
		}

		/**
		 * get_source_title.
		 *
		 * @version 4.0.0
		 * @since   3.3.0
		 */
		function get_source_title( $source ) {
			switch ( $source ) {
				case 'shipping':
					return __( 'Shipping', 'order-minimum-amount-for-woocommerce' );
				case 'gateway':
					return __( 'Payment Gateways', 'order-minimum-amount-for-woocommerce' );
				default:
					return '';
			}
		}

		/**
		 * get_notice_type_setting.
		 *
		 * @version 4.0.4
		 * @since   3.3.0
		 *
		 * @param $area
		 * @param $area_settings
		 *
		 * @return array|null
		 */
		function get_notice_type_setting( $area, $area_settings ) {
			$notice_type_setting = null;
			if ( isset( $area_settings['default_notice_type'] ) ) {
				$notice_type_setting = array(
					'title'    => __( 'Notice type', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Styling.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => "alg_wc_oma_{$area}_notice_type",
					'default'  => $area_settings['default_notice_type'],
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'options'  => array(
						'error'  => __( 'Error', 'order-minimum-amount-for-woocommerce' ),
						'notice' => __( 'Notice', 'order-minimum-amount-for-woocommerce' ),
					),
				);
			}
			return $notice_type_setting;
		}

		/**
		 * add_unique_settings.
		 *
		 * @version 4.6.6
		 * @since   4.0.5
		 *
		 * @param $area
		 * @param $dynamic_settings
		 *
		 * @return array
		 */
		function add_unique_settings( $area, $dynamic_settings ) {
			if ( 'product_page' == $area ) {
				$dynamic_settings = array_merge( $dynamic_settings, array(
					array(
						'title'             => __( 'Smart product scope', 'order-minimum-amount-for-woocommerce' ),
						'desc'              => __( 'Show only product scope messages relevant to the current product', 'order-minimum-amount-for-woocommerce' ),
						'id'                => "alg_wc_oma_{$area}_notice_smart_product_scope",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
					),
				) );
			} elseif ( 'checkout' == $area ) {
				$dynamic_settings = array_merge( $dynamic_settings, array(
					array(
						'title'    => __( 'Force refresh', 'order-minimum-amount-for-woocommerce' ),
						'desc'     => __( 'Refresh the notice if something changes at checkout form', 'order-minimum-amount-for-woocommerce' ),
						'desc_tip' => __( 'Enable if the notice is not getting updated, for example after shipping changes.', 'order-minimum-amount-for-woocommerce' ),
						'id'       => "alg_wc_oma_{$area}_force_refresh",
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'desc'    => __( 'Hook used to refresh the notice.', 'order-minimum-amount-for-woocommerce' ),
						'id'      => "alg_wc_oma_{$area}_force_refresh_hook",
						'default' => 'no',
						'class'   => 'woocommerce_review_order_after_order_total',
						'options' => array(
							'woocommerce_review_order_after_order_total' => __( 'woocommerce_review_order_after_order_total', 'order-minimum-amount-for-woocommerce' ),
							'woocommerce_review_order_before_submit'     => __( 'woocommerce_review_order_before_submit', 'order-minimum-amount-for-woocommerce' ),
						),
						'type'    => 'select',
					),
				) );
			}
			return $dynamic_settings;
		}

		/**
		 * get_settings.
		 *
		 * @version 4.4.0
		 * @since   1.2.0
		 *
		 * @todo    add optional "Message on requirements met"
		 * @todo    deprecate "Checkout" messages, i.e. use "Cart" messages everywhere?
		 */
		function get_settings() {

			$header = array(
				array(
					'title' => __( 'Messages', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => __( 'Messages informing users about minimum and maximum requirements.', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_message_header',
				),
				array(
					'title'    => __( 'Display on empty cart', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Display messages even if cart is empty', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'This option will probably make more sense if the product page notice is enabled.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'checkbox',
					'default'  => 'no',
					'id'       => 'alg_wc_oma_display_messages_on_empty_cart',
				),
				array(
					'title'    => __( 'Multiple messages', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Display one message for each unmet requirement', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Disable to display only a single message for the first unmet requirement.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'checkbox',
					'default'  => 'yes',
					'id'       => 'alg_wc_oma_display_multiple_msg',
				),
				array(
					'title'    => __( 'Force display', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Always display messages, regardless of the requirements', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'checkbox',
					'default'  => 'no',
					'id'       => 'alg_wc_oma_msg_force_display',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_message_header',
				),
			);

			$dynamic_settings      = array();
			$dynamic_settings_info = alg_wc_oma()->core->messages->get_messages_info();
			foreach ( $dynamic_settings_info as $area => $area_settings ) {
				$title            = $area_settings['title'];
				$dynamic_settings = array_merge( $dynamic_settings, array(
					array(
						'title' => $title,
						'type'  => 'title',
						'id'    => "alg_wc_oma_message_content_{$area}_options",
					),
					array(
						'title'   => sprintf( __( '%s notices', 'order-minimum-amount-for-woocommerce' ), $title ),
						'desc'    => '<strong>' . __( 'Enable', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
						'id'      => "alg_wc_oma_{$area}_notice_enabled",
						'default' => 'no',
						'type'    => 'checkbox',
					),
					$this->get_notice_type_setting( $area, $area_settings ),
					array(
						'title'   => __( 'Positions', 'order-minimum-amount-for-woocommerce' ),
						//'id'      => "alg_wc_oma_message_positions_{$setting_key}",
						'id'      => "alg_wc_oma_{$area}_area_message_positions",
						'default' => alg_wc_oma()->core->messages->get_message_default_positions( $area ),
						'type'    => 'multiselect',
						'class'   => 'chosen_select',
						'options' => $area_settings['positions']
					),
				) );

				foreach ( alg_wc_oma()->core->get_enabled_amount_limits() as $min_or_max ) {
					foreach ( alg_wc_oma()->core->get_enabled_amount_types() as $amount_type ) {
						foreach ( apply_filters( 'alg_wc_oma_enabled_scopes', array( '' ) ) as $scope ) {
							foreach ( apply_filters( 'alg_wc_oma_enabled_message_sources', array( '' ) ) as $source ) {
								if ( '' != $scope && '' != $source ) {
									continue;
								}
								$id               = alg_wc_oma()->core->messages->get_message_option_id( $area, $scope, $source );
								$dynamic_settings = array_merge( $dynamic_settings, array(
									array(
										'title'          => alg_wc_oma()->core->get_title( $min_or_max, $amount_type, array( $this->get_scope_title( $scope ), $this->get_source_title( $source ) ), true ),
										'id'             => "alg_wc_oma_{$min_or_max}_{$amount_type}_message[{$id}]",
										'default'        => alg_wc_oma()->core->messages->get_default_message( $min_or_max, $scope, $source, $amount_type ),
										'type'           => 'textarea',
										'css'            => 'width:100%;',
										'alg_wc_oma_raw' => true,
									),
								) );
							}
						}
					}
				}
				$dynamic_settings = $this->add_unique_settings( $area, $dynamic_settings );
				$dynamic_settings = array_merge( $dynamic_settings, array(
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_oma_message_content_{$area}_options",
					),
				) );
			}

			$advanced_settings = array(
				array(
					'title' => __( 'Advanced Options', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_message_advanced_options',
				),
				array(
					'title'    => __( 'Format amounts', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Enable', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Affects %s placeholders.', 'order-minimum-amount-for-woocommerce' ),
						'<code>' . implode( '</code>, <code>', array( '%amount%', '%total%', '%diff%' ) ) . '</code>' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_oma_message_format_types_enabled',
					'default'  => 'yes',
				),
				array(
					'desc_tip' => __( 'Choose which amount types should be formatted in messages. E.g. it will add "pcs" to the "Quantity" amounts, or it will round and add currency symbol to the "Sum" amounts.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					              __( 'Leave empty to format all amount types.', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => alg_wc_oma()->core->amounts->get_types(),
					'id'       => 'alg_wc_oma_message_format_types',
					'default'  => array(),
				),
				array(
					'title'    => __( 'Remove old notices', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Will remove old WooCommerce notices on AJAX add to cart.', 'order-minimum-amount-for-woocommerce' ) . ' ' .
					              sprintf( __( 'This is useful if you have checked "%s" option in %s and there are cross-sells products available on the cart page.', 'order-minimum-amount-for-woocommerce' ),
						              __( 'Enable AJAX add to cart buttons on archives', 'order-minimum-amount-for-woocommerce' ),
						              '<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=products' ) . '">' .
						              __( 'WooCommerce > Settings > Products', 'order-minimum-amount-for-woocommerce' ) .
						              '</a>' ),
					'desc'     => __( 'Remove', 'order-minimum-amount-for-woocommerce' ),
					'type'     => 'checkbox',
					'id'       => 'alg_wc_oma_remove_notices_on_added_to_cart',
					'default'  => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_message_advanced_options',
				),
			);

			$notes        = array();
			$placeholders = array( '%amount%', '%total%', '%diff%', '%amount_raw%', '%total_raw%', '%diff_raw%' );
			$placeholders = 'yes' === get_option( 'alg_wc_oma_add_fee_for_each_limit', 'no' ) ? array_merge( $placeholders, array( '%fee_amount%' ) ) : $placeholders;
			$notes[]      = sprintf( __( 'Available placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
				'<div style="padding: 0px 0px 15px;">' .
				'<code>' . implode( '</code>, <code>', $placeholders ) . '</code>' .
				'</div>' );

			if ( array() != apply_filters( 'alg_wc_oma_enabled_scopes', array() ) ) {
				$notes[] = sprintf( __( 'For "Per product" you can also use these additional placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
					'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%product_title%' ) ) . '</code>' .
					'</div>' );

				$notes[] = sprintf( __( 'For "Per product category" and "Per product tag" messages you can also use these additional placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
					'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%term_title%', '%term_link%', '%term_title_with_link%' ) ) . '</code>' .
					'</div>' );
			}

			if ( 'yes' === get_option( 'alg_wc_oma_by_shipping_enabled', 'no' ) && 'yes' === get_option( 'alg_wc_oma_by_shipping_messages_enabled', 'no' ) ) {
				$notes[] = sprintf( __( 'For "Shipping" messages you can also use these additional placeholders: %s', 'order-minimum-amount-for-woocommerce' ),
					'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%shipping_method%', '%shipping_zone%', '%shipping_zone_locations%' ) ) . '</code>' .
					'</div>' );
			}

			if ( 'yes' === get_option( 'alg_wc_oma_by_gateway_enabled', 'no' ) && 'yes' === get_option( 'alg_wc_oma_by_gateway_messages_enabled', 'no' ) ) {
				$notes[] = sprintf( __( 'For "Payment Gateways" messages you can also use this additional placeholder: %s', 'order-minimum-amount-for-woocommerce' ),
					'<div style="padding: 0 0 15px;">' .
					'<code>' . implode( '</code>, <code>', array( '%payment_gateway%' ) ) . '</code>' .
					'</div>' );
			}

			$notes[] = __( 'Identical messages will be filtered, i.e. only one of them will be shown on the frontend.', 'order-minimum-amount-for-woocommerce' );

			$notes[] = __( 'You can use HTML in the messages.', 'order-minimum-amount-for-woocommerce' );

			$notes[] = sprintf( __( 'You can also use shortcodes in the messages, for example, for WPML/Polylang translations: %s', 'order-minimum-amount-for-woocommerce' ),
				'<br><pre style="background-color: #E0E0E0; padding: 15px; margin-bottom:0">' .
				'[alg_wc_oma_translate lang="DE"]Text for DE[/alg_wc_oma_translate]' .
				'[alg_wc_oma_translate lang="NL"]Text for NL[/alg_wc_oma_translate]' .
				'[alg_wc_oma_translate not_lang="DE,NL"]Text for other languages[/alg_wc_oma_translate]' .
				'</pre>' );

			$notes_settings = array(
				array(
					'title' => __( 'Good to know', 'order-minimum-amount-for-woocommerce' ),
					'desc'  => $this->section_notes( $notes ),
					'type'  => 'title',
					'id'    => 'alg_wc_oma_message_notes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_message_notes',
				),
			);

			return array_merge( $header, $dynamic_settings, $advanced_settings, $notes_settings );
		}

	}

endif;

return new Alg_WC_OMA_Settings_Messages();
