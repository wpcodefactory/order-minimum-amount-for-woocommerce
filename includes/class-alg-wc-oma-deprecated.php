<?php
/**
 * Order Minimum/Maximum Amount Limits for WooCommerce - Deprecated Options Class.
 *
 * Handles deprecated options, placeholders, etc.
 *
 * @version 4.1.5
 * @since   3.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Deprecated' ) ) :

	class Alg_WC_OMA_Deprecated {

		/**
		 * Constructor.
		 *
		 * @version 4.1.5
		 * @since   3.0.0
		 */
		function __construct() {
			add_action( 'alg_wc_oma_version_updated', array( $this, 'version_updated' ) );
			add_action( 'alg_wc_oma_before_settings_user_roles', array( $this, 'settings_user_roles' ) );
			add_filter( 'shortcode_atts_alg_wc_oma_amount_msg', array( $this, 'shortcode_atts' ), 10, 3 );
			add_filter( 'shortcode_atts_alg_wc_order_min_max_amount', array( $this, 'shortcode_atts' ), 10, 3 );
			add_filter( 'alg_wc_oma_placeholders', array( $this, 'notice_placeholders' ), 10, 6 );
		}

		/**
		 * version_updated.
		 *
		 * @version 3.0.0
		 * @since   2.0.0
		 */
		function version_updated() {
			$options = array(
				'alg_wc_order_minimum_amount_stop_from_seeing_checkout' => 'alg_wc_oma_block_checkout',
				'alg_wc_order_minimum_amount_exclude_shipping'          => 'alg_wc_oma_exclude_shipping',
				'alg_wc_order_minimum_amount_exclude_discounts'         => 'alg_wc_oma_exclude_discounts',
				'alg_wc_order_minimum_amount'                           => 'alg_wc_oma_min_sum',
				'alg_wc_order_minimum_amount_enabled'                   => 'alg_wc_oma_plugin_enabled',
				'alg_wc_order_minimum_amount_checkout_notice_enabled'   => 'alg_wc_oma_checkout_notice_enabled',
				'alg_wc_order_minimum_amount_cart_notice_enabled'       => 'alg_wc_oma_cart_notice_enabled',
				'alg_wc_order_minimum_amount_checkout_notice_type'      => 'alg_wc_oma_checkout_notice_type',
				'alg_wc_order_minimum_amount_cart_notice_type'          => 'alg_wc_oma_cart_notice_type',
			);
			foreach ( $options as $old_option => $new_option ) {
				if ( false === get_option( $new_option, false ) && false !== ( $value = get_option( $old_option, false ) ) ) {
					update_option( $new_option, $value );
					delete_option( $old_option );
				}
			}
			$_val = get_option( 'alg_wc_oma_min_sum_message', array() );
			if ( empty( $_val ) ) {
				if ( false !== ( $value = get_option( 'alg_wc_order_minimum_amount_error_message', false ) ) ) {
					$_val['checkout'] = $value;
					delete_option( 'alg_wc_order_minimum_amount_error_message' );
				}
				if ( false !== ( $value = get_option( 'alg_wc_order_minimum_amount_cart_notice_message', false ) ) ) {
					$_val['cart'] = $value;
					delete_option( 'alg_wc_order_minimum_amount_cart_notice_message' );
				}
				update_option( 'alg_wc_oma_min_sum_message', $_val );
			}
		}

		/**
		 * settings_user_roles.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 */
		function settings_user_roles() {
			$data_version           = get_option( 'alg_wc_oma_data_version', array() );
			$data_version_user_role = ( isset( $data_version['user_role'] ) ? $data_version['user_role'] : 0 );
			if ( version_compare( $data_version_user_role, '2.0.0', '<' ) ) {
				$data = get_option( 'alg_wc_oma_min_sum_by_user_role', array() );
				foreach ( alg_wc_oma()->core->get_all_user_roles() as $role_key => $role_title ) {
					if ( ! isset( $data[ $role_key ] ) ) {
						$data[ $role_key ] = get_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key, 0 );
					}
					delete_option( 'alg_wc_order_minimum_amount_by_user_role_' . $role_key );
				}
				$data_version['user_role'] = alg_wc_oma()->version;
				update_option( 'alg_wc_oma_min_sum_by_user_role', $data );
				update_option( 'alg_wc_oma_data_version', $data_version );
			}
		}

		/**
		 * shortcode_atts.
		 *
		 * @version 3.0.0
		 * @since   3.0.0
		 */
		function shortcode_atts( $out, $pairs, $atts ) {
			if ( isset( $atts['sum_or_qty'] ) ) {
				$out['type'] = $atts['sum_or_qty'];
			}
			return $out;
		}

		/**
		 * notice_placeholders.
		 *
		 * @version 3.3.0
		 * @since   3.0.0
		 */
		function notice_placeholders( $placeholders, $min_or_max, $amount_type, $amount_data, $total, $diff ) {
			switch ( $amount_type ) {
				case 'sum':
					$placeholders = array_merge( $placeholders, array(
						"%{$min_or_max}_order_sum%"      => wc_price( $amount_data['amount'] ),
						"%cart_total_sum%"               => wc_price( $total ),
						"%{$min_or_max}_order_sum_diff%" => wc_price( $diff ),
						"%minimum_order_amount%"         => wc_price( $amount_data['amount'] ), // deprecated since v2.0.0
						"%cart_total%"                   => wc_price( $total ),                 // deprecated since v2.0.0
					) );
					break;
				case 'qty':
					$placeholders = array_merge( $placeholders, array(
						"%{$min_or_max}_order_qty%"      => $amount_data['amount'],
						"%cart_total_qty%"               => $total,
						"%{$min_or_max}_order_qty_diff%" => $diff,
					) );
					break;
			}
			return $placeholders;
		}

	}

endif;

return new Alg_WC_OMA_Deprecated();
