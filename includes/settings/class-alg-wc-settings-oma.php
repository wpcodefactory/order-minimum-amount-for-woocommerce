<?php
/**
 * Order Minimum Amount for WooCommerce - Settings
 *
 * @version 4.0.0
 * @since   1.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_OMA' ) ) :

class Alg_WC_Settings_OMA extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 4.0.0
	 * @since   1.0.0
	 *
	 * @todo    [maybe] `memberships`: move to right after `users` || `user-roles`?
	 */
	function __construct() {
		$this->id    = 'alg_wc_oma';
		$this->label = __( 'Order Min/Max Amount', 'order-minimum-amount-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'maybe_unsanitize_option' ), PHP_INT_MAX, 3 );
		// Sections
		require_once( 'class-alg-wc-oma-settings-section.php' );
		require_once( 'class-alg-wc-oma-settings-general.php' );
		require_once( 'class-alg-wc-oma-settings-amounts.php' );
		require_once( 'class-alg-wc-oma-settings-messages.php' );
		require_once( 'class-alg-wc-oma-settings-user-roles.php' );
		require_once( 'class-alg-wc-oma-settings-users.php' );
		require_once( 'class-alg-wc-oma-settings-products.php' );
		require_once( 'class-alg-wc-oma-settings-shipping.php' );
		require_once( 'class-alg-wc-oma-settings-gateways.php' );
		require_once( 'class-alg-wc-oma-settings-memberships.php' );
		require_once( 'class-alg-wc-oma-settings-currencies.php' );
		require_once( 'class-alg-wc-oma-settings-coupons.php' );
		require_once( 'class-alg-wc-oma-settings-cart-products.php' );
		require_once( 'class-alg-wc-oma-settings-products-cart-total.php' );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 *
	 * @todo    [later] find better solution
	 */
	function maybe_unsanitize_option( $value, $option, $raw_value ) {
		return ( ! empty( $option['alg_wc_oma_raw'] ) ? $raw_value : $value );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'    => __( 'Reset Settings', 'order-minimum-amount-for-woocommerce' ),
				'type'     => 'title',
				'id'       => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'    => __( 'Reset section settings', 'order-minimum-amount-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Reset', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'order-minimum-amount-for-woocommerce' ),
				'id'       => $this->id . '_' . $current_section . '_reset',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'order-minimum-amount-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
			}
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'order-minimum-amount-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

	/**
	 * Output sections.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/4.9.2/includes/admin/settings/class-wc-settings-page.php#L100
	 */
	function output_sections() {
		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $this->style_section_label( $label, $id ) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}

	/**
	 * style_section_label.
	 *
	 * @version 4.0.0
	 * @since   4.0.0
	 */
	function style_section_label( $label, $id ) {
		$enable_section_options = array(
			'cart_products'       => 'alg_wc_oma_products_enabled',
			'coupons'             => 'alg_wc_oma_coupons_enabled',
			'currencies'          => 'alg_wc_oma_by_currency_enabled',
			'gateways'            => 'alg_wc_oma_by_gateway_enabled',
			'memberships'         => 'alg_wc_oma_by_membership_enabled',
			'products'            => array( 'alg_wc_oma_per_product_enabled', 'alg_wc_oma_per_product_cat_enabled', 'alg_wc_oma_per_product_tag_enabled' ),
			'products_cart_total' => 'alg_wc_oma_products_cart_total_enabled',
			'shipping'            => 'alg_wc_oma_by_shipping_enabled',
			'user_roles'          => 'alg_wc_oma_by_user_role_enabled',
			'users'               => 'alg_wc_oma_by_user_enabled',
		);
		$is_section_enabled = false;
		if ( isset( $enable_section_options[ $id ] ) ) {
			if ( is_array( $enable_section_options[ $id ] ) ) {
				foreach ( $enable_section_options[ $id ] as $option ) {
					if ( 'yes' === get_option( $option, 'no' ) ) {
						$is_section_enabled = true;
						break;
					}
				}
			} else {
				$is_section_enabled = ( 'yes' === get_option( $enable_section_options[ $id ], 'no' ) );
			}
		}
		return ( $is_section_enabled ? '<span style="color:green;">' . $label . '</span>' : $label );
	}

}

endif;

return new Alg_WC_Settings_OMA();
