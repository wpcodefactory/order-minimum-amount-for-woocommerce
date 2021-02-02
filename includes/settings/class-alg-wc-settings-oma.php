<?php
/**
 * Order Minimum Amount for WooCommerce - Settings
 *
 * @version 3.4.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_OMA' ) ) :

class Alg_WC_Settings_OMA extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 3.4.0
	 * @since   1.0.0
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
		require_once( 'class-alg-wc-oma-settings-cart-products.php' );
		require_once( 'class-alg-wc-oma-settings-products-cart-total.php' );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
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

}

endif;

return new Alg_WC_Settings_OMA();
