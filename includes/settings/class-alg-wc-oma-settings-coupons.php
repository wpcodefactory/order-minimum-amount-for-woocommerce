<?php
/**
 * Order Minimum Amount for WooCommerce - Coupons Section Settings
 *
 * @version 4.0.0
 * @since   4.0.0
 *
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_OMA_Settings_Coupons' ) ) :

	class Alg_WC_OMA_Settings_Coupons extends Alg_WC_OMA_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function __construct() {
			$this->id   = 'coupons';
			$this->desc = __( 'Coupons', 'order-minimum-amount-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * get_coupons.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function get_coupons() {
			$coupons = array();
			$args    = array(
				'post_type'      => 'shop_coupon',
				'post_status'    => 'any',
				'posts_per_page' => - 1,
				'orderby'        => 'ID',
				'order'          => 'DESC',
				'fields'         => 'ids',
			);
			$loop    = new WP_Query( $args );
			if ( $loop->have_posts() ) {
				foreach ( $loop->posts as $coupon_id ) {
					$coupons[ $coupon_id ] = get_the_title( $coupon_id ) . " (#{$coupon_id})";
				}
			}
			return $coupons;
		}

		/**
		 * get_settings.
		 *
		 * @version 4.0.0
		 * @since   4.0.0
		 */
		function get_settings() {

			$coupons = $this->get_coupons();

			$settings = array(
				array(
					'title' => __( 'Coupons', 'order-minimum-amount-for-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Skip min/max amount checks if selected coupons were applied.', 'order-minimum-amount-for-woocommerce' ),
					'id'    => 'alg_wc_oma_coupons_options',
				),
				array(
					'title'             => __( 'Coupons', 'order-minimum-amount-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Enable section', 'order-minimum-amount-for-woocommerce' ) . '</strong>',
					'id'                => 'alg_wc_oma_coupons_enabled',
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_oma_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'   => __( 'Validate all coupons', 'order-minimum-amount-for-woocommerce' ),
					'desc'    => __( 'Validate all applied coupons, or at least one coupon', 'order-minimum-amount-for-woocommerce' ),
					'id'      => 'alg_wc_oma_coupons_validate_all',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'    => __( 'Require', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Only check min/max amounts if selected coupon(s) were applied.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_coupons_include',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $coupons,
				),
				array(
					'title'    => __( 'Exclude', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => __( 'Do not check min/max amounts if selected coupon(s) were applied.', 'order-minimum-amount-for-woocommerce' ),
					'id'       => 'alg_wc_oma_coupons_exclude',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $coupons,
				),
				array(
					'title'    => __( 'Exclude all', 'order-minimum-amount-for-woocommerce' ),
					'desc'     => __( 'Do not check min/max amounts if any coupons have been applied', 'order-minimum-amount-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'The %s option will overwrite this option.', 'order-minimum-amount-for-woocommerce' ), __( 'Require', 'order-minimum-amount-for-woocommerce' ) ),
					'id'       => 'alg_wc_oma_coupons_exclude_all',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_oma_coupons_options',
				),
			);

			return $settings;
		}

	}

endif;

return new Alg_WC_OMA_Settings_Coupons();
