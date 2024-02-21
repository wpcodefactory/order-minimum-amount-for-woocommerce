<?php
/*
Plugin Name: Order Minimum/Maximum Amount for WooCommerce
Plugin URI: https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/
Description: Set required minimum and/or maximum order amounts (e.g. sum, quantity, weight, volume, etc.) in WooCommerce.
Version: 4.4.1
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: order-minimum-amount-for-woocommerce
Domain Path: /langs
Copyright: © 2023 WPFactory
WC tested up to: 8.6
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Handle is_plugin_active function
if ( ! function_exists( 'alg_wc_oma_is_plugin_active' ) ) {
	/**
	 * alg_wc_oma_is_plugin_active.
	 *
	 * @version 4.0.7
	 * @since   4.0.7
	 */
	function alg_wc_oma_is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', (array) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
}

// Check for active plugins
if (
	! alg_wc_oma_is_plugin_active( 'woocommerce/woocommerce.php' ) ||
	( 'order-minimum-amount-for-woocommerce.php' === basename( __FILE__ ) && alg_wc_oma_is_plugin_active( 'order-minimum-amount-for-woocommerce-pro/order-minimum-amount-for-woocommerce-pro.php' ) )
) {
	return;
}

if ( ! class_exists( 'Alg_WC_OMA' ) ) :
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
endif;

if ( ! class_exists( 'Alg_WC_OMA' ) ) :

	/**
	 * Main Alg_WC_OMA Class.
	 *
	 * @version 3.4.1
	 * @since   1.0.0
	 *
	 * @class   Alg_WC_OMA
	 */
	final class Alg_WC_OMA {

		/**
		 * Plugin version.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $version = '4.4.1';

		/**
		 * $_instance.
		 *
		 * @since 1.0.0
		 * @var   Alg_WC_OMA The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * Alg_WC_OMA_Pro.
		 *
		 * @since 4.1.7
		 *
		 * @var Alg_WC_OMA_Pro
		 */
		public $pro;

		/**
		 * Main Alg_WC_OMA Instance.
		 *
		 * Ensures only one instance of Alg_WC_OMA is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @static
		 * @return  Alg_WC_OMA - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Initializer.
		 *
		 * @version 4.2.7
		 * @since   4.2.7
		 *
		 * @access  public
		 */
		function init() {
			// Set up localisation
			add_action( 'init', array( $this, 'localize' ) );

			// Pro
			if ( 'order-minimum-amount-for-woocommerce-pro.php' === basename( __FILE__ ) ) {
				$this->pro = require_once 'includes/pro/class-alg-wc-oma-pro.php';
			}

			// Include required files
			$this->includes();

			// Admin
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * localize.
		 *
		 * @version 3.4.1
		 * @since   3.4.1
		 */
		function localize() {
			load_plugin_textdomain( 'order-minimum-amount-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		function includes() {
			// Handling deprecated options
			require_once 'includes/class-alg-wc-oma-deprecated.php';
			// Core
			$this->core = require_once 'includes/class-alg-wc-oma-core.php';
		}

		/**
		 * admin.
		 *
		 * @version 3.0.0
		 * @since   1.2.0
		 */
		function admin() {
			// Action links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			// Settings
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			// Version update
			if ( get_option( 'alg_wc_oma_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_updated' ) );
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 *
		 * @param   mixed $links
		 *
		 * @return  array
		 */
		function action_links( $links ) {
			$custom_links   = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_oma' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			if ( 'order-minimum-amount-for-woocommerce.php' === basename( __FILE__ ) ) {
				$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/">' .
								  __( 'Go Pro', 'order-minimum-amount-for-woocommerce' ) . '</a>';
			}
			return array_merge( $custom_links, $links );
		}

		/**
		 * Add Order Minimum Amount settings tab to WooCommerce settings.
		 *
		 * @version 3.0.0
		 * @since   1.0.0
		 */
		function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once 'includes/settings/class-alg-wc-settings-oma.php';
			return $settings;
		}

		/**
		 * version_updated.
		 *
		 * @version 3.0.0
		 * @since   1.2.0
		 */
		function version_updated() {
			do_action( 'alg_wc_oma_version_updated' );
			update_option( 'alg_wc_oma_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return  string
		 */
		function plugin_url() {
			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return  string
		 */
		function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	}

endif;

if ( ! function_exists( 'alg_wc_oma' ) ) {
	/**
	 * Returns the main instance of Alg_WC_OMA to prevent the need to use globals.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 *
	 * @return  Alg_WC_OMA
	 */
	function alg_wc_oma() {
		return Alg_WC_OMA::instance();
	}
}

add_action(
	'plugins_loaded',
	function () {
		$plugin = alg_wc_oma();
		$plugin->init();
	}
);


add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
